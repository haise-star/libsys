<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit();
}

// Include the database connection
require 'config.php';

// Fetch all borrowed books with student information
try {
    $sql = "SELECT b.title, u.username, br.due_date, br.returned_at 
            FROM borrowings br 
            JOIN books b ON br.book_id = b.id 
            JOIN users u ON br.user_id = u.id 
            ORDER BY br.due_date ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<h1>All Borrowed Books</h1>";

if (count($borrowedBooks) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Book Title</th><th>Borrowed By</th><th>Due Date</th><th>Returned</th></tr>";
    foreach ($borrowedBooks as $borrowed) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($borrowed['title']) . "</td>";
        echo "<td>" . htmlspecialchars($borrowed['username']) . "</td>";
        echo "<td>" . htmlspecialchars($borrowed['due_date']) . "</td>";
        echo "<td>" . ($borrowed['returned_at'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No borrowed books found.</p>";
}

echo "<a href='librarian_dashboard.php'>Go back to dashboard</a>";
echo "<a href='logout.php'>Logout</a>";
?>
