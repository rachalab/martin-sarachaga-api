<?php
require_once '../src/AuctionService.php'; // Include the AuctionService class
require_once '../src/NightService.php'; // Include the NightService class
require_once '../src/CategoryService.php'; // Include the CategoryService class
require_once '../src/BatchService.php'; // Include the BatchService class

header('Content-Type: application/json');


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


$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;

$batchService = new BatchService();
$lote["lote"] = $batchService->getDirectSaleBathById($id);

//Si no existe el ID devolvemos un mensaje de error
if (empty($lote["lote"]["id"])) {
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");

    echo json_encode(['error' => 'Lote no encontrada'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
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