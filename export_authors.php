<?php
require_once 'vendor/autoload.php';
require_once 'Dbconn.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;

// Fetch all authors from the database
$query = "SELECT * FROM users WHERE UserType = 'Author'";
$result = $pdo->query($query);

// Check if there are authors
$authors = $result->rowCount() > 0 ? $result->fetchAll(PDO::FETCH_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['exportPdf'])) {
        exportToPdf($authors);
    } elseif (isset($_POST['exportExcel'])) {
        exportToExcel($authors);
    } elseif (isset($_POST['exportTxt'])) {
        exportToText($authors);
    }
}

function exportToPdf($authors) {
    // Clean (erase) the output buffer and disable output buffering
    ob_end_clean();

    require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('YourAppName');
    $pdf->SetAuthor('YourAppName');
    $pdf->SetTitle('Authors List');

    // Set header data
    $pdf->SetHeaderData('', 0, 'Authors List', '');

    // Set fonts and margins
    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 8]);
    $pdf->SetDefaultMonospacedFont('courier');
    $pdf->SetFooterMargin(10);
    $pdf->SetMargins(10, 40, 10);
    $pdf->SetAutoPageBreak(true, 40);

    // Add a page
    $pdf->AddPage();

    // Generate HTML content
    $html = '<h2>Authors List</h2><table border="1"><tr><th>Author Name</th><th>Email</th></tr>';
    foreach ($authors as $author) {
        $html .= '<tr><td>' . $author['Full_Name'] . '</td><td>' . $author['email'] . '</td></tr>';
    }
    $html .= '</table>';

    // Write HTML content to the PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Output the PDF to the browser for download
    $pdfFileName = 'authors_list.pdf';
    $pdf->Output($pdfFileName, 'D');
    exit(); // Ensure that no further output is generated
}

function exportToExcel($authors) {
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getActiveSheet()->fromArray($authors);

    $excelWriter = new Xlsx($spreadsheet);
    $excelFileName = 'authors_list.xlsx';

    // Set headers for Excel file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $excelFileName . '"');
    header('Cache-Control: max-age=0');

    // Save Excel file to output
    $excelWriter->save('php://output');
    exit(); // Ensure that no further output is generated
}

function exportToText($authors) {
    $textFileName = 'authors_list.txt';
    $file = fopen($textFileName, 'w');

    // Add headers
    fwrite($file, implode("\t", array_keys($authors[0])) . PHP_EOL);

    // Add data
    foreach ($authors as $author) {
        fwrite($file, implode("\t", $author) . PHP_EOL);
    }

    fclose($file);

    // Set headers for Text file
    header('Content-Type: plain/text');
    header('Content-Disposition: attachment;filename="' . $textFileName . '"');
    header('Cache-Control: max-age=0');

    // Read and output Text file content
    readfile($textFileName);

    // Delete the file after download
    unlink($textFileName);
    exit(); // Ensure that no further output is generated
}
?>
