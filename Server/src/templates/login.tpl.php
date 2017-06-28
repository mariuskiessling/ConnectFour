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

        <div id="loginWrapper" class="authenticationWrapper">
            <div id="authenticationContent">
                <h1>Login</h1>

                <form action="/login" method="post">
                    <div class="form">
                        <label for="identifier">E-Mailadresse oder Nutzername</label>
                        <input type="text" id="identifier" name="identifier" value="<?= @$identifier ?>" />

                        <label for="password">Passwort</label>
                        <input type="password" id="password" name="password"  />
                    </div>

                    <div id="bottomButtons">
                        <input type="submit" class="button" value="Anmelden" />
                        <a href="/register"><span class="icon_plus_alt2"></span> Account erstellen</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
