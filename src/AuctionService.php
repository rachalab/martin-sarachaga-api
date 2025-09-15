<?php
require_once 'Database.php';
require_once 'models/Auction.php'; // Include the Auction model
require_once 'helpers/DateHelper.php'; // Include any helper functions if needed

class AuctionService {
    private $db;
    private $dateHelper;    

    public function __construct() {
        $this->db = new Database();
        $this->dateHelper = new DateHelper(); // Assuming you have a DateHelper class for date formatting         
    }

    /**
     * Trae la subasta actual o la próxima si no hay una en curso
     * @param int|null $id Si se pasa un ID, busca esa subasta específica
     * @return Auction|null Datos de la subasta actual o null si no existe
     */

    public function getCurrentAuction($id = null) {
        $conn = $this->db->getConnection();

        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM subastas WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("SELECT * FROM subastas WHERE status = 1 AND subasta_online = 0 AND fechainicio <= CURDATE() AND fechafin >= CURDATE() LIMIT 1");
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
        }

        // Si no hay subasta actual, buscar la próxima a realizar
        if (!$data) {
            $stmt = $conn->prepare("SELECT * FROM subastas WHERE status = 1 AND subasta_online = 0 AND fechainicio > CURDATE() ORDER BY fechainicio ASC LIMIT 1");
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
        }

        //La ultima finalizada  
        if (!$data) {
            $stmt = $conn->prepare("SELECT * FROM subastas WHERE status = 1 AND subasta_online = 0 AND fechainicio < CURDATE() ORDER BY fechainicio DESC LIMIT 1");
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
        }


        if (!$data) {
            return null;
        }

        // Crea el objeto Auction
        $auction = new Auction($data);

        // Convierte el objeto a array
        $array = $auction->toArray();

        if (!empty($auction->fechainicio)) {
            $array['fechainicio'] = $this->dateHelper->formatearFechaEspanol($auction->fechainicio);
        }
        if (!empty($auction->fechafin)) {
            $array["fechafin"] = $this->dateHelper->formatearFechaEspanol($auction->fechafin);
            }

        if (!empty($auction->fechacarga)) {
            $array["fechacarga"] = $this->dateHelper->formatearFechaEspanol($auction->fechacarga);
        }


        $array["url"] = "/subasta-presencial/". $array["id"];

        return $array ?: null;
    }

    /**
     * Trae la subasta la ultima subasta activa
     * @return Auction|null Datos de la subasta actual o null si no existe
     */    
    public function getLastAuctionId() {
        $conn = $this->db->getConnection();


        $stmt = $conn->prepare("SELECT id FROM subastas WHERE status = 1 AND subasta_online = 0 AND fechainicio <= CURDATE() AND fechafin >= CURDATE() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        

        // Si no hay subasta actual, buscar la próxima a realizar
        if (!$data) {
            $stmt = $conn->prepare("SELECT id FROM subastas WHERE status = 1 AND subasta_online = 0 AND fechainicio > CURDATE() ORDER BY fechainicio ASC LIMIT 1");
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
        }

        //La ultima finalizada  
        if (!$data) {
            $stmt = $conn->prepare("SELECT id FROM subastas WHERE status = 1 AND subasta_online = 0 AND fechainicio < CURDATE() ORDER BY fechainicio DESC LIMIT 1");
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
        }

        if (!$data) {
            return null;
        }

        // Crea el objeto Auction
        $auction = new Auction($data);
        $auctions = $auction->toArray();

        $auctions["url"] = "/subasta-presencial/". $auctions["id"];


        return $auctions ?: null;
    }
}