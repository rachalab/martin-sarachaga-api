<?php
require_once '../src/BatchService.php'; // Include the BatchService class
require_once '../src/CategoryService.php'; // Include the CategoryService class
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
$venta["lotes"] = $batchService->getDirectSaleBatches();

if(!empty($venta["lotes"])){

    // Traer solo las categorías
    $categorias = array_column($venta["lotes"], "categoria");

    // Eliminar duplicados
    $categoriasUnicas = array_unique($categorias);

    //Trear datos de las categorías
    $categoriaService = new CategoryService();
    $venta["categorias"] = $categoriaService->getCategoryByDirectSale($categoriasUnicas);
}

$data = utf8ize($venta);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);