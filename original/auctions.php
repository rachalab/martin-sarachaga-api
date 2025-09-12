<?php
require_once 'querys.php'; // Incluye las funciones

header('Content-Type: application/json');

$autor = isset($_REQUEST['autor']) ? $_REQUEST['autor'] : false;
$categoria = isset($_REQUEST['categoria']) ? $_REQUEST['categoria'] : false;
$noche = isset($_REQUEST['noche']) ? $_REQUEST['noche'] : false;
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

// Si no se pasa un ID, se busca la subasta actual
$subasta["subasta"] = traerSubastaActual($id);

$subastId = !empty($subasta["subasta"]["id"]) ? $subasta["subasta"]["id"] : false;

if (!$subastId) {
    echo json_encode(['error' => 'Subasta no encontrada'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$subasta["noches"] = traerNoches($subastId);
$subasta["categorias"] = traerCategorias($subastId);
$subasta["autores"] = traerAutores($subastId);
$subasta["lotes"] = traerLotes($subastId, $autor, $categoria, $noche);


echo json_encode($subasta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);