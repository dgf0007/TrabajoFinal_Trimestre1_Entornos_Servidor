<?php
    function depurar($entrada) //Creada para evitar insercion de sql y espacios en blanco
    {
        $salida = htmlspecialchars($entrada);
        $salida = trim($salida);
        return $salida;
    }
?>