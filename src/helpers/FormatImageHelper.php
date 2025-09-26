<?php
class FormatImageHelper
{
    /**
     * Genera un array con las URLs de las imágenes asociadas a un lote
     * @param int $id ID del lote
     * @return array|null Array con las URLs de las imágenes o null si no hay imágenes
     */
    public function ArrayformatImage($id)
    {
        //Array para guardar las imágenes
        $batchs = [];
        $imageCounter = 1;

        while (true) {
            $urlImage = "/imagenes_lotes/" . $id . "_" . $imageCounter . "_grande.jpg";
            $localImagePath = __DIR__ . "/../.." . $urlImage;
            
            if (file_exists($localImagePath)) {
                // Si la imagen existe, agrega la URL al array
                $batchs[] = "https://martinsarachaga.com" . $urlImage;
                $imageCounter++;
            } else {
                // Si la imagen no existe, sal del bucle
                break;
            }
        }
        
        // Si no se encontró ninguna imagen, puedes asignar un valor por defecto
        if (empty($batchs)) {
            $batchs = null; // O una URL a una imagen por defecto
        }

        return $batchs;
    }

    /**
     * Obtiene la URL de la primera imagen de un lote
     * @param int $id ID del lote
     * @return string|null URL de la primera imagen o null si no existe
     */

    public function getFirstImage($id)
    {
        $urlImage = "/imagenes_lotes/" . $id . "_1_grande.jpg";
        $localImagePath = __DIR__ . "/../.." . $urlImage;

        if (file_exists($localImagePath)) {
            return "https://martinsarachaga.com" . $urlImage;
        }

        return null;
    }
}