<?php
require_once 'Database.php';
require_once 'models/Category.php'; // Include the Category model
require_once 'helpers/SlugHelper.php'; // Include any helper functions if needed
require_once 'helpers/FormatStringHelper.php';

class CategoryService {
    private $db;
    private $slugHelper;    
    private $formatStringHelper;

    public function __construct() {
        $this->db = new Database();
        $this->slugHelper = new SlugHelper(); // Assuming you have a SlugHelper class for Slug String     
        $this->formatStringHelper = new FormatStringHelper();           
    }

    /**
     * Obtiene una categoría por su ID
     * @param int $id ID de la categoría
     * @return Category|null Datos de la categoría o null si no existe
     */
    public function getCategoryById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM categorias WHERE id = ?");

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categories = $result->fetch_assoc();
        $stmt->close();

        $categories = new Category($categories);
        $categories = $categories->toArray();

        $categories["nombre"] = $this->formatStringHelper->formatCategoria($categories["nombre"]);

        $categories["url"] = "/". $this->slugHelper->slugify($categories["nombre"]); 

        return $categories ?: null;
    }

    /**
     * Obtiene todas las categorías de una subasta
     * @param int $id ID de la subasta
     * @return Category[]|null Lista de categorías o null si no existen
     */
    public function getCategoryByAuctionId($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT DISTINCT categorias.id, categorias.nombre
FROM lotes
JOIN categorias ON lotes.categoria = categorias.id
WHERE lotes.subasta = ?");


        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while ($data = $result->fetch_assoc()) {
            $category = new Category($data);

            $categori = [];
            $categori = $category->toArray();

            $categori["nombre"] = $this->formatStringHelper->formatCategoria($categori["nombre"]); 

            $categori["url"] = "/". $this->slugHelper->slugify($categori["nombre"]); 

            $categories[] = $categori;
        }
        $stmt->close();
        return $categories ?: null;
    }

    /**
     * Obtiene todas las categorías de lotes en venta directa
     * @param array $ventas Array de lotes en venta directa
     * @return Category[]|null Lista de categorías o null si no existen
     */
    public function getCategoryByDirectSale($ventas) {
        // Traer solo las categorías
        $categorias = array_column($ventas, "categoria");
        $categoriasUnicas = array_unique($categorias);

        $categories = [];
        foreach ($categoriasUnicas as $key => $value) {
            $category = $this->getCategoryById($value);


            foreach($ventas as $venta){
                if($venta["categoria"] == $value){
                    $category["lotes"][] = $venta;
                }
            }

            if ($category) {
                $categories[] = $category;
            }
        }

        return $categories ?: null;
    }
}