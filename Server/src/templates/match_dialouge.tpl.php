<?php
include('header.tpl.php');
?>
<body>
    <?php
    include('nav.tpl.php');
    ?>
    <div id="dialogueMessage" class="centerWrapper">
        <span id="icon" class="<?= $dialouge['icon'] ?>"></span>
        <h1><?= $dialouge['title'] ?></h1>
        <p><?= $dialouge['message'] ?></p>

        <div id="buttons">
            <?php
            foreach($dialouge['buttons'] as $button) {
            ?>
            <button class="button"><a href="<?= $button['link']?> "><?= $button['label'] ?></a></button>
            <?php
            }
            ?>
        </div>
    </div>
</body>
</html>
