<?php
require_once 'vendor/autoload.php';
require_once 'Dbconn.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch all users from the database
$query = "SELECT * FROM users";
$result = $pdo->query($query);

// Check if there are users
$users = $result->rowCount() > 0 ? $result->fetchAll(PDO::FETCH_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['exportPdf'])) {
        exportToPdf($users);
    } elseif (isset($_POST['exportExcel'])) {
        exportToExcel($users);
    } elseif (isset($_POST['exportTxt'])) {
        exportToText($users);
    }
}

function exportToPdf($users) {
    $pdf = new \FPDF();
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('Arial', 'B', 12);

    // Add headers
    foreach (array_keys($users[0]) as $header) {
        $pdf->Cell(40, 10, $header, 1);
    }
    $pdf->Ln();

    // Add data
    foreach ($users as $user) {
        foreach ($user as $value) {
            $pdf->Cell(40, 10, $value, 1);
        }
        $pdf->Ln();
    }

    // Save to PDF file
    $pdfFileName = 'users_list.pdf';
    $pdf->Output($pdfFileName, 'D'); // Download the file
}

function exportToExcel($users) {
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()
        ->setTitle('Users List')
        ->setCreator('Your Application Name')
        ->setDescription('List of Users');

    $sheet = $spreadsheet->getActiveSheet();
    $headers = array_keys($users[0]);
    $sheet->fromArray([$headers], null, 'A1');

    $rowData = [];
    foreach ($users as $user) {
        $rowData[] = array_values($user);
    }
    $sheet->fromArray($rowData, null, 'A2');

    $writer = new Xlsx($spreadsheet);
    $excelFileName = 'users_list.xlsx';
    $writer->save($excelFileName);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $excelFileName . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}

function exportToText($users) {
    $textFileName = 'users_list.txt';
    $file = fopen($textFileName, 'w');

    // Add headers
    fwrite($file, implode("\t", array_keys($users[0])) . PHP_EOL);

    // Add data
    foreach ($users as $user) {
        fwrite($file, implode("\t", $user) . PHP_EOL);
    }

    fclose($file);

    header('Content-Type: plain/text');
    header('Content-Disposition: attachment;filename="' . $textFileName . '"');
    header('Cache-Control: max-age=0');
    readfile($textFileName);
    unlink($textFileName); // Delete the file after download
}
?>
