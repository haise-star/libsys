<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "test2"; 

try {
    // Create connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Example: Edit user function
    function editUser($conn, $userId, $newName) {
        $stmt = $conn->prepare("UPDATE users SET name = :name WHERE id = :id");
        $stmt->bindParam(':name', $newName);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->rowCount(); // Returns the number of affected rows
    }

    // Example usage
    $userId = 1; // Assuming you're editing user with ID 1
    $newName = "New User Name"; // The new name for the user

    $affectedRows = editUser($conn, $userId, $newName);
    echo "$affectedRows rows updated.";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
