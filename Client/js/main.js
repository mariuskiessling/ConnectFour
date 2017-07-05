let Game = {
    width: 0,
    height: 0,
    matchId: "",
    active: false,
    userCode: 0,
    moveInProgress: false,

    settings: {
        chipMoveDownAnimationSpeed: 250 //ms
    },

    init: function(width, height, matchId) {
        Game.width = width;
        Game.height = height;
        Game.matchId = matchId;

        let ajax = new XMLHttpRequest();
        ajax.open("GET", "/match/update?match_id="+Game.matchId);
        ajax.addEventListener('load', function(event) {
            if(ajax.status == 200) {
                let response = JSON.parse(ajax.responseText);
                console.log(JSON.parse(response.field));

                // Create new field that can be edited without beeing inside the DOM.
                let field = document.createElement("div");
                field.id = "field";

                for(let y = 0; y < height; y++) {
                    for(let x = 0; x < width; x++) {
                        let newFieldElement = document.createElement("div");
                        newFieldElement.className = "fieldTile";
                        newFieldElement.dataset.x = x;
                        newFieldElement.dataset.y = y;

                        let fieldHole = document.createElement("div");
                        fieldHole.className = "fieldHole";
                        newFieldElement.addEventListener("click", Game.gameFieldClicked);
                        newFieldElement.appendChild(fieldHole);

                        field.appendChild(newFieldElement);
                    }
                }

                field = Game.loadField(field, JSON.parse(response.field));
                field.className = response.color_scheme_class;

                setTimeout(function() {
                    document.getElementById("loadGameLabel").className = "hidden";
                    document.getElementById("gameContent").appendChild(field);

                    Game.updateMatchInformation();

                    console.info("Game successfully initialized.\nWidth: " + Game.width + "\nHeight: " + Game.height + "\nMatch ID: " + Game.matchId);

                    setInterval(Game.updateMatchInformation, 3000);
                }, 1000);
            } else
            {
                // TODO: Add error handling
            }
        });
        ajax.send();
    },

    loadField: function(element, field) {
        for(let y = 0; y < Game.height; y++) {
            for(let x = 0; x < Game.width; x++) {
                let tile = element.querySelectorAll("[data-x='" + x + "'][data-y='" + y + "']")[0];

                if(field[y][x] == 0) {
                    tile.className = "fieldTile";
                }
                if(field[y][x] == 1) {
                    tile.className = "fieldTile hasChip hasChipA";
                }
                if(field[y][x] == 2) {
                    tile.className = "fieldTile hasChip hasChipB";
                }
            }
        }

        return element;
    },

    gameFieldClicked: function(event) {
        if(Game.active) {
            Game.active = false;
            Game.moveInProgress = true;

            let chipPositionX = parseInt(event.target.parentElement.dataset.x);
            let chipPositionY = parseInt(event.target.parentElement.dataset.y);

            if(Game.userCode == 1) {
                event.target.parentElement.className += " hasChip hasChipA";
            }
            if(Game.userCode == 2) {
                event.target.parentElement.className += " hasChip hasChipB";
            }

            // Check if chip already exists at this position
            if(!event.target.classList.contains("hasChip")) {

                // Check if chip is inserted on a higher level
                if(chipPositionY < Game.height - 1) {
                    Game.moveChipDown(event.target.parentElement);
                } else {
                    Game.pushMoveToServer(document.getElementById("field"));
                }
            }
        }
    },

    /*
        Moves the chip inside a given field element to the lowest position
        possible. Animates the descent using the chipMoveDownAnimationSpeed
        property.
     */
    moveChipDown: function(element) {
        console.log("Moving chip down.");

        let currentXPosition = parseInt(element.dataset.x);
        let currentYPosition = parseInt(element.dataset.y);

        let fieldBelow = document.querySelectorAll("[data-x='" + currentXPosition + "'][data-y='" + (currentYPosition+1) + "']")[0];

        if(fieldBelow != undefined && !fieldBelow.classList.contains('hasChip')) {
            element.classList.remove("hasChip");

            if(element.classList.contains("hasChipA")) {
                element.classList.remove("hasChipA");
                fieldBelow.className += " hasChip hasChipA";
            }
            if(element.classList.contains("hasChipB")) {
                element.classList.remove("hasChipB");
                fieldBelow.className += " hasChip hasChipB";
            }

            setTimeout(function() {
                Game.moveChipDown(fieldBelow);
            }, Game.settings.chipMoveDownAnimationSpeed);
        } else {
            Game.pushMoveToServer(document.getElementById("field"));
        }
    },

    updateMatchInformation: function() {
        if(!Game.active && !Game.moveInProgress) {
            let ajax = new XMLHttpRequest();
            ajax.open("GET", "/match/update?match_id="+Game.matchId);
            ajax.addEventListener('load', function(event) {
                if(ajax.status == 200) {
                    let response = JSON.parse(ajax.responseText);
                    let field = Game.loadField(document.getElementById("field"), JSON.parse(response.field));
                    let gameContent = document.getElementById("gameContent");
                    gameContent.replaceChild(field, document.getElementById("field"));

                    Game.active = response.active;
                    Game.userCode = response.user_code;

                    document.getElementById("moves").innerHTML = response.moves;

                    if(Game.active) {
                        document.getElementById("pendingOpponentMove").className = "hidden";
                        document.getElementById("pendingUserMove").className = "";
                    } else {
                        document.getElementById("pendingOpponentMove").className = "";
                        document.getElementById("pendingUserMove").className = "hidden";
                    }

                    Game.controlEndOverlay(response.status);
                } else
                {
                    // TODO: Add error handling
                }
            });
            ajax.send();
        }
    },

    controlEndOverlay: function(status) {
        if(Game.userCode == 1 && status == 2 || Game.userCode == 2 && status == 3) {
            document.getElementById("endOverlay").getElementsByTagName("h1")[0].innerHTML = "Gewonnen";
        }
        if(Game.userCode == 1 && status == 3 || Game.userCode == 2 && status == 2) {
            document.getElementById("endOverlay").getElementsByTagName("h1")[0].innerHTML = "Verloren";
        }

        if(Game.userCode == 1 && status == 4 || Game.userCode == 2 && status == 5) {
            document.getElementById("endOverlay").getElementsByTagName("h1")[0].innerHTML = "Sie haben aufgegeben";
        }
        if(Game.userCode == 1 && status == 5 || Game.userCode == 2 && status == 4) {
            document.getElementById("endOverlay").getElementsByTagName("h1")[0].innerHTML = "Der Gegner hat aufgegeben";
        }

        if(status != 1) {
            document.getElementById("endOverlay").className = "";
        }
    },

    pushMoveToServer: function(element) {
        document.getElementById("pendingOpponentMove").className = "";
        document.getElementById("pendingUserMove").className = "hidden";

        let field = [];

        for(let y = 0; y < Game.height; y++) {
            field[y] = [];
            for(let x = 0; x < Game.width; x++) {
                let tile = element.querySelectorAll("[data-x='" + x + "'][data-y='" + y + "']")[0];

                if(tile.classList.contains("hasChip")) {
                    if(tile.classList.contains("hasChipA")) {
                        field[y][x] = 1;
                    }
                    if(tile.classList.contains("hasChipB")) {
                        field[y][x] = 2;
                    }
                } else {
                    field[y][x] = 0;
                }
            }
        }

        let ajax = new XMLHttpRequest();
        ajax.open("POST", "/match/make_move");
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        ajax.addEventListener('load', function(event) {
            if(ajax.status == 200) {
                let response = JSON.parse(ajax.responseText);
                document.getElementById("moves").innerHTML = response.moves;
                Game.controlEndOverlay(response.status);
                Game.moveInProgress = false;
            } else
            {
                // TODO: Add error handling
            }
        });
        ajax.send("match_id="+Game.matchId+"&field="+JSON.stringify(field));

        console.log(field);
    }
};

let Interface = {
    init: function() {

    },

    addCreateNewMatchButtonClickedEventListener: function(elementId) {
        document.getElementById(elementId).addEventListener("click", Interface.createNewMatchButtonClickedEventListener);
    },

    createNewMatchButtonClickedEventListener: function() {
        document.getElementById("createNewMatchForm").className = "hidden";
        document.getElementById("createNewMatchAccessLink").className = "";

        let ajax = new XMLHttpRequest();
        ajax.open("POST", "/match/create");
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        ajax.addEventListener('load', function(event) {
            if(ajax.status == 200) {
                let response = JSON.parse(ajax.responseText);
                document.getElementById("quickAccessCodeLinkPlaceholder").innerHTML = response.quick_access_code;
                document.getElementById("quickAccessCodeValuePlaceholder").innerHTML = response.quick_access_code;
                document.getElementById("openCreatedMatchButton").href = "/match/"+response.public_id;
            } else
            {
                // TODO: Add better error handling
                alert("Bei der Erstellung des Spiels ist ein Fehler aufgetreten.");
            }
        });
        ajax.send("color_scheme="+encodeURIComponent(document.getElementById("color_scheme").value));
    },

    addSurrenderButtonClickedEventListener: function(elementId) {
        document.getElementById(elementId).addEventListener("click", Interface.surrenderButtonClickedEventListener);
    },

    surrenderButtonClickedEventListener: function() {
        if(Game.active) {
            document.getElementById("gameContent").className = "hidden";
            document.getElementById("surrenderQuestion").className = "";
        } else {
            alert("Sie können nur aufgeben, wenn Sie am Zug sind.");
        }
    },

    addConfirmSurrenderButtonClickedEventListener: function(elementId) {
        document.getElementById(elementId).addEventListener("click", Interface.confirmSurrenderButtonClickedEventListener);
    },

    confirmSurrenderButtonClickedEventListener: function() {
        if(Game.active) {
            if(document.getElementById("surrenderText").innerHTML == document.getElementById("surrenderTextAnswer").value) {
                let ajax = new XMLHttpRequest();
                ajax.open("GET", "/match/surrender?match_id="+Game.matchId);
                ajax.addEventListener('load', function(event) {
                    if(ajax.status == 200) {
                        console.log();
                        let response = JSON.parse(ajax.responseText);
                        Game.controlEndOverlay(response.status);
                    } else
                    {
                        // TODO: Add better error handling
                        alert("Beim Aufgeben ist ein Fehler aufgetreten.");
                    }
                });
                ajax.send();
            } else {
                document.getElementById("surrenderError").className = "";
            }
        } else {
            alert("Sie können nur aufgeben, wenn Sie am Zug sind.");
        }
    },

    addCancelSurrenderLinkClickedEventListener: function(elementId) {
        document.getElementById(elementId).addEventListener("click", Interface.cancelSurrenderLinkClickedEventListener);
    },

    cancelSurrenderLinkClickedEventListener: function() {
        document.getElementById("gameContent").className = "";
        document.getElementById("surrenderQuestion").className = "hidden";
    }
};
