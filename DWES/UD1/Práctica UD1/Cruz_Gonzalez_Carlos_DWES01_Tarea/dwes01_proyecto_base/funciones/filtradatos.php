<?php

// ---- Función que filtra las visitas para los residentes seleccionados en el formulario, recibe los residentes seleccionados y el array de visitas ----
function filtrarVisitasCarlosCruzGonzalez(array $residentes, array &$visitas): int
{
    //Para comprobar si los residentes son válidos, creo un array con los nombres únicos de cada uno
    $residentesBuenos = quitarResidentesRepetidos($visitas);

    //Contadores
    $residentesEncontrados = 0;
    $visitasEliminadas = 0;

    //Valido que los residentes recibidos son válidos y existen
    if (sonResidentesCorrectosCarlosCruzGonzalez($residentesBuenos, $residentes)) {

        //Compruebo si hay elementos en el array de residentes recibidos, ya que la opción 0 contempla que no se seleccione nada   
        if (count($residentes) === 0) {
            //Si no hay selección, dejo el contador igual        
            $residentesEncontrados;
        } else {

            /*Con un foreach recorro el indice de $visitas donde está el array con los datos de las visitas (2),
            obtengo la clave de cada visita, su índice, para poder modificar el elemento entero de esa posición
            y por último $visita obtiene una visita completa en cada vuelta del bucle, lo usaré para acceder al valor 'residente' de cada elemento recorrido
            */
            foreach ($visitas[2] as $clave => $visita) {

                //Con in_array voy a comprobar que el nombre del residente de $visitas esté en el array de residentes seleccionados, accediendo al valor mediante su clave
                if (in_array($visita['residente'], $residentes['residentes'], true)) {

                    $residentesEncontrados++;
                } else {
                    //Si no está, borro el indice dentro del array de visitas que pertece a la clave
                    unset($visitas[2][$clave]);
                    $visitasEliminadas++;
                }
            }
        }
    }
    return $visitasEliminadas;
}
