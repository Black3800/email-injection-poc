<?php
require '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile
$stmt = $conn->prepare("SELECT username, email, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$username = htmlspecialchars($user['username']);
$email = $user['email'];
$bio = htmlspecialchars($user['bio'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Profile</h1>
        <p><strong>Username:</strong> <?php echo $username; ?></p>
        <p><strong>Email:</strong> <?php echo $email; ?></p>
        <p><strong>Bio:</strong> <?php echo nl2br($bio); ?></p>

        <div class="text-center">
            <a href="edit_profile.php?user_id=<?php echo $user_id; ?>" class="text-blue-500">Edit Profile</a> |
            <a href="logout.php" class="text-red-500">Logout</a>
        </div>
    </div>
</body>
</html>
