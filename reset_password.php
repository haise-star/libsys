<?php
// Assuming you've already set up the database connection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];

    // Validate token
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = :token LIMIT 1");
    $stmt->bindParam(':token', $token);
    
    if ($stmt->execute() && $stmt->rowCount() > 0) {
        $userId = $stmt->fetchColumn();
        
        // Update user password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :user_id");
        $updateStmt->bindParam(':password', $hashedPassword);
        $updateStmt->bindParam(':user_id', $userId);
        
        if ($updateStmt->execute()) {
            echo "Password has been updated successfully.";
        } else {
            echo "Error updating password.";
        }

        // Optionally, delete the token after use
        $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
        $deleteStmt->bindParam(':token', $token);
        $deleteStmt->execute();
    } else {
        echo "Invalid or expired token.";
    }
}
?>

<!-- HTML form for reset password -->
<form method="POST">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
    <input type="password" name="new_password" required placeholder="New Password">
    <button type="submit">Reset Password</button>
</form>
