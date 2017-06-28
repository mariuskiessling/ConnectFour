<?php
include('header.tpl.php');
?>

<body>
    <div id="authentication">
        <?php
        if(isset($notification)) { ?>
            <div class="notification">
                <h3 class="title"><span class="<?= $notification['icon'] ?>"></span><?= $notification['title'] ?></h3>
                <p class="content">
                    <?= $notification['message'] ?>
                </p>
            </div>
        <?php } ?>

        <span id="projectInformation">This project is part of the Webengineering course at the Corporate State University Stuttgart.<br>Author: Marius Kießling</span>

        <div id="backgroundBox"></div>

        <div id="registerWrapper" class="authenticationWrapper">
            <div id="authenticationContent">
                <h1>Registrieren</h1>

                <form action="/register" method="post">
                    <div class="form">
                        <label for="email">E-Mailadresse</label>
                        <input type="text" id="email" name="email" value="<?= @$email ?>" />

                        <label for="username">Nutzername</label>
                        <input type="text" id="username" name="username" value="<?= @$username ?>" />

                        <label for="password">Passwort</label>
                        <input type="password" id="password" name="password" value="<?= @$password ?>" />
                    </div>

                    <div id="bottomButtons">
                        <input type="submit" class="button" value="Registrieren">
                        <a href="/login"><span class="arrow_back"></span> Zur Anmeldung</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
