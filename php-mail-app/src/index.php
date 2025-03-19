<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>PHP MySQL App</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Welcome to the App</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="text-center">You are logged in!</p>
            <div class="text-center">
                <a href="profile.php" class="text-blue-500">Go to Profile</a> |
                <a href="logout.php" class="text-red-500">Logout</a>
            </div>
        <?php else: ?>
            <p class="text-center">Please register or log in to continue.</p>
            <div class="text-center">
                <a href="register.php" class="text-green-500">Register</a> |
                <a href="login.php" class="text-blue-500">Login</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
