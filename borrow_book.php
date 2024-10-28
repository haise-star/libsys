<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require 'config.php';

$successMessage = '';
$errorMessage = '';

// Pagination variables
$booksPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $booksPerPage;

try {
    // Get the total number of books
    $totalBooksSql = "SELECT COUNT(*) FROM books WHERE supply_count > 0";
    $totalBooksStmt = $conn->query($totalBooksSql);
    $totalBooks = $totalBooksStmt->fetchColumn();
    $totalPages = ceil($totalBooks / $booksPerPage);

    // Fetch the books for the current page
    $sql = "SELECT * FROM books WHERE supply_count > 0 LIMIT :offset, :booksPerPage";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':booksPerPage', $booksPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = "Error: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['borrow_book'])) {
    $bookId = $_POST['book_id'];
    $userId = $_SESSION['user_id'];
    $borrowDate = date('Y-m-d H:i:s');
    $dueDate = date('Y-m-d H:i:s', strtotime('+14 days'));

    try {
        $sql = "INSERT INTO borrowings (user_id, book_id, borrow_date, due_date) VALUES (:user_id, :book_id, :borrow_date, :due_date)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
        $stmt->bindParam(':borrow_date', $borrowDate);
        $stmt->bindParam(':due_date', $dueDate);

        if ($stmt->execute()) {
            $sqlUpdate = "UPDATE books SET supply_count = supply_count - 1 WHERE id = :id";
            $updateStmt = $conn->prepare($sqlUpdate);
            $updateStmt->bindParam(':id', $bookId, PDO::PARAM_INT);
            $updateStmt->execute();

            $successMessage = "You have successfully borrowed the book!";
        } else {
            $errorMessage = "Failed to borrow the book. Please try again.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow a Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Global Styles */
        * { box-sizing: border-box; padding: 0; margin: 0; font-family: 'Poppins', sans-serif; }
        body { background-color: #f3f6f9; color: #333; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; padding: 20px; }

        /* Layout */
        .container { display: flex; gap: 20px; width: 90%; max-width: 1200px; margin-top: 90px; }
        .sidebar { position: sticky; top: 70px; flex: 1; max-width: 350px; height: calc(100vh - 110px); overflow-y: auto; background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; }
        .main { flex: 2; background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); }

        /* Headers */
        h1, h2 { color: #333; margin-bottom: 15px; font-weight: 600; }
        
        /* Top Navigation */
        .top-nav { position: fixed; top: 0; left: 0; right: 0; background-color: #ffffff; padding: 15px 20px; display: flex; justify-content: flex-start; gap: 15px; align-items: center; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); z-index: 100; }
        .top-nav a { color: #007BFF; text-decoration: none; font-weight: 500; font-size: 1em; padding: 8px 15px; border-radius: 8px; background-color: #f3f6f9; display: flex; align-items: center; gap: 8px; transition: background-color 0.3s; }
        .top-nav a i { font-size: 1.2em; }
        .top-nav a:hover { background-color: #007BFF; color: #ffffff; }

        /* Book Card */
        .book-card { background: linear-gradient(135deg, #f0f5ff, #d7e3fc); padding: 15px; margin: 10px 0; border-radius: 10px; transition: box-shadow 0.3s ease, transform 0.3s ease; cursor: pointer; }
        .book-card:hover { box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); transform: translateY(-5px); }
        .book-title { font-size: 1.1em; font-weight: bold; color: #0056b3; }
        .book-author { color: #555; font-size: 0.9em; }

        /* Pagination */
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 20px; }
        .pagination a { padding: 8px 14px; color: #007BFF; background-color: #e9ecef; border-radius: 6px; text-decoration: none; transition: background-color 0.3s; }
        .pagination a:hover, .pagination a.active { background-color: #007BFF; color: #fff; }

        /* Button Styles */
        .btn { padding: 12px 25px; background: linear-gradient(135deg, #007BFF, #0056b3); color: white; border: none; border-radius: 8px; font-size: 1em; cursor: pointer; transition: background 0.3s ease; width: 100%; }
        .btn:hover { background: linear-gradient(135deg, #0056b3, #004085); }
        .btn:disabled { background-color: #ccc; cursor: not-allowed; }

        /* Book Details Section */
        #main-book-details {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        #main-book-title {
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        #main-book-author, #main-book-genre, #main-book-id, #main-book-copies {
            display: flex;
            align-items: center;
            font-size: 1.1em;
            color: #555;
            margin: 5px 0;
        }
        #main-book-author span, #main-book-genre span, #main-book-id span, #main-book-copies span {
            font-size: 1.3em;
            margin-right: 8px;
            color: #007BFF;
        }
    </style>
    <script>
        function selectBook(bookTitle, bookAuthor, bookId, availableCopies, bookGenre) {
            document.getElementById('main-book-title').textContent = bookTitle;
            document.getElementById('main-book-author').innerHTML = `<span>👤</span> ${bookAuthor}`;
            document.getElementById('main-book-genre').innerHTML = `<span>📖</span> ${bookGenre}`;
            document.getElementById('main-book-id').innerHTML = `<span>🆔</span> ${bookId}`;
            document.getElementById('main-book-copies').innerHTML = `<span>📚</span> ${availableCopies}`;
            document.getElementById('modal-book-id').value = bookId;
            const borrowBtn = document.getElementById('borrow-btn');
            borrowBtn.style.display = 'block';
            borrowBtn.disabled = availableCopies <= 0;
        }
    </script>
</head>
<body>
    <div class="top-nav">
        <a href="student_dashboard.php">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <div class="container">
        <div class="sidebar">
            <h1>Borrow a Book</h1>
            <?php if ($successMessage) echo $successMessage; ?>
            <?php if ($errorMessage) echo $errorMessage; ?>
            <input type="text" id="bookSearch" onkeyup="filterBooks()" placeholder="Search for books..." class="book-search">
            <div class="books-grid">
                <?php foreach ($books as $book): ?>
                    <div class="book-card" onclick="selectBook('<?php echo htmlspecialchars($book['title']); ?>', '<?php echo htmlspecialchars($book['author']); ?>', '<?php echo $book['id']; ?>', '<?php echo $book['supply_count']; ?>', '<?php echo htmlspecialchars($book['genre']); ?>')">
                        <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                        <div class="book-author">by <?php echo htmlspecialchars($book['author']); ?></div>
                        <div class="book-copies">Available: <?php echo htmlspecialchars($book['supply_count']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?php echo $currentPage - 1; ?>">Previous</a>
                <?php endif; ?>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?php echo $currentPage + 1; ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="main">
            <h2>Book Details</h2>
            <div id="main-book-details">
                <div id="main-book-title">Select a book to see details</div>
                <div id="main-book-author"><span>👤</span><span>Author:</span> Not selected</div>
                <div id="main-book-genre"><span>📖</span><span>Genre:</span> Not selected</div>
                <div id="main-book-id"><span>🆔</span><span>ID:</span> Not selected</div>
                <div id="main-book-copies"><span>📚</span><span>Available Copies:</span> Not selected</div>
                <form method="POST" style="width: 100%;">
                    <input type="hidden" name="book_id" id="modal-book-id">
                    <button type="submit" name="borrow_book" id="borrow-btn" class="btn" style="display: none; width: 100%;">Borrow</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

