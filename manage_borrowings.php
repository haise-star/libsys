<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include the database connection
require 'config.php';

// Fetch all borrowings
try {
    $sql = "SELECT b.id, u.username, bo.title, b.borrowed_at, b.due_date, b.returned_at 
            FROM borrowings b 
            JOIN users u ON b.user_id = u.id 
            JOIN books bo ON b.book_id = bo.id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<h1>Manage Borrowings</h1>";
echo "<h2>All Borrowing Transactions</h2>";

// pinapakita dito ang listahan ng mga nahiram na libro
if (count($borrowings) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>User</th><th>Book Title</th><th>Borrowed At</th><th>Due Date</th><th>Returned At</th><th>Actions</th></tr>";
    foreach ($borrowings as $borrowing) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($borrowing['id']) . "</td>";
        echo "<td>" . htmlspecialchars($borrowing['username']) . "</td>";
        echo "<td>" . htmlspecialchars($borrowing['title']) . "</td>";
        echo "<td>" . htmlspecialchars($borrowing['borrowed_at']) . "</td>";
        echo "<td>" . htmlspecialchars($borrowing['due_date']) . "</td>";
        echo "<td>" . htmlspecialchars($borrowing['returned_at'] ?? 'Not Returned') . "</td>";
        echo "<td>
                <a href='delete_borrowing.php?id=" . htmlspecialchars($borrowing['id']) . "' onclick='return confirm(\"Are you sure?\");'>Delete</a>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No borrowings found.</p>";
}

echo "<a href='logout.php'>Logout</a>";
?>
