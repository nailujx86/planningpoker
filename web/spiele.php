<?php
session_start();
require_once("database.php");

if (!isset($errors)) {
    $errors = [];
}

/**
 * Spiel hinzufügen
 */
if (!isset($_SESSION['username'])) {
    header('location: login.php');
} else if (isset($_POST['add_game']) && isset($_POST['task'])) {
    $beschreibung = $_POST['beschreibung'] ?: "";
    $spiel;
    $adminUser = $DB_LINK->getUser(null, $_SESSION['username']);
    if (isset($_POST['kartenset']) && $_POST['kartenset'] != '') {
        $kartenset = json_decode($_POST['kartenset']);
        $spiel = new Spiel($_POST['task'], $beschreibung, $adminUser, $kartenset);
    } else {
        $spiel = new Spiel($_POST['task'], $beschreibung, $adminUser);
    }
    $DB_LINK->addSpiel($spiel);
    header('location: spiele.php', true, 303);
    exit;
} else if (isset($_POST['add_game'])) {
    $errors[] = "Fehlender Task-Titel";
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
?>
<!DOCTYPE html>
<html lang="de">
<?php
session_start();
$title = "Spiele - ScrumPoker";
include("partials/header.part.php");
?>

<body>
    <div class="c">
        <?php include("partials/account.part.php"); ?>
        <header>
            <h1 class="center"><a class="undecorated" href="/index.php">Spiele</a></h1>
        </header>
        <div class="r gimme_space">
            <div class="i 8">
                <div class="card">
                    <div class="c">
                        <?php include("errors.php"); ?>
                        <?php if ($spiele) : ?>
                            <table class="full">
                                <tr>
                                    <th>Task</th>
                                    <th style="width:50%">Beschreibung</th>
                                    <th>Erstellt</th>
                                    <th></th>
                                </tr>
                                <?php foreach ($spiele as $spiel) : ?>
                                    <tr>
                                        <td><?= $spiel->getTask() ?><?= $spiel->getAdmin()->getUsername() == $_SESSION['username'] ? " <span title='Administrator'>⭐</span>" : "" ?></td>
                                        <td><?= $spiel->getBeschreibung() ?></td>
                                        <td><?= $spiel->getErstellt() ?></td>
                                        <td><a href="spiel.php?id=<?= $spiel->getID() ?>">Öffnen</a></td>
                                    </tr>
                                <?php endforeach ?>
                            </table>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="i 4">
                <div class="card">
                    <div class="c">
                        <h3>Spiel erstellen</h3>
                        <form method="POST">
                            Task:<br>
                            <input class="full" type="text" name="task" placeholder="Scrum Task" required><br>
                            Beschreibung:<br>
                            <input class="full" type="text" name="beschreibung" placeholder="Beschreibung"><br>
                            Kartenset:<br>
                            <input class="full" type="text" name="kartenset" placeholder='["0,", "1", "2", "3", "4", "5", "6", "7", "8", "9", "☕"]'><br><br>
                            <input class="b" type="submit" name="add_game" value="Spiel starten">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>