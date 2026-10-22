<?php

//Importo el archivo para cargar la  funcion (require guarda y ejecuta en el momento)
require_once __DIR__ . '/_cargadatos.php';

//Importo el archivo validaciones son las funciones para validar la hora y la fecha
require_once __DIR__ . '/validaciones.php';

// Función que recibe el archivo CSV y filtra las visitas para seguir el formato válido respetando las cabeceras asigandas para cada dato.  Devuelve 3 arrays (correctos, descartados, array de visitas)
function cargarVisitasCarlosCruzGonzalez(string $archivo): array
{
    //Creo las variables con la información que la función irá contabilizando
    $cantidadCorrecta = 0;
    $cantidadDescartada = 0;

    //Este array guardará al final los campos del CSV que cumplan todas las validaciones 
    $visitasCorrectas = [];     

    //utilizo la funcion cargar datos para cargarlos en la varible datos
    $datos = cargarCSV($archivo);    
    
    //recorro el array de datos fila a fila    
    foreach ($datos as $fila) {        

        //si el numero de elementos de la fila es igual al num total de cabeceras, valido y sigo
        if (count($fila) === count(CABECERAS)) {      

            //Si el vinculo de la fila está incluido en el array de vinculos, valido
            if (in_array(($fila[2]), VINCULOS, true)) { //Compruebo el valor 2 de la fila (vinculo) con el array de vinculos
                
                //Creo variables con los datos que necesito validar
                $fecha = $fila[3];
                $horaEntrada = $fila[4];
                $horaSalida = $fila[5];

                //Si la hora de entrada y de salida cumple la validación de la función, entra a la siguiente validación 
                if (esHoraValidaCarlosCruzGonzalez($horaEntrada) && esHoraValidaCarlosCruzGonzalez($horaSalida)) {

                    //Si la hora de salida es menor o igual que a la de entrada, se descarta y sumo +1 al contador de descartados
                    if ($horaSalida <= $horaEntrada) {
                        $cantidadDescartada += 1;
                        //Si no, valido por último el formato de la fecha con la función
                    } elseif (esFechaValidaCarlosCruzGonzalez($fecha)) {

                        // Si es válida, sumo +1 al contador de valores válidos y añado el campo al array de visitas correctas, asignando su cabecera correspondiente con array_combine
                        $cantidadCorrecta += 1;
                        
                        /* Creo el array que añadiré al array de salida de la función, 
                        usando array_combine para crear un array con las cabeceras como claves, y como valor, los datos de cada fila emparejados por posición (asi cabecera1 -> valor 1, etc)                       
                        */
                        $visitasCorrectas[] = array_combine(CABECERAS, $fila);
                    } else $cantidadDescartada +=1;
                } else $cantidadDescartada += 1;
            } else $cantidadDescartada  += 1;
        } else $cantidadDescartada  += 1;
    }
    return $arrayResultadosValidos = [$cantidadCorrecta, $cantidadDescartada, $visitasCorrectas]; //Devuelvo el array compuesto con los datos solicitados
}
