<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Fetch all books
try {
    $sql = "SELECT * FROM books";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch all users for account management
try {
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Fonts and Colors */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f7fc;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        /* Navbar */
        nav {
            background-color: #4a90e2;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            max-width: 1200px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            margin: 0 5px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-weight: 500;
        }
        nav a:hover {
            background-color: #357ABD;
        }

        /* Container */
        .container {
            width: 100%;
            max-width: 1200px;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        
        /* Headings */
        h1 {
            font-size: 2.5em;
            color: #4a90e2;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 600;
        }
        h2 {
            font-size: 1.8em;
            margin: 20px 0 15px;
            color: #4a90e2;
            font-weight: 500;
            border-bottom: 2px solid #4a90e2;
            display: inline-block;
        }
        p {
            font-size: 1.1em;
            color: #666;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Table */
        .table-container {
            max-height: 300px;
            overflow-y: auto;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 1em;
        }
        th {
            background-color: #4a90e2;
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        td {
            color: #555;
        }

        /* Delete Button */
        .delete-button {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
        }
        .delete-button:hover {
            color: #c0392b;
            text-decoration: underline;
        }

        /* Table Scrollbar */
        .table-container::-webkit-scrollbar {
            width: 8px;
        }
        .table-container::-webkit-scrollbar-thumb {
            background: #4a90e2;
            border-radius: 8px;
        }
        .table-container::-webkit-scrollbar-thumb:hover {
            background: #357ABD;
        }

        /* Responsive */
        @media (max-width: 768px) {
            h1 { font-size: 2em; }
            h2 { font-size: 1.5em; }
            nav, .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<nav>
    <a href="add_book.php"><i class="fas fa-plus"></i> Add New Book</a>
    <a href="user_management.php"><i class="fas fa-users"></i> Manage Users</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>

<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>

    <h2>All Books</h2>

    <?php
    if (count($books) > 0) {
        echo "<div class='table-container'>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Genre</th><th>Published Year</th><th>Supply Count</th><th>Action</th></tr>";
        foreach ($books as $book) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($book['id']) . "</td>";
            echo "<td>" . htmlspecialchars($book['title']) . "</td>";
            echo "<td>" . htmlspecialchars($book['author']) . "</td>";
            echo "<td>" . htmlspecialchars($book['genre']) . "</td>";
            echo "<td>" . htmlspecialchars($book['published_year']) . "</td>";
            echo "<td>" . htmlspecialchars($book['supply_count']) . "</td>";
            echo "<td><a href='delete_book.php?id=" . urlencode($book['id']) . "' onclick='return confirm(\"Are you sure you want to delete this book?\");' class='delete-button'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>No books found.</p>";
    }

    echo "<h2>User Account Management</h2>";

    if (count($users) > 0) {
        echo "<h3>All Users</h3>";
        echo "<div class='table-container'>";
        echo "<table>";
        echo "<tr><th>User ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>No users found.</p>";
    }
    ?>
</div>

</body>
</html>
