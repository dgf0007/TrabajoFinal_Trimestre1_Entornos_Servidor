<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constructor Productos</title>
</head>

<body>
    <!--Crear un objeto de PHP producto con las propiedades necesarias-->
    <?php
   class Producto {
    public $idProducto;
    public $nombre_Producto;
    public $precio;
    public $descripcion;
    public $cantidad;
    public $imagen;

    public function __construct($idProducto, $nombre_Producto, $precio, $descripcion, $cantidad, $imagen)
    {
        $this->idProducto = $idProducto;
        $this->nombre_Producto = $nombre_Producto;
        $this->precio = $precio;
        $this->descripcion = $descripcion;
        $this->cantidad = $cantidad;
        $this->imagen = $imagen;
    }
}

    ?>


</body>

</html>