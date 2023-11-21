<?php
//Para almacenar la ip
$_servidor = 'localhost';
$_usuario = 'root';
$_contrasena = 'medac';
$_base_de_Datos = 'db_tienda';

//Para enlazar con la base de datos SQL
$conexion = new Mysqli($_servidor, $_usuario, $_contrasena, $_base_de_Datos)
    or die('Error de conexión');
?>