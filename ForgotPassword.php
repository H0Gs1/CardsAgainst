<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
    <script src="scripts.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Forgot Password Page</title>
    <style>
        .forgot-container {
            border-radius: 1rem;
            width: 100%;
            max-width: 48rem;
            height: auto;
            background-color: var(--bs-primary);
            overflow-x: hidden;
            padding-top: 5rem;
            padding-right: 2rem;
            padding-bottom: 5rem;
            padding-left: 2rem;
            border: 0.1rem solid black;
            margin-top: 2rem;
            margin-bottom: 2rem;
            margin-right: 2rem;
            margin-left: 2rem;
        }

        .center-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .col-sm-8 {
            display: flex;
            justify-content: center;
        }

        /* Spinner styles */
        .spinner {
            display: none; /* Initially hidden */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
    </style>
</head>

<body style="background-color: var(--bs-secondary);">
    <div id="spinner" class="spinner">
        <img src="loading-7528_256.gif" alt="Loading..." />
    </div>

    <form id="resetPasswordForm" method="post" style="color: white">
        <div class="row center-content">
            <div class="col-sm-12 col-md-8">
                <div class="forgot-container">
                    <h1>
                        <img src="assets\bootstrap-5.3.3\Images\Cards_Against_Humanity_29.webp" height="40" width="40" />
                        Forgot Password
                    </h1>
                    <p>
                        Ok boomer we'll allow you to change it.
                    </p>
                    <h6 class="information-text">Enter your _______ email to reset your password.</h6>
                    <div class="form-group">
                        <input type="email" name="Email" id="Email" required>
                        <p><label for="Email">Email</label></p>
                        <button type="submit" onclick="showSpinner()"> Reset Password </button>
                    </div>
                    <div class="footer">
                        <h5>New here? We don't care <a href="RegistrationPagev3.php" style="color: var(--bs-info)">Sign
                            Up.</a></h5>
                        <h5>Already have a _______ account? <a href="LoginPage.php" style="color: var(--bs-info)">Sign
                            In.</a></h5>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        // Show the spinner
        function showSpinner() {
            document.getElementById('spinner').style.display = 'block'; // Display spinner
        }

        // Prevent the form from submitting the traditional way
        document.getElementById('resetPasswordForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Stop the form from submitting normally

            // Create a FormData object from the form
            let formData = new FormData(this);

            // Send the form data using AJAX
            fetch('send-password-reset.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Parse JSON response
            .then(data => {
                // Hide the spinner after the request completes
                document.getElementById('spinner').style.display = 'none';

                // Show SweetAlert based on the response
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        confirmButtonText: 'Okay'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                        confirmButtonText: 'Try Again'
                    });
                }
            })
            .catch(error => {
                // Hide the spinner in case of error
                document.getElementById('spinner').style.display = 'none';

                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'There was an error with the request. Please try again later.',
                    confirmButtonText: 'Okay'
                });
            });
        });
    </script>
</body>

</html>
