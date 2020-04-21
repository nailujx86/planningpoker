<?php
session_start();
require_once("database.php");

$errors = [];
$spiel;
$runden;
if (!isset($_SESSION['username'])) {
    header('refresh:3;url=login.php');
    $errors[] = "Nicht angemeldet! Du wirst in 3 Sekunden weitergeleitet.";
} else if (!isset($_GET['id'])) {
    header('refresh:3;url=spiele.php');
    $errors[] = "Kein Spiel ausgewählt! Du wirst in 3 Sekunden zur Spielauswahl weitergeleitet.";
} else {
    $spiel = $DB_LINK->getSpiel($_GET['id']);
    if($spiel == null) {
        header('refresh:3;url=spiele.php');
        $errors[] = "Kein Spiel mit der ID gefunden! Du wirst in 3 Sekunden zur Spielauswahl weitergeleitet.";
    } else {
        $runden = $DB_LINK->getRunden($spiel);
    }
}
?>

<?php if($spiel) : ?>
<?php 
echo "Task: ".$spiel->getTask()."<br>";
echo "Beschreibung: ".$spiel->getBeschreibung()."<br>";
?>
<?php foreach($runden as $runde) {
echo "Teilnehmer: ".count($runde->getTeilnehmer());
echo " Zuege: ".count($runde->getZuege());
echo " Abgeschlossen: ".$runde->getAbgeschlossen();
echo " Erstellt: ".$runde->getErstellt();
echo " Link: <a href='runde.php?id=".$runde->getID()."'>Runde</a><br>";
}?>
<?php endif ?>


<?php include("errors.php"); ?>