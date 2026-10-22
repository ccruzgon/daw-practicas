<?php
require_once __DIR__ . '/otrasFunciones.php';
require_once __Dir__ . '/filtradatos.php';


// ----- Función para comprobar si una fecha pasada por parámetro es válida -----
function esFechaValidaCarlosCruzGonzalez(string $fecha): bool
{
    $expresion = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})/'; //Expresión para comparar: ([0-9]{2}) -> dia   ([0-9]{2}) -> mes  ([0-9]{4}) -> año
    $partes = []; //Array de partes que se rellenará con preg_match posteriormente

    //con preg_match compruebo si $fecha cumple el patron. Si las partes coinciden, las va guardando en el array $partes (cada parte en el patrón es separada por '( )')
    if (preg_match($expresion, $fecha, $partes)) {

        //Voy guardado las coincidendias en los arrays por el orden que quiero: dd/mm/aaaa. Y paso las cantidades a int para compararlas posteriormente
        $dia = intval($partes[1]);
        $mes = intval($partes[2]);
        $anio = intval($partes[3]);

        //Comparo los datos de la fecha con chk_date, función que comprueba si la fecha pasada existe en el calendario. El orden de parámetros de la función siempre es mes, dia, año
        if (checkdate($mes, $dia, $anio)) {
            return true;
        } else {
            return false;
        }
    } else return false; // devuelvo false también si la primera comprobación de la expresión con la fecha es incorrecta
}

// ----- Función para comprobar si el formato de una hora dada es válido: hh:mm -----
function esHoraValidaCarlosCruzGonzalez(string $hora): bool
{   
    
    //La función separa la hora y los minutos en un array donde el indice 0 -> hora y 1 -> Minutos
    $horaSeparada = separarHorayMin($hora);

    // Si la variable no contiene dos indices (hora y minutos), devuelvo false
    if (count($horaSeparada) !== 2) return false;       

    //Si la hora es mayor o igual a 0 Y menor o igual a 23, la hora es válida, paso a comprobar los minutos
    if ($horaSeparada[0] >= 0 && $horaSeparada[0] <= 23) {

        //Si los minutos son mayores o iguales a 0 Y menores o iguales a 59, los minutos son válidos. Por lo tanto la hora cumple el formato pedido
        if ($horaSeparada[1] >= 0 && $horaSeparada[1] <= 59) {
            return true;
        } else return false;
        
    } else return false; //en cualquier otro caso, la hora es inválida, devuelvo false
}

// ----- Función para validar si los residentes marcados en el formulario existen, recibe un array con los nombres de los residente únicos y $_POST -----
function sonResidentesCorrectosCarlosCruzGonzalez(array $residentesBuenos, array $residentesRecibidos): bool
{
    
    //Creo un array con los datos recibidos por POST, en este caso solo de los residentes
    $residentes = $residentesRecibidos['residentes'] ?? []; // Si no hay datos en POST, creo la variable con un array vacio ya que es una casuistica a contemplar (ninguno seleccionado)      

    //Si la variable existe y no es null Y además si es un array, entro
    if (isset($residentes) && is_array($residentesRecibidos)) {

        /* array_diff crea un array con los valores del array recibido que no estén en el array de residentes, si es el caso
        si ese array esta vacio, significa que todos los recibidos están entre los permitidos (quita del primero todos los que están en el segundo)
        Si no lo está, el array recibido tiene algún valor que no está en el array de residentes, asi que no sería un valor válido
        */

        if (empty(array_diff($residentes, $residentesBuenos))) {
            return true;
        } else return false;
    } else return true;
}

// ----- Función que valida el rango válido de hora de visitas, recibe la configuracion con las horas válidas, y las horas de inicio y fin -----
function esDuracionValidaCarlosCruzGonzalez(array $conf, string $hora_inicio, string $hora_fin): bool
{
    //Si la hora tiene un formato válido, entro
    if (esHoraValidaCarlosCruzGonzalez($hora_inicio) && esHoraValidaCarlosCruzGonzalez($hora_fin)) {

        $horaInicioSeparada = separarHorayMin($hora_inicio);
        $horaFinSeparada = separarHorayMin($hora_fin);

        //Convierto la hora inicial a minutos (hora * 60 + minutos)
        $conversionHoraInicial = ($horaInicioSeparada[0] * 60) + $horaInicioSeparada[1];

        //Convierto la hora de fin a minutos
        $conversionHoraFin = ($horaFinSeparada[0] * 60) + $horaFinSeparada[1];

        //Calculo la duración de la visita restando: hora final - hora inicial
        $duracionTotal = $conversionHoraFin - $conversionHoraInicial;

        //Si la duración total es mayor o igual que el minimo permitido (30), valido
        if ($duracionTotal >= $conf['duracion_minima']) {
            return true;
        } else return false;
    }
    return false;
}

// ----- Función que comprueba que la visita esté en una franja horaria permitida, recibe la configuración con las horas válidas, y las horas de inicio y fin-----
function estaEnUnaFranjaCarlosCruzGonzalez(array $conf, string $hora_inicio, string $hora_fin): bool
{

    // Separo en dos arrays las franjas horarias (arrays dentro la clave 'horario' de conf)
    $franjaManana = $conf['horario'][0];
    $franjaTarde = $conf['horario'][1];

    // Compruebo si el formato de hora es válido
    if (esHoraValidaCarlosCruzGonzalez($hora_inicio) && esHoraValidaCarlosCruzGonzalez($hora_fin)) {

        // Compruebo si la hora de inicio dada es igual a la hora de cierre en ambas franjas, en caso correcto devuelvo false (no válido)
        if ($hora_inicio === $franjaManana[1] || $hora_inicio === $franjaTarde[1]) {            
            return false;
        }

        // Compruebo si la hora dada está en la franja de mañana
        if ($hora_inicio >= $franjaManana[0] && $hora_fin <= $franjaManana[1]) {            
            return true;
        }

        // Compruebo si la hora dada está en la franja de tarde
        if ($hora_inicio >= $franjaTarde[0] && $hora_fin <= $franjaTarde[1]) {            
            return true;
        }
    } else return false; // Si no ha pasado el filtro inicial

    return false; // Si no ha pasado ningún filtro
}

// ----- Función que comprueba si una visita se sopala con otra, recibe el array de visitas válidas, fecha de la visita, hora de inicio y hora de fin -----
function solapaConVisitasCarlosCruzGonzalez(array $visitas, string $fecha, string $hora_inicio, string $hora_fin): bool
{
    /* 
    En este formulario se envia el dato directamente como un string, 
    pero la función de filtrado necesita un array asociativo con el nombre del campo como clave y como valor otro array con los valores
    por ese motivo creo un array con la clave 'residentes' (la que utiliza la función para hacer las validaciones)
    y de valor asociado creo otro array que contiene el valor recogido con post del formulario (name='residente') 
    */

    $residenteRecibido =  ['residentes' => [$_POST['residente']]];

    // Si la función tras filtrar da un entero mayor que 0, el residente tiene visitas y hay que seguir comprobando
    if (filtrarVisitasCarlosCruzGonzalez($residenteRecibido, $visitas) > 0) {
        extract($_POST);

        /* 
        Recorro el array de visitas del residente seleccionado (la función eliminaba los residentes que no coincidian porque se pasaba $visitas por referencia) 
        para buscar coincidendias con el residente solicitado en el formulario 
        */
        foreach ($visitas[2] as $valor) {

            //Si la fecha dada coincide con alguna de las que tiene el residente, entro a validar la hora de la visita
            if ($fecha === $valor['fecha']) {
                
                //Si la nueva visita empieza antes de que termine la existente, y termina después de que empiece la existente, hay un solapamiento
                if ($hora_inicio < $valor['fin'] && $hora_fin > $valor['inicio']) {
                    return true;
                } else return false;
            } 
        }
    }
    return false;
}
