<?php
session_start();
require_once("database.php");

if(!isset($errors)) {
    $errors = [];
}

if (!isset($_SESSION['username'])) {
    header('location: login.php');
}

$spiele = [];
$user = $DB_LINK->getUser(null, $_SESSION['username']);
if ($user == null) {
    $errors[] = "Benutzer scheint nicht zu existieren!";
} else {
    $spiele = $DB_LINK->getSpiele($user);
    if ($spiele == null) {
        $errors[] = "Sie sind kein Mitglied in Spielen! Erstellen sie doch eins!";
    }
}

include("errors.php");

foreach($spiele as $spiel) {
    $task = $spiel->getTask();
    $beschreibung = $spiel->getBeschreibung();
    $id = $spiel->getID();
    echo "Task: $task, Beschreibung: $beschreibung, Beitreten: <a href='spiel.php?id=$id'>LINK</a><br>";
}