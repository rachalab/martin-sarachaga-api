<?php

/*
// Activa la visualización en pantalla
ini_set('display_errors', 1);

// Opcional: fuerza que también se muestren los errores de inicio
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
require_once '../src/AuctionService.php'; // Include the AuctionService class
require_once '../src/NightService.php'; // Include the NightService class
require_once '../src/CategoryService.php'; // Include the CategoryService class
require_once '../src/BatchService.php'; // Include the BatchService class

header('Content-Type: application/json');

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

$auctionService = new AuctionService();
$subasta["subasta"] = $auctionService->getCurrentAuction($id);
$subastId = !empty($subasta["subasta"]["id"]) ? $subasta["subasta"]["id"] : false;

//Si no existe el ID devolvemos un mensaje de error
if (!$subastId) {
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");

    echo json_encode(['error' => 'Subasta no encontrada'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

//Metadata
$subasta["meta"]["title"] = !empty($subasta['subasta']['nro']) ? 
    'Subasta Nro ' . $subasta['subasta']['nro'] .' — Categorias — Martín Saráchaga Subastas' : 
    'Categorias — Martín Saráchaga Subastas';

$subasta["meta"]["description"] = !empty($subasta["subasta"]["descripcion"]) ? 
    $subasta["subasta"]["descripcion"] : 
    'Categorias de subasta presencial Nro ' . strval($subasta['subasta']['nro']);

$subasta["meta"]["url"] = !empty($subasta["subasta"]["url"]) ? $subasta["subasta"]["url"] : "";

//Noches
$NightService = new NightService();
$subasta["noches"] = $NightService->getNights($subastId);

//Categoria
$CategoryService = new CategoryService();
$Category = $CategoryService->getCategoryByAuctionId($subastId);

//Si tiene categorias
if(!empty($Category)){
    $BatchService = new BatchService();
    $subasta["categorias"] = $BatchService->getBatchesImagesByAuctionId($subastId, $Category);
}else{
    $subasta["categorias"] = null;
}

/**
 * Convierte recursivamente cualquier codificación a UTF-8
 */
function utf8ize($mixed) {
    if (is_array($mixed)) {
        return array_map('utf8ize', $mixed);
    } elseif (is_string($mixed)) {
        // Si ya es UTF-8 válido, lo devuelve tal cual
        if (mb_check_encoding($mixed, 'UTF-8')) {
            return $mixed;
        }
        
        // Intenta detectar la codificación
        $encoding = mb_detect_encoding(
            $mixed, 
            ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], 
            true
        );
        
        // Si detectó algo, convierte a UTF-8
        if ($encoding && $encoding !== 'UTF-8') {
            return mb_convert_encoding($mixed, 'UTF-8', $encoding);
        }
        
        // Fallback: asume ISO-8859-1 (Latin1) que es común en bases de datos antiguas
        return mb_convert_encoding($mixed, 'UTF-8', 'ISO-8859-1');
    }
    return $mixed;
}

$data = utf8ize($subasta);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>