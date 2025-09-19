<?php
class DateHelper
{
    /**
     * Formatea una fecha al formato español
     * @param string $fecha Fecha en formato 'Y-m-d H:i:s'
     * @return array Array con la fecha original y la fecha formateada
     */
    public function formatearFechaEspanol($fecha)
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
            "d 'de' MMMM 'de' y"
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
}