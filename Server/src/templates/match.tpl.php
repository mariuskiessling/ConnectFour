<?php
include('header.tpl.php');
?>

<body>
    <?php
    include('nav.tpl.php');
    ?>

    <div id="match" class="centerWrapper">
        <div id="sidebar">
            <h1>Spielinformationen</h1>

            <div class="matchInformation">
                <span class="label"><span class="icon_rook"></span>Gegner:</span>
                <span class="value"><?= $opponent ?></span>
            </div>
            <div class="matchInformation">
                <span class="label"><span class="icon_grid-3x3"></span>Züge:</span>
                <span class="value"><?= $moves ?></span>
            </div>
            <div class="matchInformation">
                <span class="label"><span class="icon_circle-slelected"></span>Farbe:</span>
                <span class="value"><?= $color ?></span>
            </div>

            <div class="buttons">
                <button id="surrenderButton" class="button">Aufgeben</button>
                <button class="button"><a href="/lobby">Zur Lobby</a></button>
            </div>
        </div>

        <div id="gameContent"></div>

        <div id="surrenderQuestion" class="hidden">
            <h1>Möchten Sie wirklich aufgeben?</h1>

            Dann geben Sie bitte folgendes ein:
            <span id="surrenderText">Ich bin ein Verlierer!</span>

            <input type="text" id="surrenderTextAnswer" />

            <span id="surrenderError" class="hidden">Der eingegebene Text entspricht nicht dem obigen!</span>

            <button class="button" id="confirmSurrenderButton">Aufgeben</button>
            <span id="cancelSurrenderLink"><span class="arrow_back"></span> Doch nicht aufgeben!</span>
        </div>

        <script src="/js/main.js" charset="utf-8"></script>
        <script>
            Game.init(7, 6);
            Interface.addSurrenderButtonClickedEventListener("surrenderButton");
            Interface.addConfirmSurrenderButtonClickedEventListener("confirmSurrenderButton");
            Interface.addCancelSurrenderLinkClickedEventListener("cancelSurrenderLink");
        </script>
    </div>
</body>
</html>
