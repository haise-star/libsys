<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// nireredirect ka base sa mga role ng user
switch ($_SESSION['role']) {
    case 'admin':
        header("Location: admin_dashboard.php");
        break;
    case 'librarian':
        header("Location: librarian_dashboard.php");
        break;
    case 'student':
        header("Location: student_dashboard.php");
        break;
    default:
        echo "Invalid user role.";
        exit();
}
?>
