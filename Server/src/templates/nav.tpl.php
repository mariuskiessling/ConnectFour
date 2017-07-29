<nav>
    <div id="navContent">
        <div class="centerWrapper">
            <div id="profileInformation">
                <?php
                if($profilePictureFilename == NULL)
                    echo '<div id="profilePicture" class="empty"></div>';
                if($profilePictureFilename != NULL)
                    echo '<div id="profilePicture"><img src="/storage/profile_pictures/'.$profilePictureFilename.'" width="40px" height="40px" /></div>';
                ?>
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
