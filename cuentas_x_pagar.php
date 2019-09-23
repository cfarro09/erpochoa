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
$query_Listado = "select c.tipo_comprobante, c.numerofactura, c.fecha, p.razonsocial, s.nombre_sucursal, c.total, gso.tipodoc as tipodocsinoc, gso.numero_guia as nrocomprobanteconoc, ocg.tipodocalmacen as tipodocconoc, ocg.numeroguia as nrodocconoc, IF(gso.numero_guia IS NULL, 'Orden Compra', 'Guia sin OC') as tipoAUXILIAR from compras c left join proveedor p on p.codigoproveedor = c.codigoproveedor left join sucursal s on s.cod_sucursal = c.codigosuc left join guia_sin_oc gso on gso.codigo_guia_sin_oc = c.codigo_guia_sin_oc left join ordencompra_guia ocg on ocg.codigoguia = c.codigo_orden_compra";

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

<h2 align="center"><strong>Costeo</strong></h2>

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
				<th> CODIGO</th>
				<th> FECHA </th>
				<th> PROVEEDOR </th>
				<th> IMPORTE </th>
				<th> TIPO </th>
				<th>SUCURSAL</th>
				<th>ESTADO</th>
				<th> ACCION </th>
			</tr>
		</thead>
		<tbody>
			<?php do { ?>
				
				<tr style="background-color: white">
					<td> <?php echo $i; ?> </td>
					<td><?= $row_Listado['tipo_comprobante'] . "-". $row_Listado['numerofactura'] ; ?></td>
					<td> <?= substr($row_Listado['fecha'], 0,10); ?></td>
					<td> <?php echo $row_Listado['razonsocial']; ?></td>
					<td> <?php echo $row_Listado['total']; ?></td>
					<td><?php echo $row_Listado['tipoAUXILIAR']; ?></td>
					<td><?php echo $row_Listado['nombre_sucursal']; ?></td>
					<td><?= "FALTA" ?></td>
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