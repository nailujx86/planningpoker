<?php
session_start();
require("database.php");
require_once("objects/userobj.php");

$errors = [];

/**
 * Nutzer registrieren
 */
if (isset($_POST['register']) && !isset($_SESSION['username'])) {
    $username = $_POST['name'];
    $password = $_POST['passw'];
    $passwordconf = $_POST['passwconf'];
    $mail = $_POST['mail'];

    if (empty($username)) {
        $errors[] = "Benutzername fehlt!";
    }
    if (empty($password)) {
        $errors[] = "Passwort fehlt!";
    }
    if (strlen($password) < 8) {
        $errors[] = "Passwort zu kurz! Mindestlänge: 8 Zeichen.";
    }
    if (empty($passwordconf)) {
        $errors[] = "Passwort bitte zwei mal eingeben!";
    }
    if ($password != $passwordconf) {
        $errors[] = "Passwörter stimmen nicht überein!";
    }
    if (empty($mail)) {
        $errors[] = "Email fehlt!";
    }

    if (count($errors) == 0) {
        $user = new User($username, $password, $mail);
        $id = $DB_LINK->addUser($user);
        if ($id == null) {
            $errors[] = "Benutzer mit diesen Daten existiert bereits!";
        } else {
            $_SESSION['username'] = $username;
            header("location: index.php");
        }
    }
} else {
    if (isset($_SESSION['username'])) {
        $errors[] = "Bereits angemeldet!";
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<?php
    session_start();
    $title = "Registrieren - ScrumPoker";
    include("partials/header.part.php");
?>
<body>
    <div class="c">
        <header>
            <h1 class="center"><a class="undecorated" href="/index.php">Registrieren</a></h1>
        </header>
        <div class="card gimme_space">
            <div class="c">
            <?php include("errors.php") ?>
            <form action="register.php" method="POST">
                Username:<br>
                <input class="full" type="text" name="name" placeholder="scrumMeister111" required><br>
                Passwort:<br>
                <input class="full" type="password" name="passw" required><br>
                Passwort bestätigen:<br>
                <input class="full" type="password" name="passwconf" required><br>
                Email:<br>
                <input class="full" type="email" name="mail" required><br><br>
                <input type="submit" name="register" value="Registrieren" class="b primary">
            </form>
            </div>
        </div>
    </div>
</body>
<?php include("partials/footer.part.php"); ?>
</html>