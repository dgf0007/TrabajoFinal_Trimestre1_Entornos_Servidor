<?php
require '../util/base_de_datos.php';
session_start();
if (!isset($_SESSION["usuario"]) || empty($_POST["precioTotal"]) || empty($_POST["idCesta"]) || empty($_POST["numeroProductos"])) {
    // Redireccionar si algún dato necesario no está presente
    header("location: formulario_Inicio_Sesion.php");
    exit(); // Terminar el script después de redirigir
}

// Recibir y validar datos
$usuario = $_SESSION["usuario"];
$precioTotal = floatval($_POST["precioTotal"]); // Convertir a decimal
$idCesta = $_POST["idCesta"];
$fechaActual = date("Y/m/d");
$numeroProductos = intval($_POST["numeroProductos"]); // Convertir a entero

// Consulta para insertar en la tabla Pedidos
$pedidoCesta = "INSERT INTO pedidos (usuario, precioTotal, fechaPedido) VALUES ('$usuario', '$precioTotal', '$fechaActual')";
$conexion->query($pedidoCesta);

// Consulta para obtener el idPedido de la tabla Pedidos
$idPedidoQuery = "SELECT idPedido FROM pedidos WHERE usuario = '$usuario' AND precioTotal = '$precioTotal' AND fechaPedido = '$fechaActual'";
$resultadoPedido = $conexion->query($idPedidoQuery);
$idPedido = ($resultadoPedido->num_rows > 0) ? $resultadoPedido->fetch_assoc()["idPedido"] : null;

if ($idPedido) {
    // Consulta para obtener el idProducto y cantidad de productosCestas
    $comboCesta = "SELECT idProducto, cantidad FROM productoscestas WHERE idCesta = '$idCesta'";
    $resCombo = $conexion->query($comboCesta);

    // Crear arrays para almacenar los datos necesarios
    $idProducto = [];
    $cantidad = [];
    while ($row = $resCombo->fetch_assoc()) {
        $idProducto[] = $row['idProducto'];
        $cantidad[] = $row['cantidad'];
    }

    for ($i = 0; $i < $numeroProductos; $i++) {
        $separar = $i + 1;
        $precioQuery = "SELECT precio FROM productos WHERE idProducto = '$idProducto[$i]'";
        $precioResultado = $conexion->query($precioQuery);
        $sacarPrecio = ($precioResultado->num_rows > 0) ? $precioResultado->fetch_assoc()["precio"] : null;

        if ($sacarPrecio !== null) {
            $lineaPedido = "INSERT INTO lineaspedidos VALUES ('$separar', '$idProducto[$i]', '$idPedido', '$sacarPrecio', '$cantidad[$i]')";
            $conexion->query($lineaPedido);
        }
    }

    // Modificar la tabla cestas
    $contador = 0;
    while ($contador < $numeroProductos) {
        $borrarProductos = "DELETE FROM productoscestas WHERE idProducto = '{$idProducto[$contador]}'";
        $conexion->query($borrarProductos);
        $contador++;
    }

    $cambiarPrecio = "UPDATE cestas SET precioTotal = '0.0' WHERE idCesta = '$idCesta'";
    $conexion->query($cambiarPrecio);
}

// Redireccionar a la página principal
header("location:paginaPrincipal.php");
exit();

?>