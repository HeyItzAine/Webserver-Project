<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: index.html'); // Redirect to login if not logged in
    exit;
}

$user = $_SESSION['user'];
$firstName = $user['firstName'];
$lastName = $user['lastName'];
$email = $user['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Global styles */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(to bottom, #111, #333); /* Black gradient background */
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .container {
            text-align: center;
            background: rgba(0, 0, 0, 0.7); /* Semi-transparent black background */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.8);
        }

        h1 {
            font-size: 2em;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .btn-logout {
            padding: 12px 24px;
            background: #444;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-logout:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Dashboard</h1>

        <!-- Display user information -->
        <div class="user-info">
            <p><strong>First Name:</strong> <?php echo $firstName; ?></p>
            <p><strong>Last Name:</strong> <?php echo $lastName; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
        </div>

        <p>Successfully logged in as <strong><?php echo $firstName . ' ' . $lastName; ?></strong></p>

        <!-- Logout button with event listener for redirection -->
        <button class="btn-logout" id="logoutButton">Logout</button>
    </div>

    <script>
        document.getElementById('logoutButton').addEventListener('click', function() {
            // Perform logout logic here (e.g., making an API call to logout)
            // Then redirect to index.html
            window.location.href = '/index.html';
        });
    </script>
</body>
</html>
