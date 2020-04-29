<?php if (count($errors) > 0) : ?>
    <div class="error">
        <h3>Fehler:</h3>
        <ul>
        <?php foreach ($errors as $error) : ?>
            <li><?=htmlspecialchars($error)?></li>
        <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>