<?php
include('header.tpl.php');
?>

<body>
    <div id="registerConfirmationEmail">
        <div id="mailWindow">
            <div id="mailWindowContent">
                <span id="recipient"><?= @$username ?> &lt;<?= @$email ?>&gt;</span>
                <div id="mailContent">
                    Hallo <?= @$username ?>,<br /><br />

                    wir haben Ihren Account erfolgreich erstellt. Bitte klicken Sie auf den folgenden Link, um Ihren Account zu bestätigen:<br /><br />

                    <a href="<?= @$host ?>/register/confirm?token=<?= @$registrationToken ?>" class="formatted"><?= @$host ?>/register/confirm?token=<?= @$registrationToken ?></a><br /><br />

                    Wir wünschen viel Spaß mit 4-Gewinnt!
                </div>
            </div>
        </div>
    </div>
</body>
</html>
