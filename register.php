<?php
session_start();
require("database.php");
require_once("user.php");

$errors = [];

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
    if(empty($passwordconf)) {
        $errors[] = "Passwort bitte zwei mal eingeben!";
    }
    if($password != $passwordconf) {
        $errors[] = "Passwörter stimmen nicht überein!";
    }
    if(empty($mail)) {
        $errors[] = "Email fehlt!";
    }

    if(count($errors) == 0) {
        $user = new User($username, $password, $mail);
        $id = $DB_LINK->addUser($user);
        if($id == null) {
            $errors[] = "Benutzer mit diesen Daten existiert bereits!";
        } else {
            $_SESSION['username'] = $username;
            header("location: index.php");
        }
    }
} else {
    if(isset($_SESSION['username'])) {
        $errors[] = "Bereits angemeldet!";
    }
}

?>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrieren</title>
</head>
<?php include("errors.php"); ?>
<body>
    <form action="register.php" method="POST">
        Username: <input type="text" name="name" placeholder="scrumMeister111" required><br>
        Passwort: <input type="password" name="passw" required><br>
        Passwort bestätigen: <input type="password" name="passwconf" required><br>
        Email: <input type="email" name="mail" required><br>
        <input type="submit" name="register" value="Registrieren">
    </form>
</body>

</html>