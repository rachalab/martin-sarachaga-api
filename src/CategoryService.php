<?php
require_once 'Database.php';
require_once 'models/Category.php'; // Include the Category model

class CategoryService {
    private $db;

    public function __construct() {
        $this->db = new Database();
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

        return $categories ?: null;
    }

    /**
     * Obtiene todas las categorías de una subasta
     * @param int $id ID de la subasta
     * @return Category[]|null Lista de categorías o null si no existen
     */
    public function getCategoryByAuctionId($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT categorias.id, categorias.nombre FROM lotes 
              JOIN categorias ON lotes.categoria = categorias.id 
              WHERE lotes.subasta = ?
              GROUP BY categorias.nombre");


        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $categories = [];
        while ($data = $result->fetch_assoc()) {
            $category = new Category($data);
            $categories[] = $category->toArray();
        }
        $stmt->close();
        return $categories ?: null;
    }
}