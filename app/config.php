<?php

//Definir las constantes para la conexion a la base de datos
define('SERVIDOR','localhost');
define('PUERTO','3306');
define('USUARIO','root');
define('PASSWORD','digital12');
define('BD','sehuacho');

//Definir la variable $servidor con los datos de la conexion
$servidor = "mysql:host=".SERVIDOR.";port=".PUERTO.";dbname=".BD;

try{
    //Realizar la conexion a la base de datos
    $pdo = new PDO($servidor,USUARIO,PASSWORD,array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES utf8"));
    /* echo "La conexión a la base de datos fue con exito"; */
}catch (PDOException $e){
    //print_r($e);
    echo "Error al conectar a la base de datos";
}

//Definir la variable $URL con la ruta del sistema
$URL = "https://tufibra.ddns.net/sehuacho/";

//Definir la zona horaria
date_default_timezone_set("America/lima");
//Definir la fecha y hora actual
$fecha = date('Y-m-d');
$hora = date('H:i:s');
$fechaHora = $fecha.' '.$hora;





