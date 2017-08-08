<div class="centerWrapper">
    <h1>Öffentlichem Spiel beitreten</h1>

    <?php
    if($openMatches->num_rows == 0) {
        echo '<h3 class="emptyListMessage"><span class="icon_info_alt"></span> Es stehen keine öffentlichen Spiele zum Beitreten zur Verfügung. Erstellen Sie ein neues oder bitten Sie einen anderen Nutzer ein neues zu erstellen.</h3>';
    } else {
    ?>

        <p>
            Die folgende Liste zeigt eine Übersicht aller Spiele, denen beigetreten werden kann.
        </p>

        <table>
            <tr class="header">
                <td>Erstellt von</td>
                <td>Erstellt am / um</td>
                <td>Aktion</td>
            </tr>

            <?php
            while($row = $openMatches->fetch_assoc()) {
            ?>

            <tr>
                <td><?= $row['creator'] ?></td>
                <td><?= (new DateTime($row['created_at']))->format('d.m.Y (H:i').' Uhr)' ?></td>
                <td><a href="/match/join?qac=<?= $row['quick_access_code'] ?>" class="formatted">Beitreten</a></td>
            </tr>

            <?php
            }
            ?>
        </table>

    <?php } ?>
</div>
