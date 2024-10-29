<?php
session_start();
require 'config.php';

if (!isset($_GET['id'])) {
    echo "No book ID specified.";
    exit();
}

$bookId = $_GET['id'];
$book = null;
$successMessage = '';

try {
    $sql = "SELECT * FROM books WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $bookId, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if (!$book) {
    echo "Book not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $published_year = $_POST['published_year'];
    $supply_count = $_POST['supply_count'];

    try {
        $sql = "UPDATE books SET title = :title, author = :author, genre = :genre, published_year = :published_year, supply_count = :supply_count WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':published_year', $published_year);
        $stmt->bindParam(':supply_count', $supply_count);
        $stmt->bindParam(':id', $bookId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $successMessage = "Book updated successfully!";
        } else {
            echo "<p>Failed to update the book. Please try again.</p>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            transition: box-shadow 0.3s;
        }
        .container:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        h1 {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
        }
        .success-message {
            background-color: #e1f3e7;
            color: #28a745;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 0.95em;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: 700;
            color: #495057;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"] {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            max-width: 100%;
            box-sizing: border-box;
            font-size: 0.9em;
            color: #333;
            background-color: #f8f9fa;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #5cb85c;
            box-shadow: 0 4px 8px rgba(92, 184, 92, 0.2);
            outline: none;
        }
        .btn {
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9em;
            font-weight: bold;
            text-decoration: none;
            color: #fff;
            text-align: center;
            display: inline-block;
            width: calc(33.33% - 6px);
            box-sizing: border-box;
            transition: background 0.3s, transform 0.2s;
        }
        .btn-update {
            background-color: #28a745;
        }
        .btn-update:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .btn-dashboard {
            background-color: #007bff;
        }
        .btn-dashboard:hover {
            background-color: #0069d9;
            transform: translateY(-2px);
        }
        .btn-logout {
            background-color: #dc3545;
        }
        .btn-logout:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .button-container {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Book</h1>
        
        <?php if ($successMessage): ?>
            <div class="success-message">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="title">Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>

            <label for="author">Author:</label>
            <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>

            <label for="genre">Genre:</label>
            <input type="text" name="genre" value="<?php echo htmlspecialchars($book['genre']); ?>" required>

            <label for="published_year">Published Year:</label>
            <input type="number" name="published_year" value="<?php echo htmlspecialchars($book['published_year']); ?>" required>

            <label for="supply_count">Supply Count:</label>
            <input type="number" name="supply_count" value="<?php echo htmlspecialchars($book['supply_count']); ?>" required min="0">
            
            <div class="button-container">
                <input type="submit" name="edit_book" value="Update" class="btn btn-update">
                <a href="admin_dashboard.php" class="btn btn-dashboard">Dashboard</a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </form>
    </div>
</body>
</html>

