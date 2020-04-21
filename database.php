<?php
require("databaseSettings.php");
require("interfaces/database.php");
require("interfaces/userdata.php");
require("interfaces/spieldata.php");
require("interfaces/rundedata.php");
require("interfaces/zugdata.php");
require_once("objects/userobj.php");
require_once("objects/spielobj.php");
require_once("objects/zugobj.php");
require_once("objects/rundeobj.php");

class Database implements DatabaseInterface, UserDataInterface, SpielDataInterface, RundeDataInterface, ZugDataInterface
{
    private $link;

    public function __construct(string $host = "localhost", string $user, string $passw, string $name, int $port = 3306)
    {
        $this->link = mysqli_connect($host, $user, $passw, $name, $port) or die("Fehler: " . mysqli_error($this->link));
    }

    public function addUser(User $user): ?int
    {
        $statement = mysqli_prepare($this->link, "INSERT INTO User (vorname, mail, passwort, datum) VALUES (?, ?, ?, ?)");
        echo $this->link->error;
        $name = mysqli_escape_string($this->link, $user->getUsername());
        $passw = mysqli_escape_string($this->link, $user->getPasswhash());
        $mail = mysqli_escape_string($this->link, $user->getMail());
        $datum = $user->getErstellt();
        echo $datum;
        mysqli_stmt_bind_param($statement, "ssss", $name, $mail, $passw, $datum);
        mysqli_stmt_execute($statement);
        if (mysqli_affected_rows($this->link) == 1) {
            return mysqli_insert_id($this->link);
        } else {
            return null;
        }
    }

    public function getUser(int $id = null, string $name = null): ?User
    {
        $statement = null;
        if ($name == null && $id != null) {
            $statement = mysqli_prepare($this->link, "SELECT vorname, mail, passwort, datum FROM User WHERE User.ID = ?");
            mysqli_stmt_bind_param($statement, "i", $id);
        } else if ($name != null) {
            $statement = mysqli_prepare($this->link, "SELECT vorname, mail, passwort, datum FROM User WHERE User.Vorname LIKE ?");
            mysqli_stmt_bind_param($statement, "s", $name);
        } else {
            return null;
        }
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement);
        if (mysqli_stmt_num_rows($statement) != 1) {
            return null;
        }
        mysqli_stmt_bind_result($statement, $username, $mail, $pwhash, $timestamp);
        mysqli_stmt_fetch($statement);
        return new User($username, $pwhash, $mail, $timestamp);
    }

    public function addSpiel(Spiel $spiel): ?int
    {
        $statement = mysqli_prepare($this->link, "INSERT INTO Spiel (task, beschreibung, kartenset, datum) VALUE (?, ?, ?, ?)");
        $task = mysqli_escape_string($this->link, $spiel->getTask());
        $beschreibung = mysqli_escape_string($this->link, $spiel->getBeschreibung());
        $datum = mysqli_escape_string($this->link, $spiel->getBeschreibung());
        $kartenset = mysqli_escape_string($this->link, json_encode($spiel->getKarten()));
        mysqli_stmt_bind_param($statement, "ssss", $task, $beschreibung, $kartenset, $datum);
        mysqli_stmt_execute($statement);
        if (mysqli_affected_rows($this->link) == 1) {
            return mysqli_insert_id($this->link);
        } else {
            return null;
        }
    }

    public function getSpiel(int $id): ?Spiel
    {
        $statement = mysqli_prepare($this->link, "SELECT id, task, beschreibung, kartenset, datum FROM Spiel WHERE Spiel.ID = ?");
        mysqli_stmt_bind_param($statement, "i", $id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement);
        if (mysqli_stmt_num_rows($statement) != 1) {
            return null;
        }
        mysqli_stmt_bind_result($statement, $id, $task, $beschreibung, $kartenset, $datum);
        mysqli_stmt_fetch($statement);
        $kartenset = json_decode($kartenset);
        return new Spiel($task, $beschreibung, $kartenset, $datum, $id);
    }

    public function addZug(Zug $zug): ?int
    {
        $statement = mysqli_prepare($this->link, "INSERT INTO UserRunde (runde, user, karte) VALUE (?, ?, ?)");
        $runde = $zug->getRunde()->getId();
        if ($runde == -1) {
            return null;
        }
        $user = $zug->getUser()->getUsername();
        $karte = mysqli_escape_string($this->link, $zug->getKarte());
        mysqli_stmt_bind_param($statement, "iss", $runde, $user, $karte);
        mysqli_stmt_execute($statement);
        if (mysqli_affected_rows($this->link) == 1) {
            return mysqli_insert_id($this->link);
        } else {
            return null;
        }
    }

    public function updateZug(Zug $zug): bool
    {
        $statement = mysqli_prepare($this->link, "UPDATE UserRunde SET Karte = ? WHERE UserRunde.Runde = ? AND UserRunde.User = ?");
        mysqli_stmt_bind_param($statement, "sii", $zug->getKarte(), $zug->getRunde(), $zug->getUser());
        mysqli_stmt_execute($statement);
        if (mysqli_affected_rows($this->link) == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function addRunde(Runde $runde): ?int
    {
        $statement = mysqli_prepare($this->link, "INSERT INTO Runde (spiel, abgeschlossen, datum) VALUE (?, ?, ?)");
        $spiel = $runde->getSpiel()->getId();
        if ($spiel == -1) {
            return null;
        }
        $abgeschlossen = $runde->getAbgeschlossen();
        $datum = mysqli_escape_string($this->link, $runde->getErstellt());
        mysqli_stmt_bind_param($statement, "iis", $spiel, $abgeschlossen, $datum);
        mysqli_stmt_execute($statement);
        if (mysqli_affected_rows($this->link) == 1) {
            return mysqli_insert_id($this->link);
        } else {
            return null;
        }
    }

    public function getSpiele(User $user): ?array
    {
        $statement = mysqli_prepare($this->link, "SELECT DISTINCT Spiel.ID, Spiel.Task, Spiel.Beschreibung, Spiel.Kartenset, Spiel.Datum FROM Spiel, Runde, User, UserRunde WHERE (Runde.Spiel = Spiel.ID) AND (User.ID = UserRunde.User) AND (Runde.ID = UserRunde.Runde) AND (User.Vorname LIKE ?)");
        $username = mysqli_escape_string($this->link, $user->getUsername());
        $spiele = [];
        mysqli_stmt_bind_param($statement, "s", $username);
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement);
        if (mysqli_stmt_num_rows($statement) < 1) {
            return null;
        }
        mysqli_stmt_bind_result($statement, $id, $task, $beschreibung, $kartenset, $datum);
        while (mysqli_stmt_fetch($statement)) {
            $spiele[] = new Spiel($task, $beschreibung, json_decode($kartenset), $datum, $id);
        }
        return $spiele;
    }

    public function getRunden(Spiel $spiel): ?array
    {
        $statement = mysqli_prepare($this->link, "SELECT DISTINCT Runde.ID, Runde.Abgeschlossen, Runde.Datum FROM Spiel, Runde WHERE (Runde.Spiel = Spiel.ID) AND Spiel.ID = ? ORDER BY Runde.Datum DESC");
        mysqli_stmt_bind_param($statement, "i", $spiel->getId());
        $runden = [];
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement);
        if (mysqli_stmt_num_rows($statement) < 1) {
            return null;
        }
        mysqli_stmt_bind_result($statement, $id, $abgeschlossen, $datum);
        while (mysqli_stmt_fetch($statement)) {
            $runde = new Runde($spiel, $abgeschlossen, $datum, $id);
            $zugStatement = mysqli_prepare($this->link, "SELECT DISTINCT UserRunde.Runde, UserRunde.User, UserRunde.Karte FROM UserRunde WHERE (UserRunde.Runde = ?)");
            mysqli_stmt_bind_param($zugStatement, "i", $id);
            mysqli_stmt_execute($zugStatement);
            mysqli_stmt_store_result($zugStatement);
            mysqli_stmt_bind_result($zugStatement, $zugRunde, $zugUser, $zugKarte);
            while (mysqli_stmt_fetch($zugStatement)) {
                $user = $this->getUser($zugUser);
                $runde->addUser($user);
                $runde->addZug(new Zug($runde, $user, $zugKarte));
            }
            $runden[] = $runde;
        }
        return $runden;
    }

    public function updateRundeAbgeschlossen(Runde $runde): ?bool
    {
        $statement = mysqli_prepare($this->link, "SELECT DISTINCT Runde.ID FROM Runde, UserRunde WHERE Runde.ID = UserRunde.Runde AND UserRunde.Karte IS NOT NULL AND UserRunde.Karte != '' AND Runde.ID = ?");
        mysqli_stmt_bind_param($statement, "i", $runde->getId());
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement);
        $abgeschlossen = true;
        if (mysqli_stmt_num_rows($statement) < 1) {
            $abgeschlossen = false;
        }
        $updateStatement = mysqli_prepare($this->link, "UPDATE Runde SET Abgeschlossen = ? WHERE Runde.ID = ?");
        mysqli_stmt_bind_param($updateStatement, "ii", $abgeschlossen, $runde->getId());
        mysqli_stmt_execute($updateStatement);
        return $abgeschlossen;
    }

    /**
     * Get the value of link
     */
    public function getLink()
    {
        return $this->link;
    }
}

$DB_LINK = new Database($DB_HOST, $DB_USER, $DB_PASSW, $DB_NAME, $DB_PORT);

?>