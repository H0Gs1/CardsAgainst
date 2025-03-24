function drawCards(Type) {// load game
    const Types = new FormData();
    Types.append('cardType', Type);

    fetch('draw_cards.php', {
        method: 'POST',
        body: Types
    })
    .then((response) => response.json())
    .then((data) => {
        if (Type === "Black") {
            console.log(data);
            // Update the Black card content
            document.getElementById("cardBlack").querySelector('.card-content').innerHTML = data;
        } else {
            // Loop through the card data and update
            console.log(data);
            data.forEach((card, index) => {
                const cardElement = document.getElementById(`1Card${index + 1}`);
                if (cardElement) {
                    cardElement.querySelector('.card-content').innerHTML = card.Content;
                }
            });
        }
    })
    .catch((error) => {
        console.error("Error fetching cards:", error); // Log errors to the console
    });
}

async function cardSubmition(cardContent) {
    const content = new FormData();
    content.append('cardContent', cardContent);

    try {
        const response = await fetch('submit_card.php', {
            method: 'POST',
            body: content
        });
        const data = await response.text();
        if (data.includes("Ready")) {
            return true;
        } else {
            console.log("Submission not ready:", data);
            return false;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        return false;
    }
}



function fetchCards(){// display chosen cards
    fetch('fetch_submitted_cards.php')
    .then((response) => response.json())
    .then((data) => {
        console.log('Parsed data:', data);
        data.forEach((card, index) => {
            console.log(`Card at index ${index}:`, card);
            const cardElement = document.getElementById(`2Card${index}`);
            if (cardElement) {
                const content = card?.CardContent; // Safely access cardContent
                console.log(`Content for card ${index}:`, content);
                if (content) {
                    cardElement.querySelector('.card-content').innerHTML = content;
                } else {
                    console.error(`Card content is undefined for card ${index}`);
                }
            } else {
                console.warn(`No element found for ID: 2Card${index}`);
            }
        });
    })
    .catch((error) => console.error('Error:', error));

}
async function voteCard(cardContent) {
    const content = new FormData();
    content.append('cardContent', cardContent);

    try {
        const response = await fetch('determine_round_winner.php', {
            method: 'POST',
            body: content,
        });
        const data = await response.text();
        console.log(data);
    } catch (error) {
        console.error("Error in getWinner:", error);
    }
}

function findWinner(){
    const testElement = document.getElementById("test2");
    const cardContent = testElement.querySelector('.card-content');

    fetch('get_winner.php')
    .then((response) => response.text())
    .then((data) =>{
        console.log(data);
        cardContent.innerHTML = data;
    })
}

function displayWinner(winner) {
    const testElement = document.getElementById("test2");
    if (testElement) {
        const cardContent = testElement.querySelector('.card-content');
        if (cardContent) {
            cardContent.innerHTML = winner; // Update content
            return true; // Indicate success
        } else {
            console.error("Error: '.card-content' not found in '#test2'.");
            return false; // Indicate failure
        }
    } else {
        console.error("Error: '#test2' element not found.");
        return false; // Indicate failure
    }
}

async function checkRound() {//resets the round
    const idk = new FormData();
    idk.append('content', "idk");

    try {
        const response = await fetch('change_round.php', {
            method: 'POST',
            body: idk
        });

        const data = await response.text();
        console.log(data); // Handle the fetched data

        if (!(data.includes("Not"))) {
            console.log(data);
            document.getElementById("test2").querySelector('.card-content').innerHTML = null;
            return true; // Indicate round is ready
        } else {
            return false; // Indicate round is not ready
        }
    } catch (error) {
        console.error('Error fetching the file:', error);
        return false; // Handle the error case
    }
}

async function checkPlayed() {
    try {
        const response = await fetch('check_played.php');
        const data = await response.text();
        console.log(data);
        return data.includes("Ready"); // Returns true or false based on the response
    } catch (error) {
        console.error("Error checking played status:", error);
        return false; // Return false in case of an error
    }
}



function changeStatus(status){
    const form = new FormData();
    form.append("status",status);

    fetch('change_status.php',{
        method: 'POST',
        body: form
    })
    .then((response) => response.text())
    .then((data) =>{
        console.log(data);
    })
    .catch((error) => {
        console.error("Error in getWinner:", error);
    });
}

async function checkVotes() {
    try {
        const response = await fetch('check_votes.php');
        const data = await response.text();
        console.log(data);
        return data ? true : false; // Return true or false based on the data received
    } catch (error) {
        console.error("Error checking votes:", error);
        return false; // Return false in case of error
    }
}

async function handleVotedState() {
    // Run the winner calculation first
    findWinner();

    // Wait for the round check before proceeding
    const roundReady = await checkRound();

    if (roundReady) {
        // If the round is ready, proceed with DOM manipulation
        const chosenCards = document.querySelectorAll("#chosenCards .card-Answer");
        chosenCards.forEach((chose) => {
            chose.classList.add("disabled");
            chose.querySelector(".card-content").innerText = null;
        });

        const playableCards = document.querySelectorAll("#playableCards .card-Answer");
        playableCards.forEach((card) => {
            card.classList.remove("disabled");
        });

        // Now, change the status after ensuring everything is ready
        changeStatus("Ready");
        wait = true;
    } else {
        // If something went wrong, alert the user
        
    }
}

async function isReady(){

    try {
        const response = await fetch('check_ready.php');
        const data = await response.text();
        console.log(data);
        return data ? true : false; // Return true or false based on the data received
    } catch (error) {
        console.error("Error checking ready:", error);
        return false; // Return false in case of error
    }
}

function reset(){
    const chosenCards = document.querySelectorAll("#chosenCards .card-Answer");
    chosenCards.forEach((chose) => {
        chose.classList.add("disabled");
        chose.querySelector(".card-content").innerText = null;
    });

    const playableCards = document.querySelectorAll("#playableCards .card-Answer");
    playableCards.forEach((card) => {
        card.classList.remove("disabled");
    });

    document.getElementById("test2").querySelector('.card-content').innerHTML = null;
}

function changRound(){

    fetch('draw_cards.php')
    .then((response) => response.json())
    .then((data) => {
        console.log(data);
    })
    .catch((error) => {
        console.error("Error fetching cards:", error); // Log errors to the console
    });
}

async function increaseRound(){
    try {
        const response = await fetch('increase_round.php');
        const data = await response.text();
        console.log(data);
        return data ? true : false; // Return true or false based on the data received
    } catch (error) {
        console.error("Error checking ready:", error);
        return false; // Return false in case of error
    }
}

function updateScore(){
    fetch('correct_scores.php')
    .then((response) => response.text())
    .then((data) => {
        console.log(data);
    })
    .catch((error) => {
        console.error("Error fetching cards:", error); // Log errors to the console
    });
}

function showPlayers(){
    fetch('players_and_points.php')
    .then((response) => response.json())
    .then((data) => {
            console.log(data);
            const playerList = document.getElementById('playerList');

            // Check if there are players
            if (data.length > 0) {
                // Clear the existing list (if any)
                playerList.innerHTML = '';
    
                // Loop through the players and add each one as a list item
                data.forEach(player => {
                    const li = document.createElement('li');
                    li.textContent = player.name + ": " +player.points; // Assuming the player object has a 'name' property
                    playerList.appendChild(li);
                });
            } else {
                // If no players, display a message
                playerList.innerHTML = '<li>No Players</li>';
            }
    })
    .catch((error) => {
        console.error("Error fetching players and points", error); // Log errors to the console
    });
}

function leaveGame(){
    fetch('leave_game.php')
    .then((response) => response.text())
    .then((data) => {
        console.log(data);
    })
    .catch((error) => {
        console.error("Error leaving:", error); // Log errors to the console
    });
}

function changeWhite(){
    fetch('change_white.php')
    .then((response) => response.text())
    .then((data) => {
        console.log("start1");
        console.log(data);
        console.log("end1");
        
        if (!(data.includes("No card"))) {
            for (let i = 0; i < 10; i++) {
                console.log("Start2")
                const cardElement = document.getElementById(`1Card${i + 1}`);
                if (cardElement) {
                    console.log("start3")
                    var content = cardElement.querySelector('.card-content').innerHTML;
                    if (content === data) {
                        console.log("defining log");
                        fetch('get_white.php')
                        .then((response) => response.text())
                        .then((data2) => {
                            console.log(data2);
                            console.log("DOES IT EVEN REACH HERE");
                            cardElement.querySelector('.card-content').innerHTML = data2;
                        })
                        .catch((error) => {
                            console.error("Error leaving:", error); // Log errors to the console
                        }); 
                        
                    }
                }
              }
        } else {
            console.log("not working lil bro");
        }
    })
    .catch((error) => {
        console.error("Error leaving:", error); // Log errors to the console
    });
}

function endGame(){
    fetch('end_game.php')
    .then((response) => response.text())
    .then((data) => {
        console.log(data);
    })
    .catch((error) => {
        console.error("Error leaving:", error); // Log errors to the console
    });
}