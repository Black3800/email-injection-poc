<?php
require '../config/database.php';
require '../config/mailer.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id, token FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        if (!is_null($result['token']) && strlen($result['token']) == 100) {
            echo "<p class='text-red-500 text-center'>Error: You have already registered. Please check your email to confirm your account.</p>";
        } else {
            echo "<p class='text-red-500 text-center'>Error: Username or email is already taken.</p>";
        }
    } else {
        // Generate a new email confirmation token
        $token = bin2hex(random_bytes(50));

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, token) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $token);
        if ($stmt->execute()) {
            $confirmation_link = "http://localhost:7654/confirm.php?token=$token";
            $email_sent = sendEmail($email, "Confirm Your Email", "Click here to confirm your account: $confirmation_link");

            if ($email_sent) {
                echo "<p class='text-green-500 text-center'>Registration successful. Check your email for confirmation.</p>";
            } else {
                echo "<p class='text-yellow-500 text-center'>Registration successful, but failed to send confirmation email.</p>";
            }
        } else {
            echo "<p class='text-red-500 text-center'>Error: Could not register user.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <div class="text-center">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </div>
</body>
</html>
