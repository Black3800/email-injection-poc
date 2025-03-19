<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];

    // Update profile (security vulnerability: allows editing any user!)
    $stmt = $conn->prepare("UPDATE users SET email = ?, bio = ? WHERE id = ?");
    $stmt->bind_param("ssi", $email, $bio, $user_id);
    $stmt->execute();

    echo "<p class='text-green-500 text-center'>Profile updated successfully.</p>";
} else {
    // Get user_id from GET request (simulate edit mode)
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
    } else {
        die("Error: No user ID provided.");
    }

    // Fetch current user data
    $stmt = $conn->prepare("SELECT email, bio FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    $email = $user['email'] ?? '';
    $bio = $user['bio'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>"> <!-- Hidden ID -->
            
            <label>Email:</label>
            <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label>Bio:</label>
            <textarea name="bio" class="textarea-full"><?php echo htmlspecialchars($bio); ?></textarea>

            <button type="submit">Update</button>
        </form>
        <div class="text-center">
            <a href="profile.php">Back to Profile</a>
        </div>
    </div>
</body>
</html>
