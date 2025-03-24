<?php
global $conn;
session_start();
require('db_connecter.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the content type for JSON response
// header('Content-Type: application/json');

// $response = [
//     'totalUserAccounts' => 0,
//     'totalVenues' => 0,
//     'totalTickets' => 0,
//     'nextEventName' => 0,
//     'nextEventDateTime' => 0,
//     'nextEventVenue' => 0,
// ];

// // Obtain number of user accounts
// $query_totalUserAccounts = "SELECT COUNT(*) as totalUserAccounts FROM UserAccount WHERE UserRole = 2 LIMIT 1";
// $result_totalUserAccounts = mysqli_query($conn, $query_totalUserAccounts);

// if ($result_totalUserAccounts) {
//     if ($row = mysqli_fetch_assoc($result_totalUserAccounts)) {
//         $response['totalUserAccounts'] = $row["totalUserAccounts"];
//     }
//     mysqli_free_result($result_totalUserAccounts);
// }

// // Obtain total ticket sales
// $query_totalTickets = "SELECT SUM(Quantity) as totalTickets FROM UserAccountTicketLink LIMIT 1";
// $result_totalTickets = mysqli_query($conn, $query_totalTickets);

// if ($result_totalTickets) {
//     if ($row = mysqli_fetch_assoc($result_totalTickets)) {
//         $response['totalTickets'] = $row["totalTickets"] ?? 0; // Default to 0 if null
//     }
//     mysqli_free_result($result_totalTickets);
// }

// // Obtain total revenue
// // $query_totalRevenue = "SELECT SUM(Amount) as totalRevenue FROM Payment LIMIT 1";
// // Do the rest here

// // Obtain number of venues
// $query_totalVenues = "SELECT COUNT(*) as totalVenues FROM Venue LIMIT 1";
// $result_totalVenues = mysqli_query($conn, $query_totalVenues);

// if ($result_totalVenues) {
//     if ($row = mysqli_fetch_assoc($result_totalVenues)) {
//         $response['totalVenues'] = $row["totalVenues"];
//     }
//     mysqli_free_result($result_totalVenues);
// }

// // Obtain next event
// $query_nextEvent = "SELECT Event.EventName, Event.EventDateTime, Venue.LocationName AS Venue FROM Event
// LEFT JOIN Venue ON Event.Venue = Venue.Id
// WHERE EventDateTime >= NOW()
// ORDER BY EventDateTime ASC
// Limit 1";
// $result_nextEvent = mysqli_query($conn, $query_nextEvent);

// if ($result_nextEvent) {
//     if ($row = mysqli_fetch_assoc($result_nextEvent)) {
//         $response['nextEventName'] = $row["EventName"];
//         $response['nextEventDateTime'] = $row["EventDateTime"];
//         $response['nextEventVenue'] = $row["Venue"];
//     }
//     mysqli_free_result($result_nextEvent);
// }

// // Return JSON response
// echo json_encode($response);


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Simulating Online Payment</title>

    <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Spinner related */
        html, body {
            height: 100%;
            }

        body {
            align-items: center;
            background-color: #1D1F20;
            display: flex;
            justify-content: center;
            }

        .loader  {
            animation: rotate 1.2s infinite;  
            height: 50px;
            width: 50px;
            }

        .loader:before,
        .loader:after {   
            border-radius: 50%;
            content: '';
            display: block;
            height: 20px;  
            width: 20px;
            }
        .loader:before {
            animation: ball1 1.2s infinite;  
            background-color: var(--bs-info);
            box-shadow: 30px 0 0 var(--bs-secondary);
            margin-bottom: 10px;
            }
        .loader:after {
            animation: ball2 1.2s infinite; 
            background-color:var(--bs-primary);
            box-shadow: 30px 0 0 var(--bs-warning);
            }

        @keyframes rotate {
        0% { 
            -webkit-transform: rotate(0deg) scale(0.8); 
            -moz-transform: rotate(0deg) scale(0.8);
        }
        50% { 
            -webkit-transform: rotate(360deg) scale(1.2); 
            -moz-transform: rotate(360deg) scale(1.2);
        }
        100% { 
            -webkit-transform: rotate(720deg) scale(0.8); 
            -moz-transform: rotate(720deg) scale(0.8);
        }
        }

        @keyframes ball1 {
        0% {
            box-shadow: 30px 0 0 var(--bs-primary);
        }
        50% {
            box-shadow: 0 0 0 var(--bs-primary);
            margin-bottom: 0;
            -webkit-transform: translate(15px,15px);
            -moz-transform: translate(15px, 15px);
        }
        100% {
            box-shadow: 30px 0 0 var(--bs-primary);
            margin-bottom: 10px;
        }
        }

        @keyframes ball2 {
        0% {
            box-shadow: 30px 0 0 var(--bs-warning);
        }
        50% {
            box-shadow: 0 0 0 var(--bs-warning);
            margin-top: -20px;
            -webkit-transform: translate(15px,15px);
            -moz-transform: translate(15px, 15px);
        }
        100% {
            box-shadow: 30px 0 0 var(--bs-warning);
            margin-top: 0;
        }
        }

        /* Credit/Debit Card container related */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f7f7f7;
            height: 100vh;
            }

            .payment-method {
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 20px;
                width: 90%; /* Use a percentage to make it responsive */
                max-width: 400px; /* Ensure it doesn’t exceed this width */
                margin: 0 auto; /* Center the container */
            }

            /* Add media queries for smaller devices */
            @media (max-width: 768px) {
                .payment-method {
                    padding: 15px; /* Reduce padding for smaller screens */
                }
            }

            @media (max-width: 480px) {
                .payment-method {
                    padding: 10px; /* Further reduce padding for very small screens */
                }
            }

        h1, h2 {
        margin: 0;
        font-weight: 700;
        }

        h1 {
        font-size: 20px;
        margin-bottom: 15px;
        }

        h2 {
        font-size: 16px;
        margin-bottom: 5px;
        }

        p {
        font-size: 12px;
        color: #666;
        margin-bottom: 15px;
        }

        form {
        display: flex;
        flex-direction: column;
        }

        .input-group {
        margin-bottom: 15px;
        position: relative;
        }

        .input-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        }

        .input-group label {
        display: block;
        font-size: 12px;
        color: #333;
        margin-bottom: 5px;
        }

        .input-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        color: #333;
        }

        .input-group .card-icon,
        .input-group .check-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        font-size: 16px;
        color: #4caf50;
        }

        input:focus {
        outline: none;
        border-color: #4caf50;
        }

        input::placeholder {
        color: #bbb;
        }
        /* Fade in effect */
        .fadeIn {
            -webkit-animation-name: fadeIn;
            animation-name: fadeIn;
            -webkit-animation-duration: 1s;
            animation-duration: 1s;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both;
            }
            @-webkit-keyframes fadeIn {
            0% {opacity: 0;}
            100% {opacity: 1;}
            }
            @keyframes fadeIn {
            0% {opacity: 0;}
            100% {opacity: 1;}
            } 
    </style>
    

   <script>
        // Get Bootstrap's primary and secondary colours from CSS variable
        const primaryColour = getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim();
        const secondaryColour = getComputedStyle(document.documentElement).getPropertyValue('--bs-secondary').trim();
        // const infoColour = getComputedStyle(document.documentElement).getPropertyValue('--bs-info').trim();

        // // Fetch data from the backend
        // fetch('Dashboard.php')
        //     .then(response => response.json()) // Parse response as JSON
        //     .then(data => {
        //         document.getElementById('totalUserAccounts').innerText = data.totalUserAccounts;
                

        //     })
        //     .catch(error => {
        //         console.error('Error fetching dashboard data:', error);
        //         document.getElementById('totalUserAccounts').innerText = 'Couldnt find total User Accounts';
               
        //     });
    </script> 

</head>
<body>
    <div class="container text-center">
        <!-- Loaded the Credit / Debit Card container  -->
        <div class="payment-method">
            <h2>Credit / Debit Card</h2>
            <p>You may be directed to your bank's 3D secure process to authenticate your information.</p>
            <form>
                <div class="input-group">
                    <label for="card-number">Card number</label>
                    <input type="text" id="card-number" placeholder="**** **** **** ****">
                    <span class="card-icon">💳</span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <label for="expiry-date">Expiry date</label>
                            <input type="text" id="expiry-date" placeholder="MM/YY">
                            <span class="check-icon">✔</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label for="cvc">CVC / CVV</label>
                            <input type="text" id="cvc" placeholder="***">
                            <span class="check-icon">✔</span>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <label for="name-on-card">Name on card</label>
                    <input type="text" id="name-on-card" placeholder="Full name">
                </div>
            </form>
            <!-- Simulate Success -->
            <form action="paymentSimulationPayed.php" method="POST" id="paymentForm">
                <div class="col-md-12">
                    <a href="#" class="btn btn-primary" id="payNowButton">Pay Now</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        //working payment without sweetalert
    //     document.getElementById('payNowButton').addEventListener('click', function (event) {
    //     event.preventDefault(); // Prevent the default link action
    //     document.getElementById('paymentForm').submit();
    //     Swal.fire({
    //         title: 'Thank you for your payment!',
    //         text: 'Click the button below to return to Cards Against.',
    //         icon: 'success',
    //         confirmButtonText: 'Return to App',
    //         confirmButtonColor: primaryColour,
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             window.location.href = 'LoginSuccess.php'; // Redirect to the payment confirmation page
    //         }
    //     });
    // });

    document.getElementById('payNowButton').addEventListener('click', function (event) {
    event.preventDefault(); // Prevent the default link action

    // Get the form data
    var formData = new FormData(document.getElementById('paymentForm'));

    // Submit the form using fetch
    fetch('paymentSimulationPayed.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(responseText => {
        // Show SweetAlert based on the response
        Swal.fire({
            title: 'Thank you for your payment!',
            text: responseText, // Display the response from the PHP script
            icon: responseText.includes('success') ? 'success' : 'error', // Success or error based on response
            confirmButtonText: 'Return to App',
            confirmButtonColor: primaryColour
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'LoginSuccess.php'; // Redirect to the payment confirmation page
            }
        });
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'There was an issue with your payment. Please try again.',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });
});

</script>
</body>
</html>