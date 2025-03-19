<?php
require '../config/database.php';

if (!isset($_GET['token'])) {
    die("Error: Invalid request.");
}

$token = $_GET['token'];

// Check if the token is valid
$stmt = $conn->prepare("SELECT email FROM users WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Error: Invalid or expired token.");
}

$email = $user['email']; // Now it's guaranteed to exist

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Update user password and clear token
    $stmt = $conn->prepare("UPDATE users SET password = ?, token = NULL WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);
    $stmt->execute();

    echo "<p class='text-green-500 text-center'>Password reset successful. You can now <a href='login.php'>log in</a>.</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <form method="POST">
            <input type="password" name="password" placeholder="New Password" required>
            <button type="submit">Reset Password</button>
        </form>
        <div class="text-center">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
