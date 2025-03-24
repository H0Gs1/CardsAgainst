<?php
    session_start();

    // Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id']; // Access the user ID from the session
    $_SESSION["UserId"] = $userId;
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: loginPage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Card Maker</title>
    <!-- Bootstrap CSS -->
    <link href="assets\bootstrap-5.3.3\scss\bootstrap.css" rel="stylesheet" />
    <style>
    .site-header {
        background-color: rgba(0, 0, 0, 0.85);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        backdrop-filter: saturate(180%) blur(20px);
    }

    .site-header a {
        color: #727272;
        transition: color 0.15s ease-in-out;
    }

    .site-header a:hover {
        color: #fff;
        text-decoration: none;
    }

    /* Styling for the profile button */
    .profile-btn {
        background-color: var(--bs-info);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 30px;
        font-size: 1rem;
        font-weight: 500;
        transition: background-color 0.3s ease, transform 0.2s ease;
        cursor: pointer;
    }

    .profile-btn:hover {
        background-color: #0276b3;
        transform: translateY(-2px); /* Adds a subtle lift effect */
    }

    .profile-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(38, 143, 255, 0.5);
    }
</style>

</head>
<body class="bg-light">

<nav class="site-header sticky-top py-1">
    <div class="container d-flex flex-column flex-md-row justify-content-between">
        <a class="nav-link" href="LoginSuccess.php">
            <img src="assets\bootstrap-5.3.3\Images\Cards_Against_Humanity_29.webp" height="40" width="40" />
        </a>
        <button onclick="window.location.href='update_profile.php';" class="profile-btn">Profile</button>
    </div>
</nav>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0 text-center">Create a New Card</h4>
                    </div>
                    <div class="card-body">
                        <form action="community_submit.php" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea id="content" name="Content" class="form-control" rows="5" required></textarea>
                                <div class="invalid-feedback">
                                    Please provide the content for the card.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="isAnswer" class="form-label">Is this an Answer?</label>
                                <select id="isAnswer" name="IsAnswer" class="form-select" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select if this is an answer.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="packId" class="form-label">Pack</label>
                                <select id="packId" name="PackId" class="form-select" required>
                                    <?php include 'load_pack.php'; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a pack.
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-secondary">Submit Card</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable Bootstrap validation styles
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
