<?php require_once('Connections/Ventas.php'); ?>
<?php

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT rc.tipomoneda, rc.tipo_comprobante, rc.numerocomprobante, s.nombre_sucursal, p.ruc, p.razonsocial, rc.subtotal, rc.igv, rc.total from registro_compras rc inner JOIN proveedor p on p.codigoproveedor=rc.codigoproveedor inner join sucursal s on s.cod_sucursal=rc.codigosuc";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas



include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//________________________________________________________________________________________________________________
$i = 1;

?>

<h2 align="center"><strong>REGISTRO COMPRAS</strong></h2>

<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty ?>
	<div class="alert alert-danger" style="margin-top: 20px">
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