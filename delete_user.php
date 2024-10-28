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
    
    //kakayahan neto makapag bura ng user
    function deleteUser($conn, $userId) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->rowCount(); // Returns the number of affected rows
    }

   
    $userId = 1; // Assuming you want to delete user with ID 1

    $affectedRows = deleteUser($conn, $userId);
    if ($affectedRows > 0) {
        echo "$affectedRows row(s) deleted.";
    } else {
        echo "No user found with that ID.";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
