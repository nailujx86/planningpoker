<?php if($_SESSION['username']): ?>
<span class="acc">Hallo <?php echo $_SESSION['username'];?>! / <a href="/logout.php">Logout</a></span>
<?php else: ?>
<span class="acc"><a href="/login.php">Login</a> / <a href="/register.php">Register</a></span>
<?php endif ?>