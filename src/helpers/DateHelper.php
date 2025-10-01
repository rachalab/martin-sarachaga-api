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
                "format" => null,
                "short" => null
            ];
        }

        $timestamp = strtotime($fecha);

        // Formato completo: "14 de mayo de 2025"
        $formatterFull = new IntlDateFormatter(
            'es_ES',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            null,
            null,
            "d 'de' MMMM 'de' y"
        );
        $formatoCompleto = $formatterFull->format($timestamp);
        $formatoCompleto = mb_convert_case(mb_substr($formatoCompleto, 0, 1, 'UTF-8'), MB_CASE_UPPER, 'UTF-8') .
                        mb_substr($formatoCompleto, 1, null, 'UTF-8');

        // Formato mobile: "14 de may de 2025"
        $formatterMobile = new IntlDateFormatter(
            'es_ES',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            null,
            null,
            "d MMM y"
        );
        $formatoMobile = $formatterMobile->format($timestamp);
        $formatoMobile = mb_convert_case(mb_substr($formatoMobile, 0, 1, 'UTF-8'), MB_CASE_UPPER, 'UTF-8') .
                        mb_substr($formatoMobile, 1, null, 'UTF-8');

        return [
            "system" => $fecha,
            "format" => $formatoCompleto,
            "short" => $formatoMobile
        ];
    }
}