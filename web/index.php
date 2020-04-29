<?php session_start(); ?>
<!DOCTYPE html>
<html lang="de">
<?php
    session_start();
    $title = "PlanningPoker";
    include("partials/header.part.php");
?>
<body>
    <div class="c">
        <?php include("partials/account.part.php"); ?>
        <header>
            <h1 class="center"><a class="undecorated" href="/index.php">PlanningPoker</a></h1>
            <p class="center">Eine PlanningPoker Webanwendung</p>
        </header>
        <div class="gimme_space center">
            <?php if(!isset($_SESSION['username'])): ?>
            <p><b>Du bist nicht angemeldet!</b></p><br>
            <a href="/login.php" class="b primary">Login</a>
            <p>oder</p>
            <a href="/register.php" class="b primary">Registrieren</a>
            <?php else: ?>
            <a href="/spiele.php" class="b primary">Zu den Spielen</a>
            <?php endif ?>
        </div>
    </div>
</body>
<?php include("partials/footer.part.php"); ?>
</html>