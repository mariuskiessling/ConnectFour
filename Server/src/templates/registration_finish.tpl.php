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

        <div id="finishRegistrationWrapper" class="authenticationWrapper">
            <div id="authenticationContent">
                <h1>Profildaten</h1>

                <form action="/register/finish" method="post" enctype="multipart/form-data">
                    <div class="form">
                        <label for="name">Vor- und Nachname</label>
                        <input type="text" id="name" name="name"  />

                        <label for="birthday">Geburtstag</label>
                        <input type="text" id="birthday" name="birthday"  />

                        <label for="sex">Geschlecht</label>
                        <select name="sex">
                            <option value="m">Männlich</option>
                            <option value="f">Weiblich</option>
                        </select>

                        <label>Profilbild (PNG oder JPG, max. 1MB)</label>
                        <input id="profilePicture" name="profile_picture" type="file" accept="image/x-png,image/jpeg">
                    </div>

                    <div id="bottomButtons">
                        <input type="submit" class="button" value="Daten aktualisieren">
                        <a href="/lobby"><span class="icon_close_alt2"></span> Überspringen</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
