<?php
    session_start();
    if($_SESSION['username']) {
        echo "Logged in as ".$_SESSION["username"];
        echo "<a href='logout.php'>LOGOUT</a>";
    } else {
        echo "<a href='login.php'>LOGIN</a>";
    }
?>