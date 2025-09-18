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

$autor = isset($_REQUEST['autor']) ? $_REQUEST['autor'] : false;
$categoria = isset($_REQUEST['categoria']) ? $_REQUEST['categoria'] : false;
$noche = isset($_REQUEST['noche']) ? $_REQUEST['noche'] : false;
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

$NightService = new NightService();
$subasta["noches"] = $NightService->getNights($subastId);

$CategoryService = new CategoryService();
$Category = $CategoryService->getCategoryByAuctionId($subastId);



//Si tiene categorias
if(!empty($Category)){
    $BatchService = new BatchService();
    $subasta["categorias"] = $BatchService->getBatchesImagesByAuctionId($subastId, $Category);
}else{
    $subasta["categorias"] = null;
}

function utf8ize($mixed) {
    if (is_array($mixed)) {
        return array_map('utf8ize', $mixed);
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
    }
    return $mixed;
}

$data = utf8ize($subasta);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>