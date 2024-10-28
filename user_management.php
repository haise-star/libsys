<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include the database connection
require 'config.php';

// Handle pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchQuery = $search ? "WHERE name LIKE :search OR email LIKE :search OR username LIKE :search" : "";

// Fetch total user count for pagination
try {
    $countQuery = "SELECT COUNT(*) FROM users $searchQuery";
    $countStmt = $conn->prepare($countQuery);
    if ($search) {
        $countStmt->bindValue(':search', '%' . $search . '%');
    }
    $countStmt->execute();
    $total = $countStmt->fetchColumn();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch users with pagination
$users = [];
try {
    $sql = "SELECT * FROM users $searchQuery LIMIT :start, :limit";
    $stmt = $conn->prepare($sql);
    if ($search) {
        $stmt->bindValue(':search', '%' . $search . '%');
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Calculate total pages
$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrQkTy43WKh6zqf1bQ+1KAQULvl8G+1hj9mIlkpLkWRhv69I1zp6FgEw5n2lz4W3NMRa67HhhPv2d0pCJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* All original styles from your code remain unchanged */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #f3f4f7;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: row;
            gap: 20px;
            width: 95%;
            max-width: 1200px;
            margin-top: 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            background-color: #343a40;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .nav-links a i {
            font-size: 18px;
        }

        .nav-links a:hover {
            background-color: #495057;
        }

        .form-section {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: 300px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 14px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-section .btn {
            background-color: #28a745;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .form-section .btn:hover {
            background-color: #218838;
        }

        .table-section {
            flex: 2;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search-bar input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .search-bar button {
            background-color: #007bff;
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-bar button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        .pagination {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .pagination a {
            padding: 8px 12px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .pagination a:hover {
            background-color: #0056b3;
        }

        .pagination a.disabled {
            background-color: #ddd;
            color: #888;
            pointer-events: none;
        }

        .action-links {
            display: flex;
            gap: 10px;
        }

        .edit-link, .delete-link {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .delete-link:hover {
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .form-section,
            .table-section {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Form Section -->
        <div class="form-section">
            <h2>Add User</h2>
            <form action="process_user.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" name="name" placeholder="Enter full name" required>
                </div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" placeholder="Enter username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" placeholder="Enter email address" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" placeholder="Enter password" required>
                </div>

                <div class="form-group">
                    <label for="role">Role:</label>
                    <select name="role" required>
                        <option value="admin">Admin</option>
                        <option value="librarian">Librarian</option>
                        <option value="student">Student</option>
                    </select>
                </div>

                <input type="submit" value="Add User" class="btn">
            </form>

            <div class="nav-links">
                <a href="admin_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="table-section">
            <h2>Existing Users</h2>
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search by name, email, or username" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" onclick="window.location.href='?search=' + document.getElementsByName('search')[0].value">Search</button>
            </div>
            <table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td class="action-links">
                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="edit-link">
                    <i class="fas fa-edit"></i> <!-- Edit icon -->
                </a>
                <a href="process_user.php?action=delete&id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="delete-link">
                    <i class="fas fa-trash-alt"></i> <!-- Delete icon -->
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Previous</a>
                <?php else: ?>
                    <a class="disabled">Previous</a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Next</a>
                <?php else: ?>
                    <a class="disabled">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
