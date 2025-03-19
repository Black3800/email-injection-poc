<?php
require '../config/database.php';

$message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token belongs to an **email confirmation**
    $stmt = $conn->prepare("SELECT id FROM users WHERE token = ? AND LENGTH(token) = 100");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        // Remove the confirmation token (marking email as confirmed)
        $stmt = $conn->prepare("UPDATE users SET token = NULL WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        $message = "<p class='text-green-500 text-center'>Your email has been confirmed. You can now <a href='login.php'>log in</a>.</p>";
    } else {
        $message = "<p class='text-red-500 text-center'>Invalid or expired confirmation link.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Email Confirmation</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Email Confirmation</h1>
        <p class="text-center"><?php echo $message; ?></p>
    </div>
</body>
</html>
