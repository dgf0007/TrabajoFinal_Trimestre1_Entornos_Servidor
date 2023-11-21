<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrar Sesión</title>
</head>
<body>
<?php   
    //Primero recuperamos la sesión
    session_start();
    //Luego destruimos la sesión
    session_destroy();
    header('location: paginaPrincipal.php');

    ?>
</body>
</html>