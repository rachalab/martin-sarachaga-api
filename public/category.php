<?php
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

echo json_encode($subasta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>