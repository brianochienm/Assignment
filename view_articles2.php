<?php
session_start();

require_once 'Dbconn.php';

// Check if the user is logged in and is an Author
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'Author') {
    header('Location: index.php');
    exit();
}

// Fetch and display the last 6 posted articles
$query = "SELECT * FROM articles WHERE authorId = ? ORDER BY article_created_date DESC LIMIT 6";
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['userId']]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include the HTML for displaying articles and export links
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
            <a href="author1.php">Back</a>
            <a href="logout.php">Log out</a>
        </div>
    </div>

    <h2>View Articles</h2>

    <!-- Display articles -->
    <?php if (!empty($articles)) : ?>
        <ul>
            <?php foreach ($articles as $article) : ?>
                <li>
                    <strong><?= $article['article_title']; ?></strong>
                    <p>Created Date: <?= $article['article_created_date']; ?></p>
                    <p><?= $article['article_full_text']; ?></p>

                    
                    <!-- Export links for each article -->
                    <div>
                        <a href="export.php?type=pdf&articleId=<?= $article['articleId']; ?>">Export to PDF</a>
                        <a href="export.php?type=text&articleId=<?= $article['articleId']; ?>">Export to Text</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No articles found.</p>
    <?php endif; ?>

</body>
</html>



