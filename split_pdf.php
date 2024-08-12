<?php
require 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf'])) {
    $fileTmpPath = $_FILES['pdf']['tmp_name'];
    $fileName = $_FILES['pdf']['name'];
    $fileSize = $_FILES['pdf']['size'];
    $fileType = $_FILES['pdf']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    if ($fileExtension == 'pdf') {
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fileTmpPath);

        $splitSize = 200;
        $part = 1;

        for ($i = 1; $i <= $pageCount; $i += $splitSize) {
            $newPdf = new Fpdi();
            for ($j = $i; $j < $i + $splitSize && $j <= $pageCount; $j++) {
                $newPdf->AddPage();
                $newPdf->setSourceFile($fileTmpPath);
                $tplIdx = $newPdf->importPage($j);
                $newPdf->useTemplate($tplIdx);
            }
            $newFileName = 'split_part_' . $part . '.pdf';
            $newPdf->Output($newFileName, 'D');
            $part++;
        }
    } else {
        echo "Please upload a valid PDF file.";
    }
} else {
    echo "No file uploaded.";
}
?>