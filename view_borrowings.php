<?php
session_start();
// Check if the user is logged in and has the student role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Include the database connection
require 'config.php';

// Fetch borrowed books for the current student
try {
    $sql = "SELECT b.title, br.id AS borrowing_id, br.due_date, br.returned_at 
            FROM borrowings br 
            JOIN books b ON br.book_id = b.id 
            WHERE br.user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowed Books</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
        }

        h1 {
            color: #4CAF50;
            font-size: 2.5em;
            text-align: center;
            margin-top: 20px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
        }

        .table-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .table-container {
            flex: 1;
            min-width: 45%;
            max-width: 48%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            overflow: hidden;
            height: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 1em;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .returned {
            color: #4CAF50;
        }

        .not-returned {
            color: #ff4d4d;
        }

        .no-borrowed-books {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            font-size: 1.1em;
        }

        .header-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .header-buttons a {
            text-decoration: none;
            color: #fff;
            background-color: #4CAF50;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 10px;
        }

        .header-buttons a:hover {
            background-color: #388E3C;
        }

        .section-title {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 10px;
            text-align: left;
        }

        @media (max-width: 768px) {
            .table-wrapper {
                flex-direction: column;
            }
            .table-container {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="header-buttons">
        <a href='student_dashboard.php'>Dashboard</a>
        <a href='logout.php'>Logout</a>
    </div>

    <h1>My Borrowed Books</h1>

    <div class="container">
        <div class="table-wrapper">
            <!-- Currently Borrowed Books Table -->
            <div class="table-container">
                <div class="section-title">Currently Borrowed Books</div>
                <div>
                    <?php
                    $notReturnedBooks = array_filter($borrowedBooks, fn($book) => !$book['returned_at']);
                    if (count($notReturnedBooks) > 0): ?>
                        <table>
                            <tr><th>Title</th><th>Due Date</th><th>Status</th></tr>
                            <?php foreach ($notReturnedBooks as $borrowed): ?>
                                <tr>
                                    <td><?= htmlspecialchars($borrowed['title']) ?></td>
                                    <td><?= htmlspecialchars($borrowed['due_date']) ?></td>
                                    <td class="not-returned">Not Returned</td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <div class="no-borrowed-books">
                            <p>No currently borrowed books.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Returned Books Table -->
            <div class="table-container">
                <div class="section-title">Returned Books</div>
                <div>
                    <?php
                    $returnedBooks = array_filter($borrowedBooks, fn($book) => $book['returned_at']);
                    if (count($returnedBooks) > 0): ?>
                        <table>
                            <tr><th>Title</th><th>Due Date</th><th>Returned Date</th></tr>
                            <?php foreach ($returnedBooks as $borrowed): ?>
                                <tr>
                                    <td><?= htmlspecialchars($borrowed['title']) ?></td>
                                    <td><?= htmlspecialchars($borrowed['due_date']) ?></td>
                                    <td class="returned"><?= htmlspecialchars($borrowed['returned_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <div class="no-borrowed-books">
                            <p>No returned books found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>


