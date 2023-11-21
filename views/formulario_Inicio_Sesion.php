<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <?php require '../util/base_de_datos.php' ?>
</head>

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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = $_POST["usuario"];
        $contrasena = $_POST["contrasena"];

        $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows === 0) {
            echo "NO EXISTE EL USUARIO";
        } else {
            while ($fila = $resultado->fetch_assoc()) {
                $contrasena_cifrada = $fila["contrasena"];
            }
            //Para comprobar si la contrasñea original es igual a la contraseña introducida
            $acceso_valido = password_verify($contrasena, $contrasena_cifrada);

            if ($acceso_valido) {
                echo "NOS HEMOS LOGEADO CON EXITO";
                //Crea una sesion nueva o recupera una existente
                session_start();
                $_SESSION["usuario"] = $usuario;
                header('location:paginaPrincipal.php');
            } else {
                echo "LA CONTRASEÑA ESTÁ MAL";
            }
        }
    }
    ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Iniciar Sesión</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label class="form-label">Usuario:</label>
                                <input type="text" class="form-control" name="usuario" placeholder="Ingresa tu usuario">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" name="contrasena"
                                    placeholder="Ingresa tu contraseña">
                            </div>
                            <div class="d-grid gap-2">
                                <input class="btn btn-primary" type="submit" value="Enviar">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>