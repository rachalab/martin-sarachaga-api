<?php
require_once '../src/BatchService.php'; // Include the BatchService class
require_once '../src/CategoryService.php'; // Include the CategoryService class
require_once '../src/helpers/FormatStringHelper.php';
require_once '../src/helpers/SlugHelper.php';
header('Content-Type: application/json');

function utf8ize($mixed) {
    if (is_array($mixed)) {
        return array_map('utf8ize', $mixed);
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
    }
    return $mixed;
}

//Convierte params de Htaccess a variables
if (isset($_GET['params'])) {
    $parts = explode('/', $_GET['params']);
    for ($i = 0; $i < count($parts); $i += 2) {
        if (isset($parts[$i+1])) {
            $_GET[$parts[$i]] = urldecode($parts[$i+1]);
        }
    }
}

//Metadata
$meta = isset($_GET['meta']) ? $_GET['meta'] : false;


//Si se pide solo metadatos - Titulos | Descripcion
if($meta == "metadata"){
    $data["title"] = 'Venta privada';
    $data["description"] = 'Descripcion de privada';

    $data = utf8ize($data);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$batchService = new BatchService();
$lotes["lotes"] = $batchService->getDirectSaleBatches();

if(!empty($lotes["lotes"])){
    //Trear datos de las categorías con los lotes
    $categoriaService = new CategoryService();
    $venta["categorias"] = $categoriaService->getCategoryByDirectSale($lotes["lotes"]);
    
    
    $venta["autores"] = [];
    
    // Traer solo los autores
    $autores = array_column($lotes["lotes"], "autor");
    $categoriasUnicas = array_unique($autores);
    
    //Si hay autores
    if(!empty($categoriasUnicas) && is_array($categoriasUnicas)){
        $formatStringHelper = new FormatStringHelper();
        $slugHelper = new SlugHelper();

        // Formatear y preparar URLs
        foreach ($categoriasUnicas as $autor_string) {
            $autor = $formatStringHelper->formatAutor($autor_string);

            $venta["autores"][] = [
                "original" => $autor,
                "url" => "/" . $slugHelper->slugify($autor)
            ];
        }
    }
}

$data = utf8ize($venta);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);