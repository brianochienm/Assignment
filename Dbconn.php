<?php

require_once "constants.php";

try {
    $pdo = new PDO("mysql:host=$Host_Name;dbname=$Database_Name", $Database_User, $Password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Return the PDO instance
return $pdo;
?>
