<?php
//Primero importo los ficheros que voy a necesitar
require_once __DIR__ . '/../funciones/visitas.php';
require_once __DIR__ . '/../funciones/otrasFunciones.php';
require_once __DIR__ . '/../funciones/filtradatos.php';
require_once __DIR__.'/../funciones/validaciones.php';
$archivo = __DIR__ . '/../datos/visitas.csv';

//Cargo las visitas filtradas del CSV a una variable mediante la función
$visitas = cargarVisitasCarlosCruzGonzalez($archivo);

//Filtro la lista de visitas para obtener los nombres únicos de residentes
$residentesBuenos = quitarResidentesRepetidos($visitas);

//Filtro para las visitas en función de la selección del usuario en el formulario
$visitasEliminadas = filtrarVisitasCarlosCruzGonzalez($_POST, $visitas);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de visitas - Tarea unidad 1</title>
</head>

<body>
    <h1>Visitas concertadas</h1>

    <a href="index.php">Quitar el filtro</a> |
    <a href="../ej3/index.php">Solicitar una visita</a> |
    <a href="../index.php">Ir a la página principal</a>

    <p>
        <!--Añado los valores de campos correctos y campos incorrectos llamando al indice del array que contiene cada uno.-->
        Se han cargado <span id="filas_correctas"><?php echo $visitas[0] ?> </span> visitas del archivo
        y se han descartado <span id="filas_incorrectas"><?php echo $visitas[1] ?></span> filas
        porque no cumplen el formato.
    </p>
    <?php
    //Si la comprobación de residentes correctos seleccionados da false (se ha seleccionado un valor incorrecto como 'residente no válido') muestro el mensaje de alerta
    if (!sonResidentesCorrectosCarlosCruzGonzalez($residentesBuenos, $_POST)) {
        echo '<div id="aviso">';
        echo '<h3 style="color:red">¡Atención!</h3>';
        echo '<p><strong style="color:red">"Se han seleccionado residentes que no están en el CSV"</strong></p>';
        echo '</div>';
    }
    ?>
    <h2>Filtrar por residentes</h2>
    <h3>Selecciona los residentes por los que desees filtrar</h3>
    <form id="form_filtro_residentes" action="index.php" method="POST">
        <?php

        //Para darle el formato de dos selectores por fila, creo una etiqueta div que contendrá los botones, con un ancho de 620px por línea ya que cada botón ocupará 300px de la línea 
        echo '<div style=width:620px>';

        //Variable para almacenar en un array los valores escogidos. Si no hay valores creo un array vacio. (La usaré para comprobar valores ya marcados y aplicar 'checked')
        $valoresEscogidos = $_POST['residentes'] ??  [];

        //Con un foreach recorro el array con los nombres únicos, y voy creando un botón del formulario por cada nombre
        foreach ($residentesBuenos as $nombre) {  

            /*La variable checked será true si el nombre recorrido está dentro del array de valores seleccionados por el usuario
            Si está, se marca como 'checked', y si no, se conserva vacía */         
            $checked = in_array($nombre, $valoresEscogidos) ? 'checked' : '';
            echo '<label style="display:inline-block;width:300px;">';
            echo '<input type="checkbox" name="residentes[]" value="'.htmlentities($nombre).'" '.$checked.'>'.htmlentities($nombre);
            echo '</label>';
        }
        echo '<label style="display:inline-block;width:300px;">';
        echo '<input id="residente_no_valido" type="checkbox" name= "residentes[]" value="Residente no válido" > Residente no válido';
        echo '</label>';
        echo '</div>';
        echo '<br>';
        echo '<button type="submit" id="boton_filtrar">Filtrar</button>';
        ?>
    </form>
    <?php
    //Muestro el mensaje del números de residentes eliminados si la función de filtrado me devuelve algún valor
    if ($visitasEliminadas > 0):

        //En el texto muestro la variable con el resultado de la función, que es el número de residentes eliminados
        echo '<H3> Se han filtrado los datos por los residentes seleccionados. Se dejan de mostrar <span id="recuentoEliminadas"> ' . $visitasEliminadas . ' </span> visitas.</H3>';
    endif ?>

    <table id="tabla_visitas" border="1">
        <thead>
            <tr>
                <th id="th_residente">Residente</th>
                <th id="th_solicitante">Solicitante</th>
                <th id="th_vinculo">Vínculo</th>
                <th id="th_fecha">Fecha</th>
                <th id="th_desde">Desde</th>
                <th id="th_hasta">Hasta</th>
                <th id="th_observaciones">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            //recorro las filas del array de visitas (indice 2 del array creado al importar el csv)           
            foreach ($visitas[2] as $valor) {

                /*
            Al haber creado un array asociativo con las cabeceras(key) unidas a su respectivo dato del CSV (valor), 
            con extract, extraigo cada clave con su valor en variables que llevan de nombre el de la propia clave (que son las cabeceras)
            */
                extract($valor);

                echo '<tr>'; //abre una fila

                //Los datos son pasados previamente por htmlentities para evitar problemas de seguridad, sobre todo en el campo observaciones (toma el codigo HTML como literal)
                echo '<td>' . htmlentities($residente) . '</td>'; //celda para el valor de la clave residente
                echo '<td>' . htmlentities($solicitante) . '</td>'; //celda para el valor de la clave solicitante
                echo '<td>' . htmlentities($vinculo) . '</td>'; //celda para el valor de la clave vinculo
                echo '<td>' . htmlentities($fecha) . '</td>'; //celda para el valor de la clave fecha
                echo '<td>' . htmlentities($inicio) . '</td>'; //celda para el valor de la clave inicio
                echo '<td>' . htmlentities($fin) . '</td>'; //celda para el valor de la clave fin
                echo '<td>' . htmlentities($observaciones) . '</td>'; //celda para el valor de la clave observaciones
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <hr>
    <footer id="author">"Carlos Cruz Gonzalez - Ejercicio 2 - Tarea de la unidad 1"></footer>
</body>
</html>