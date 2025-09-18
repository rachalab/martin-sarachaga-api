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

        return $autor;
    }
}