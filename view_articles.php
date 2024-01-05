<?php
session_start();

require_once 'Dbconn.php';

// Check if the user is logged in and is a Super User
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'Super_User') {
    header('Location: index.php');
    exit();
}

try {
    // Fetch the last 6 posted articles in descending order by article_created_date
    $query = "SELECT * FROM articles ORDER BY article_created_date DESC LIMIT 6";
    $result = $pdo->query($query);

    // Check if there are articles
    if ($result->rowCount() > 0) {
        $articles = $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $articles = [];
    }
} catch (PDOException $e) {
    echo "Error fetching articles: " . $e->getMessage();
}

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
            <a href="superuser1.php">Back</a>
            <a href="logout.php">Log out</a>
        </div>
    </div>

    <h1>View Articles</h1>

    <?php if (!empty($articles)) : ?>
        <ul>
            <?php foreach ($articles as $article) : ?>
                <li>
                    <strong><?php echo $article['article_title']; ?></strong><br>
                    <em><?php echo $article['article_created_date']; ?></em><br>
                    <?php echo $article['article_full_text']; ?>
                    <br><br>
                    <hr>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No articles available.</p>
    <?php endif; ?>

</body>
</html>

