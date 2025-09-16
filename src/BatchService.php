<?php
require_once 'Database.php'; // Assuming Database.php is in the same directory
require_once 'models/Batch.php'; // Include the Batch model
require_once 'helpers/SlugHelper.php'; // Include any helper functions if needed
require_once 'helpers/FormatAutorHelper.php';

class BatchService {
    private $db;
    private $slugHelper;
    private $formatAutorHelper;

    public function __construct() {
        $this->db = new Database();
        $this->slugHelper = new SlugHelper();
        $this->formatAutorHelper = new FormatAutorHelper();
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

        //Slug y url
        $url = "/subasta-presencial/". $batchs["subasta"] ."/obras/";
        $batchs['slug'] = $batchs["id"] . "-" .$this->slugHelper->slugify($batchs["titulo"]);
        $batchs['url'] = $url . $batchs['slug'];


        $batchs['autor'] = $this->formatAutorHelper->formatAutor($batchs["autor"]);

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

            //Slug y url
            $batch_set = $batch->toArray();
            $url = "/subasta-presencial/". $batch_set["subasta"] ."/obras/";
            $batch_set['url'] = $url . $batch_set["id"] ."-".$this->slugHelper->slugify($batch_set["titulo"]);


            $batch_set['autor'] = $this->formatAutorHelper->formatAutor($batch_set["autor"]);


            $batches[] = $batch_set;
        }

        $stmt->close();
        return $batches ?: [];
    }



    public function getAuthorsByAuctionId($id)
    {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT DISTINCT autor FROM lotes WHERE subasta = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $autores = [];
        while ($row = $result->fetch_assoc()) {
            $autor = $this->formatAutorHelper->formatAutor($row['autor']);
            $autores[] = [
                "original" => $autor,
                "url" => "/" . $this->slugHelper->slugify($autor)
            ];
        }

        $stmt->close();
        return $autores;
    }

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

            $resultados[] = [
                "id" => $categoria["id"],
                "nombre" => $categoria["nombre"],
                "url" => $categoria["url"],
                "images" => $images
            ];
        }

        return $resultados;
    }
}