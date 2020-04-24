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
} else if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
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
    if($spiel != null && $spiel->getAdmin()->getUsername() == $_SESSION['username']) {
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
        if ($mitgliedUser != null) {
            $zug = new Zug($runde, $mitgliedUser);
            $DB_LINK->addZug($zug);
        } else {
            $errors[] = $mitglied . " ist kein gültiger Benutzer!";
        }
    }
    header('location: runde.php?id='.$runde->getId());
    exit;
} else if (isset($_POST['add_runde']) && !isset($_POST['mitglieder'])) {
    $errors[] = "Keine Mitglieder angegeben!";
} else if( isset($_POST['add_runde']) && !$isAdmin) {
    $errors[] = "Sie sind nicht der Runden-Administrator!";
}

if(isset($_POST['transfer_ownership']) && isset($_SESSION['username']) && isset($spiel) && isset($_POST['username']) && $isAdmin) {
    $user = $DB_LINK->getUser(null, $_POST['username']);
    if($user == null) {
        $errors[] = $_POST['username'] . " ist kein gültiger Benutzer!";
    } else {
        $spiel->setAdmin($user);
        $DB_LINK->updateSpielAdmin($spiel);
        header('location: '.htmlspecialchars($_SERVER['REQUEST_URI']), true, 303);
        exit;
    }
}

$lastTeilnehmerArr = [];
if($spiel) {
    if(!$runden || count($runden) == 0) {
        $lastTeilnehmer = [];
        $lastTeilnehmerArr = [$_SESSION['username']];
    } else {
        $lastTeilnehmer = $runden[count($runden) - 1]->getTeilnehmer();
        $lastTeilnehmerArr = [];
        foreach ($lastTeilnehmer as $teilnehmer) {
            $lastTeilnehmerArr[] = $teilnehmer->getUsername();
        }
    }
}
$lastTeilnehmerArrJson = json_encode($lastTeilnehmerArr);

?>
<!DOCTYPE html>
<html lang="de">
<?php
    $title = "Spielübersicht - PlanningPoker";
    include("partials/header.part.php");
?>
<script>
    var lastTeilnehmer = <?=$lastTeilnehmerArrJson?>;
</script>
<body>
    <div class="c">
        <?php include("partials/account.part.php"); ?>
        <header>
            <h1 class="center"><a class="undecorated" href="/index.php">Spielübersicht</a></h1>
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
        <?php if($spiel): ?>
        <div class="r gimme_space">
            <div class="i 8">
                <div class="card">
                    <div class="c">
                        <table class="full">
                            <tr>
                                <th>Teilnehmer</th>
                                <th>Züge</th>
                                <th>Abgeschlossen</th>
                                <th>Erstellt</th>
                                <th></th>
                            </tr>
                            <?php foreach($runden as $runde): ?>
                            <tr>
                                <td><?=count($runde->getTeilnehmer())?></td>
                                <td><?=array_reduce($runde->getZuege(), function($c, $i) {return $c + ($i->getKarte() != '') ? 1 : 0;}, 0)?></td>
                                <td><?=$runde->getAbgeschlossen()?"✔":"❌"?>
                                <td><?=$runde->getErstellt()?></td>
                                <td><a href="runde.php?id=<?=$runde->getID()?>">Öffnen</a></td>
                            </tr>
                            <?php endforeach ?>
                        </table>
                    </div>
                </div>
                <br><a href="/spiele.php" class="b primary">Zurück zu den Spielen</a>  
            </div>
            <div class="i 4">
                <div class="card">
                    <div class="c">
                        <h3>Spielinfo</h3>
                        <h4>Task</h4>
                        <?=htmlspecialchars($spiel->getTask())?>
                        <h4>Beschreibung</h4>
                        <?=htmlspecialchars($spiel->getBeschreibung())?>
                        <?php if($isAdmin): ?>
                        <h4>Administrator?</h4>
                        ja
                        <?php endif ?>
                    </div>
                </div>
                <?php if($isAdmin): ?>
                <br>
                <div class="card">
                    <div class="c">
                        <h3>Administration</h3>
                        <h4>Neue Runde erstellen</h4>
                        <form method="POST">
                            User:<br>
                            <textarea class="full" name="mitglieder" placeholder="Benutzernamen" id="mitglieder" required></textarea><br>
                            <button class="b full" type="submit" name="add_runde">Neue Runde mit Benutzern erstellen</button><br>
                            <button class="b full" type="button" onclick="document.getElementById('mitglieder').value = lastTeilnehmer.join('\n');">Mit letzten Teilnehmern füllen</button>
                        </form>
                        <h4>Administratorrechte übertragen</h4>
                        <form method="POST">
                            Neuer Administrator:<br>
                            <input class="full" type="text" name="username" placeholder="Benutzername" required><br><br>
                            <input type="submit" name="transfer_ownership" value="Rechte übertragen" class="b">
                        </form>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>
        <?php endif ?>
    </div>
</body>
</html>