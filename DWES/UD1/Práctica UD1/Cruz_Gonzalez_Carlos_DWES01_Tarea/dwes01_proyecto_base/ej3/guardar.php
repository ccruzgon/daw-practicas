<?php
//_cargadatos lo necesito para usar las cabeceras
require_once __DIR__ . '/../funciones/_cargadatos.php';
require_once __DIR__ . '/../funciones/validaciones.php';
require_once __DIR__ . '/../funciones/otrasFunciones.php';
$conf = require __DIR__ . '/../conf/horario.php';
require_once __DIR__ . '/../funciones/visitas.php';
require_once __DIR__ . '/../funciones/_guardardatos.php';

// Cargo las visitas
$archivo = __DIR__ . '/../datos/visitas.csv';
$visitas = cargarVisitasCarlosCruzGonzalez($archivo);

// Array con las cabeceras, lo usaré para comprobaciones
$valoresPermitidos = CABECERAS;

// Variables para almacenar las franjas horarias de visitas, las usaré para las comprobaciones
$franjaInicio = implode(',', $conf['horario'][0]);
$franjaTarde = implode(',', $conf['horario'][1]);

// Variables booleanas que usaré para validar datos que quiero mantener en el formulario si son correctos
$esFechaValida = true;
$esObservacionesValido = true;
$esHoraInicioValida = true;
$esHoraFinValida = true;

// Array que contiene los mensajes de error que irán dando las comprobaciones
$errores = [];

// Array donde guardaré los valores recibidos una vez compruebe que son válidos
$datosCorrectos = [];

// Elimino espacios blancos en los string recibidos recorriendo POST mediante sus claves y valores, y reasigando los valores sin espacios a su correspondiente clave
foreach ($_POST as $clave => $valor) {
    //Ya que todos los campos recibidos deben ser string, lo compruebo antes de entrar
    if (is_string($valor)) {
        // Voy rellenando el array de con el mismo valor que había dentro de su clave, pero quitando posibles espacios en blanco
        $_POST[$clave] = trim($valor);
    }
}

//Compruebo que los datos recibidos no son null, y que es un array
if (isset($_POST) && is_array($_POST)) {

    //Compruebo que los campos recibidos coinciden con los campos permitidos (cabeceras)
    if (empty(array_diff(array_keys($_POST), $valoresPermitidos))) {

        // ---------------- COMPROBACIONES ----------------

        // Valido si el residente recibido existe en la lista de residentes (usando la función que devuelve un array con los residentes únicos). Válido -> true
        if (!in_array($_POST['residente'], quitarResidentesRepetidos($visitas)))
            $errores[] = "El residente  \"{$_POST['residente']}\" no está en la lista o no es válido, elija otro";

        // Valido que el vínculo sea uno de los admitidos. Válido -> true
        if (!in_array($_POST['vinculo'], VINCULOS))
            $errores[] = "El vículo escogido no es válido. Debe escoger entre los siguientes: " . implode(', ', VINCULOS);

        // Valido que se haya escrito quien solicita la visita
        if (empty($_POST['solicitante']) || is_null($_POST['solicitante'] || is_numeric($$_POST['solicitante'])))
            $errores[] = "El solicitante {$_POST['solicitante']} no es válido o se ha dejado en blanco. Debe escribirse un nombre.";

        // Valido que la fecha esté bien escrita y esté en el calendario. Válido -> true
        if (!esFechaValidaCarlosCruzGonzalez($_POST['fecha'])) {
            $esFechaValida = false;
            $errores[] = "La fecha {$_POST['fecha']} no es válida: se escribe con el formato dd/mm/aaaa y tiene que ser un día que exista en el calendario";
        }

        // Valido si el texto recibido de observaciones no pasa de 200 caracteres
        if (strlen($_POST['observaciones']) > 200){
            $esObservacionesValido = false;
            $errores[] =  "El campo observaciones sobrepasa el límite de caracteres permitido en " . strlen($_POST['observaciones']) . ". El máximo permitido son 200 caracteres.";
        } 

        // Valido que las dos horas estén bien escritas. Válido -> true
        if (esHoraValidaCarlosCruzGonzalez($_POST['inicio']) && esHoraValidaCarlosCruzGonzalez($_POST['fin'])) {

            // Valido si la hora de fin es posterior a la de principio. afirmativo -> true
            if (horaFinEsPosterior($_POST['inicio'], $_POST['fin'])) {

                // Valido si es está en una franja horaria válida. afirmativo -> true
                if (!estaEnUnaFranjaCarlosCruzGonzalez($conf, $_POST['inicio'], $_POST['fin']))
                    $errores[] = "La franja horaria solicitada de {$_POST['inicio']} a {$_POST['fin']} no es válida. Las horas deben estar dentro de las siguiente franjas:  
                mañana: " . $franjaInicio . ". Tarde: " . $franjaTarde . ".";

                // Valido si la duración es mayor o igual al limite permitido (30). Válida -> true
                if (!esDuracionValidaCarlosCruzGonzalez($conf, $_POST['inicio'], $_POST['fin']))
                    $errores[] = "La duración de la visita debe durar como mínimo " . $conf['duracion_minima'] . " minutos.";

                // Para comprobar el solapamiento, primero compruebo si el residente existe y si la fecha dada es válida
                if (in_array($_POST['residente'], quitarResidentesRepetidos($visitas), true) && esFechaValidaCarlosCruzGonzalez($_POST['fecha'])) {

                    // Y luego compruebo si se solapa. afirmativo -> true
                    if (solapaConVisitasCarlosCruzGonzalez($visitas, $_POST['fecha'], $_POST['inicio'], $_POST['fin']))
                        $errores[] = "Ya hay otra visita concertada con {$_POST['residente']} el día {$_POST['fecha']} que se solapa con la franja de {$_POST['inicio']} a {$_POST['fin']}.";
                }
            } else $errores[] =  "La hora indicada de fin ({$_POST['fin']}), debe ser posterior a la hora indicada de inicio ({$_POST['inicio']}).";
        } else {
            //Si las horas no cumplen con el formato válido, modifico su variable correspondiente a false para resetar el valor del formulario y que se vuelve a introducir, y añado el error.
            if (!esHoraValidaCarlosCruzGonzalez($_POST['inicio'])) {
                $esHoraInicioValida = false;
                $errores[] = "La hora de inicio introducida ({$_POST['inicio']}) no cumplen el formato obligatorio (hh:mm).";
            }

            if (!esHoraValidaCarlosCruzGonzalez($_POST['fin'])) {
                $esHoraFinValida = false;
                $errores[] = "La hora de fin introducida ({$_POST['fin']}) no cumplen el formato obligatorio (hh:mm).";
            }
        }
    }
}

/* 
Si errores[] está vacio significa que los datos insertados han pasado las validaciones, asi que añado los datos validados al array que usaré para cargar la visita al fichero. 
Los añado en el orden correcto de inserción, ya que el formulario intercambiaba el orden en dos valores.
*/
if (empty($errores)) {
    $datosCorrectos['residente'] = $_POST['residente'];
    $datosCorrectos['solicitante'] = $_POST['solicitante'];
    $datosCorrectos['vinculo'] = $_POST['vinculo'];
    $datosCorrectos['fecha'] = $_POST['fecha'];
    $datosCorrectos['inicio'] = $_POST['inicio'];
    $datosCorrectos['fin'] = $_POST['fin'];
    $datosCorrectos['observaciones'] = $_POST['observaciones'];

    // Extraigo los datos en variables con la clave, para comodidad a la hora de acceder a ellos
    extract($datosCorrectos);

    // Cuento las visitas del residente seleccionado, para sumarle +1 si la insercción es correcta y mostrar la cantidad al usuario
    $cuentaVisitas = contarVisitasPorResidente($visitas, $residente);

    if (guardarVisitaCSV($archivo, $datosCorrectos)) {
        $cuentaVisitas++;
        echo '<h1>Resultado de la solicitud</h1>';
        echo '<p id="resultado"> Queda concertada la visita a ' . $residente . ' el ' . $fecha . ' de ' . $inicio . ' 
            a ' . $fin . ', solicitada por ' . $solicitante . '. Ahora hay ' . $cuentaVisitas . ' visitas concertadas.</p>';
    } else echo 'No se ha podido solicitar la visita.';

    //Si hay errores, hago un require para mandarlos a la página del formulario
} else {
    require_once __DIR__ . '/index.php';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de la solicitud - Tarea unidad 1</title>
</head>

<body>
    <a href="index.php">Solicitar otra visita</a> |
    <a href="../ej2/index.php">Ver las visitas concertadas</a> |
    <a href="../index.php">Ir a la página principal</a>
    <hr>
    <footer id="autor">"Carlos Cruz Gonzalez - Ejercicio 3 - Tarea de la unidad 1"</footer>
</body>

</html>