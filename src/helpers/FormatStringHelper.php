<?php
class FormatStringHelper
{
   public function formatAutor(?string $autor): string {
    // Si es null o cadena vacía o solo espacios
    if (empty(trim($autor))) {
        return "Anónimo";
    }

    // Limpiar espacios al inicio y final
    $autor = trim($autor);

    // PRIMERO: Convertir a UTF-8 si no lo está ya
    $autor = $this->utf8ize($autor);

    // Pasar todo a minúscula primero
    $autor = mb_strtolower($autor, 'UTF-8');

    // Poner mayúscula después de cada espacio o coma
    $autor = mb_convert_case($autor, MB_CASE_TITLE, "UTF-8");

    // Si contiene coma, asumimos formato "Apellido, Nombre"
    if (strpos($autor, ',') !== false) {
        [$apellido, $nombre] = array_map('trim', explode(',', $autor, 2));
        $autor = $nombre . ' ' . $apellido;
    }

    return $autor;
}

/**
 * Convierte cualquier codificación a UTF-8
 */
private function utf8ize($mixed) {
    if (is_string($mixed)) {
        // Si ya es UTF-8 válido, lo devuelve tal cual
        if (mb_check_encoding($mixed, 'UTF-8')) {
            return $mixed;
        }
        
        // Intenta detectar la codificación
        $encoding = mb_detect_encoding(
            $mixed, 
            ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'CP1252', 'ISO-8859-15'],
            true
        );
        
        // Si detectó algo, convierte a UTF-8
        if ($encoding && $encoding !== 'UTF-8') {
            return mb_convert_encoding($mixed, 'UTF-8', $encoding);
        }
        
        // Fallback: asume ISO-8859-1 (Latin1)
        return mb_convert_encoding($mixed, 'UTF-8', 'ISO-8859-1');
    }
    return $mixed;
}

    public function cleanTitle(?string $string): string {
        if ($string === null) {
            return '';
        }
        
        $string = $this->utf8ize($string);

        // Trim espacios adelante y atrás
        $string = trim($string);

        // Eliminar comillas simples, dobles, acentos de comilla y puntos
        $string = str_replace(
            ['"', "'", "´", "`", "."],
            "",
            $string
        );

        // Eliminar dobles espacios
        $string = preg_replace('/\s+/', ' ', $string);

        return $string;
    }

    public function formatCategoria(?string $categoria): string {
        if ($categoria === null || trim($categoria) === '') {
            return '';
        }

        // Limpiar
        $categoria = $this->cleanTitle($categoria);

        // Pasar todo a minúsculas
        $categoria = mb_strtolower($categoria, 'UTF-8');

        // Capitalizar solo la primera letra
        $categoria = ucfirst($categoria);

        return $categoria;
    }
}