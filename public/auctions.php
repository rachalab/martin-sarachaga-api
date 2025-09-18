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



//Verificamos si se pide la ultima Subasta
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : false;

$auctionService = new AuctionService();

if($action == "lastAuctions"){
    $subasta["subasta"] = $auctionService->getLastAuctionId();
    $data = utf8ize($subasta);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}


//Buscamos por el ID
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

//Convierte params de Htaccess a variables
if (isset($_GET['params'])) {
    $parts = explode('/', $_GET['params']);
    for ($i = 0; $i < count($parts); $i += 2) {
        if (isset($parts[$i+1])) {
            $_GET[$parts[$i]] = urldecode($parts[$i+1]);
        }
    }
}

//Capturamos los parametros de Filtros
$autor = isset($_GET['autor']) ? $_GET['autor'] : false;
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : false;
$noche = isset($_GET['noche']) ? $_GET['noche'] : false;

//Traemos La subasta
$subasta["subasta"] = $auctionService->getCurrentAuction($id);

$subastId = !empty($subasta["subasta"]["id"]) ? $subasta["subasta"]["id"] : false;

//Si no existe el ID devolvemos un mensaje de error
if (!$subastId) {
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");

    echo json_encode(['error' => 'Subasta no encontrada'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

//Treamos las noches
$NightService = new NightService();
$subasta["noches"] = $NightService->getNights($subastId);

//Treamos las categorias
$CategoryService = new CategoryService();
$subasta["categorias"] = $CategoryService->getCategoryByAuctionId($subastId);

//Traemos los lotes
$BatchService = new BatchService();
$subasta["autores"] = $BatchService->getAuthorsByAuctionId($subastId); 
$subasta["lotes"] = $BatchService->getBatchesByAuctionId($subastId, $autor, $categoria, $noche);
$data = utf8ize($subasta);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
?>