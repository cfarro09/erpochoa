<?php require_once('Connections/Ventas.php'); ?>
<?
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
	<div class="modal fade" id="mSetPrecioVenta" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document" style="width: 1300px">
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h2 class="modal-title" id="moperation-title">Asignar precio venta</h2>
				</div>
				<div class="modal-body">
					<form id="saveOrdenCompra">
						<div class="container-fluid">
							<div class="row" style="margin-top:20px">
								<div class="col-xs-12 col-md-12">

									<table class="table">
										<thead>
											<th>Producto</th>
											<th>Marca</th>
											<th>P. Compra</th>
											<th>P. Venta</th>
											<th class="text-center">% V 1</th>
											<th class="text-center">P V 1</th>
											<th class="text-center">% V 2</th>
											<th class="text-center">P V 2</th>
											<th class="text-center">% V 3</th>
											<th class="text-center">P V 3</th>
										</thead>
										<tbody id="detalleComprax">
											<tr>
												<td id="productopv2"></td>
												<td id="marcapv2"></td>
												<td id="preciocomprapv2"></td>
												<td id="precioventapv2"></td>
												<td >
													<input type="number" oninput="inputdynamic(this)" data-index="1" data-type="porcentaje" id="porcentaje1" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)" data-index="1" data-type="precio" id="precio1" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)" data-index="2" data-type="porcentaje" id="porcentaje2" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)" data-index="2" data-type="precio" id="precio2" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)" data-index="3" data-type="porcentaje" id="porcentaje3" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)" data-index="3" data-type="precio" id="precio3" class="form-control">
												</td>
												

											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<button type="submit" id="btn_save_precioventa1" style="display: none" class="btn btn-success">Guardar</button>
						<button type="button" data-dismiss="modal" class="modal_close btn btn-danger">Cerrar</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-striped table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th>N&deg; </th>
				<th>CODIGO </th>
				<th>PRODUCTO </th>
				<th>MARCA </th>
				<th>P COMPRA</th>
				<th>P VENTA</th>
				<th>TOTAL PROD</th>
				<th>ACCION </th>
			</tr>
		</thead>
		<tbody>
			<?php do { ?>
				<tr>
					<td> <?php echo $i; ?> </td>
					<td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['codigoprod']; ?> </a>                                                          </td>
					<td> <?php echo $row_Listado['nombre_producto']; ?></td>
					<td align="center"> <?= $row_Listado['Marca']; ?></td>
					<td align="right"> <?= $row_Listado['precio_compra']; ?></td>
					<td align="right"> <?= $row_Listado['precio_venta1']; ?></td>
					<td align="center"> <?= $row_Listado['saldo'];?></td>
					<td><a href="#" data-nombreproducto="<?= $row_Listado['nombre_producto'] ?>" data-marca="<?= $row_Listado['Marca']; ?>" data-preciocompra="<?= $row_Listado['precio_compra']; ?>" data-precioventa="<?= $row_Listado['precio_venta1']; ?>" data-codproducto="<?= $row_Listado['codigoprod'] ?>" onClick="asignarprecioventa(this)" >Asignar</a></td>
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
		function asignarprecioventa(e){
			$("#mSetPrecioVenta").modal();
			btn_save_precioventa1.style.display = ""
			preciocomprapv2.textContent = e.dataset.preciocompra
			precioventapv2.textContent = e.dataset.precioventa
			marcapv2.textContent = e.dataset.marca
			productopv2.textContent = e.dataset.nombreproducto
		}
		function inputdynamic(e){
			debugger
			if(e.value < 0){
				e.value = 0;
				return;
			}

			const preciocompra =  parseFloat(preciocomprapv2.textContent);
			const type = e.dataset.type;
			const i = e.dataset.index;
			const currentvalue = parseFloat(e.value);
			const towrite = type == "porcentaje" ? "precio" : "porcentaje";

			if(type == "porcentaje")
				getSelector(`#${towrite}${i}`).value = (preciocompra*(100+currentvalue)/100).toFixed(2)
			else
				getSelector(`#${towrite}${i}`).value = (currentvalue*100/preciocompra).toFixed(2)
		}
	</script>