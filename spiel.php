<?php
session_start();
require_once("database.php");

$errors = [];
$spiel;
$runden;
$isAdmin = false;

if (!isset($_SESSION['username'])) {
    header('refresh:3;url=login.php');
    $errors[] = "Nicht angemeldet! Du wirst in 3 Sekunden weitergeleitet.";
} else if (!isset($_GET['id'])) {
    header('refresh:3;url=spiele.php');
    $errors[] = "Kein Spiel ausgewählt! Du wirst in 3 Sekunden zur Spielauswahl weitergeleitet.";
} else {
    $spiel = $DB_LINK->getSpiel($_GET['id']);
    if ($spiel == null) {
        header('refresh:3;url=spiele.php');
        $errors[] = "Kein Spiel mit der ID gefunden! Du wirst in 3 Sekunden zur Spielauswahl weitergeleitet.";
    } else {
        $runden = $DB_LINK->getRunden($spiel);
    }
    if($spiel->getAdmin()->getUsername() == $_SESSION['username']) {
        $isAdmin = true;
    }
}
if (isset($_POST['add_runde']) && isset($_SESSION['username']) && isset($spiel) && isset($_POST['mitglieder']) && $isAdmin) {
    $runde = new Runde($spiel);
    $user = $DB_LINK->getUser(null, $_SESSION['username']);
    $mitglieder = preg_split('/\r\n|\r|\n/', $_POST['mitglieder']);
    $runde->setId($DB_LINK->addRunde($runde));
    foreach ($mitglieder as $mitglied) {
        $mitgliedUser = $DB_LINK->getUser(null, $mitglied);
        if ($mitgliedUser) {
            $zug = new Zug($runde, $mitgliedUser);
            $DB_LINK->addZug($zug);
        } else {
            $errors[] = $mitglied . " ist kein gültiger Benutzer!";
        }
    }
    header('location: runde.php?id='.$runde->getId());
} else if (isset($_POST['add_runde']) && !isset($_POST['mitglieder'])) {
    $errors[] = "Keine Mitglieder angegeben!";
} else if( isset($_POST['add_runde']) && !$isAdmin) {
    $errors[] = "Sie sind nicht der Runden-Administrator!";
}

?>

<?php if ($spiel) : ?>
<?php
    echo "Task: " . $spiel->getTask() . "<br>";
    echo "Beschreibung: " . $spiel->getBeschreibung() . "<br>";
    echo "Biste Admin?: " . json_encode($isAdmin) . "<br>";
    $lastTeilnehmer = $runden[count($runden) - 1]->getTeilnehmer();
    $lastTeilnehmerArr = [];
    foreach ($lastTeilnehmer as $teilnehmer) {
        $lastTeilnehmerArr[] = $teilnehmer->getUsername();
    }
    $lastTeilnehmerArrJson = json_encode($lastTeilnehmerArr);
?>
<script>
var lastTeilnehmer = <?php echo $lastTeilnehmerArrJson; ?>;
</script>
<?php foreach ($runden as $runde) {
        echo "Teilnehmer: " . count($runde->getTeilnehmer());
        echo " Zuege: " . array_reduce($runde->getZuege(), function($c, $i) {return $c + ($i->getKarte() != '') ? 0 : 1;}, 0);
        echo " Abgeschlossen: " . json_encode($runde->getAbgeschlossen());
        echo " Erstellt: " . $runde->getErstellt();
        echo " Link: <a href='runde.php?id=" . $runde->getID() . "'>Runde</a><br>";
    } ?>
<?php endif ?>
<?php if($isAdmin && isset($runde)): ?>
<form method="POST">
User: <textarea name="mitglieder" placeholder="Benutzernamen" id="mitglieder" required></textarea><br>
<button type="submit" name="add_runde">Neue Runde mit Benutzern erstellen</button>
<button type="button" onclick="document.getElementById('mitglieder').value = lastTeilnehmer.join('\n');">Mit letzten Teilnehmern füllen</button>
</form>
<?php endif ?>

<?php include("errors.php"); ?>