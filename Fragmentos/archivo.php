<?php 
$nombre_archivo = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
//verificamos si en la ruta nos han indicado el directorio en el que se encuentra
if ( strpos($nombre_archivo, '/') !== FALSE )
    //de ser asi, lo eliminamos, y solamente nos quedamos con el nombre y su extension
    $nombre_archivo = array_pop(explode('/', $nombre_archivo));
//echo $nombre_archivo;
$file=$nombre_archivo;
$extension = array_pop(explode('.',$file)); // Sacaría "jpg"
//echo $extension;
$nombre = array_shift(explode('.',$file));  // Sacaría "nombre"
//echo $nombre;
// echo $agregar = $nombre . "_add" ."." . $extension;
// echo $editar = $nombre . "_edit" ."." . $extension;

?>

