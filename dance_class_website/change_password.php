<?php
session_start();
require 'db_connect.php'; // Ensure database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to create a password hash with a salt
function create_password_hash($password) {
    // Create a random salt
    $salt = bin2hex(random_bytes(32)); // Generates a secure random salt
    
    // Hash the password using sha256 with the salt
    $hash = hash('sha256', $password . $salt);
    
    // Return the hash and salt together so we can verify later
    return $salt . '$' . $hash;
}

// Function to verify the password against the stored hash
function verify_password($password, $stored_hash) {
    // Separate the salt and hash from the stored value
    list($salt, $hash) = explode('$', $stored_hash);
    
    // Hash the password with the stored salt
    $new_hash = hash('sha256', $password . $salt);
    
    // Check if the new hash matches the stored hash
    return $new_hash === $hash;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Fetch the current password hash and salt from the database
    $sql = "SELECT password FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    // Extract the stored hash and salt
    $stored_password = $user['password'];

    // Verify the current password
    if (!verify_password($current_password, $stored_password)) {
        $error_message = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_new_password) {
        $error_message = "New passwords do not match.";
    } else {
        // Hash the new password with a salt
        $new_hashed_password = create_password_hash($new_password);

        // Update the password in the database
        $update_sql = "UPDATE users SET password = '$new_hashed_password' WHERE id = '$user_id'";
        if ($conn->query($update_sql) === TRUE) {
            $success_message = "Password changed successfully!";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Dance Class</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="navbar">
        <div>Dance Class</div>
        <div>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <section class="change-password">
        <h1>Change Password</h1>

        <?php if (isset($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Current Password</label>
            <input type="password" name="current_password" required>

            <label>New Password</label>
            <input type="password" name="new_password" required>

            <label>Confirm New Password</label>
            <input type="password" name="confirm_new_password" required>

            <button type="submit">Change Password</button>
        </form>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Dance Class. All Rights Reserved.</p>
    </footer>
</body>
</html>
