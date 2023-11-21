<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/estilos.css">
    <?php require '../util/base_de_datos.php' ?>
    <?php require '../util/productos.php' ?>
    <?php require '../util/depurar.php' ?>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        var table = document.querySelector('.center-table');
        table.classList.add('show');
    });
</script>

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

<body>

    <?php
    session_start();
    if (isset($_SESSION["usuario"])) {
        $usuario = $_SESSION["usuario"];

    } else {
        $_SESSION["usuario"] = "invitado";
        $usuario = $_SESSION["usuario"];
    }

    $sql = "SELECT * FROM productos";
    $productos = [];
    $resultado = $conexion->query($sql);
    while ($row = $resultado->fetch_assoc()) {
        $producto = new Producto($row['idProducto'], $row['nombreProducto'], $row['precio'], $row['descripcion'], $row['cantidad'], $row['imagen']);
        array_push($productos, $producto);
    }
    //Esto es nuevo
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["idProducto"])) {
            $idProducto = depurar($_POST["idProducto"]);
            $usuario = $_SESSION["usuario"];
            $sqlObtenerCesta = "SELECT * FROM cestas WHERE usuario = '$usuario'";
            $resultadoCesta = $conexion->query($sqlObtenerCesta);
            $idCesta = -1;
            $tempCantidad = depurar($_POST["cantidad"]);

            // Comprobar cantidad
            if ($tempCantidad <= 0 || $tempCantidad > 5) {
                $errCantidad = "La cantidad no es válida debe ser entre 1 y 5";
            } else {
                $cantidad = $tempCantidad;
            }

            while ($filaCesta = $resultadoCesta->fetch_assoc()) {
                $idCesta = $filaCesta["idCesta"];
            }

            if ($idCesta != -1 && isset($cantidad)) {
                $sqlObtenerCantidad = "SELECT cantidad FROM productos WHERE idProducto = $idProducto";
                $cantidadTotal = $conexion->query($sqlObtenerCantidad)->fetch_assoc()["cantidad"];

                $sqlObtenerCantidadEnCesta = "SELECT cantidad FROM productosCestas WHERE idProducto = $idProducto AND idCesta = $idCesta";
                $cantidadEnCesta = $conexion->query($sqlObtenerCantidadEnCesta)->fetch_assoc()["cantidad"];

                if ($cantidad > 0 && $cantidad <= $cantidadTotal) {
                    if ($cantidadEnCesta == 0) {
                        $sqlInsertarCesta = "INSERT INTO productosCestas (idProducto, idCesta, cantidad) VALUES ($idProducto, $idCesta, $cantidad)";
                        $resultado = $conexion->query($sqlInsertarCesta);
                    } else {
                        $cantidadEnCesta += $cantidad;
                        $sqlActualizarCesta = "UPDATE productosCestas SET cantidad = $cantidadEnCesta WHERE idProducto = $idProducto AND idCesta = $idCesta";
                        $resultado = $conexion->query($sqlActualizarCesta);
                    }

                    $acierto = "Producto añadido a la cesta";

                    $sqlObtenerPrecio = "SELECT precio FROM productos WHERE idProducto = '$idProducto'";
                    $resPrecio = $conexion->query($sqlObtenerPrecio);
                    $filaPrecio = $resPrecio->fetch_assoc();
                    $precio = $filaPrecio["precio"];
                    $precioCantidad = $precio * $cantidad;

                    $sqlActualizarPrecioTotal = "UPDATE Cestas SET precioTotal = precioTotal + $precioCantidad WHERE idCesta = $idCesta";
                    $conexion->query($sqlActualizarPrecioTotal);

                    $cantidadTotal -= $cantidad;
                    $sqlActualizarCantidad = "UPDATE productos SET cantidad = $cantidadTotal WHERE idProducto = $idProducto";
                    $conexion->query($sqlActualizarCantidad);

                    header("Location: paginaPrincipal.php");
                }
            }
        }
    }

    ?>

    <div class="container">
        <h2>Bienvenido
            <?php echo $usuario; ?> esta es la página principal
        </h2> <br>
        <div class="center-table">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre del Producto</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Imagen</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto) { ?>
    <tr>
        <td>
            <?php echo $producto->idProducto; ?>
        </td>
        <td>
            <?php echo $producto->nombre_Producto; ?>
        </td>
        <td>
            <?php echo $producto->precio; ?>
        </td>
        <td>
            <?php echo $producto->descripcion; ?>
        </td>
        <td>
            <?php
            if ($producto->cantidad > 0) {
                echo $producto->cantidad;
            } else {
                echo "Agotado";
            }
            ?>
        </td>
        <td><img src="<?php echo $producto->imagen; ?>" alt="Imagen del Producto" style="max-width: 100px; height: auto;"></td>
        <td>
            <?php if ($producto->cantidad > 0) { ?>
                <form action="" method="post">
                    <input type="hidden" name="idProducto" value="<?php echo $producto->idProducto; ?>">
                    <button type="submit" class="btn btn-primary">Añadir a la cesta</button>
                    <select name="cantidad" class="form-select">
                        <option selected value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </form>
            <?php } else { ?>
                <button class="btn btn-secondary" disabled>Agotado</button>
            <?php } ?>
        </td>
    </tr>
<?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html> 