<?php require_once('../Connections/Ventas.php'); ?>
<?php
//MX Widgets3 include




$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}



$colname_Producto = "-1";
if (isset($_GET['codigoprod'])) {
  $colname_Producto = $_GET['codigoprod'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Producto = "SELECT * FROM producto WHERE codigoprod = '$colname_Producto'";
$Producto = mysql_query($query_Producto, $Ventas) or die(mysql_error());
$row_Producto = mysql_fetch_assoc($Producto);
$totalRows_Producto = mysql_num_rows($Producto);

$Icono="fa fa-cubes";
$Color="font-blue";
$Titulo="Editar Producto - ".$row_Producto['codigoprod']."<p> ".$row_Producto['nombre_producto']
."<p> Marca".$row_Producto['nombre_producto']."<p> Color".$row_Producto['nombre_producto']."<p> Categoria".$row_Producto['nombre_producto'];
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
 ?>

<?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Ingresar")){
	if (!isset($_FILES['image']['tmp_name'])) {
	echo "";
	}else{
	$file=$_FILES['image']['tmp_name'];
	$image= addslashes(file_get_contents($_FILES['image']['tmp_name']));
	$image_name= addslashes($_FILES['image']['name']);
	$image_size= getimagesize($_FILES['image']['tmp_name']);

	
		if ($image_size==FALSE) {
		
			echo "That's not an image!";
			
		}else{
			
			$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			$ext = strtolower($ext);
			move_uploaded_file($_FILES["image"]["tmp_name"],"../reservation/img/products/" . $colname_Producto.".".$ext);
			
			$location=$colname_Producto.".jpg";
			
			$pname=$row_Producto['codigoprod'];
			$update=mysql_query("INSERT INTO producto_imagen (codigoprod, foto) VALUES ('$pname','$location')");
header("location: productofoto.php");
			exit();
		
			}
	}
}
?>
<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<form action="#" method="post" enctype="multipart/form-data" name="addproduct" onsubmit="return validateForm()">
Room Image: <br /><input type="file" name="image" class="ed"><br />
 
    
 <?php   //------------- Inicio Botones------------
include("Botones/BotonesAgregar.php"); 
//------------- Fin Botones------------
?>
  <input type="hidden" name="MM_insert" value="Ingresar">
</form>

                  
<?php include("Fragmentos/pie.php"); 

?>
<?php


?>

