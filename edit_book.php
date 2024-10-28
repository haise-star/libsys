<?php
session_start();


require 'config.php';

// Handle form submission for adding a new book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_book'])) {
    // Retrieve form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $published_year = $_POST['published_year'];
    $supply_count = $_POST['supply_count']; 

    // nialalagay nito ang mga bagong libro na yong pinasok sa database
    try {
        $sql = "INSERT INTO books (title, author, genre, published_year, supply_count) VALUES (:title, :author, :genre, :published_year, :supply_count)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':published_year', $published_year);
        $stmt->bindParam(':supply_count', $supply_count); 

        if ($stmt->execute()) {
            $message = "<div class='success-message'>Book added successfully!</div>";
        } else {
            $message = "<div class='error-message'>Failed to add the book. Please try again.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='error-message'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fb;
            color: #343a40;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        h1 {
            margin-bottom: 10px;
            text-align: center;
            color: #28a745;
            font-size: 2.5rem;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            margin-top: 10px;
            position: relative;
        }
        label {
            margin-bottom: 5px;
            display: block;
            font-weight: bold;
            font-size: 1.1rem;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #28a745;
            outline: none;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            width: 100%;
            font-size: 1.1rem;
            margin-top: 15px;
        }
        input[type="submit"]:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            gap: 10px;
        }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.3s;
            width: 48%;
            text-align: center;
        }
        .button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .button i {
            margin-right: 5px;
        }
        .success-message {
            color: #28a745;
            text-align: center;
            margin-top: 15px;
            font-size: 1.1rem;
        }
        .error-message {
            color: #dc3545;
            text-align: center;
            margin-top: 15px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<h1>Add New Book</h1>

<form action="" method="POST">
    <label for="title">Title:</label>
    <input type="text" name="title" required>

    <label for="author">Author:</label>
    <input type="text" name="author" required>

    <label for="genre">Genre:</label>
    <input type="text" name="genre" required>

    <label for="published_year">Published Year:</label>
    <input type="number" name="published_year" required>

    <label for="supply_count">Supply Count:</label>
    <input type="number" name="supply_count" required min="0">

    <input type="submit" name="add_book" value="Add Book">

    <?php if (isset($message)) echo $message; ?>

    <div class="button-group">
        <a href='librarian_dashboard.php' class='button'><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href='logout.php' class='button'><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</form>

</body>
</html>

