<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Gather and validate input
        $fullname = trim($_POST['fullname']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        if (empty($fullname) || empty($username) || empty($email) || empty($password) || empty($role)) {
            echo "All fields are required. <a href='user_management.php'>Go back</a>";
            exit();
        }

        // Check if username or email already exists
        $duplicateCheckSql = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
        $checkStmt = $conn->prepare($duplicateCheckSql);
        $checkStmt->bindParam(':username', $username);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() > 0) {
            echo "Username or email already exists. <a href='user_management.php'>Go back</a>";
            exit();
        }

        // Hash the password and insert user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (fullname, username, email, password, role) VALUES (:fullname, :username, :email, :password, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        echo "User added successfully! <a href='user_management.php'>Go back</a>";

    } elseif ($action === 'delete' && isset($_GET['id'])) {
        // Delete user
        $userId = $_GET['id'];
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        echo "User deleted successfully! <a href='user_management.php'>Go back</a>";

    } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Edit user
        $userId = $_POST['id'];
        $fullname = trim($_POST['fullname']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];

        if (empty($fullname) || empty($username) || empty($email) || empty($role)) {
            echo "All fields are required. <a href='user_management.php'>Go back</a>";
            exit();
        }

        // Check for duplicate username or email, excluding current user
        $duplicateCheckSql = "SELECT COUNT(*) FROM users WHERE (username = :username OR email = :email) AND id != :id";
        $checkStmt = $conn->prepare($duplicateCheckSql);
        $checkStmt->bindParam(':username', $username);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() > 0) {
            echo "Username or email already exists. <a href='user_management.php'>Go back</a>";
            exit();
        }

        // Update user details
        $sql = "UPDATE users SET fullname = :fullname, username = :username, email = :email, role = :role WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        echo "User updated successfully! <a href='user_management.php'>Go back</a>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
