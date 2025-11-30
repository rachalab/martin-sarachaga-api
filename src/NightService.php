<?php
require_once 'Database.php';
require_once 'models/Night.php'; // Include the Night model
require_once 'helpers/DateHelper.php'; // Include any helper functions if needed

class NightService {
    private $db;
    private $dateHelper;

    public function __construct() {
        $this->db = new Database();
        $this->dateHelper = new DateHelper(); // Assuming you have a DateHelper class for date formatting
    }

    public function getNights($auctionId, $nightId = null) {
        $conn = $this->db->getConnection();

        if ($nightId !== null) {
            $stmt = $conn->prepare("SELECT * FROM `dias_horarios_noches` WHERE idSubasta = ? AND noche = ?");
            $stmt->bind_param("ii", $auctionId, $nightId);
        } else {
            $stmt = $conn->prepare("SELECT * FROM `dias_horarios_noches` WHERE idSubasta = ?");
            $stmt->bind_param("i", $auctionId);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $nights = [];

        while ($data = $result->fetch_assoc()) {
            $night = new Night($data);
            
            $nights[] = $night->toArray();

            if (!empty($night->dia)) {
                $nights[count($nights) - 1]['dia'] = $this->dateHelper->formatearFechaEspanol($night->dia);
            }

            if (!empty($night->horario)) {

                $nights[count($nights) - 1]['horario'] = [
                "system" => $night->horario,
                "format" => str_replace('.', ':', $night->horario),
                ];
            }
        }

        $stmt->close();

        // Si nightId fue pasado, devuelve solo el primer objeto (o null)
        if ($nightId !== null) {
            return $nights[0] ?? null;
        }

        return $nights ?: null;
    }

}