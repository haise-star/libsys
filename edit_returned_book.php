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

// Retrieve current details of the returned book
try {
    $stmt = $conn->prepare("SELECT rb.id, rb.return_date, bo.title AS book_title, u.username AS student_username 
                            FROM return_books rb 
                            JOIN books bo ON rb.book_id = bo.id 
                            JOIN users u ON rb.user_id = u.id 
                            WHERE rb.id = :id");
    $stmt->bindParam(':id', $returnedBookId, PDO::PARAM_INT);
    $stmt->execute();
    $returnedBook = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$returnedBook) {
        echo "Returned book not found.";
        exit();
    }
} catch (PDOException $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage());
    exit();
}

// Update details upon form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newReturnDate = $_POST['return_date'];

    try {
        $updateStmt = $conn->prepare("UPDATE return_books SET return_date = :return_date WHERE id = :id");
        $updateStmt->bindParam(':return_date', $newReturnDate);
        $updateStmt->bindParam(':id', $returnedBookId, PDO::PARAM_INT);
        $updateStmt->execute();

        $successMessage = "The returned book record has been successfully updated.";
    } catch (PDOException $e) {
        $errorMessage = "Error updating the returned book record: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Returned Book</title>
</head>
<body>
    <h2>Edit Returned Book</h2>

    <?php if ($errorMessage): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <p style="color: green;"><?php echo $successMessage; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <p><strong>Book Title:</strong> <?php echo htmlspecialchars($returnedBook['book_title']); ?></p>
        <p><strong>Returned By:</strong> <?php echo htmlspecialchars($returnedBook['student_username']); ?></p>

        <label for="return_date">Return Date:</label>
        <input type="date" id="return_date" name="return_date" value="<?php echo htmlspecialchars($returnedBook['return_date']); ?>" required>

        <br><br>
        <input type="submit" value="Update Return Record">
    </form>

    <br>
    <a href="librarian_dashboard.php">Back to Dashboard</a>
</body>
</html>
