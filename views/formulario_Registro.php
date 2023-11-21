<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <?php require '../util/base_de_datos.php'; ?>
    <?php require '../util/depurar.php'; ?>
    <link rel="stylesheet" href="./style/estilos.css">
</head>

<body id="registro" >

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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $temp_usuario = depurar($_POST["usuario"]);
        $temp_contrasena = depurar($_POST["contrasena"]);
        $temp_fecha = depurar($_POST["fechaNacimiento"]);
        # Validación de usuario
        if (strlen($temp_usuario) == 0) {
            $err_usuario = "El usuario es obligatorio";
        } elseif (strlen($temp_usuario) < 4) {
            $err_usuario = "No puede tener menos de 4 caracteres";
        } elseif (strlen($temp_usuario) > 12) {
            $err_usuario = "No se puede exceder de 12 caracteres";
        } elseif (!preg_match('/^[a-zA-Z0-9\s]+$/', $temp_usuario)) {
            $err_usuario = 'Solo se permiten caracteres, números y espacios en blanco';
        } else {
            $usuario = $temp_usuario;
        }
        # Validación de contrasena
        if (strlen($temp_contrasena) < 8 || strlen($temp_contrasena) > 20) {
            $err_contrasena = "La contraseña debe tener entre 8 y 20 caracteres";
        } else {
            // Validar la complejidad de la contraseña usando expresiones regulares
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,20}$/', $temp_contrasena)) {
                $err_contrasena = "La contraseña debe contener al menos un carácter en minúscula, uno en mayúscula, un número y un carácter especial";
            } else {
                $contrasena = $temp_contrasena;
                // Para cifrar una contraseña
                $contrasena_cifrada = password_hash($contrasena, PASSWORD_DEFAULT);
                echo $contrasena_cifrada; // Esto es para demostración, deberás almacenar $contrasena_cifrada en la base de datos
            }
        }

        # Validación de fecha de nacimiento
        if (strlen($temp_fecha) == 0) {
            $err_fecha = "La fecha de nacimiento es obligatoria";
        } elseif (strlen($temp_fecha) < 0 || strlen($temp_fecha) > 120) {
            $err_fecha = "La edad debe estar entre 0 y 120 años";
        } else {
            $fecha = $temp_fecha;
        }
    }
    ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title text-center">Registrarse</h1>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario:</label>
                                <input type="text" class="form-control" id="usuario" name="usuario"
                                    placeholder="Introduzca su nombre">
                                <?php if (isset($err_usuario)): ?>
                                    <div class="alert alert-danger mt-2" role="alert">
                                        <?php echo $err_usuario; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena"
                                    placeholder="Introduzca una contraseña">
                                <?php if (isset($err_contrasena)): ?>
                                    <div class="alert alert-danger mt-2" role="alert">
                                        <?php echo $err_contrasena; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento:</label>
                                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento">
                                <?php if (isset($err_fecha)): ?>
                                    <div class="alert alert-danger mt-2" role="alert">
                                        <?php echo $err_fecha; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary" type="submit">Registrarse</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php

    if (isset($usuario) && isset($contrasena) && isset($fecha)) {
        // Verificar si el usuario ya existe
        $consulta_usuario = "SELECT usuario FROM usuarios WHERE usuario = '$usuario'";
        $resultado_usuario = $conexion->query($consulta_usuario);

        if ($resultado_usuario->num_rows > 0) {
            // Si el nombre de usuario ya existe, mostrar un mensaje
            echo "El nombre de usuario ya está en uso. Por favor, elige otro.";
        } else {
            // Si el nombre de usuario no está en uso, proceder con la inserción del nuevo usuario
            $sql = "INSERT INTO usuarios (usuario, contrasena, fechaNacimiento) VALUES ('$usuario', '$contrasena_cifrada', '$fecha')";
            if ($conexion->query($sql) === TRUE) {
                // Obtener el ID del usuario insertado
                $idUsuario = $conexion->insert_id;

                // Insertar cesta
                $sqlCesta = "INSERT INTO cestas (idCesta, usuario, precioTotal) VALUES (DEFAULT, '$usuario', 0)";
                if ($conexion->query($sqlCesta) === TRUE) {
                    echo "Usuario registrado con éxito.";
                } else {
                    echo "Error al crear la cesta: " . $conexion->error;
                }
            } else {
                echo "Error al registrar el usuario: " . $conexion->error;
            }
        }
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>