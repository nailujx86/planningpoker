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
} else if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('refresh:3;url=spiele.php');
    $errors[] = "Kein Spiel ausgewählt! Du wirst in 3 Sekunden zur Spielauswahl weitergeleitet.";
} else {
    $runde = $DB_LINK->getRunde($_GET['id']);
    if ($runde == null) {
        header('refresh:3;url=spiele.php');
        $errors[] = "Diese Runde existiert nicht! Du wirst in 3 Sekunden zur Spielauswahl weitergeleitet.";
    } else {
        if (isset($_SESSION['username'])) {
            foreach ($runde->getTeilnehmer() as $teilnehmer) {
                if ($teilnehmer->getUsername() == $_SESSION['username']) {
                    $isTeilnehmer = true;
                    $curUser = $teilnehmer;
                    break;
                }
            }
        }
        if ($runde->getSpiel()->getAdmin()->getUsername() == $_SESSION['username']) {
            $isAdmin = true;
        }
        if (!$isTeilnehmer && !$isAdmin) {
            $errors[] = "Du bist kein Mitglied dieser Runde!";
            //$runde = null;
        } else {
            foreach ($runde->getZuege() as $zugObj) {
                if ($zugObj->getUser()->getUsername() == $_SESSION['username']) {
                    $zug = $zugObj;
                }
            }
        }
    }
}

/**
 * Nutzer hinzufügen
 */
if (isset($_POST['add_user']) && $isAdmin && isset($_POST['username']) && isset($runde)) {
    $user = $DB_LINK->getUser(null, $_POST['username']);
    if ($user == null) {
        $errors[] = "Dieser Benutzer existiert nicht!";
    } else {
        $zug = new Zug($runde, $user);
        $zugAdded = $DB_LINK->addZug($zug);
        if (!$zugAdded) {
            $errors[] = "Dieser Benutzer wurde bereits hinzugefügt!";
        } else {
            $runde->addZug($zug);
            $runde->setAbgeschlossen($DB_LINK->updateRundeAbgeschlossen($runde));
            header('location: ' . htmlspecialchars($_SERVER['REQUEST_URI']), true, 303);
            exit;
        }
    }
} else if (isset($_POST['add_user']) && !$isAdmin) {
    $errors[] = "Du bist nicht der Admin dieses Spiels!";
}

/**
 * Karte aktualisieren
 */
if (isset($_POST['update_karte']) && $isTeilnehmer && isset($_POST['karte']) && isset($runde) && $runde->getAbgeschlossen() == false) {
    $zug = new Zug($runde, $curUser, $_POST['karte']);
    $updated = $DB_LINK->updateZug($zug);
    if (!$updated) {
        $errors[] = "Konnte Karte nicht aktualisieren!";
    } else {
        $runde->setZug($zug);
        $runde->setAbgeschlossen($DB_LINK->updateRundeAbgeschlossen($runde));
        header('location: ' . htmlspecialchars($_SERVER['REQUEST_URI']), true, 303);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<?php
$title = "Rundenübersicht - PlanningPoker";
include("partials/header.part.php");
?>

<body>
    <div class="c">
        <?php include("partials/account.part.php"); ?>
        <header>
            <h1 class="center"><a class="undecorated" href="/index.php">Rundenübersicht</a></h1>
            <?php if (isset($zug)) : ?>
                <p class="center"></p>
            <?php endif ?>
        </header>
        <?php if (count($errors) > 0) : ?>
            <div class="r gimme_space">
                <div class="card">
                    <div class="c">
                        <?php include("errors.php") ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
        <div class="r gimme_space">
            <div class="i 8">
                <?php if (isset($zug)) : ?>
                    <?php if (isset($zug) && $zug->getKarte() != '') : ?>
                        <?php foreach ($runde->getZuege() as $zugObj) : ?>
                            <div class="card">
                                <div class="c">
                                    <h4><?= htmlspecialchars($zugObj->getUser()->getUsername()) ?>:</h4>
                                    <p><?= htmlspecialchars($zugObj->getKarte()) ?></p>
                                </div>
                            </div>
                            <br>
                        <?php endforeach ?>
                    <?php else : ?>
                        <div class="card">
                            <div class="c">
                                <h4>Noch nicht gezogen!</h4>
                            </div>
                        </div>
                        <br>
                    <?php endif ?>
                <?php endif ?>
                <?php if ($isTeilnehmer && isset($zug) && $runde->getAbgeschlossen() == false) : ?>
                    <div class="card">
                        <div class="c">
                            <form method="POST">
                                Karte:<br>
                                <input class="full" type="text" id="kartenfeld" name="karte" placeholder="<?= htmlspecialchars($zug->getKarte()) ?>" required><br><br>
                                <?php foreach($runde->getSpiel()->getKarten() as $karte): ?>
                                <button class="b" type="button" onclick="document.getElementById('kartenfeld').value = '<?=htmlspecialchars($karte)?>';"><?=htmlspecialchars($karte)?></button>
                                <?php endforeach ?>
                                <br><br>
                                <input class="b primary" type="submit" name="update_karte" value="Karte aktualisieren">
                            </form>
                        </div>
                    </div>
                <?php endif ?>
                <?php if (isset($runde)) : ?><br><a class="b primary" href="/spiel.php?id=<?= $runde->getSpiel()->getId() ?>">Zurück zum Spiel</a><?php endif ?>
            </div>
            <?php if ($isAdmin && isset($runde)) : ?>
                <div class="i 4">
                    <div class="card">
                        <div class="c">
                            <h3>Administration</h3>
                            <h4>Benutzer hinzufügen:</h4>
                            <form method="POST">
                                User:<br>
                                <input class="full" type="text" name="username" placeholder="Benutzername" required><br><br>
                                <input type="submit" name="add_user" value="Benutzer hinzufügen" class="b">
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</body>

</html>