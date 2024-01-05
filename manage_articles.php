<?php
session_start();

require_once 'Dbconn.php';

// Check if the user is logged in and is an Author
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'Author') {
    header('Location: index.php');
    exit();
}

$errors = [];
$successMessage = '';

// Handle Add Article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addArticle'])) {
    $articleTitle = filter_input(INPUT_POST, 'articleTitle', FILTER_SANITIZE_STRING);
    $articleContent = filter_input(INPUT_POST, 'articleContent', FILTER_SANITIZE_STRING);

    // Validate input
    if (empty($articleTitle) || empty($articleContent)) {
        $errors[] = 'Article title and content are required';
    }

    // If no errors, insert the new article
    if (empty($errors)) {
        try {
            $insertQuery = "INSERT INTO articles (authorId, article_title, article_full_text, article_created_date) VALUES (?, ?, ?, NOW())";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([$_SESSION['userId'], $articleTitle, $articleContent]);
            $insertStmt->closeCursor();

            $successMessage = 'Article added successfully';
        } catch (PDOException $e) {
            $errors[] = 'Error adding article: ' . $e->getMessage();
        }
    }
}

// Handle Update or Delete Article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateDeleteAction'])) {
    $selectedArticleId = $_POST['selectedArticleId'];

    // Validate article ID
    if (!is_numeric($selectedArticleId)) {
        $errors[] = 'Invalid article ID';
    }

    if ($_POST['updateDeleteAction'] === 'update') {
        // Fetch the selected article for the update form
        $query = "SELECT * FROM articles WHERE articleId = ? AND authorId = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$selectedArticleId, $_SESSION['userId']]);
        $selectedArticle = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no article is found, display an error
        if (!$selectedArticle) {
            $errors[] = 'Article not found for updating';
        }
    } elseif ($_POST['updateDeleteAction'] === 'delete') {
        // If delete action, then delete the article
        try {
            $deleteQuery = "DELETE FROM articles WHERE articleId = ? AND authorId = ?";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->execute([$selectedArticleId, $_SESSION['userId']]);
            $deleteStmt->closeCursor();

            $successMessage = 'Article deleted successfully';
        } catch (PDOException $e) {
            $errors[] = 'Error deleting article: ' . $e->getMessage();
        }
    }
}

// Handle Update Article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateArticle'])) {
    $updatedArticleTitle = filter_input(INPUT_POST, 'updatedArticleTitle', FILTER_SANITIZE_STRING);
    $updatedArticleContent = filter_input(INPUT_POST, 'updatedArticleContent', FILTER_SANITIZE_STRING);
    $selectedArticleIdUpdate = $_POST['selectedArticleIdUpdate'];

    // Validate input
    if (empty($updatedArticleTitle) || empty($updatedArticleContent) || !is_numeric($selectedArticleIdUpdate)) {
        $errors[] = 'Invalid input for updating article';
    }

    // If no errors, update the article
    if (empty($errors)) {
        try {
            $updateQuery = "UPDATE articles SET article_title = ?, article_full_text = ? WHERE articleId = ? AND authorId = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$updatedArticleTitle, $updatedArticleContent, $selectedArticleIdUpdate, $_SESSION['userId']]);
            $updateStmt->closeCursor();

            $successMessage = 'Article updated successfully';
        } catch (PDOException $e) {
            $errors[] = 'Error updating article: ' . $e->getMessage();
        }
    }
}

// Fetch all articles for display
$query = "SELECT * FROM articles WHERE authorId = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['userId']]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Articles</title>
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

<h2>Manage Articles</h2>

<?php
// Display success message or errors
if (!empty($successMessage)) {
    echo '<p class="success-message">' . $successMessage . '</p>';
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo '<p class="error-message">' . $error . '</p>';
    }
}
?>

<!-- Add Article Form -->
<form action="manage_articles.php" method="post">
    <label for="articleTitle">Article Title:</label>
    <input type="text" id="articleTitle" name="articleTitle" required>

    <label for="articleContent">Article Content:</label>
    <textarea id="articleContent" name="articleContent" rows="4" required></textarea>

    <button type="submit" name="addArticle">Add New Article</button>
</form>

<!-- Display articles list -->
<h3>Articles List</h3>
<?php if (!empty($articles)) : ?>
    <ul>
        <?php foreach ($articles as $article) : ?>
            <li>
                <strong><?= $article['article_title']; ?></strong>
                <p><?= $article['article_full_text']; ?></p>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Update and Delete Article Form -->
    <form action="manage_articles.php" method="post">
        <label for="selectedArticleId">Select Article:</label>
        <select id="selectedArticleId" name="selectedArticleId" required>
            <?php foreach ($articles as $article) : ?>
                <option value="<?= $article['articleId']; ?>"><?= $article['article_title']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="updateDeleteAction" value="update">Update Article</button>
        <button type="submit" name="updateDeleteAction" value="delete">Delete Article</button>
    </form>

    <!-- Update Article Form -->
    <?php if (isset($selectedArticle)) : ?>
        <form action="manage_articles.php" method="post" id="updateForm">
            <label for="updatedArticleTitle">Updated Article Title:</label>
            <input type="text" id="updatedArticleTitle" name="updatedArticleTitle" value="<?= $selectedArticle['article_title']; ?>" required>

            <label for="updatedArticleContent">Updated Article Content:</label>
            <textarea id="updatedArticleContent" name="updatedArticleContent" rows="4" required><?= $selectedArticle['article_full_text']; ?></textarea>

            <input type="hidden" name="selectedArticleIdUpdate" value="<?= $selectedArticle['articleId']; ?>">
            <button type="submit" name="updateArticle">Update Article</button>
        </form>
    <?php endif; ?>

<?php else : ?>
    <p>No articles found.</p>
<?php endif; ?>

</body>
</html>
