<?php if($_SESSION['username']): ?>
<span class="acc">Hallo <?=htmlspecialchars($_SESSION['username'])?>! / <a href="/logout.php">Logout</a></span>
<?php else: ?>
<span class="acc"><a href="/login.php">Login</a> / <a href="/register.php">Registrieren</a></span>
<?php endif ?>