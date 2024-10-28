<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'config.php';

$notification = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $published_year = $_POST['published_year'];
    $supply_count = $_POST['supply_count'];

    try {
        $sql = "INSERT INTO books (title, author, genre, published_year, supply_count) VALUES (:title, :author, :genre, :published_year, :supply_count)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':published_year', $published_year);
        $stmt->bindParam(':supply_count', $supply_count);

        if ($stmt->execute()) {
            $notification = "<div class='notification success'>Book added successfully! <a href='admin_dashboard.php'>Go back to dashboard</a> <span class='close'>&times;</span></div>";
        } else {
            $notification = "<div class='notification error'>Failed to add the book. Please try again. <span class='close'>&times;</span></div>";
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
    <title>Add Book</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e0eafc, #cfdef3);
            color: #333;
        }

        .container {
            max-width: 600px;
            width: 100%;
            padding: 2rem;
            background-color: #fff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        h1 {
            font-size: 1.8em;
            color: #333;
            text-align: center;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #ddd;
            padding-bottom: 0.5rem;
        }

        form {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            color: #555;
            margin-bottom: 0.3rem;
        }

        input[type="text"],
        input[type="number"] {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #4CAF50;
        }

        .button-group {
            display: flex;
            justify-content: center;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .notification {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
            font-size: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .success {
            background-color: #e6f9e8;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        .error {
            background-color: #fdecea;
            color: #d32f2f;
            border-left: 4px solid #d32f2f;
        }

        .close {
            cursor: pointer;
            font-size: 1.2rem;
            color: #888;
            margin-left: 1rem;
        }

        .close:hover {
            color: #555;
        }

        .links {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
            color: #333;
        }

        .links a {
            color: #4CAF50;
            text-decoration: none;
            margin: 0 0.5rem;
        }

        .links a:hover {
            text-decoration: underline;
        }

        @media (min-width: 600px) {
            form {
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }

            .form-group label {
                margin-top: 1rem;
            }

            .button-group {
                grid-column: 1 / -1;
                justify-content: center;
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const closeButtons = document.querySelectorAll(".notification .close");
            closeButtons.forEach(button => {
                button.addEventListener("click", () => {
                    button.parentElement.style.display = "none";
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Add Book</h1>
        <?php echo $notification; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" name="author" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre:</label>
                <input type="text" name="genre" required>
            </div>
            <div class="form-group">
                <label for="published_year">Published Year:</label>
                <input type="number" name="published_year" required>
            </div>
            <div class="form-group">
                <label for="supply_count">Supply Count:</label>
                <input type="number" name="supply_count" required min="0">
            </div>
            <div class="button-group">
                <input type="submit" name="add_book" value="Add Book">
            </div>
        </form>
        <div class="links">
            <a href='admin_dashboard.php'>Go back to dashboard</a> |
            <a href='logout.php'>Logout</a>
        </div>
    </div>
</body>
</html>
