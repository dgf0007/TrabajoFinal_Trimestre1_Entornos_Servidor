<?php
//Para eliminar el contenido de la cesta
session_start();
require "../util/base_de_datos.php";
//Procedo a traerme todos los elementos a eliminar.
$id = $_POST["id"];
$precio = $_POST["precioTotal"];
$idCesta = $_POST["idCesta"];
$usuario = $_POST["usuario"];

//Me traigo la cantidad de la tabla productosCestas
$productosCestas = "SELECT cantidad FROM productoscestas WHERE idProducto = $id";
$cantidad = $conexion -> query($productosCestas) ->fetch_assoc()["cantidad"];

//Elimino  el contenido que tenga la tabla productosCestas
$borrarfila = "DELETE FROM productosCestas WHERE idProducto = $id";
$conexion->query($borrarfila);

//Modifico la tabla Cestas con los nuevos datos
$cestaVacia = "UPDATE cestas SET precioTotal = '$precio' WHERE idCesta = '$idCesta'";
$conexion->query($cestaVacia);

$cestaProductos = "UPDATE productos SET cantidad = cantidad + $cantidad WHERE idProducto = $id";
$conexion -> query($cestaProductos);

//Por último indico a que archivo se le aplica
header ("location: cesta.php")
?>
