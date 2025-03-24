<?php 

$is_invalid = false;
$error_message = "";
ob_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mysqli = require __DIR__ . "/db_connecter.php";

    // Securely escape input and create SQL query
    $sql = sprintf(
        "SELECT * FROM Account WHERE Email = '%s'",
        $mysqli->real_escape_string($_POST["Email"])
    );
    
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($_POST["Password"], $user["Password"])) {
            session_start();
            session_regenerate_id();
            $_SESSION["user_id"] = $user["Id"];
            $_SESSION["user_username"] = $user["UserName"];
            $_SESSION['UserRole'] = $user['UserRole']; // Save user role
    

            if ($user['UserRole'] === '1') {
                header("Location: LoginSuccess.php");
            } else if ($user['UserRole'] === '0') {
                header("Location: AdminView2.php?token=some_token_value&dashboard=1");

            }
            exit;
        } else {
            $is_invalid = true;
            $error_message = "You really forgot your own login?";
        }
    } else {
        $is_invalid = true;
        $error_message = "You really forgot your own login?";
    }
}
?>



<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Full-width input fields */
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* Set a style for all buttons */
        button {
            background-color: #04AA6D;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            opacity: 0.8;
        }

        /* Extra styles for the cancel button */
        .cancelbtn {
            width: auto;
            padding: 10px 18px;
            background-color: var(--bs-danger);
        }

        /* Center the image and position the close button */
        .imgcontainer {
            text-align: center;
            margin: 24px 0 12px 0;
            position: relative;
        }

        img.avatar {
            width: 40%;
            border-radius: 50%;
        }

        .container {
            padding: 16px;
        }

        span.password {
            float: right;
            padding-top: 16px;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
            padding-top: 60px;
        }

        /* Modal Content/Box */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto 15% auto;
            /* 5% from the top, 15% from the bottom and centered */
            border: 1px solid #888;
            width: 80%;
            /* Could be more or less, depending on screen size */
        }

        /* The Close Button (x) */
        .close {
            position: absolute;
            right: 25px;
            top: 0;
            color: #000;
            font-size: 35px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: var(--bs-danger);
            cursor: pointer;
        }

        /* Add Zoom Animation */
        .animate {
            -webkit-animation: animatezoom 0.6s;
            animation: animatezoom 0.6s
        }

        @-webkit-keyframes animatezoom {
            from {
                -webkit-transform: scale(0)
            }

            to {
                -webkit-transform: scale(1)
            }
        }

        @keyframes animatezoom {
            from {
                transform: scale(0)
            }

            to {
                transform: scale(1)
            }
        }

        /* Change styles for span and cancel button on extra small screens */
        @media screen and (max-width: 300px) {
            span.password {
                display: block;
                float: none;
            }

            .cancelbtn {
                width: 100%;
            }
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background-color: white;
            border-radius: 0.5rem;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .login-box h2 {
            margin-bottom: 20px;
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
        }

        .login-btn {
            background-color: #04AA6D;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 0.5rem;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover {
            background-color: #037f59;
        }

        .login-btn:focus {
            outline: none;
        }
    </style>
</head>

<body
style="background-color: var(--bs-secondary);">
    
    <div class="login-container">
        <div class="login-box">
            <h2>Login to Cards against ________, whoever</h2>
            <?php if ($is_invalid): ?> 
                <em style="color: red;"><?= htmlspecialchars($error_message) ?></em>  
            <?php endif; ?>  
            <button onclick="document.getElementById('id01').style.display='block'" class="login-btn">Login</button>
        </div>
    </div>

<div id="id01" class="modal">

    <form class="modal-content animate" action="" method="post">
        <div class="imgcontainer">
            <span onclick="document.getElementById('id01').style.display='none'" class="close"
                title="Close Modal">&times;</span>
            <img src="assets\bootstrap-5.3.3\Images\Cards_Against_Humanity_29.webp" height="60" width="60">
        </div>

        <div class="container">
            <label for="Email"><b>Email</b></label>
            <input type="email" placeholder="Enter email" name="Email" id="Email" value="<?= htmlspecialchars($_POST["Email"] ?? "") ?>" required>

            <label for="Password"><b>Password</b></label>                
            <?php if ($is_invalid): ?> 
                <em style="color: red;"><?= htmlspecialchars($error_message) ?></em>  
            <?php endif; ?>  
            <input type="password" placeholder="Enter Password" name="Password" id="Password" required>

            <button type="submit">Login</button>
        </div>


        <div class="container" style="background-color:var(--bs-secondary)">
            <button type="button" onclick="document.getElementById('id01').style.display='none'"
                class="cancelbtn">Cancel</button>
            <span class="password"><a href="ForgotPassword.php">Forgot password? </a></span> <br>
            <span class="password" style="text-align: right;"><a href="RegistrationPagev3.php">Register here
                </a></span>
        </div>
    </form>
</div>

<script>

    var modal = document.getElementById('id01');

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }



</script>

</body>

</html>
