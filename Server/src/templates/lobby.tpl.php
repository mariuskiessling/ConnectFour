<?php
include('header.tpl.php');
?>

<body>
    <nav>
        <div id="navContent">
            <div class="centerWrapper">
                <div id="profileInformation">
                    <div id="profilePicture" class="empty"></div>
                    <div id="profileTextInformation">
                        <span id="profileUsernameLabel">Angemeldet als:</span>
                        <span id="profileUsername"><?= @$username ?></span>
                    </div>
                </div>

                <div id="logout">
                    <span class="icon_lock_alt"></span>
                    <a href="/logout">Abmelden</a>
                </div>
            </div>
        </div>
    </nav>
    <div id="lobby">
        <div id="quickJoinMatch" class="centerWrapper">
            <h1>Einem Spiel beitreten (Quick-Access-Code)</h1>
            <p>Sie können jederzeit einem Spiel mit einem Quick-Access-Code beitreten. Geben dazu einefach den Quick-Access-Code Ihres Mitspielers im folgenden Feld ein. Falls Sie einem anderen öffentlichen Spiel beitreten möchten, nutzen Sie bitte die Liste weiter unten.</p>

            <form action="#" method="post">
                <label for="qac">Quick-Access-Code</label>
                <input type="text" name="qac" id="qac" />
                <input type="submit" value="Spiel beitreten" class="button">
            </form>
        </div>

        <div id="userGames">
            <div class="centerWrapper">
                <h1>Meine Spiele</h1>
                <table>
                    <tr class="header">
                        <td>Gegner</td>
                        <td>Quick-Access-Code</td>
                        <td>Erstellt am / um</td>
                        <td>Status</td>
                        <td>Aktion</td>
                    </tr>

                    <?php
                    while($row = $userMatches->fetch_assoc()) {
                    ?>

                    <tr>
                        <td><?= $row['opponent'] == NULL ? 'Noch kein Gegner' : $row['opponent'] ?></td>
                        <td><?= $row['quick_access_code'] ?></td>
                        <td><?= (new DateTime($row['created_at']))->format('d.m.Y (H:i').' Uhr)' ?></td>
                        <td>
                        <?php
                        switch($row['status']) {
                            case 1:
                                echo 'Noch nicht beendet';
                                break;

                            case 2:
                                echo ($_SESSION['userId'] == $row['creator_id'] ? '<span class="icon_star"></span> Gewonnen' : '<span class="icon_star_alt"></span> Verloren');
                                break;
                            case 3:
                                echo ($_SESSION['userId'] == $row['opponent_id'] ? '<span class="icon_star_alt"></span> Gewonnen' : '<span class="icon_star_alt"></span> Verloren');
                                break;
                            case 4:
                                echo ($_SESSION['userId'] == $row['creator_id'] ? '<span class="icon_dislike"></span> Sie haben aufgegeben' : '<span class="icon_star-half_alt"></span> Ihr Gegner hat aufgegeben');
                                break;
                            case 5:
                                echo ($_SESSION['userId'] == $row['opponent_id'] ? '<span class="icon_dislike"></span> Sie haben aufgegeben' : '<span class="icon_star-half_alt"></span> Ihr Gegner hat aufgegeben');
                                break;
                        }
                        ?>
                        </td>
                        <?php
                        if($row['status'] == 1) {
                            echo '<td><a href="/match?m='.$row['public_id'].'" target="_blank" class="formatted">Betreten</a></td>';
                        } else {
                            echo '<td><span class="icon_minus-06"></span></td>';
                        }
                        ?>
                    </tr>

                    <?php } ?>
                </table>
            </div>
        </div>

        <div id="createNewMatch">
            <div class="centerWrapper">
                <h1>Neues Spiel erstellen</h1>
                <p>
                    Ein neues Spiel ist nach der Erstellung immer öffentlich sichtbar. Das bedeutet, dass dem neu erstellten Spiel jeder andere Spieler beitreten kann.
                    Falls Sie einem Mitspieler schnell Zugriff auf das neu erstellte Spiel geben wollen, können Sie den fünfstelligen Spielcode (z.B. 5Z6LA) an Ihren Mitspieler weitergeben.
                </p>

                <div id="createNewMatchForm">
                    <label for="color_scheme">Chip-Farbe</label>
                    <select name="color_scheme" id="color_scheme">
                        <?php
                        while($row = $colorSchemes->fetch_assoc()) {
                            echo '<option value="'.$row['id'].'">'.$row['description'].'</option>';
                        }
                        ?>
                    </select>
                    <button class="button" id="createNewMatchButton">Spiel erstellen</button>
                </div>
                <div id="createNewMatchAccessLink" class="hidden">
                    <h2>Das Spiel wurde erstellt! Sie können Ihrem Mitspieler folgenden Link zum schnellen Beitreten geben:</h2>
                    <span id="quickAccessLink"><span class="icon_link"></span><?= $host ?>/match/join/?qac=<span id="quickAccessCodeLinkPlaceholder"></span></span>

                    <h2>Ihr Mitspieler kann ebenfalls über folgenden Quick-Access-Code beitreten:</h2>
                    <span id="quickAccessCode"><span class="icon_tag_alt"></span><span id="quickAccessCodeValuePlaceholder"></span></span>

                    <button class="button"><a id="openCreatedMatchButton" href="" target="_blank">Erstelltes Spiel aufrufen</a></button>
                </div>
            </div>
        </div>

        <div id="joinPublicMatch">
            <div class="centerWrapper">
                <h1>Öffentlichem Spiel beitreten</h1>
                <p>
                    Die folgende Liste zeigt eine Übersicht aller Spiele, denen beigetreten werden kann.
                </p>

                <table>
                    <tr class="header">
                        <td>Gegner</td>
                        <td>Erstellt am / um</td>
                        <td>Aktion</td>
                    </tr>
                    <tr>
                        <td>Noch kein Gegner</td>
                        <td>26.06.2017 (12:30 Uhr)</td>
                        <td><a href="#" class="formatted">Betreten</a></td>
                    </tr>
                    <tr>
                        <td>FluffyCool96</td>
                        <td>03.06.2017 (09:21 Uhr)</td>
                        <td><a href="#" class="formatted">Betreten</a></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <script src="/js/main.js" charset="utf-8"></script>
    <script>
        Interface.addCreateNewMatchButtonClickedEventListener("createNewMatchButton");
    </script>
</body>
</html>
