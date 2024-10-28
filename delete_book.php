<?php
require 'config.php';

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    try {
        // Check if book is referenced in borrowings table
        $sql = "SELECT COUNT(*) FROM borrowings WHERE book_id = :book_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['book_id' => $book_id]);
        $borrowings_count = $stmt->fetchColumn();

        if ($borrowings_count > 0) {
            echo "Cannot delete this book. It is currently referenced in borrowings.";
        } else {
            // Delete the book if there are no references
            $sql = "DELETE FROM books WHERE id = :book_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['book_id' => $book_id]);
            echo "Book deleted successfully.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Book ID not provided.";
}
