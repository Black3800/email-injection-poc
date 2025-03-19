<?php
require '../config/database.php';
require '../config/mailer.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Allow password reset only if:
    // - The user is confirmed (token is NULL or has a reset token)
    // - The token is NOT a confirmation token (100 characters)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND (token IS NULL OR LENGTH(token) = 50)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        // Generate a new 50-character password reset token
        $reset_token = bin2hex(random_bytes(25));

        // Update the token to allow another reset
        $stmt = $conn->prepare("UPDATE users SET token = ? WHERE email = ?");
        $stmt->bind_param("ss", $reset_token, $email);
        $stmt->execute();

        $reset_link = "http://localhost:7654/reset_password.php?token=$reset_token";
        $email_sent = sendEmail($email, "Reset Password", "Click here to reset your password: $reset_link");

        if ($email_sent) {
            echo "<p class='text-green-500 text-center'>Reset password email sent.</p>";
        } else {
            echo "<p class='text-yellow-500 text-center'>Error: Email could not be sent. Try again later.</p>";
        }
    } else {
        echo "<p class='text-red-500 text-center'>Error: Either the email is incorrect or the account has not been confirmed.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <div class="text-center">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
