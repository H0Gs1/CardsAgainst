<?php

include_once('db_connecter.php');
session_start();

if (isset($_SESSION["user_username"])) {
    $player = $_SESSION["user_username"];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cards Against Humanity</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.2/dist/sweetalert2.min.css">
    <style>
        /* General body and background styling */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Header styling */
        #header {
            background-color: #005d64;
            color: #fff;
            padding: 5px;
            text-align: center;
            font-size: 1.5rem; /* Adjusted font size for smaller text */
            border-bottom: 1px solid #fff;
        }

        /* Flexbox layout for Players, Cards, and Pack Name */
        .game-layout {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 20px;
        }

        /* Players and Pack Name section styles */
        #players, #packName {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        /* Styling for players list */
        #players h4 {
            margin-bottom: 10px;
            color: #333;
        }
        #players ul {
            list-style-type: none;
            padding: 0;
        }
        #players li {
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        /*   button start  */

        .button-82-pushable {
  position: relative;
  border: none;
  background: transparent;
  padding: 0;
  cursor: pointer;
  outline-offset: 4px;
  transition: filter 250ms;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
}

.button-82-shadow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 12px;
  background: hsl(0deg 0% 0% / 0.25);
  will-change: transform;
  transform: translateY(2px);
  transition:
    transform
    600ms
    cubic-bezier(.3, .7, .4, 1);
}

.button-82-edge {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 12px;
  background: linear-gradient(
    to left,
    hsl(340deg 100% 16%) 0%,
    hsl(340deg 100% 32%) 8%,
    hsl(340deg 100% 32%) 92%,
    hsl(340deg 100% 16%) 100%
  );
}

.button-82-front {
  display: block;
  position: relative;
  padding: 10px 22px;  /* Reduced padding */
  border-radius: 12px;
  font-size: 1rem;  /* Reduced font size */
  color: white;
  background: var(--bs-danger);
  will-change: transform;
  transform: translateY(-4px);
  transition:
    transform
    600ms
    cubic-bezier(.3, .7, .4, 1);
}

@media (min-width: 768px) {
  .button-82-front {
    font-size: 1.1rem;  /* Slightly smaller font size for larger screens */
    padding: 10px 30px;  /* Reduced padding for larger screens */
  }
}

.button-82-pushable:hover {
  filter: brightness(110%);
  -webkit-filter: brightness(110%);
}

.button-82-pushable:hover .button-82-front {
  transform: translateY(-6px);
  transition:
    transform
    250ms
    cubic-bezier(.3, .7, .4, 1.5);
}

.button-82-pushable:active .button-82-front {
  transform: translateY(-2px);
  transition: transform 34ms;
}

.button-82-pushable:hover .button-82-shadow {
  transform: translateY(4px);
  transition:
    transform
    250ms
    cubic-bezier(.3, .7, .4, 1.5);
}

.button-82-pushable:active .button-82-shadow {
  transform: translateY(1px);
  transition: transform 34ms;
}

.button-82-pushable:focus:not(:focus-visible) {
  outline: none;
}

        /*   button end  */

        /* Cards Section */
        #cards {
            flex: 3;
        }

        /* Styling for the cards */
        .card-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            overflow-x: auto;
        }

        .card-Answer, .card-Question {
            width: 120px;
            height: 160px;
            border-radius: 10px;
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .card-Answer:hover, .card-Question:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        /* Disable interaction for the last two final cards */
        #finalCards .card-Answer,
        #finalCards .card-Question {
            pointer-events: none;
        }


        .card-content {
            font-size: 14px;
            color: #333;
            padding: 15px;
            text-align: center;
        }

        /* Final Cards, Chosen Cards, and Playable Cards area */
        #finalCards, #chosenCards, #playableCards {
            margin-bottom: 20px;
        }

        .disabled {
            pointer-events: none;
            opacity: 0.6; /* Dim the cards to visually indicate they are disabled */
        }

        .enabled {
            pointer-events: auto;
            opacity: 1; /* Dim the cards to visually indicate they are disabled */
        }


        /* Pack Name Section */
        #packName {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .game-layout {
                flex-direction: column;
                align-items: center;
            }

            #players, #packName {
                width: 100%;
                margin-bottom: 20px;
            }

            #cards {
                width: 100%;
            }
        }
    </style>
</head>
<body>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="header">
            <h2>Cards Against Humanity</h2>
        </div>
    </div>

    <div class="row game-layout">
        <!-- Players Section -->
        <div class="col-md-2" id="players">
            <h4>Players</h4>
            <ul id="playerList">
           
            </ul>
        </div>

        <!-- Cards Section -->
        <div class="col-md-8" id="cards">
            <!-- Final Cards -->
            <div id="finalCards">
                <div class="card-container">
                    <div class="card-Question" id="cardBlack"><div class="card-content"style="background-color:#262626; color:White; border-radius: 10px #262626;">Black Card</div></div>
                    <div class="card-Answer" id="test2"><div class="card-content">Final Card 2</div></div>
                </div>
            </div>

            <!-- Chosen Cards -->
            <div id="chosenCards">
                <div class="card-container">
                    <div class="card-Answer" id="2Card0"><div class="card-content">Chosen 1</div></div>
                    <div class="card-Answer" id="2Card1"><div class="card-content">Chosen 2</div></div>
                    <div class="card-Answer" id="2Card2"><div class="card-content">Chosen 3</div></div>
                    <div class="card-Answer" id="2Card3"><div class="card-content">Chosen 4</div></div>
                    <div class="card-Answer" id="2Card4"><div class="card-content">Chosen 5</div></div>
                    <div class="card-Answer" id="2Card5"><div class="card-content">Chosen 6</div></div>
                    <div class="card-Answer" id="2Card6"><div class="card-content">Chosen 7</div></div>
                    <div class="card-Answer" id="2Card7"><div class="card-content">Chosen 8</div></div>
                </div>
            </div>
        </div>

        <!-- Pack Name Section -->
        <div class="col-md-2" id="packName">
            <!-- <h4>Pack Name</h4>
            <p>Here you can show the name or description of the current card pack.</p> -->
            <button class="button-82-pushable" role="button" onclick="abort()" >
            <span class="button-82-shadow"></span>
            <span class="button-82-edge"></span>
            <span class="button-82-front text">
                ABORT
            </span>
</button>
        </div>
    </div>

    <!-- Playable Cards Section -->
    <div class="row">
        <div class="col-md-12" id="playableCards">
            <div class="card-container">
                <div class="card-Answer" id="1Card1"><div class="card-content">Playable 1</div></div>
                <div class="card-Answer" id="1Card2"><div class="card-content">Playable 2</div></div>
                <div class="card-Answer" id="1Card3"><div class="card-content">Playable 3</div></div>
                <div class="card-Answer" id="1Card4"><div class="card-content">Playable 4</div></div>
                <div class="card-Answer" id="1Card5"><div class="card-content">Playable 5</div></div>
                <div class="card-Answer" id="1Card6"><div class="card-content">Playable 6</div></div>
                <div class="card-Answer" id="1Card7"><div class="card-content">Playable 7</div></div>
                <div class="card-Answer" id="1Card8"><div class="card-content">Playable 8</div></div>
                <div class="card-Answer" id="1Card9"><div class="card-content">Playable 9</div></div>
                <div class="card-Answer" id="1Card10"><div class="card-content">Playable 10</div></div>
            </div>
        </div>
    </div>
</div>

<script src="game.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.2/dist/sweetalert2.all.min.js"></script>
<script>
    drawCards("Black");
    drawCards("White");
    var update =false;
    showPlayers();

function abort(){
    Swal.fire({
    title: "Are you sure you want to leave?",
    text: "You might ruin it for others >:(",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#A7194B",
    cancelButtonColor: "#089000" ,
    confirmButtonText: "Let me go!",
    cancelButtonText: "Fine, i'll stay..."
    }).then((result) => {
    if (result.isConfirmed) {
        leaveGame();
        window.location.href = "LoginSuccess.php";
    }
});
}

 // Initialize the EventSource
let eventSource = new EventSource("sse2.php");
var once =false;
eventSource.onmessage = function(event) {
    // Parse the JSON string to an object
    const jsonData = JSON.parse(event.data);
    console.log(jsonData.event);
    switch(jsonData.event) {
        case "waiting":
            // code block
            var data = jsonData.data;
            console.log("Waiting for players:", data.message, "Players joined:", data.playerCount);
            break;
        case "Ready":
            // code block
            var data = jsonData.data;
            console.log("Game Ready:", data.message);
            update =false;
            drawCards("Black");
            updateScore();
            showPlayers();
            while (!once) {
                once = true; 
            }
            
            setTimeout(reset,3000)

            break;
        case "choosing":
            once =false;
            showPlayers();
            var data = jsonData.data;
            console.log("Players are choosing:", data.message);
            break;
        case "chosen":
            // code block
            var data = jsonData.data;
            console.log("All players have chosen:", data.message);
            fetchCards();
            const chosenCards = document.querySelectorAll("#chosenCards .card-Answer");
            chosenCards.forEach((card) => {
                card.classList.remove("disabled");
            });
            break;
        case "voting":

            // code block
            var data = jsonData.data;
            console.log("Players are voting:", data.message);
            break;
        case "voted":
            // code block
            var data = jsonData.data;
            console.log("Winner:", data.message);
            
                findWinner();
                setTimeout(() => {
                    isReady().then((ready) => {
                        console.log(ready); // Now 'ready' will be true or false
                        if (ready) {
                            changeStatus("updating");
                        }
                    }).catch((error) => {
                        console.error("Error in isReady:", error);
                    });
                }, 2000);
                changeWhite();
                console.log("CHANCE1");
                
            break;
        case "updating":
            ///updating code
            var data = jsonData.data;
            console.log("Message", data.message);
            changeWhite();
            console.log("CHANCE 2");
            
            setTimeout(function() {
                    console.log("This message will appear after 2 seconds.");
                }, 900);
            while(!update){
                increaseRound().then((ready) => {
                    console.log(ready); // Now 'ready' will be true or false
                    if(ready){
                        changeStatus("Ready")
                    }
                }).catch((error) => {
                    console.error("Error in isReady:", error);
                });
                update =true;
            }
            break;
        case "over":
            // code block
            var data = event.data;
            console.log("Game Over:", data.message);
            endGame();
            window.location.href = "ResultsPage.php";
            break;
        case "error":
            // code block
            console.log("Raw event data:", event); // Check the raw data first
            try {
                var data = jsonData.data;
                console.error("Error occurred:", data.message);
            } catch (e) {
                console.error("Failed to parse event data:", e);
            }
            break;
        default:
            // code block
}
};


    // Wait for the document to load
document.addEventListener("DOMContentLoaded", function () {
    // Initially disable all chosen cards
    const chosenCards = document.querySelectorAll("#chosenCards .card-Answer");
    chosenCards.forEach((card) => {
        card.classList.add("disabled");
    });

    // Get all playable cards
    const playableCards = document.querySelectorAll("#playableCards .card-Answer");

    // Add click event listeners to playable cards
    playableCards.forEach((card) => {
    card.addEventListener("click", async function () { // Make the callback async
        card.classList.add("disabled");
        playableCards.forEach((played) => {
            played.classList.add("disabled");
        });
        changeStatus("choosing");

        const cardContent = card.querySelector('.card-content').innerText;
        
        // Wait for the card submission to complete
        var submit = await cardSubmition(cardContent); 
        if (submit) {
            var check = await checkPlayed(); // Assuming checkPlayed is also async
            if (check) {
                changeStatus("chosen");
            } else {
                console.log("lock in my g");
            }
        }

        console.log("Playable card clicked:", card.querySelector(".card-content").innerText);
    });
});

    // Add click event listeners to chosen cards
    chosenCards.forEach((card) => {
        card.addEventListener("click", async function () {
            card.classList.add("disabled");
            chosenCards.forEach((played) => {
                played.classList.add("disabled");
            });
            changeStatus("voting");
        // Example action when a chosen card is clicked
        card.classList.toggle("selected"); // Toggle visual feedback
        console.log("Chosen card clicked:", card.querySelector(".card-content").innerText);
        await voteCard(card.querySelector(".card-content").innerText);  // Wait for voteCard to finish
        var result = await checkVotes(); 
        console.log(result);
        if (result) {
            changeStatus("voted");
        } 
 
        });
    });
});

</script>

</body>
</html>
