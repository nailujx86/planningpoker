<?php
session_start();
require("database.php");

$errors = [];

if (isset($_POST['login']) && !isset($_SESSION['username'])) {
    $username = $_POST['name'];
    $password = $_POST['passw'];

    if (empty($username)) {
        $errors[] = "Benutzername fehlt!";
    }
    if (empty($password)) {
        $errors[] = "Passwort fehlt!";
    }

    if (count($errors) == 0) {
        $user = $DB_LINK->getUser(null, $username);
        if ($user == null || !($user->verifyPassword($password))) {
            $errors[] = "Falsche Login-Daten!";
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
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<?php include("errors.php"); ?>

<body>
    <form action="login.php" method="POST">
        Username: <input type="text" name="name" placeholder="scrumMeister111" required><br>
        Passwort: <input type="password" name="passw" required><br>
        <input type="submit" name="login" value="Login">
    </form>
</body>

</html>