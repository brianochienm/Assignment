<?php
session_start();

// Check if the user is logged in and is a Super User
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'Author') {
    header('Location: index.php');
    exit();
}

// Include the database connection
require_once 'Dbconn.php';

// Function to export users list to Excel, PDF, and text file
function exportUsersList($users) {
    // Implement the logic to export users list to Excel, PDF, and text file
    // Example: You can use a library like PHPExcel, FPDF, or PHPWord for exporting
    // Include necessary library files and write the export logic here
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Dashboard</title>
    <link rel="stylesheet" href="CSS/style.css" />
</head>
<body>
<div class="topnav"> 
    <a href="index.php">Home</a>  
    <div class="topnav-right">
            <a href="logout.php">Log out</a>
    </div>
</div>

    <h1>Welcome, Author!</h1>

    <!-- Four buttons for different functionalities -->
    <div>
        <a href="update_profile2.php"><button>Update Profile</button></a>
        <a href="manage_articles.php"><button>Manage Articles</button></a>
        <a href="view_articles2.php"><button>View Articles</button></a>
        <a href="logout.php"><button>Logout</button></a>
    </div>

</body>
</html>
