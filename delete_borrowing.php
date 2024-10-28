<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include the database connection
require 'config.php';

// Check if the borrowing ID is provided
if (!isset($_GET['id'])) {
    echo "No borrowing ID specified.";
    exit();
}

//dito tinatanggal ang borrowing records
$borrowingId = $_GET['id'];

try {
    $sql = "DELETE FROM borrowings WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $borrowingId, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "<p>Borrowing record deleted successfully! <a href='manage_borrowings.php'>Go back to Manage Borrowings</a></p>";
    } else {
        echo "<p>Failed to delete the borrowing record. Please try again.</p>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
