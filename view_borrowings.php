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
            background-color: #f3f4f6;
            color: #333;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            transition: background-color 0.3s ease;
        }

        h1 {
            color: #5c6ac4;
            font-size: 2.5em;
            text-align: center;
            margin-top: 20px;
            font-weight: 700;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            padding: 30px;
            margin: 20px 0;
            transition: box-shadow 0.3s ease;
        }

        .container:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            background-color: #fafafa;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .table-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        th {
            background-color: #f2f7ff;
            font-weight: 600;
            color: #5c6ac4;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tr:hover td {
            background-color: #f9f9ff;
        }

        .returned {
            color: #4CAF50;
            font-weight: bold;
        }

        .not-returned {
            color: #e57373;
            font-weight: bold;
        }

        .no-borrowed-books {
            background-color: #ffecec;
            color: #c62828;
            border: 1px solid #ffcccc;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            font-size: 1.1em;
            font-weight: bold;
        }

        .header-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .header-buttons a {
            text-decoration: none;
            color: #fff;
            background-color: #5c6ac4;
            padding: 10px 15px;
            border-radius: 6px;
            font-weight: bold;
            margin-left: 10px;
            transition: background-color 0.3s ease;
        }

        .header-buttons a:hover {
            background-color: #4a5ebd;
        }

        .back-to-top {
            text-align: center;
            margin-top: 15px;
            cursor: pointer;
            color: #5c6ac4;
            font-weight: bold;
            font-size: 0.9em;
            transition: color 0.3s ease;
        }

        .back-to-top:hover {
            color: #4a5ebd;
            text-decoration: underline;
        }

        .section-title {
            font-size: 1.4em;
            color: #333;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: left;
            border-left: 4px solid #5c6ac4;
            padding-left: 10px;
        }
    </style>
    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
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
                <div class="back-to-top" onclick="scrollToTop()">Back to Top</div>
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
                <div class="back-to-top" onclick="scrollToTop()">Back to Top</div>
            </div>
        </div>
    </div>
</body>
</html>






