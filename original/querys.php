<?php

$fechaHoy = date("Y-m-d");

// Ejecuta el MySQL query y devuelve el resultado
function ejecutarQuery($query){
    $conexion = new mysqli('localhost','root','','saracha');
    $result = mysqli_query($conexion, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conexion));
    }
    return $result;
}


/**
 * Formatea una fecha al formato español
 * @param string $fecha Fecha en formato 'Y-m-d H:i:s'
 * @return array Array con la fecha original y la fecha formateada
 */
function formatearFechaEspanol($fecha)
{
    if (empty($fecha)) {
        return [
            "system" => null,
            "format" => null
        ];
    }

    $formatter = new IntlDateFormatter(
        'es_ES',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        null,
        null,
        "EEEE, d 'de' MMMM 'de' y"
    );

    $timestamp = strtotime($fecha);
    $formateada = $formatter->format($timestamp);

    // Capitaliza la primera letra con soporte UTF-8
    $formateada = mb_convert_case(mb_substr($formateada, 0, 1, 'UTF-8'), MB_CASE_UPPER, 'UTF-8') .
                  mb_substr($formateada, 1, null, 'UTF-8');

    return [
        "system" => $fecha,
        "format" => $formateada
    ];
}

/**
 * Trae la subasta actual o la próxima si no hay una en curso
 *
 * @param int|bool $id Si se pasa un ID, busca esa subasta específica
 * @param array $campos Campos específicos a seleccionar, si se deja vacío, selecciona todo
 * @return array|string Datos de la subasta actual o un mensaje de error si no existe
 * */ 
function traerSubastaActual($id = false ,$campos = [])
{
    global $fechaHoy;

    if($id){
        // Buscar la subasta actual
        $query = "SELECT * FROM subastas WHERE status = 1 AND id = $id LIMIT 1";

        $rs = ejecutarQuery($query);

        // Si hay una subasta actual, devolver sus datos
        if ($rs && mysqli_num_rows($rs) > 0) {
            $reg = mysqli_fetch_assoc($rs);

            if (!empty($reg["fechainicio"])) {
                $reg["fechainicio"] = formatearFechaEspanol($reg["fechainicio"]);
            }            

            if (!empty($reg["fechafin"])) {
                $reg["fechafin"] = formatearFechaEspanol($reg["fechafin"]);
            }            
            
            if (!empty($reg["fechacarga"])) {
                $reg["fechacarga"] = formatearFechaEspanol($reg["fechacarga"]);
            }            

            return $reg;
        } else {
            return "Esta subasta no existe";
        }

    // Si no es una subasta Individual
    }else{
        // Si se pasan campos, arma el SELECT con esos campos
        $select = (is_array($campos) && count($campos) > 0) ? implode(',', $campos) . ",subasta_online,fechainicio, fechafin" : '*';

        // Buscar la subasta actual
        $query = "SELECT $select FROM subastas WHERE status = 1 AND subasta_online = 0 AND fechainicio <= '$fechaHoy' AND fechafin >= '$fechaHoy' LIMIT 1";

        $rs = ejecutarQuery($query);

        // Si hay una subasta actual, devolver sus datos
        if ($rs && mysqli_num_rows($rs) > 0) {
            $reg = mysqli_fetch_assoc($rs);

            if (!empty($reg["fechainicio"])) {
                $reg["fechainicio"] = formatearFechaEspanol($reg["fechainicio"]);
            }            

            if (!empty($reg["fechafin"])) {
                $reg["fechafin"] = formatearFechaEspanol($reg["fechafin"]);
            }            
            
            if (!empty($reg["fechacarga"])) {
                $reg["fechacarga"] = formatearFechaEspanol($reg["fechacarga"]);
            }            
            
            return $reg;
        } else {
            // Si no hay subasta actual, buscar la próxima ha realizar
            $query = "SELECT $select FROM subastas WHERE status = 1 AND subasta_online = 0 AND fechainicio > '$fechaHoy' ORDER BY fechainicio ASC LIMIT 1";

            $rs = ejecutarQuery($query);
            // Si hay una próxima subasta, devolver sus datos
            if ($rs && mysqli_num_rows($rs) > 0) {
                $reg = mysqli_fetch_assoc($rs);

                if (!empty($reg["fechainicio"])) {
                    $reg["fechainicio"] = formatearFechaEspanol($reg["fechainicio"]);
                }            

                if (!empty($reg["fechafin"])) {
                    $reg["fechafin"] = formatearFechaEspanol($reg["fechafin"]);
                }            
                
                if (!empty($reg["fechacarga"])) {
                    $reg["fechacarga"] = formatearFechaEspanol($reg["fechacarga"]);
                }

                return $reg;
            } else {
                return "No hay subastas"; // No hay subasta actual ni próxima
            }
        }
    } 
}

/**
 * Trae las noches de una subasta
 * @param int $subastaId ID de la subasta
 * @param int $noche Número de noche específica a traer, si se deja false trae todas
 * @return array Lista de noches de la subasta
 */
function traerNoches ($subastaId, $noche = false)
{
    
    $query = "SELECT * FROM `dias_horarios_noches` as dias WHERE dias.idSubasta = $subastaId";

    if ($noche) {
        $query .= " AND noche = $noche";
    }

    $rs = ejecutarQuery($query);
    
    $noches = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        if (!empty($row["dia"])) {
            $row["dia"] = formatearFechaEspanol($row["dia"]);
        }

        if (!empty($row["horario"])) {
            $row["horario"] = [
                "system" => $row["horario"],
                "format" => str_replace('.', ':', $row["horario"])
            ];
        }

        $noches[] = $row;
    }
    
    return $noches;
}

/**  
 * Traer las categorías de la subasta
 * @param int $subastaId ID de la subasta
 * @return array Lista de categorías de la subasta 
 */
function traerCategorias($subastaId)
{
    $query = "SELECT categorias.id, categorias.nombre FROM lotes 
              JOIN categorias ON lotes.categoria = categorias.id 
              WHERE lotes.subasta = $subastaId
              GROUP BY categorias.nombre";

    $rs = ejecutarQuery($query);
    
    $categorias = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        $categorias[] = $row;
    }
    
    return $categorias;
}

/**
 * Función para traer los lotes de una subasta
 * @param int $subastaId ID de la subasta
 * @param string $autor Filtro por autor, si se deja false no filtra
 * @param string $categoria Filtro por categoría, si se deja false no filtra
 * @return array Lista de lotes de la subasta
 */
function traerLotes($subastaId, $autor = false, $categoria = false, $noche = false)
{
    $query = "SELECT * FROM lotes WHERE subasta = $subastaId";

    if ($autor) {
        $query .= " AND autor = '$autor'";
    }

    if ($categoria) {
        $query .= " AND categoria = '$categoria'";

    }
    if ($noche) {
        $query .= " AND nronoche = $noche";
    }

    $query .= " ORDER BY lote ASC";

    $rs = ejecutarQuery($query);
    
    $lotes = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        $lotes[] = $row;
    }
    
    return $lotes;
}

/** 
 * Traer los Autores de la subasta
 * @param int $subastaId ID de la subasta
 * @return array Lista de autores de la subasta
 */
function traerAutores($subastaId)
{
    $query = "SELECT DISTINCT autor FROM lotes WHERE subasta = $subastaId";
    $rs = ejecutarQuery($query);
    
    $autores = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        $autor = trim($row['autor']);
        $autores[] = ($autor === "") ? "Anónimo" : $autor;
    }
    
    return $autores;
}

/**
 * Traer un lote específico por ID
 * @param int $loteId ID del lote a traer
 * @return array Datos del lote
 */
function traerLote($loteId){
    $query = "SELECT * FROM lotes WHERE id = $loteId";
    $rs = ejecutarQuery($query);
    
    //$lotes = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        $lotes = $row;
    }
    
    return $lotes;
}

/**
 * Traer una categoría específica por ID
 * @param int $id ID de la categoría a traer
 * @return array Datos de la categoría
 */
function traerCategoria($id){
    $query = "SELECT id, nombre FROM categorias
              WHERE id = $id";

    $rs = ejecutarQuery($query);
    
    $categoria = [];
    while ($row = mysqli_fetch_assoc($rs)) {
        $categoria[] = $row;
    }
    
    return $categoria;
}

?>