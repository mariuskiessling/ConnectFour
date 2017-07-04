<?php
include('header.tpl.php');
?>

<body>
    <?php
    include('nav.tpl.php');
    ?>

    <div id="match">
        <div id="endOverlay" class="hidden">
            <h1></h1>
            <a href="/lobby"><h2><span class="icon_house_alt"></span> Zur Lobby</h2></a>
        </div>

        <div class="centerWrapper">
            <div id="sidebar">
                <h1>Spielinformationen</h1>

                <div class="matchInformation">
                    <span class="label"><span class="icon_rook"></span>Gegner:</span>
                    <span class="value"><?= $opponent ?></span>
                </div>
                <div class="matchInformation">
                    <span class="label"><span class="icon_grid-3x3"></span>Züge:</span>
                    <span id="moves" class="value"><?= $moves ?></span>
                </div>
                <div class="matchInformation">
                    <span class="label"><span class="icon_circle-slelected"></span>Farbe:</span>
                    <span class="value"><?= $color ?></span>
                </div>

                <div class="matchInformation">
                    <span id="pendingOpponentMove" class="hidden">Auf Zug des Gegners warten...</span>
                    <span id="pendingUserMove" class="hidden">Sie sind am Zug...</span>
                </div>

                <div class="buttons">
                    <button id="surrenderButton" class="button">Aufgeben</button>
                    <button class="button"><a href="/lobby">Zur Lobby</a></button>
                </div>
            </div>

            <div id="gameContent">
                <h2 id="loadGameLabel">Das Spiel wird geladen...</h2>
            </div>

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
                Game.init(7, 6, "<?= $_GET['m'] ?>");
                Interface.addSurrenderButtonClickedEventListener("surrenderButton");
                Interface.addConfirmSurrenderButtonClickedEventListener("confirmSurrenderButton");
                Interface.addCancelSurrenderLinkClickedEventListener("cancelSurrenderLink");
            </script>
        </div>
    </div>
</body>
</html>
