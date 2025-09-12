<?php
require_once 'querys.php'; // Incluye las funciones

header('Content-Type: application/json');

$loteId = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

if (!$loteId) {
    echo json_encode(['error' => 'Lote no encontrado'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$lote["lote"] = traerLote($loteId);

$subastaId = !empty($lote["lote"]["subasta"]) ? $lote["lote"]["subasta"] : false;

if (!$subastaId) {
    echo json_encode(['error' => 'Subasta no encontrada'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Si no se pasa un ID, se busca la subasta actual
$lote["subasta"] = traerSubastaActual($subastaId);

$noche = traerNoches($subastaId, $lote["lote"]["nronoche"]);
$lote["noche"] = $noche[0];


$categoria = traerCategoria($lote["lote"]["categoria"]);
$lote["categoria"] = $categoria[0];


echo json_encode($lote, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);