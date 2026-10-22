<?php

// ---------------------------------------------------------------------------
// NO MODIFIQUES ESTE ARCHIVO. 
// ---------------------------------------------------------------------------

function guardarVisitaCSV(string $archivo, array $visita): bool
{
    // Si la última línea se quedó sin salto, la fila nueva se pegaría a ella.
    if (is_file($archivo) && filesize($archivo) > 0) {
        $ultimo = file_get_contents($archivo, false, null, filesize($archivo) - 1, 1);
        if ($ultimo !== "\n") {
            file_put_contents($archivo, "\n", FILE_APPEND);
        }
    }
    $f = @fopen($archivo, 'a');
    if ($f === false) {
        return false;
    }
    $escrito = fputcsv($f, array_values($visita), ',', '"', '');
    fclose($f);
    return $escrito !== false;
}
