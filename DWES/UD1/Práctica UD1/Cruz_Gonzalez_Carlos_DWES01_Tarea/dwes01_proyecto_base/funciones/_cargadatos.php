<?php

// ---------------------------------------------------------------------------
// NO MODIFIQUES ESTE ARCHIVO.
// ---------------------------------------------------------------------------

define('CABECERAS', [
    'residente',
    'solicitante',
    'vinculo',
    'fecha',
    'inicio',
    'fin',
    'observaciones'
]);

define('VINCULOS', ['PARIENTE', 'AMISTAD']);

function cargarCSV(string $archivo): array
{
    $filas = [];
    $f = @fopen($archivo, 'r');
    if ($f === false) {
        return $filas;
    }
    while (($fila = fgetcsv($f, 0, ',', '"', '')) !== false) {
        if (count($fila) === 1 && trim((string)$fila[0]) === '') {
            continue;
        }
        $filas[] = $fila;
    }
    fclose($f);
    return $filas;
}
