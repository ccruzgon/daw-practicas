<?php
require_once __DIR__ . '/../funciones/visitas.php';
require_once __DIR__ . '/../funciones/otrasFunciones.php';
require_once __DIR__ . '/../funciones/_cargadatos.php';
$archivo = __DIR__ . '/../datos/visitas.csv';

//Cargo las visitas
$visitas = cargarVisitasCarlosCruzGonzalez($archivo);

//Quito los nombres duplicados haciendo uso de la función
$nombresResidentes = quitarResidentesRepetidos($visitas);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar una visita - Tarea unidad 1</title>
</head>

<body>
    <h1>Solicitar una visita</h1>

    <a href="../ej2/index.php">Ver las visitas concertadas</a> |
    <a href="../index.php">Ir a la página principal</a>
    <?php
    // Si el array de errores no está vacio, recorro mostrando cada valor en un <li>
    if (!empty($errores)) {
        echo '<h2>No se ha podido concertar la visita</h2>';
        echo '<ul id="errores">';
        foreach ($errores as $error) {
            echo ' <li>' . $error . '</li>';
        }
        echo '</ul>';
    }
    ?>

    <form action="guardar.php" method="post">
        <p>
            <label for="residente">Residente al que se visita:</label><br>
            <select id="residente" name="residente">
                <option value="">-- Selecciona un residente --</option>
                <option value="residente-de-pruebas-no-existente">Residente de pruebas (no existente)</option>
                <!-- 
                En las validaciones se vuelven a insertar los datos recogidos por post, porque al devolver los errores esos campos han pasado la validación hecha en guardar.php-->

                //Recorro el array de nombres únicos
                <?php foreach ($nombresResidentes as $nombre) :

                    /*En una variable comparo si el nombre escogido por el usuario está incluido en el array de nombre únicos, 
                    si es lo marco como selected para que conserve el valor, y si no lo dejo vacio
                    */
                    $selected = $_POST['residente'] === $nombre ? 'selected' : ''; ?>

                    //Genero la etiqueta HTML donde value es el nombre del residente y 'selected' aparece solo si hay coincidencia
                    <option value="<?= htmlentities($nombre) ?>" <?= $selected ?>><?= htmlentities($nombre) ?></option>
                <?php endforeach ?>

            </select>
        </p>

        <p>
            <label for="vinculo">Vínculo con el residente:</label><br>
            <select id="vinculo" name="vinculo">
                <option value="">-- Selecciona el vínculo --</option>
                <option value="vinculo-erroneo-de-pruebas">Valor erróneo de vínculo (para pruebas) </option>

                //recorro con un foreach el array de constantes vínculos
                <?php foreach (VINCULOS as $vinculo) :
                    //En la variable select vuelve buscar que el vínculo escogido esté en el array de vínculos válidos
                    $selected = $_POST['vinculo'] === $vinculo ? 'selected' : ''; ?>

                    <option value="<?= htmlentities($vinculo) ?>" <?= $selected ?>><?= htmlentities($vinculo) ?></option>
                <?php endforeach ?>
            </select>
        </p>

        <p>
            <label for="solicitante">Quién solicita la visita:</label><br>
            <!--En este caso inserto los datos recibidos comprobando si los hay mediante operador ternario: inserta el dato de solicitante, y si no, lo dejas vacio-->
            <input type="text" id="solicitante" name="solicitante" size="40" value="<?= htmlentities($_POST['solicitante'] ?? '') ?>">
        </p>

        <p>
            <label for="fecha">Día de la visita (dd/mm/aaaa):</label><br>
            <!--
            Para validar la fecha uso un operador ternario con el resultado de validar si la variable $esFechaValida es válida (ha pasado la validación en guardar.php) o no, 
            si la variable no existe uso un false para evitar un error la primera vez que carga el formulario, 
            si existe y es true: la fecha será válida asi que mantengo la fecha introducida por el usuario, 
            si es false: dejo el campo en blanco porque el valor introducido será inválido    
                -->
            <input type="text" id="fecha" name="fecha" size="12" value="<?= htmlentities($esFechaValida ?? false) ? ($_POST['fecha']) : '' ?>">
        </p>

        <p>
            <label for="inicio">Hora de entrada (hh:mm):</label><br>
                <!--Con las horas uso de nuevo un operador ternario para comprobar si la variable es true o false en función de si ha pasado la validación o no en guardar.php, 
                si la pasa, muestro el dato introducido, si no, muestro el cuadro en blanco de nuevo
                -->
            <input type="text" id="inicio" name="inicio" size="6" value="<?= htmlentities($esHoraInicioValida ?? false) ? ($_POST['inicio']) : '' ?>">
        </p>

        <p>
            <label for="fin">Hora de salida (hh:mm):</label><br>            
            <input type="text" id="fin" name="fin" size="6" value="<?= htmlentities($esHoraFinValida ?? false) ? ($_POST['fin']) : '' ?>">
        </p>

        <p>
            <label for="observaciones">Observaciones (opcional):</label><br>
            <textarea id="observaciones" name="observaciones" rows="3" cols="60"><?= htmlentities($esObservacionesValido ?? false) ? ($_POST['observaciones']) : '' ?></textarea>
        </p>
        <p><button type="submit" id="enviar">Registrar la visita</button></p>
    </form>

    <hr>
    <footer id="autor">"Carlos Cruz Gonzalez - Ejercicio 3 - Tarea de la unidad 1"</footer>
</body>

</html>