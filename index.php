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
    </div>
</body>
<?php include("partials/footer.part.php"); ?>
</html>