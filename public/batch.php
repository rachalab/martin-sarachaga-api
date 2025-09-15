<?php
require_once '../src/AuctionService.php'; // Include the AuctionService class
require_once '../src/NightService.php'; // Include the NightService class
require_once '../src/CategoryService.php'; // Include the CategoryService class
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


$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

$batchService = new BatchService();
$lote["lote"] = $batchService->getBatchById($id);

//Si no existe el ID devolvemos un mensaje de error
if (empty($lote["lote"]["id"])) {
    echo json_encode(['error' => 'Lote no encontrada'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$subastaId = !empty($lote["lote"]["subasta"]) ? $lote["lote"]["subasta"] : false;

if (!$subastaId) {
    echo json_encode(['error' => 'Subasta no encontrada'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$auctionService = new AuctionService();
$lote["subasta"] = $auctionService->getCurrentAuction($subastaId);

//Si tiene noches
if(!empty($lote["lote"]["nronoche"])){
    $NightService = new NightService();
    $lote["noche"] = $NightService->getNights($subastaId, $lote["lote"]["nronoche"]);
}else{
    $lote["noche"] = null;
}

//Si tiene categoria
if(!empty($lote["lote"]["categoria"])){
    $CategoryService = new CategoryService();
    $lote["categoria"] = $CategoryService->getCategoryById($lote["lote"]["categoria"]);
}else{
    $lote["categoria"] = null;
}
$data = utf8ize($lote);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>