<?php
session_start();

if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'Administrator') {
    header('Location: index.php');
    exit();
}

require_once 'Dbconn.php';

// Fetch and display the last 6 posted articles with details
$query = "SELECT * FROM articles ORDER BY article_created_date DESC LIMIT 6";

try {
    $stmt = $pdo->query($query);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching articles: " . $e->getMessage();
}

// require 'viewArticlesView.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Articles</title>
    <link rel="stylesheet" href="CSS/style.css" />
</head>
<body>
    <div class="topnav"> 
        <a href="index.php">Home</a>  
        <div class="topnav-right">
            <a href="admin1.php">Back</a>
            <a href="logout.php">Log out</a>
        </div>
    </div>

    <h2>View Articles</h2>
    <!-- Display articles -->
    <?php if (!empty($articles)) : ?>
        <ul>
            <?php foreach ($articles as $article) : ?>
                <li>
                    <h3><?php echo $article['article_title']; ?></h3>
                    <p>Author: <?php echo $article['authorId']; ?></p>
                    <p>Date: <?php echo $article['article_created_date']; ?></p>
                    <p><?php echo $article['article_full_text']; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No articles found.</p>
    <?php endif; ?>
</body>
</html>
