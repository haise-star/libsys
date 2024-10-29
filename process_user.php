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
        // Gather input
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';

        // Validate inputs
        if (!validateFields($name, $username, $email, $password, $role)) {
            exit();
        }

        // Check for duplicate username or email
        if (isDuplicateUser($conn, $username, $email)) {
            echo "Username or email already exists. <a href='user_management.php'>Go back</a>";
            exit();
        }

        // Hash password and insert user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, username, email, password, role) VALUES (:name, :username, :email, :password, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role
        ]);

        echo "User added successfully! <a href='user_management.php'>Go back</a>";
        
    } elseif ($action === 'delete' && isset($_GET['id'])) {
        $userId = $_GET['id'];
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $userId]);

        echo "User deleted successfully! <a href='user_management.php'>Go back</a>";
        
    } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? '';

        // Validate inputs
        if (!validateFields($name, $username, $email, null, $role)) {
            exit();
        }

        // Check for duplicate username or email (excluding current user)
        if (isDuplicateUser($conn, $username, $email, $userId)) {
            echo "Username or email already exists. <a href='user_management.php'>Go back</a>";
            exit();
        }

        // Update user details
        $sql = "UPDATE users SET name = :name, username = :username, email = :email, role = :role WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':username' => $username,
            ':email' => $email,
            ':role' => $role,
            ':id' => $userId
        ]);

        echo "User updated successfully! <a href='user_management.php'>Go back</a>";
    } else {
        echo "Invalid action. <a href='user_management.php'>Go back</a>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Validate required fields
function validateFields($name, $username, $email, $password = null, $role) {
    if (empty($name) || empty($username) || empty($email) || empty($role) || ($password !== null && empty($password))) {
        echo "All fields are required. <a href='user_management.php'>Go back</a>";
        return false;
    }
    return true;
}

// Check if user exists with same username or email
function isDuplicateUser($conn, $username, $email, $excludeId = null) {
    $sql = "SELECT COUNT(*) FROM users WHERE (username = :username OR email = :email)";
    if ($excludeId) {
        $sql .= " AND id != :id";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    if ($excludeId) {
        $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}
?>
