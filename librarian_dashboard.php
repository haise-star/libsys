<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit();
}

require 'config.php';

$books = $borrowedBooks = $returnedBooks = [];
$returnedBooksCount = 0;

try {
    // Fetch all books
    $stmt = $conn->prepare("SELECT * FROM books");
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all borrowed books with details
    $stmt = $conn->prepare("SELECT borrowings.id AS borrowing_id, borrowings.borrow_date, borrowings.due_date, borrowings.status,
                            books.title AS book_title, users.username AS student_username 
                            FROM borrowings 
                            JOIN books ON borrowings.book_id = books.id 
                            JOIN users ON borrowings.user_id = users.id");
    $stmt->execute();
    $borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch count of all returned books from return_books table based on status
    $stmt = $conn->prepare("SELECT COUNT(*) FROM return_books WHERE status = 'return'");
    $stmt->execute();
    $returnedBooksCount = $stmt->fetchColumn();

    // Fetch details of returned books from return_books table
    $stmt = $conn->prepare("SELECT rb.id, bo.title AS book_title, u.username AS student_username, rb.return_date 
                            FROM return_books rb 
                            JOIN books bo ON rb.book_id = bo.id 
                            JOIN users u ON rb.user_id = u.id 
                            WHERE rb.status = 'return' 
                            ORDER BY rb.return_date DESC");
    $stmt->execute();
    $returnedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2.5rem;
            color: #007bff;
            text-align: center;
            margin-bottom: 5px;
        }
        .welcome {
            text-align: center;
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 20px;
        }
        .statistics {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            text-align: center;
            flex: 1;
            margin: 0 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }
        .stat-box:hover {
            transform: translateY(-5px);
        }
        .table-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            padding: 10px;
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e1f5fe;
        }
        .actions a {
            color: #007bff;
            text-decoration: none;
            margin: 0 5px;
            transition: color 0.2s;
        }
        .logout, .add-book-button {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            text-align: center;
            font-size: 1rem;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .logout {
            background-color: #dc3545;
        }
        .logout:hover {
            background-color: #c82333;
        }
        .add-book-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Librarian Dashboard</h1>
        <p class="welcome">Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Here’s an overview of the library activities.</p>

        <div class="statistics">
            <div class="stat-box">
                <h4><?php echo count($books); ?></h4>
                <p>Total Books</p>
            </div>
            <div class="stat-box">
                <h4><?php echo count($borrowedBooks); ?></h4>
                <p>Borrowed Books</p>
            </div>
            <div class="stat-box">
    <h4><?php echo htmlspecialchars($returnedBooksCount); ?></h4>
    <p>Returned Books</p>
</div>
        </div>

        <a href="edit_book.php" class="add-book-button">+ Add New Book</a>

        <div class="table-container">
            <h2>All Books</h2>
            <?php if (count($books) > 0): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Published Year</th>
                        <th>Supply Count</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['id']); ?></td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['genre']); ?></td>
                            <td><?php echo htmlspecialchars($book['published_year']); ?></td>
                            <td><?php echo htmlspecialchars($book['supply_count']); ?></td>
                            <td class="actions">
                                <a href='edit_books.php?id=<?php echo htmlspecialchars($book['id']); ?>'>Edit</a> |
                                <a href='delete_book.php?id=<?php echo htmlspecialchars($book['id']); ?>' onclick='return confirm("Are you sure you want to delete this book?");'>Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No books found.</p>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <h2>All Borrowed Books</h2>
            <?php if (count($borrowedBooks) > 0): ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <table>
                        <tr>
                            <th>Borrowing ID</th>
                            <th>Book Title</th>
                            <th>Borrowed By</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                        <?php foreach ($borrowedBooks as $borrowing): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($borrowing['borrowing_id']); ?></td>
                                <td><?php echo htmlspecialchars($borrowing['book_title']); ?></td>
                                <td><?php echo htmlspecialchars($borrowing['student_username']); ?></td>
                                <td><?php echo isset($borrowing['borrow_date']) ? htmlspecialchars($borrowing['borrow_date']) : 'N/A'; ?></td>
                                <td><?php echo isset($borrowing['due_date']) ? htmlspecialchars($borrowing['due_date']) : 'N/A'; ?></td>
                                <td><?php echo isset($borrowing['status']) ? htmlspecialchars($borrowing['status']) : 'N/A'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php else: ?>
                <p>No borrowed books found.</p>
            <?php endif; ?>
        </div>

        <div class="table-container">
    <h2>Recently Returned Books</h2>
    <?php if (count($returnedBooks) > 0): ?>
        <div style="max-height: 400px; overflow-y: auto;">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Book Title</th>
                    <th>Returned By</th>
                    <th>Return Date</th>
                    <th>Actions</th> <!-- New column for actions -->
                </tr>
                <?php foreach ($returnedBooks as $returned): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($returned['id']); ?></td>
                        <td><?php echo htmlspecialchars($returned['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($returned['student_username']); ?></td>
                        <td><?php echo htmlspecialchars($returned['return_date']); ?></td>
                        <td>
                            <!-- Action Links for Edit and Delete -->
                            <a href="edit_returned_book.php?id=<?php echo htmlspecialchars($returned['id']); ?>">Edit</a> |
                            <a href="delete_returned_book.php?id=<?php echo htmlspecialchars($returned['id']); ?>" 
                               onclick="return confirm('Are you sure you want to delete this returned book record?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <p>No returned books found.</p>
    <?php endif; ?>
</div>


        <a class="logout" href='logout.php'>Logout</a>
    </div>
</body>
</html>