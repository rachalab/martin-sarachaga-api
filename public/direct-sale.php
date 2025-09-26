<?php
require_once '../src/BatchService.php'; // Include the BatchService class
header('Content-Type: application/json');

function utf8ize($mixed) {
    if (is_array($mixed)) {
        return array_map('utf8ize', $mixed);
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
    }
    return $mixed;
}

$batchService = new BatchService();


$venta = $batchService->getDirectSaleBatches();

$data = utf8ize($venta);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);