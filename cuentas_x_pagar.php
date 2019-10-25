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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Eliminar_Registro")) {
	$updateSQL = sprintf("UPDATE producto SET estado=%s WHERE codigoprod=%s",
		GetSQLValueString($_POST['estado'], "text"),
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
$query_Listado = "SELECT rc.tipomoneda, rc.tipo_comprobante, rc.numerocomprobante, s.nombre_sucursal, p.ruc, p.razonsocial, rc.subtotal, rc.igv, rc.total from registro_compras rc inner JOIN proveedor p on p.codigoproveedor=rc.codigoproveedor inner join sucursal s on s.cod_sucursal=rc.codigosuc";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas


//Titulo e icono de la pagina
$Icono="fa fa-building-o";
$Color="font-blue";
$Titulo="Historial de Compras";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="disabled";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//________________________________________________________________________________________________________________
$i = 1;

?>

<h2 align="center"><strong>CUENTAS POR PAGAR</strong></h2>

<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty ?>
	<div class="alert alert-danger">
		<strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>


	</div>
<?php } // Show if recordset empty ?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty ?>
	<table class="table table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th> N&deg; </th>
				<th>TIPO COMPR</th>
				<th>N° COMPROBANTE</th>
				<th>SUCURSAL</th>
				<th>RUC P</th>
				<th>RAZON SOCIAL</th>
				<th>SUBTOTAL</th>
				<th>IGV</th>
				<th>TOTAL</th>
				<th>ACCIONES</th>
			</tr>
		</thead>
		<tbody>
			<?php do { ?>
				
				<tr style="background-color: white">
					<td> <?php echo $i; ?> </td>
					<td> <?php echo $row_Listado['tipo_comprobante']; ?></td>
					<td> <?php echo $row_Listado['numerocomprobante']; ?></td>
					<td> <?php echo $row_Listado['nombre_sucursal']; ?></td>
					<td><?php echo $row_Listado['ruc']; ?></td>
					<td><?php echo $row_Listado['razonsocial']; ?></td>
					<td><?php echo $row_Listado['subtotal']; ?></td>
					<td><?php echo $row_Listado['igv']; ?></td>
					<td><?php echo $row_Listado['total']; ?></td>
					<td><a href="#" class="verDetalle">Ver</a></td>
				</td>
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
<script type="text/javascript">


</script>