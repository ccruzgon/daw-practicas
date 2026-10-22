<?php


//Variable donde almaceno el csv, para añadirlo como parámetro de la función (require solo guarda la ruta como texto)
$archivo = __DIR__ . '/datos/visitas.csv';

require_once __DIR__ . '/funciones/visitas.php';

$resultado = cargarVisitasCarlosCruzGonzalez($archivo);


$residentesRecibidos = $_POST['residentes'];

var_dump($residentesRecibidos);





