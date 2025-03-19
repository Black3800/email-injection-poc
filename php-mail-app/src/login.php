<?php
require '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists and if their email is confirmed
    $stmt = $conn->prepare("SELECT id, password, token FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        // Check if the token is an **email confirmation** token
        if (!is_null($result['token'])) {
            // Check if the token is a **confirmation token or password reset token**
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND token IS NOT NULL AND LENGTH(token) = 100");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $unconfirmed = $stmt->get_result()->fetch_assoc();

            if ($unconfirmed) {
                echo "<p class='text-red-500 text-center'>Error: Please confirm your email before logging in.</p>";
                exit();
            }
        }

        // Check password
        if (password_verify($password, $result['password'])) {
            $_SESSION['user_id'] = $result['id'];
            header("Location: profile.php");
            exit();
        } else {
            echo "<p class='text-red-500 text-center'>Invalid email or password.</p>";
        }
    } else {
        echo "<p class='text-red-500 text-center'>Invalid email or password.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="POST">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="text-center">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</body>
</html>
