<?php
session_start();
if (isset($_SESSION["usuario"]) && $_SESSION["usuario"] != "admin") {
    header("location: formulario_Inicio_Sesion.php");
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Formulario de Productos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <?php require_once '../util/depurar.php'; ?>
</head>
<?php
require '../util/base_de_datos.php'
    ?>

<body>
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

    <?php
    $err_nombre_Producto = $err_precio = $err_descripcion = $err_cantidad = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $temp_nombre_Producto = depurar($_POST["nombre_Producto"]);
        $temp_precio = depurar($_POST["precio_Producto"]);
        $temp_Descripcion = depurar($_POST["descripcion_Producto"]);
        $temp_Cantidad = depurar($_POST["cantidad_Producto"]);

        # Validación de la imagen
    if ($_FILES["imagen"]["error"] == UPLOAD_ERR_OK) {
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
        $max_size = 1024 * 1024; // 1MB

        $nombre_imagen = $_FILES["imagen"]["name"];
        $tipo_imagen = $_FILES["imagen"]["type"];
        $tamano_imagen = $_FILES["imagen"]["size"];
        $ruta_temporal = $_FILES["imagen"]["tmp_name"];
        $ruta_final = "./images/" . $nombre_imagen;

        $err_imagen = '';

        if (!in_array($tipo_imagen, $allowed_types)) {
            $err_imagen = "Solo se permiten archivos JPG, JPEG o PNG.";
        } elseif ($tamano_imagen > $max_size) {
            $err_imagen = "La imagen es demasiado grande. El tamaño máximo permitido es 1MB.";
        } else {
            // Si la imagen cumple con los requisitos, mueve el archivo al directorio final
            move_uploaded_file($ruta_temporal, $ruta_final);
        }

        if ($err_imagen !== '') {
            // Si hay errores en la imagen, asigna el mensaje a $err_nombre_Producto (o a la variable que desees)
            $err_nombre_Producto = $err_imagen;
        }
    } else {
        $err_imagen =  "La imagen no se ha podido subir.";
    }

        # Validación de Producto.
        if (strlen($temp_nombre_Producto) == 0) {
            $err_nombre_Producto = "El nombre de Producto es obligatorio";
        } elseif (strlen($temp_nombre_Producto) > 40) {
            $err_nombre_Producto = "No se puede exceder de 40 caracteres";
        } elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', $temp_nombre_Producto)) {
            $err_nombre_Producto = 'Solo se permiten caracteres, números y espacios en blanco';
        } else {
            $nombre_Producto = $temp_nombre_Producto;
        }
        # Validación de Precio
        if (strlen($temp_precio) == 0) {
            $err_precio = "El precio es obligatorio";
        } elseif (!is_numeric($temp_precio)) {
            $err_precio = "El precio debe ser un número";
        } elseif ($temp_precio < 0) {
            $err_precio = "El precio debe ser positivo";
        } elseif (floatval($temp_precio) > 99999.99) {
            $err_precio = "El precio máximo es 99999.99";
        } else {
            $precio = (float) $temp_precio;
        }
        # Validación de Descripción
        if (strlen($temp_Descripcion) == 0) {
            $err_descripcion = "La descripción es obligatoria";
        } elseif (strlen($temp_Descripcion) > 255) {
            $err_descripcion = "Solo se permiten 255 caracteres";
        } else {
            $descripcion = $temp_Descripcion;
        }
        # Validación de Cantidad
        if (strlen($temp_Cantidad) == 0) {
            $err_cantidad = "La cantidad es obligatoria";
        } elseif ($temp_Cantidad < 0) {
            $err_cantidad = "La cantidad debe ser positiva";
        } elseif (floatval($temp_Cantidad) > 99999.99) {
            $err_cantidad = "La cantidad máxima es 99999.99";
        } else {
            $cantidad = (int) $temp_Cantidad;
        }
    }
    ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h2>Agregar Producto</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nombre_Producto" class="form-label">Nombre del Producto:</label>
                                <input type="text" minlength="1" maxlength="40" class="form-control"
                                    id="nombre_Producto" name="nombre_Producto"
                                    value="<?php echo isset($nombre_Producto) ? $nombre_Producto : ''; ?>">
                                <?php if (isset($err_nombre_Producto))
                                    echo $err_nombre_Producto; ?>
                            </div>
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio:</label>
                                <input type="text" class="form-control" id="precio_Producto" name="precio_Producto"
                                    value="<?php echo isset($precio) ? $precio : ''; ?>">
                                <?php if (isset($err_precio))
                                    echo $err_precio; ?>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción:</label>
                                <textarea class="form-control" id="descripcion_Producto"
                                    name="descripcion_Producto"><?php echo isset($descripcion) ? $descripcion : ''; ?></textarea>
                                <?php if (isset($err_descripcion))
                                    echo $err_descripcion; ?>
                            </div>
                            <div class="mb-3">
                                <label for="cantidad" class="form-label">Cantidad:</label>
                                <input type="number" class="form-control" id="cantidad_Producto"
                                    name="cantidad_Producto" value="<?php echo isset($cantidad) ? $cantidad : ''; ?>">
                                <?php if (isset($err_cantidad))
                                    echo $err_cantidad; ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Imagen</label>
                                <input class="form-control" type="file" name="imagen">
                                <?php if (isset($err_imagen))
                                    echo $err_imagen; ?>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (isset($nombre_Producto) && isset($precio) && isset($descripcion) && isset($cantidad) && isset($ruta_final)) {
        echo "Éxito!";
        $sql = "INSERT INTO productos (nombreProducto, precio, descripcion, cantidad, imagen) VALUES ('$nombre_Producto', '$precio', '$descripcion', '$cantidad', '$ruta_final')";
        $result = $conexion->query($sql);
        if ($result) {
            echo "El producto se ha agregado correctamente a la base de datos.";
        } else {
            echo "Error al agregar el producto: " . $conexion->error;
        }
    }
    ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>