<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require 'config.php';

try {
    // Retrieve borrowed books that have not been returned
    $sql = "SELECT b.id, bo.title, bo.id AS book_id FROM borrowings b JOIN books bo ON b.book_id = bo.id WHERE b.user_id = :user_id AND b.returned_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_book'])) {
    $borrowingId = $_POST['borrowing_id'];
    try {
        // Fetch the book and user details
        $sqlFetchDetails = "SELECT bo.title, bo.id AS book_id FROM books bo JOIN borrowings b ON b.book_id = bo.id WHERE b.id = :id";
        $fetchStmt = $conn->prepare($sqlFetchDetails);
        $fetchStmt->bindParam(':id', $borrowingId, PDO::PARAM_INT);
        $fetchStmt->execute();
        $book = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        if ($book) {
            // Insert the return record into the return_books table
            $sqlInsertReturn = "INSERT INTO return_books (book_id, user_id, return_date) VALUES (:book_id, :user_id, NOW())";
            $insertStmt = $conn->prepare($sqlInsertReturn);
            $insertStmt->bindParam(':book_id', $book['book_id'], PDO::PARAM_INT);
            $insertStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $insertStmt->execute();

            // Update the supply count in the books table
            $sqlUpdate = "UPDATE books SET supply_count = supply_count + 1 WHERE id = :book_id";
            $updateStmt = $conn->prepare($sqlUpdate);
            $updateStmt->bindParam(':book_id', $book['book_id'], PDO::PARAM_INT);
            $updateStmt->execute();

            // Delete the entry from the borrowings table
            $sqlDeleteBorrowing = "DELETE FROM borrowings WHERE id = :id";
            $deleteStmt = $conn->prepare($sqlDeleteBorrowing);
            $deleteStmt->bindParam(':id', $borrowingId, PDO::PARAM_INT);
            $deleteStmt->execute();

            // Success message
            $successMessage = "You have successfully returned the book: <strong>" . htmlspecialchars($book['title']) . "</strong>.";
        } else {
            echo "<p>Book not found.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return a Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f4f8;
            color: #2c3e50;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 700px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            transform: translateY(15px);
            transition: all 0.3s ease-in-out;
            text-align: center;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 24px;
            text-align: center;
        }

        .success-message {
            margin: 20px auto;
            padding: 15px;
            font-size: 16px;
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            animation: fadeIn 0.5s ease;
            max-width: 90%;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 25px;
        }

        label {
            font-size: 16px;
            color: #5a5a5a;
            font-weight: 500;
            text-align: left;
            display: block;
        }

        select, input[type="submit"] {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border: 1px solid #dcdfe3;
            border-radius: 8px;
            transition: border-color 0.3s ease;
            background-color: #fafafa;
        }

        select:focus, input[type="submit"]:focus {
            border-color: #3498db;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #2e86c1;
        }

        .links {
            margin-top: 30px;
            display: flex;
            justify-content: space-around;
        }

        .links a {
            font-size: 15px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .links a:hover {
            color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Return a Book</h1>

        <!-- Success Message displayed directly below the header -->
        <?php if ($successMessage): ?>
            <div class="success-message">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="borrowing_id">Select Book to Return:</label>
            <select name="borrowing_id" required>
                <option value="">Choose a borrowed book</option>
                <?php foreach ($borrowedBooks as $borrowed): ?>
                    <option value="<?php echo htmlspecialchars($borrowed['id']); ?>">
                        <?php echo htmlspecialchars($borrowed['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="return_book" value="Return Book">
        </form>

        <div class="links">
            <a href="student_dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>

