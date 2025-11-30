<?php
class FormatImageHelper
{
    /**
     * Genera un array con las URLs de las imágenes asociadas a un lote
     * @param int $id ID del lote
     * @return array|null Array con las URLs de las imágenes o null si no hay imágenes
     */
    public function ArrayformatImage($id, $dimensions = false) {
        //Array para guardar las imágenes        
        $batchs = [];
        $imageCounter = 1;

        while (true) {
            $urlImage = "/imagenes_lotes/" . $id . "_" . $imageCounter . "_grande.jpg";
            $localImagePath = __DIR__ . "/../../.." . $urlImage;
            
            if (file_exists($localImagePath)) {
                // Obtener dimensiones reales
                $imageSize = getimagesize($localImagePath);

                //Si se piden dimensiones
                if($dimensions === true){
                    if ($imageSize) {
                        $width = $imageSize[0];
                        $height = $imageSize[1];
                    } else {
                        $width = null;
                        $height = null;
                    }
                    
                    // Agregar al array con src + dimensiones
                    $batchs[] = [
                        'src' => "https://martinsarachaga.com" . $urlImage,
                        'width' => $width,
                        'height' => $height
                    ];
                }else{
                    // Agregar al array solo con src
                    $batchs[] = "https://martinsarachaga.com" . $urlImage;
                }
                
                $imageCounter++;
            } else {
                break;
            }
        }
        
        return empty($batchs) ? null : $batchs;
    }

    /**
     * Obtiene la URL de la primera imagen de un lote, con opción de incluir dimensiones
     * @param int $id ID del lote
     * @param bool $dimensions Si es true, devuelve también ancho y alto
     * @return array|string|null URL o array con src, width, height; null si no existe
     */
    public function getFirstImage($id, $dimensions = false) {
        $urlImage = "/imagenes_lotes/" . $id . "_1_grande.jpg";
        $localImagePath = __DIR__ . "/../../.." . $urlImage;

        if (file_exists($localImagePath)) {
            $fullUrl = "https://martinsarachaga.com" . $urlImage;

            if ($dimensions === true) {
                $imageSize = getimagesize($localImagePath);
                if ($imageSize) {
                    $width = $imageSize[0];
                    $height = $imageSize[1];
                } else {
                    $width = null;
                    $height = null;
                }

                return [
                    'src' => $fullUrl,
                    'width' => $width,
                    'height' => $height
                ];
            }

            return $fullUrl;
        }

        return null;
    }
}