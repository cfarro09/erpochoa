<?php 
$server = "localhost";
$user = "root";
$pass = "";
$bd = "ventas02";
//Creamos la conexión
$conexion = mysqli_connect($server, $user, $pass,$bd) 
or die("Ha sucedido un error inexperado en la conexion de la base de datos");

//generamos la consulta
$sql = "SELECT * FROM producto";
mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

if(!$result = mysqli_query($conexion, $sql)) die();

$clientes = array(); //creamos un array

while($row = mysqli_fetch_array($result)) 
{ 
    $nombre_producto=$row['nombre_producto'];
    $clientes[] = array($nombre_producto);

}
    
//desconectamos la base de datos
$close = mysqli_close($conexion) 
or die("Ha sucedido un error inexperado en la desconexion de la base de datos");
  

//Creamos el JSON
$json_string = json_encode($clientes);
$json_string;
//echo $json_string;


$file = 'productos.json';
file_put_contents($file, $json_string);

    

?>