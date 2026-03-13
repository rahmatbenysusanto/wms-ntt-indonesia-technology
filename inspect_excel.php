<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = __DIR__ . '/STORAGE SPARE.xlsx';

$spreadsheet = IOFactory::load($file);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();

echo json_encode(array_slice($rows, 0, 10), JSON_PRETTY_PRINT);
