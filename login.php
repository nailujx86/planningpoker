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
<!DOCTYPE html>
<html lang="de">
<?php
    session_start();
    $title = "Login - PlanningPoker";
    include("partials/header.part.php");
?>
<body>
    <div class="c">
        <header>
            <h1 class="center"><a class="undecorated" href="/index.php">Login</a></h1>
        </header>
        <div class="card gimme_space">
            <div class="c">
            <?php include("errors.php") ?>
            <form action="login.php" method="POST">
                Username:<br>
                <input class="full" type="text" name="name" placeholder="scrumMeister111" required><br>
                Passwort:<br>
                <input class="full" type="password" name="passw" required><br><br>
                <input type="submit" name="login" value="Login" class="b primary">
            </form>
            </div>
        </div>
    </div>
</body>
<?php include("partials/footer.part.php"); ?>
</html>