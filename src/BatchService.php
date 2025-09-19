<?php
require_once 'Database.php'; // Assuming Database.php is in the same directory
require_once 'models/Batch.php'; // Include the Batch model
require_once 'helpers/SlugHelper.php'; // Include any helper functions if needed
require_once 'helpers/FormatStringHelper.php';
require_once 'helpers/FormatImageHelper.php';

class BatchService {
    private $db;
    private $slugHelper;
    private $formatStringHelper;
    private $formatImageHelper;

    public function __construct() {
        $this->db = new Database();
        $this->slugHelper = new SlugHelper();
        $this->formatStringHelper = new FormatStringHelper();
        $this->formatImageHelper = new FormatImageHelper();
    }

    /**
     * Obtiene un lote por su ID
     * @param int $id ID del lote
     * @return Batch|null Datos del lote o null si no existe
     */
    public function getBatchById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM lotes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        if (!$data) {
            return null;
        }

        // Crea el objeto Batch
        $batch = new Batch($data);
        $batchs = $batch->toArray();


        //Titulo
        $batchs['titulo'] = $this->formatStringHelper->cleanTitle($batchs["titulo"]);

        //Slug y url
        $url = "/subasta-presencial/". $batchs["subasta"] ."/obras/";
        $batchs['slug'] = $batchs["id"] . "-" .$this->slugHelper->slugify($batchs["titulo"]);
        $batchs['url'] = $url . $batchs['slug'];

        //Autores
        $batchs['autor'] = $this->formatStringHelper->formatAutor($batchs["autor"]);

        //Array para guardar las imágenes
        $batchs['images'] = $this->formatImageHelper->ArrayformatImage($batchs["id"]);
 
        // Convierte el objeto a array
        return $batchs ?: null;
    }

    public function getBatchesByAuctionId($subastaId, $autor = null, $categoria = null, $noche = null) {
        $conn = $this->db->getConnection();
        $query = "SELECT * FROM lotes WHERE subasta = ?";
        $params = [$subastaId];

        if (!empty($autor)) {
            //Traer todos los autores
            $autores = $this->getAuthorsByAuctionId($subastaId);
            //Comparar slug con Nombre de autor original
            $original = $this->slugHelper->findOriginalBySlug($autor, $autores);

            $query .= " AND autor = ?";
            $params[] = $original;
        }

        if (!empty($categoria)) {
            $query .= " AND categoria = ?";
            $params[] = $categoria;
        }

        if (!empty($noche)) {
            $query .= " AND nronoche = ?";
            $params[] = $noche;
        }

        $query .= " ORDER BY lote ASC";
        
        // Prepare and execute the statement
        $stmt = $conn->prepare($query);
        if ($params) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch results
        $batches = [];
        while ($data = $result->fetch_assoc()) {
            $batch = new Batch($data);
            $batch_set = $batch->toArray();
            
            //Titulo            
            $batch_set['titulo'] = $this->formatStringHelper->cleanTitle($batch_set["titulo"]);

            //Slug y url
            $url = "/subasta-presencial/". $batch_set["subasta"] ."/obras/";
            $batch_set['url'] = $url . $batch_set["id"] ."-".$this->slugHelper->slugify($batch_set["titulo"]);

            //Autores
            $batch_set['autor'] = $this->formatStringHelper->formatAutor($batch_set["autor"]);

            //Array para guardar las imágenes
            $batch_set['images'] = $this->formatImageHelper->ArrayformatImage($batch_set["id"]);

            $batches[] = $batch_set;
        }

        $stmt->close();
        return $batches ?: [];
    }

    /**
     * Obtiene todos los autores únicos para una subasta dada
     * @param int $id ID de la subasta
     * @return array Array de autores con su nombre original y slug
     */

    public function getAuthorsByAuctionId($id)
    {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT DISTINCT autor FROM lotes WHERE subasta = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $autores = [];
        while ($row = $result->fetch_assoc()) {
            $autor = $this->formatStringHelper->formatAutor($row['autor']);
            $autores[] = [
                "original" => $autor,
                "url" => "/" . $this->slugHelper->slugify($autor)
            ];
        }

        $stmt->close();
        return $autores;
    }

    /**
     * Obtiene imágenes de lotes por ID de subasta y categorías
     * @param int $subastaId ID de la subasta
     * @param array $categorias Array de categorías con sus IDs
     * @return array Array de categorías con sus imágenes asociadas
     */
    public function getBatchesImagesByAuctionId($subastaId, $categorias = []) {
        $conn = $this->db->getConnection();
        $resultados = [];

        foreach ($categorias as $categoria) {
            $stmt = $conn->prepare("
                SELECT id 
                FROM lotes 
                WHERE subasta = ? AND categoria = ? 
                ORDER BY lote ASC 
                LIMIT 5
            ");
            $stmt->bind_param("ii", $subastaId, $categoria['id']);
            $stmt->execute();
            $res = $stmt->get_result();

            $images = [];
            while ($row = $res->fetch_assoc()) {
                $images[] = "https://martinsarachaga.com/imagenes_lotes/" . $row['id'] . "_1_grande.jpg";
            }
            $stmt->close();

            $resultados[] = array_merge(
                $categoria,
                ["images" => $images]
            );
        }

        return $resultados;
    }
}