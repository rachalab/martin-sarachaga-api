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