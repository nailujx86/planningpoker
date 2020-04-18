<?php
require("database.php");
$user = $DB_LINK->getUser(null, "test");
$spiele = $DB_LINK->getSpiele($user);
$runden = $DB_LINK->getRunden($spiele[0]);
foreach($runden as $runde) {
    $zuege = $runde->getZuege();
    echo count($zuege);
    foreach($zuege as $zug) {
        echo $zug->getKarte()."<br>";
    }
}
?>