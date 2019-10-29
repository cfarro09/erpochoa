<?php require_once('Connections/Ventas.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
		if (PHP_VERSION < 6) {
			$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
		}

		$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

		switch ($theType) {
			case "text":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;    
			case "long":
			case "int":
			$theValue = ($theValue != "") ? intval($theValue) : "NULL";
			break;
			case "double":
			$theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
			break;
			case "date":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
			case "defined":
			$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			break;
		}
		return $theValue;
	}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
	$editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_eliminar"])) && ($_POST["MM_eliminar"] == "Eliminar_Registro")) {
	$updateSQL = sprintf("DELETE from producto_imagen WHERE codigoprod=%s",
		GetSQLValueString($_POST['codigoprod'], "int"));

	mysql_select_db($database_Ventas, $Ventas);
	$Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

	$updateGoTo = "product_list.php";
	if (isset($_SERVER['QUERY_STRING'])) {
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Eliminar_Registro")) {
	
	
	
	$updateSQL = sprintf("UPDATE producto p, producto_stock ps SET p.estado=%s WHERE p.codigoprod=%s and ps.stock=0 and ps.codigoprod=%s",
		GetSQLValueString($_POST['estado'], "text"),
		GetSQLValueString($_POST['codigoprod'], "int"),
		GetSQLValueString($_POST['codigoprod'], "int"));

	mysql_select_db($database_Ventas, $Ventas);
	$Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

	$updateGoTo = "product_list.php";
	if (isset($_SERVER['QUERY_STRING'])) {
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $updateGoTo));
}
mysql_select_db($database_Ventas, $Ventas);
$querySucursales = "select * from sucursal where estado = 1" ;
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "select * from vt_listaproducto1";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);

 //Enumerar filas de data tablas
$i = 1;

//Titulo e icono de la pagina
$Icono="fa fa-cubes"; 
$Color="font-blue";
$Titulo="Listado de Productos";
$NombreBotonAgregar="Agregar"; 
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 330;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//__________________________________________________________________
?>        

<!--  ----------------------------------------------------------------------------------------------------------------------------------->

<?php if ($totalRows_Listado == 0) { // Show if recordset empty ?>
	<div class="alert alert-danger">
		<strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>


	</div>
<?php } // Show if recordset empty ?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty ?>
	<table class="table table-striped table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th  > N&deg; </th>
				<th  > CODIGO </th>
				<th  > PRODUCTO </th>
				<th  > MARCA </th>
				<th  > P COMPRA</th>
				<th  > P VENTA</th>
				
				<th  > TOTAL PROD</th>
				
				
				<th  > ACCION </th>
			</tr>
		</thead>
		<tbody>
			<?php do { ?>
				<tr>
					<td> <?php echo $i; ?> </td>
					<td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['codigoprod']; ?> </a>                                                          </td>
					<td> <?php echo $row_Listado['nombre_producto']; ?></td>
					<td align="center"> <?php echo $row_Listado['Marca']; ?></td>
					<td> <?php echo $row_Listado['precio_compra']; ?></td>
					<td align="center"> <?php echo $row_Listado['precio_venta']; ?></td>
					<td> <?= 1;?></td>
				
					<td><a href="#" data-nombreproducto = "<?= $row_Listado['nombre_producto'] ?>" data-codproducto="<?= $row_Listado['codigoprod'] ?>" class="ver-kardex">Asignar</a></td>

				</tr>
				<?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
			</tbody>
		</table>
		
	<?php } // Show if recordset not empty ?>
	<?php 
//___________________________________________________________________________________________________________________
	include("Fragmentos/footer.php");
	include("Fragmentos/pie.php");

	mysql_free_result($Listado);
	?>
	