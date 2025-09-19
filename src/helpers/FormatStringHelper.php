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

    public function cleanTitle(?string $string): string {
        if ($string === null) {
            return '';
        }

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