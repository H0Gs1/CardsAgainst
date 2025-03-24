<?php
include_once("db_connecter.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    echo "Session user_id not set"; // Debugging line
    header("Location: LoginPage.php");
    exit;
}


$userId = $_SESSION["user_id"];

// Fetch user data
$sql = "SELECT * FROM Account WHERE Id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$username = $user['UserName'];
$email = $user['Email'];
$profilePic = $user['ProfilePicture'];
$userRole = $user['UserRole'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/bootstrap-5.3.3/scss/bootstrap.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Profile Information</title>
    <style>
        body {
            background-color: var(--bs-light);
            color: var(--bs-body-color);
        }

        .profile-container {
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 0.5rem;
            background-color: var(--bs-white);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-img {
            border-radius: 50%;
            border: 2px solid var(--bs-info);
        }

        .btn-custom {
            background-color: var(--bs-info);
            color: var(--bs-white);
        }

        .btn-custom:hover {
            background-color: var(--bs-info-hover, #0b5ed7); /* Default info hover color */
        }

        .field-label {
            font-weight: bold;
            color: var(--bs-info);
            cursor: pointer;
        }

        .field-value {
            color: var(--bs-body-color);
        }

        .upload-section {
            margin-top: 1rem;
        }

        .action-buttons {
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container profile-container">
        <h2 class="text-center" style="color: var(--bs-info);">User Profile</h2>
        <p class="text-center">Welcome, <strong><?= htmlspecialchars($username) ?></strong>!</p>

        <div class="text-center mb-3">
            <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture" class="profile-img" width="150" height="150">
        </div>
<!-- new upload form  -->
        <form action="upload_profile_pic.php" method="POST" enctype="multipart/form-data" class="upload-section text-center">
            <label for="image" class="form-label">Upload Profile Picture:</label>
            <input type="file" name="image" id="image" class="form-control mb-2" accept="image/*" required>
            <button type="submit" class="btn btn-custom">Upload</button>
        </form>

<!-- old upload form  -->
                <!-- Profile Picture Upload Form -->

        <!-- <form action="upload_profile_pic.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="1048576">
            <label for="image">Choose a file:</label>
            <input type="file" name="image" id="image">
            <button type="submit" class="btn btn-custom">Upload</button>
        </form> -->


        <div class="mt-4">
        <h5>Press on Username/Email to change your info. Or you can request to change password</h5>
            <p>
            <p>
    <span class="field-label" onclick="updateField('username')">Username:</span>
    <span class="field-value"><?= htmlspecialchars($username) ?></span>
</p>

            </p>
            <p>
                <span class="field-label" onclick="updateField('email')">Email:</span>
                <span class="field-value"><?= htmlspecialchars($email) ?></span>
            </p>
            <!-- Show for users only -->
        <?php if ($userRole === 1): ?>  
            <div class="mt-3">
                <a href="LoginSuccess.php" class="btn btn-warning">Go to Home</a>
            </div>
        <?php endif; ?>
        </div>

        <div class="action-buttons text-center">
            <a href="reset_password.php" onclick="confirmResetPassword(event)" class="btn btn-warning">Reset Password</a>
            <a href="Logout.php" class="btn btn-danger">Log out</a>
        </div>
    </div>

    <script>
        // Function to update fields
// Function to update fields
function updateField(field) {
    Swal.fire({
        title: `Update ${field}`,
        input: 'text',
        inputLabel: `Enter new ${field}:`,
        inputPlaceholder: `New ${field}`,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: (value) => {
            if (!value) {
                Swal.showValidationMessage(`${field} cannot be empty`);
            }
            return value;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const value = result.value; // Get the new value entered by the user
            $.post("update_account.php", { 
                id: <?= $userId ?>, 
                field: field, 
                value: value 
            }, function(response) {
                try {
                    const res = JSON.parse(response); // Parse server response
                    if (res.success) {
                        Swal.fire('Updated!', `${field} updated successfully.`, 'success').then(() => {
                            location.reload(); // Reload the page to reflect changes
                        });
                    } else {
                        Swal.fire('Error', res.error || `Failed to update ${field}.`, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Invalid response from the server.', 'error');
                }
            });
        }
    });
}


        // Confirmation for reset password
        function confirmResetPassword(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "This will redirect you to the password reset page.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'ForgotPassword.php';
                }
            });
        }

        // Handle file upload feedback
        $("form").on("submit", function (e) {
            e.preventDefault(); // Prevent default form submission
            const formData = new FormData(this);
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.fire('Uploaded!', 'Profile picture updated successfully.', 'success').then(() => {
                        location.reload();
                    });
                },
                error: function () {
                    Swal.fire('Error', 'Failed to upload profile picture.', 'error');
                }
            });
        });
    </script>
</body>
</html>
