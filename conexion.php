<?php
$ipServidor = "localhost";
$usuario = "root";
$pass = "";
$nombreBD = "registro";

$conn = mysqli_connect($ipServidor, 
                $usuario,
                $pass,
                $nombreBD);

if($conn){
    echo " ";

}else{
    echo "Error en la conn";

}
