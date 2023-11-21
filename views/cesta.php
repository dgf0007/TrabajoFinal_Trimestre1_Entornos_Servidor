<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cesta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/estilos.css">
    <?php require '../util/base_de_datos.php' ?>
    <?php require '../util/productos.php' ?>
    <?php require '../util/cestaUsuario.php' ?>

    <!-- Barra de navegación-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Tienda Daniel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="formulario_Inicio_Sesion.php">Inicio de Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formulario_Productos.php">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="formulario_Registro.php">Registro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="paginaPrincipal.php">Página Principal</a>
                    </li>
                    <li>
                        <a class="nav-link" href="PaginaCerrarSesion.php">Cerrar sesión</a>
                    </li>
                    <li>
                        <a class="nav-link" href="cesta.php">Cesta</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</head>
<?php
session_start();
//Comprobar si el usuario no ha iniciado sesión o es un invitado.
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"] == "invitado") {
    header("location: formulario_Inicio_Sesion.php");
    exit; // Detiene la ejecución del código restante
} else {
    $usuario = $_SESSION["usuario"];
    $numeroProductos = 0;

    $sql1 = "SELECT * FROM Cestas WHERE usuario = '$usuario'";
    $resultado = $conexion->query($sql1);

    while ($row = $resultado->fetch_assoc()) {
        $idCesta = $row["idCesta"];
        $precioTotal = $row["precioTotal"];
    }

    $sql2 = "SELECT p.nombreProducto as nombre, p.precio as precio, p.imagen as imagen, pc.cantidad as cantidad FROM productos p JOIN productosCestas pc ON p.idProducto = pc.idProducto WHERE pc.idCesta = $idCesta";
    $resultado2 = $conexion->query($sql2);

    $productos = [];
    while ($row = $resultado2->fetch_assoc()) {
        $nuevoProducto = new productoCesta($row["nombre"], $row["precio"], $row["imagen"], $row["cantidad"]);
        array_push($productos, $nuevoProducto);
        $numeroProductos++;
    }

}
?>

<body>
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-primary">
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Imagen</th>
                <th scope="col">Cantidad</th>
                <th scope="col">Precio Unitario</th>
                <th scope="col">Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td>
                        <?= $producto->nombre ?>
                    </td>
                    <td><img src="<?= $producto->imagen ?>" alt="Imagen Producto" style="width: 100%; height: 100%;"></td>
                    <td>
                        <?= $producto->cantidad ?>
                    </td>
                    <td>
                        <?= $producto->precio ?>
                    </td>
                    <td>
                        <form action="quitarProductosCesta.php" method="POST">
                            <?php
                            $sqlID = "SELECT idProducto FROM productos WHERE nombreProducto = '$producto->nombre'";
                            $resultadoID = $conexion->query($sqlID);
                            while ($fila = $resultadoID->fetch_assoc()) {
                                $idProducto = $fila["idProducto"];
                            }
                            ?>
                            <input type="hidden" name="id" value="<?php echo $idProducto ?>">
                            <?php
                            $precioEliminar = $producto->precio * $producto->cantidad;
                            $precioTotal2 = $precioTotal - $precioEliminar;
                            ?>
                            <input type="hidden" name="precioTotal" value="<?php echo $precioTotal2 ?>">
                            <input type="hidden" name="idCesta" value="<?php echo $idCesta ?>">
                            <input type="submit" value="Eliminar" class="btn btn-danger">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="table-secondary">
            <tr>
                <td colspan="4">Total del carrito</td>
                <td>
                    <?= $precioTotal ?>
                </td>
            </tr>

        </tfoot>

    </table>
    <form method="post" action="realizarPedido.php">
        <input type="hidden" name="precioTotal" value="<?php echo $precioTotal ?>">
        <input type="hidden" name="idCesta" value="<?php echo $idCesta ?>">
        <input type="hidden" name="numeroProductos" value="<?php echo $numeroProductos ?>">
        <input type="submit" name="Enviar" value="Realizar compra " class="btn btn-danger btn-lg">
    </form>

</body>

</html>