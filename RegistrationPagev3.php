<?php
ob_start(); // Start output buffering
include_once('db_connecter.php');

// Ensure passed is explicitly checked
$passed = $_POST['passed'] ?? false;

if ($passed) {
    header("Location: RegistrationSucces.html");
    exit(); // Ensure no further code runs
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <title>Register</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.2/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
  <script src="scripts.js" defer></script>
  <script>
    document.querySelector("form").addEventListener("submit", function (event) {
        // Perform additional client-side validation if needed
        const username = document.getElementById("UserName").value.trim();
        const email = document.getElementById("Email").value.trim();
        const password = document.getElementById("Password").value.trim();
        const confirmPassword = document.getElementById("CPassword").value.trim();

        if (!username || !email || !password || !confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Please fill in all fields.'
            });
            event.preventDefault(); // Stop form submission
            return;
        }

        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'Passwords must match.'
            });
            event.preventDefault(); // Stop form submission
            return;
        }
    });
</script>


<style>
    /* Form container */
    .form-container {
        max-width: 500px;
        margin: 50px auto;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
    }

    /* Input fields */
    .form-control {
        border-radius: 4px;
        box-shadow: none;
        padding: 10px;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: #66afe9;
        box-shadow: 0 0 8px rgba(102, 175, 233, 0.6);
    }

    /* Error message */
    .error {
        color: red;
        font-size: 0.875rem;
        margin-top: 5px;
    }

    /* Message box for password strength */
    #message {
        display: none;
        margin-top: 10px;
        background-color: #f8d7da;
        border-radius: 4px;
        padding: 10px;
        color: #721c24;
    }

    #message p {
        margin: 5px 0;
    }

    .invalid {
        color: #dc3545;
    }

    .valid {
        color: #28a745;
    }

    /* Buttons */
    .btn {
        border-radius: 4px;
        padding: 10px;
        font-size: 1rem;
    }

    .btn-secondary {
        background-color: #6c757d;
    }

    .btn-info {
        background-color: #17a2b8;
    }

    .btn:hover {
        opacity: 0.9;
    }

    /* Layout for buttons */
    .row {
        display: flex;
        gap: 10px;
    }

    .col-sm-6 {
        flex: 1;
    }

    /* Mobile responsiveness */
    @media (max-width: 576px) {
        .form-container {
            padding: 15px;
        }
    }
</style>
</head>

<body
style="background-color: var(--bs-secondary);">

  <div class="container">

    <div class="row">
      <div class="col-sm-12">
        <div class="row">


<!-- Form -->
<div class="form-container">
<div style="text-align: center">
            <div class="row"  >
              <div class="col-sm-12" style="text-align: center; position: inherit; display: flex">
                <h1>
                  <img src="assets\bootstrap-5.3.3\Images\Cards_Against_Humanity_29.webp" height="30" width="30" /> Register
                </h1>

              </div>
            </div>
            <div class="row">
              <div class="col-sm-12" style="text-align: left; position: inherit; display: flex">
                <h3>I like my comedy from _ _ _ _ _ </h3>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12" style="text-align: center; position: inherit; display: flex">
                <p>Explore a word of terrible humour and horrible people</p>
              </div>
            </div>
          </div>
    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post" novalidate>
        <!-- Username -->
        <div class="form-group">
            <label for="UserName" class="text-dark">Username</label>
            <input class="form-control" placeholder="Username" type="text" id="UserName" name="UserName" />
            <div class="error"></div>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="Email" class="text-dark">Email</label>
            <input class="form-control" placeholder="Email" type="email" id="Email" name="Email" />
            <div class="error"></div>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="Password" class="text-dark">Password</label>
            <input class="form-control" placeholder="Password" type="password" id="Password" name="Password" />
            <div class="error"></div>
        </div>

        <div id="message">
            <h3>Password must contain the following:</h3>
            <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
            <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
            <p id="number" class="invalid">A <b>number</b></p>
            <p id="length" class="invalid">Longer than <b>8 characters</b></p>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="CPassword" class="text-dark">Confirm Password</label>
            <input class="form-control" placeholder="Confirm password" type="password" id="CPassword" name="CPassword" />
            <div class="error"></div>
        </div>

        <!-- Buttons -->
        <div class="row">
            <div class="col-sm-6">
                <button class="btn btn-success w-100" name="submit" type="submit" value="register">Register</button>
            </div>
            <div class="col-sm-6">
                <a href="LoginPage.php">
                    <button class="btn btn-info w-100" type="button">Back to Login</button>
                </a>
            </div>
        </div>
    </form>
</div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <script>
      var myInput = document.getElementById("Password");
      var letter = document.getElementById("letter");
      var capital = document.getElementById("capital");
      var number = document.getElementById("number");
      var length = document.getElementById("length");

      // When the user clicks on the password field, show the message box
      myInput.onfocus = function () {
        document.getElementById("message").style.display = "block";
      }

      // When the user clicks outside of the password field, hide the message box
      myInput.onblur = function () {
        document.getElementById("message").style.display = "none";
      }

      // When the user starts to type something inside the password field
      myInput.onkeyup = function () {
        // Validate lowercase letters
        var lowerCaseLetters = /[a-z]/g;
        if (myInput.value.match(lowerCaseLetters)) {
          letter.classList.remove("invalid");
          letter.classList.add("valid");
        } else {
          letter.classList.remove("valid");
          letter.classList.add("invalid");
        }

        // Validate capital letters
        var upperCaseLetters = /[A-Z]/g;
        if (myInput.value.match(upperCaseLetters)) {
          capital.classList.remove("invalid");
          capital.classList.add("valid");
        } else {
          capital.classList.remove("valid");
          capital.classList.add("invalid");
        }

        // Validate numbers
        var numbers = /[0-9]/g;
        if (myInput.value.match(numbers)) {
          number.classList.remove("invalid");
          number.classList.add("valid");
        } else {
          number.classList.remove("valid");
          number.classList.add("invalid");
        }

        // Validate length
        if (myInput.value.length > 8) {
          length.classList.remove("invalid");
          length.classList.add("valid");
        } else {
          length.classList.remove("valid");
          length.classList.add("invalid");
        }
      }
      
    </script>

</body>

</html>


<?php
ob_start(); // Start output buffering
include 'PasswordUtil.php'; 
$validPassword = false;
$validEmail = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $UserName = filter_input(INPUT_POST, "UserName", FILTER_SANITIZE_SPECIAL_CHARS);
    $Email = filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL);
    $Password = filter_input(INPUT_POST, "Password", FILTER_SANITIZE_SPECIAL_CHARS);

    // Validate email
    $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
    if (!preg_match($regex, $Email)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'The email address $Email is not valid. Please try again.'
            });
        </script>";
        exit();
    } else {
        $validEmail = true;
    }

    // Ensure passwords match
    if ($_POST["Password"] !== $_POST["CPassword"]) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'Passwords must match.'
            });
        </script>";
        exit();
    }

    // Validate password
    $result = validatePassword($Password);
    if ($result !== true) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Password',
                text: '$result'
            });
        </script>";
        exit();
    } else {
        $validPassword = true;
    }

    // Ensure fields are not empty
    if (empty($UserName) || empty($Email) || empty($Password)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Please fill in all required fields.'
            });
        </script>";
        exit();
    }

    // Check if email already exists
    $checkEmailQuery = "SELECT * FROM Account WHERE Email = '$Email'";
    $result = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Email Already Used',
                text: 'The email address $Email is already in use. Please try a different one.'
            });
        </script>";
        exit();
    }

    // If validation passes, hash password and insert into database
    if ($validPassword && $validEmail) {
        $password_hash = password_hash($_POST["Password"], PASSWORD_DEFAULT);

        $profilePicturePath = 'uploads/default_pfp.webp'; // Relative path to the image
        $sql = "INSERT INTO Account (UserName, Email, Password, UserRole, ProfilePicture) 
                VALUES ('$UserName', '$Email', '$password_hash', '1', '$profilePicturePath')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful',
                    text: 'You have successfully registered!'
                }).then(() => {
                    window.location.href = 'LoginPage.php';
                });
            </script>";
            exit();
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Database Error',
                    text: 'Error: " . mysqli_error($conn) . "'
                });
            </script>";
            exit();
        }
    }
}

mysqli_close($conn);
?>


