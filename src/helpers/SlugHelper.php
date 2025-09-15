<?php
class SlugHelper
{
    /**
     * Convierte un texto a slug amigable para URLs
     */
    public function slugify($text)
    {
        // Pasar a minúsculas
        $text = mb_strtolower($text, 'UTF-8');

        // Reemplazar acentos y caracteres especiales
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);

        // Reemplazar cualquier cosa que no sea letra o número por guiones
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);

        // Eliminar guiones extra
        $text = trim($text, '-');

        return $text;
    }

    /**
     * Busca el nombre original a partir de un slug dentro de un array de autores
     * $autores = [
     *   ["original" => "BERNI, ANTONIO", "slug" => "berni-antonio"],
     *   ...
     * ]
     */
    public function findOriginalBySlug($slug, $autores)
    {
        foreach ($autores as $autor) {
            if (strcasecmp($autor['url'], $slug) === 0) {
                return $autor['original'];
            }
        }
        return null;
    }
}
