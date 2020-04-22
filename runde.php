<?php
session_start();
require_once("database.php");

$errors = [];
$runde;
$zug;
$isTeilnehmer = false;
$isAdmin = false;
$curUser;

if (!isset($_SESSION['username'])) {
    header('refresh:3;url=login.php');
    $errors[] = "Nicht angemeldet! Du wirst in 3 Sekunden weitergeleitet.";
} else if (!isset($_GET['id'])) {
    header('refresh:3;url=spiele.php');
    $errors[] = "Kein Spiel ausgewählt! Du wirst in 3 Sekunden zur Spielauswahl weitergeleitet.";
} else {
    $runde = $DB_LINK->getRunde($_GET['id']);
    if($runde == null) {
        $errors[] = "Diese Runde existiert nicht!";
    } else {
        if(isset($_SESSION['username'])) {
            foreach($runde->getTeilnehmer() as $teilnehmer) {
                if($teilnehmer->getUsername() == $_SESSION['username']) {
                    $isTeilnehmer = true;
                    $curUser = $teilnehmer;
                    break;
                }
            }
        }
        if($runde->getSpiel()->getAdmin()->getUsername() == $_SESSION['username']) {
            $isAdmin = true;
        }
        if(!$isTeilnehmer && !$isAdmin) {
            $errors[] = "Du bist kein Mitglied dieser Runde!";
            $runde = null;
        } else {
            foreach($runde->getZuege() as $zugObj) {
                if($zugObj->getUser()->getUsername() == $_SESSION['username']) {
                    $zug = $zugObj;
                }
            }
        }
    }
}

if(isset($_POST['add_user']) && $isAdmin && isset($_POST['username']) && isset($runde)) {
    $user = $DB_LINK->getUser(null, $_POST['username']);
    if($user == null) {
        $errors[] = "Dieser Benutzer existiert nicht!";
    } else {
        $zug = new Zug($runde, $user);
        $zugAdded = $DB_LINK->addZug($zug);
        if(!$zugAdded) {
            $errors[] = "Dieser Benutzer wurde bereits hinzugefügt!";
        } else {
            $runde->addZug($zug);
            $runde->setAbgeschlossen($DB_LINK->updateRundeAbgeschlossen($runde));
        }
    }
} else if(isset($_POST['add_user']) && !$isAdmin) {
    $errors[] = "Du bist nicht der Admin dieses Spiels!";
}

if(isset($_POST['update_karte']) && $isTeilnehmer && isset($_POST['karte']) && isset($runde) && $runde->getAbgeschlossen() == false) {
    $zug = new Zug($runde, $curUser, $_POST['karte']);
    $updated = $DB_LINK->updateZug($zug);
    if(!$updated) {
        $errors[] = "Konnte Karte nicht aktualisieren!";
    } else {
        $runde->setZug($zug);
        $runde->setAbgeschlossen($DB_LINK->updateRundeAbgeschlossen($runde));
    }
}

?>
<?php include("errors.php"); ?>
<?php if(isset($zug)) {
if(isset($zug) && $zug->getKarte() != '') {
    foreach($runde->getZuege() as $zugObj) {
        echo "Teilnehmer: " . $zugObj->getUser()->getUsername() . "; Karte: ".$zugObj->getKarte() . "<br>";
    }
   
} else {
    echo "Nicht gezogen!";
}

}
?>
<?php if($isTeilnehmer && isset($zug) && $runde->getAbgeschlossen() == false): ?>
<form method="POST">
Karte: <input type="text" name="karte" placeholder="<?php echo $zug->getKarte() ?>" required><br>
<input type="submit" name="update_karte" value="Karte aktualisieren">
</form>
<?php endif ?>
<?php if($isAdmin && isset($runde)): ?>
<form method="POST">
User: <input type="text" name="username" placeholder="Benutzername" required><br>
<input type="submit" name="add_user" value="Benutzer hinzufügen">
</form>
<?php endif ?>