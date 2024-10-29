<?php
session_start();
require 'database_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'config.php';

if (!isset($_GET['id'])) {
    echo "No user ID specified.";
    exit();
}

$userId = $_GET['id'];
$user = null;
$rowsUpdated = 0;

try {
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        
        $updateSql = "UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':name', $name, PDO::PARAM_STR);
        $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $updateStmt->bindParam(':role', $role, PDO::PARAM_STR);
        $updateStmt->bindParam(':id', $userId, PDO::PARAM_INT);
        
        $updateStmt->execute();
        $rowsUpdated = $updateStmt->rowCount();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0eafc, #cfdef3);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 15px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .success-message {
            width: 100%;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #28a745;
            color: #fff;
            border-radius: 8px;
            font-weight: 500;
            font-size: 16px;
            text-align: center;
        }
        h1 {
            text-align: center;
            font-size: 28px;
            font-weight: 600;
            color: #444;
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 500;
            color: #555;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="email"],
        select {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            transition: border 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        select:focus {
            border: 1px solid #007bff;
            outline: none;
        }
        input[type="submit"] {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .links a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($rowsUpdated > 0): ?>
            <div class="success-message">User details successfully updated!</div>
        <?php endif; ?>

        <h1>Edit User</h1>
        <form action="" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="role">Role:</label>
            <select name="role" required>
                <option value="librarian" <?php echo (isset($user['role']) && $user['role'] === 'librarian') ? 'selected' : ''; ?>>Librarian</option>
                <option value="student" <?php echo (isset($user['role']) && $user['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
            </select>

            <input type="submit" value="Update User">
        </form>

        <div class="links">
            <a href='user_management.php'>Go back</a>
            <a href='logout.php'>Logout</a>
        </div>
    </div>
</body>
</html>
