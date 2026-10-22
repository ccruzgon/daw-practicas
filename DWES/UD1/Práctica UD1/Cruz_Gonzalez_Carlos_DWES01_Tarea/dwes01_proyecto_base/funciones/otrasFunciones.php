<?php

// ---- Función que crea un array con los nombres únicos del fichero importado-----
function quitarResidentesRepetidos(array $visitas): array
{
    //Almaceno en un nuevo array los nombres de los residentes, extraidos con array_colum que genera un array con los valores de la clave pasada como parámetro
    $nombres = array_column($visitas[2], 'residente'); //visitas[2] es el indice que contiene el array asociativo de residentes validados, obtenido con la función cargarVisitas

    //Con array_unique genero un nuevo array eliminando los elementos duplicados en el array anterior
    $arrayNombresUnicos = array_unique($nombres);

    //Aplico el sort al array para ordenarla alfabéticamente
    sort($arrayNombresUnicos);

    return $arrayNombresUnicos; //devuelvo el array de nombres únicos
}

// ---- Función que crea un array con las cabeceras -----
function obtenerCabeceras(): array
{
    $arrayCabeceras = [];
    foreach (CABECERAS as $nombre) {
        $arrayCabeceras[] = $nombre;
    }
    return $arrayCabeceras;
}

// ---- Función que separa una hora dada y la devuelve en un array donde: 0-hora y 1-minutos -----
function separarHorayMin(string $hora): array
{

    $partesHora = [];

    // Primero compruebo que el separador esté bien escrito (':'). Si está, separo en un array la cadena con la hora pasada por parámetro     
    if (substr($hora, 2, 1) === ':') {
        $partesHora = explode(':', $hora);
    } else return $resultado = [];    // Si no está, devuelvo el array vacío

    // Si no hay dos partes, alguna no tiene dos caracteres o alguna no es numérica, devuelvo un array vacío 
    if (
        count($partesHora) !== 2 ||
        strlen($partesHora[0]) !== 2 ||
        strlen($partesHora[1]) !== 2 ||
        !is_numeric($partesHora[0]) ||
        !is_numeric($partesHora[1])
    ) {
        return $resultado = [];
    } else {
        //Si pasan las validaciones, convierto a int la hora y mins y los asigno a variables, para poder hacer la comprobación de rangos 
        $hora = intval($partesHora[0]);
        $min = intval($partesHora[1]);

        return $resultado = [$hora, $min]; //Finalmente devuelvo un array con la hora y los
    }
}

// ---- Función para comprobar si una hora de fin es posterior una de inicio dada-----
function horaFinEsPosterior(string $inicio, string $fin): bool
{
    if ($fin > $inicio) return true;
    else return false;
}
return false;

// ---- Función que devuelve el número de visitas confirmadas que tiene un residente dado
function contarVisitasPorResidente(array $visitas, string $residente): int
{
    $cuenta = 0;
    foreach ($visitas[2] as $visita) {
        $nombre = $visita['residente'];
        if ($nombre === $residente) $cuenta++;
    }
    return $cuenta;
}
