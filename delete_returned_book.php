<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit();
}

require 'config.php';

if (!isset($_GET['id'])) {
    echo "No returned book ID specified.";
    exit();
}

$returnedBookId = $_GET['id'];
$successMessage = '';
$errorMessage = '';

// Delete the returned book record
try {
    $stmt = $conn->prepare("DELETE FROM return_books WHERE id = :id");
    $stmt->bindParam(':id', $returnedBookId, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $successMessage = "The returned book record has been successfully deleted.";
    } else {
        $errorMessage = "Failed to delete the returned book record. Please try again.";
    }
} catch (PDOException $e) {
    $errorMessage = "Database error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Returned Book</title>
</head>
<body>
    <h2>Delete Returned Book</h2>

    <?php if ($errorMessage): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
    <?php endif; ?>

    <a href="librarian_dashboard.php">Back to Dashboard</a>
</body>
</html>
