let Game = {
    width: 0,
    height: 0,

    settings: {
        chipMoveDownAnimationSpeed: 250 //ms
    },

    init: function(width, height) {
        Game.width = width;
        Game.height = height;

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

        document.getElementById("gameContent").appendChild(field);

        console.info("Game successfully initialized.\nWidth: " + Game.width + "\nHeight: " + Game.height);
        console.info(field);
    },

    gameFieldClicked: function(event) {
        let chipPositionX = parseInt(event.target.parentElement.dataset.x);
        let chipPositionY = parseInt(event.target.parentElement.dataset.y);

        event.target.parentElement.className += " hasChip hasBlueChip";

        // Check if chip already exists at this position
        if(!event.target.classList.contains("hasChip")) {

            // Check if chip is inserted on a higher level
            if(chipPositionY < Game.height - 1) {
                Game.moveChipDown(event.target.parentElement);
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
            element.classList.remove("hasBlueChip");

            fieldBelow.className += " hasChip hasBlueChip";

            setTimeout(function() {
                Game.moveChipDown(fieldBelow);
            }, Game.settings.chipMoveDownAnimationSpeed);
        }
    },
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
    }
};
