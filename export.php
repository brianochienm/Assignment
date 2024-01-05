<?php
session_start();

require_once 'Dbconn.php';

// Ensure the 'mpdf' library is loaded
require_once 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type'])) {
    $articleId = isset($_GET['articleId']) ? $_GET['articleId'] : null;
    $exportType = $_GET['type'];

    if ($articleId && $exportType) {
        // Fetch article details
        $query = "SELECT * FROM articles WHERE articleId= ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$articleId]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($article) {
            // Handle export based on the selected type
            switch ($exportType) {
                case 'pdf':
                    exportToPDF($article);
                    break;

                case 'text':
                    exportToText($article);
                    break;

                default:
                    // Handle other export types if needed
                    break;
            }
        }
    }
}

// Function to export article to PDF using mpdf
function exportToPDF($article) {
    require_once 'vendor/autoload.php';

    $mpdf = new \Mpdf\Mpdf();

    // Add content to PDF
    $content = "Article Title: " . $article['article_title'] . "\n\n\n";
    $content .= "Content: " . $article['article_full_text'];

    $mpdf->WriteHTML($content);

    // Output PDF content
    $mpdf->Output('article_export.pdf', 'D');
    exit;
}

// Function to export article to Text
function exportToText($article) {
    // Prepare text content
    $textContent = "Article Title: " . $article['article_title'] . "\n\n";
    $textContent .= "Content: " . $article['article_full_text'];

    // Output text content or handle download as needed
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="article_export.txt"');
    echo $textContent;
    exit;
}
?>
