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
$query_Listado = "select g.codigoguia,compras.subtotal,  g.codigoordcomp, o.codigoref1,g.codigoacceso, g.numeroguia, g.estado as estadoGuia, g.fecha, o.codigo,o.codigoordcomp,o.codigoproveedor, o.fecha_emision, o.estadofact, o.sucursal, p.ruc, p.razonsocial from ordencompra_guia g inner JOIN ordencompra o on g.codigoordcomp=o.codigoordcomp inner JOIN proveedor p on p.codigoproveedor=o.codigoproveedor left join compras on compras.codigo_orden_compra = g.codigoguia where g.estado=2 or g.estado=3";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
$i = 1;


//para asignar precio y cantidad
$query_Listado1 = "SELECT a.codigoprod, a.nombre_producto from producto a INNER JOIN detalle_compras b ON a.codigoprod = b.codigoprod group by a.codigoprod";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);
 //Enumerar filas de data tablas

$queryguiasinoc = "SELECT c.codigo_guia_sin_oc, compras.subtotal,a.usuario,p.ruc, s.nombre_sucursal, c.codigoref2,c.estado, c.numero_guia, p.razonsocial, p.codigoproveedor as codigoproveedor, c.fecha FROM guia_sin_oc c inner join proveedor p on c.codigoproveedor=p.codigoproveedor left join sucursal s on s.cod_sucursal = c.sucursal left join acceso a on a.codacceso = c.codacceso left join compras on compras.codigo_guia_sin_oc = c.codigo_guia_sin_oc where c.estado = 2";
$listaguiasinoc = mysql_query($queryguiasinoc, $Ventas) or die(mysql_error());
$row_listaguiasinoc = mysql_fetch_assoc($listaguiasinoc);
$totalRows_listaguiasinoc = mysql_num_rows($listaguiasinoc);


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
				<th class="none">Total </th>
				<th class="none">SUBTOTAL</th>
				<th class="none"> IVA </th>
				<th> PROVEEDOR </th>
				<th> FECHA </th>
				<th> VER </th>
				<th>TIPO</th>
				<th> IMPRIMIR </th>
			</tr>
		</thead>
		<tbody>
			<?php do { ?>
				<?php 
				$color = "#bde8dc";

				if(isset($row_Listado['subtotal']) && $row_Listado['subtotal'] ){
				}else{
					$row_Listado['subtotal'] = 0;
				}
				?>
				<tr style="background-color: #26c281">
					<td> <?php echo $i; ?> </td>
					<td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"
						data-toggle="modal">
						<?= "Doc. Referencia ". $row_Listado['codigoref1']." - N° Guia ".$row_Listado['numeroguia']  ?> </a>
					</td>

					<td><?php  echo "&#36; ".number_format($row_Listado['subtotal'],2)*1.18; ?> </td>
					<td> <?php echo "&#36; ".number_format($row_Listado['subtotal'],2); ?></td>
					<td> <?php echo "&#36; ".number_format($row_Listado['subtotal'],2)*0.18; ?>
				</td>
				<td> <?php echo $row_Listado['razonsocial']; ?></td>
				<td> <?php echo $row_Listado['fecha_emision']; ?></td>
				<?php if($row_Listado['subtotal']): ?>
					<td><a href="#" data-estado="<?= $row_Listado['estado'] ?>" data-codigo="<?= $row_Listado['codigo'] ?>"
						class="verOrden">Ver</a>
					</td>
					<?php else: ?>
						<td><a href="#" class="aux_compras" data-type="ordencompra"
							data-codigo="<?= $row_Listado['codigo'] ?>">Asignar</a></td>
						<?php endif ?>
						<td>
							<a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante"
							href="Imprimir/orden_compra.php?codigocompras=<?php echo $row_Listado['codigo']; ?>&codigo=<?php echo $row_Listado['codigoref1']; ?>"
							target="new"><i class="glyphicon glyphicon-credit-card"></i></a>
						</td>
						<td>Orden Compra</td>
					</td>
				</tr>
				<?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>

				<?php if($totalRows_listaguiasinoc > 0): do { ?>
					<?php 

					if(isset($row_listaguiasinoc['subtotal']) && $row_listaguiasinoc['subtotal'] ){
					}else{
						$row_listaguiasinoc['subtotal'] = 0;
					}
					?>
					<tr style="background-color: #b8cbec">
						<td> <?php echo $i; ?> </td>
						<td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_listaguiasinoc['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"
							data-toggle="modal"> <?= "Numero Guia ". $row_listaguiasinoc['numero_guia']; ?> </a></td>
							<td><?php  echo "&#36; ".number_format($row_listaguiasinoc['subtotal'],2); ?> </td>
							<td> <?php echo "&#36; ".number_format($row_listaguiasinoc['subtotal']/1.18,2); ?></td>
							<td> <?php echo "&#36; ".number_format(($row_listaguiasinoc['subtotal']-number_format($row_listaguiasinoc['subtotal']/1.18,2)),2); ?>
						</td>
						<td> <?php echo $row_listaguiasinoc['razonsocial']; ?></td>
						<td> <?php echo $row_listaguiasinoc['fecha']; ?></td>
						<?php if($row_listaguiasinoc['subtotal'] != 0): ?>
							<td>
								<a href="#" data-estado="<?= $row_listaguiasinoc['estado'] ?>"
									data-codigo="<?= $row_listaguiasinoc['codigo_guia_sin_oc'] ?>" class="verOrdenSinOc">Ver</a>
								</td>
								<?php else: ?>
									<td><a href="#" class="aux_compras" data-type="guia_sin_oc"
										data-codigo="<?= $row_listaguiasinoc['codigo_guia_sin_oc'] ?>">Facturar</a></td>
									<?php endif ?>
									<td>
										<a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante"
										href="Imprimir/orden_compra.php?codigocompras=<?php echo $row_listaguiasinoc['codigo']; ?>&codigo=<?php echo $row_listaguiasinoc['codigoref1']; ?>"
										target="new"><i class="glyphicon glyphicon-credit-card"></i></a>
									</td>
									<td>Guia sin OC</td>
								</td>
							</tr>
							<?php $i++;} while ($row_listaguiasinoc = mysql_fetch_assoc($listaguiasinoc)); endif; ?>
						</tbody>
					</table>
					<div class="modal fade" id="mOrdenCompra" role="dialog" data-backdrop="static" data-keyboard="false">
						<div class="modal-dialog" role="document">
							<div class="modal-content m-auto">
								<div class="modal-header">
									<h5 class="modal-title" id="moperation-title"></h5>
								</div>
								<div class="modal-body">
									<form id="saveOrdenCompra">
										<input type="hidden" id="codigoOrdenCompra">
										<input type="hidden" id="codigoordcomp">
										<input type="hidden" id="codigoguia" value="">
										<div class="container-fluid">

											PROVEEDOR: <span id="mproveedor"></span> <BR>
											SUCURSAL: <span id="msucursal"></span> <BR>
											FECHA DE EMISION : <span id="mfechaemision"></span> <br>
											VALOR TOTAL: <span id="mvalortotal"></span><BR>
											CODIGO DE REF 1 : <span id="mcodref1"></span> <br>
											CODIGO REF2: : <span id="mcodref2"></span> <br>
											GENERADA POR: : <span id="mgeneradapor"></span> <br>
											RUC : <span id="mruc"></span>

											<div class="row" style="margin-top:20px">
												<div class="col-xs-12 col-md-12">
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label for="field-1" class="control-label">Numero Guia</label>
																<input type="text" readonly class="form-control" name="numero-guia"
																id="numero-guia">
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label for="field-1" class="control-label">Observacion</label>
																<input type="text" readonly class="form-control" name="observacion"
																id="observacion">
															</div>
														</div>
													</div>
													<table class="table">
														<thead>
															<th>Nº</th>
															<th>Cantidad Solicitada</th>
															<th>Producto</th>
															<th id="th-saldo" style="display: none">Saldo</th>
															<th>Cantidad Recibida</th>
														</thead>
														<tbody id="detalleTableOrden-facturacion-list">
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<button type="button" id="btn-finalice" style="display: none"
										class="btn btn-primary">Finalizar</button>
										<button type="submit" id="btn-guardarGuia-facturacion" class="btn btn-success">Guardar</button>
										<button type="button" data-dismiss="modal" class="modal_close btn btn-danger">Cerrar</button>
									</form>
								</div>
							</div>
						</div>
					</div>
					
					<div class="modal fade" id="mFacturaCompra" role="dialog" data-backdrop="static" data-keyboard="false">
						<div class="modal-dialog" role="document" style="width: 700px">
							<div class="modal-content m-auto">
								<div class="modal-header">
									<h2 class="modal-title" id="moperation-title">Facturar Orden de compra</h2>
								</div>
								<div class="modal-body">
									<form id="saveFacturar">
										<div class="container-fluid">
											<div class="row">
												<div class="col-xs-12 col-md-12">
													<b>
														<div style="text-align: right">
															FECHA DE EMISION: : <span id="mfechaemision1"></span> <br>
															VALOR TOTAL: : <span id="mvalortotal1"></span><BR>
														</div>
														PROVEEDOR: <span id="mproveedor1"></span> <BR>
														SUCURSAL: <span id="msucursal1"></span> <BR>
														DOC ALMACEN : <span id="mcodref11"></span> <br>
														DOC REF 2: : <span id="mcodref21"></span> <br>
														GENERADA POR: : <span id="mgeneradapor1"></span> <br>
														RUC : <span id="mruc1"></span>
													</b>
													<input type="hidden" id="codigoproveedor">
													<input type="hidden" id="codigosucursal">
													<input type="hidden" id="codigo_orden_compra">
													<input type="hidden" id="codigo_guia_sin_oc">

													<div style="margin-top: 10px"	>
														<label class="" for="check_transporte">¿Incluye transporte?</label>
														<input type="checkbox" class="" id="check_transporte">

														<div class="row" style="display: none" id="container_transporte">
															<div class="col-sm-6 text-center">
																<button type="button" class="btn btn-success" id="prorrateo">PRORRATEO</button>
															</div>
															<div class="col-sm-6 text-center">
																<button type="button" class="btn btn-success" id="participacion">PARTICIPACION EN COMPRAS</button>
															</div>
														</div>
													</div>
													
													<div style="margin-top: 10px"	>
														<label class="" for="check_estibador">¿Incluye Estibador?</label>
														<input type="checkbox" class="" id="check_estibador">

														<div class="row" style="display: none" id="container_estibador">
															<div class="col-sm-3">
																<label class="control-label" for="tipocomprobanteestibador">Tipo Comprobante</label>
																<select class="form-control select2-allow-clear" name="tipocomprobanteestibador" id="tipocomprobanteestibador">
																	<option value="">Select</option>
																	<option value="factura">Factura</option>
																	<option value="boleta">Boleta</option>
																	<option value="notaventa">Nota venta</option>
																	<option value="recibo">Recibo</option>
																	<option value="otros">Otros</option>
																</select>
															</div>
															<div class="col-sm-3">
																<label class="control-label" for="numerocomprobanteestibador">Nro Comprobante</label>
																<input class="form-control" name="" id="numerocomprobanteestibador">
															</div>
															<div class="col-sm-3">
																
															</div>
															<div class="col-sm-3">
																<label class="control-label" for="precio_estibador">Precio</label>
																<input class="form-control" type="number" name="" id="precio_estibador">
															</div>
														</div>
													</div>
													<div class="row" style="margin-top: 20px">
														<div class="col-xs-12 col-md-12">
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group text-center">
																		<span class="" style="font-weight: bold; font-size: 25px">SubT:
																		</span>
																		<span class="" id="subtotal-facturacion"
																		style="font-weight: bold; font-size: 25px; margin-right: 15px" >0.0</span>
																		<span class="" style="font-weight: bold; font-size: 25px">IGV:
																		</span>
																		<span class="" id="igv-facturacion"
																		style="font-weight: bold; font-size: 25px; margin-right: 15px"> 0.0</span>
																		<span class="" style="font-weight: bold; font-size: 25px">Total:
																		</span>
																		<span class="" id="importe-total"
																		style="font-weight: bold; font-size: 25px">0.0</span>
																	</div>
																</div>
																<div class="col-md-2">
																	<div class="form-group">
																		<label for="field-1" class="control-label">Descuento</label>
																		<input type="number" class="form-control" oninput="changedescuento(this)" step="any" id="descuento" name="">
																	</div>
																</div>
																<div class="col-md-3">
																	<div class="form-group">
																		<label for="field-1" class="control-label">Tipo Comp</label>
																		<select class="form-control select2-allow-clear"
																		name="tipocomprobantefactura" id="tipocomprobantefactura">
																		<option value="factura">Factura</option>
																		<option value="boleta">Boleta</option>
																		<option value="notaventa">Nota venta</option>
																		<option value="recibo">Recibo</option>
																		<option value="otros">Otros</option>
																	</select>
																</div>
															</div>
															<div class="col-md-3">
																<div class="form-group">
																	<label for="field-1" class="control-label">Nro Comprobante</label>
																	<input type="text" required class="form-control"
																	name="nrocomprobante" id="nrocomprobante">
																</div>
															</div>
															<div class="col-md-4 container_moneda">
																<div class="form-group">
																	<label for="field-1" class="control-label">Moneda</label>
																	<select class="form-control" onchange="selectmoneda(this)" id="moneda" name="moneda" required>
																		<option value="soles">S/</option>
																		<option value="dolares">$</option>
																	</select>
																</div>
															</div>
															<div class="col-md-2 container_cambio" style="display: none">
																<div class="form-group">
																	<label for="field-1" class="control-label">Cambio</label>
																	<input type="number" step="any" class="form-control" id="tipocambio" name="">
																</div>
															</div>
														</div>
													</div>
												</div>
												<table class="table">
													<thead>
														<th>Nº</th>
														<th>Cantidad</th>
														<th>Producto</th>
														<th>Marca</th>
														<th width="120px">Valor Compra</th>
														<th width="120px">Importe</th>
													</thead>
													<tbody id="detalleFacturar-list">
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<button type="submit" class="btn btn-success">Guardar</button>
									<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cerrar</button>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade" id="mProrrateo" role="dialog" data-backdrop="static" data-keyboard="false">
					<div class="modal-dialog" role="document" style="width: 700px">
						<div class="modal-content m-auto">
							<div class="modal-header">
								<h2 class="modal-title" id="moperation-title">PRORRATEO POR PESO</h2>
							</div>
							<div class="modal-body">
								<div class="container-fluid">
									<div class="row">
										<div class="row">
											<div class="col-sm-6">
											<label for="field-1" class="control-label">Nro RUC</label>
											<input type="text" required class="form-control"
											name="nrorucpro" id="nrorucpro">
										</div>
										<div class="col-sm-6">
											<label for="field-1" class="control-label">Proveedor</label>
											<input type="text" required class="form-control"
											name="proveedorpro" id="proveedorpro">
										</div>
										</div>
										
										<div class="row" style="margin-top: 10px">
											<div class="col-sm-3">
												<label class="control-label" for="monedapro">Moneda</label>
												<select class="form-control " name="moneda" id="moneda">
													<option value="">Select</option>
													<option value="factura">S/</option>
													<option value="boleta">$</option>
												</select>
											</div>
											<div class="col-sm-3">
												<label class="control-label" for="tipocomprobantepro">Tipo Comprobante</label>
												<select class="form-control select2-allow-clear" name="tipocomprobantepro" id="tipocomprobantepro">
													<option value="">Select</option>
													<option value="factura">Factura</option>
													<option value="boleta">Boleta</option>
													<option value="notaventa">Nota venta</option>
													<option value="recibo">Recibo</option>
													<option value="otros">Otros</option>
												</select>
											</div>
											<div class="col-sm-3">
												<label class="control-label" for="nrocomprobantepro">Nro Comprobante</label>
												<input class="form-control" name="" id="nrocomprobantepro">
											</div>

											<div class="col-sm-3">
												<label class="control-label" for="preciopro">Precio</label>
												<input class="form-control" type="number" name="" id="preciopro">
											</div>
										</div>
									</div>
									<div class="row" style="margin-top:20px">
										<table class="table">
											<thead>
												<th>Nº</th>
												<th>Cantidad</th>
												<th>Producto</th>
												<th>Marca</th>
												<th width="120px">Peso</th>
												<th width="60px">Imp Ind</th>
												<th width="60px">Importe</th>
											</thead>
											<tbody id="detalleProrrateo">
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } // Show if recordset not empty ?>

			<?php 

//___________________________________________________________________________________________________________________
			include("Fragmentos/footer.php");
			include("Fragmentos/pie.php");

			mysql_free_result($Listado);
			?>
			<script type="text/javascript">
				let arrayDetalle;
				getSelector("#prorrateo").addEventListener("click", e => {
					let nro  = 0;
					arrayDetalle.detalle.forEach(r => {
						nro++;
						$("#detalleProrrateo").append(`
							<tr>
							<td>${nro}</td>
							<td class="cant_recibida" data-cant_recibida="${r.cantidad}">${r.cantidad}</td>
							<td class="nombre_producto" >${r.nombre_producto}</td>
							<td class="marca" >${r.marca}</td>
							<td><input type="number" class="form-control pesoitempro" oninput="changepeso(this)"></td>
							<td><input readonly type="number" class="form-control importeindividualpro"></td>
							<td><input readonly type="number" class="form-control importetotalpro"></td>
							</tr>`)
					});
					$("#mProrrateo").modal();
				})
				function changepeso(e){
					if (e.value < 0){
						e.value = 0;
						return;
					}
					let proccesspeso = true;
					if($("#preciopro").val()){
						let suma = 0;
						getSelectorAll(".pesoitempro").forEach(i => {
							suma += parseInt(i.value)
							if(i.value == 0 || i.value == ""){
								proccesspeso = false;
							}
						});
						if(proccesspeso){
							const unit = $("#preciopro").val() / suma;
							getSelectorAll(".pesoitempro").forEach(i => {
								const cantidad = parseInt(i.parentElement.parentElement.querySelector(".cant_recibida").textContent)
								i.parentElement.parentElement.querySelector(".importeindividualpro").value = (unit*i.value/cantidad).toFixed(4)
								i.parentElement.parentElement.querySelector(".importetotalpro").value = (unit*i.value).toFixed(4)
							});

						}else{
							getSelectorAll(".pesoitempro").forEach(i => {
							i.parentElement.parentElement.querySelector(".importeindividualpro").value = 0
							i.parentElement.parentElement.querySelector(".importetotalpro").value = 0
						})
						}
					}else{
						getSelectorAll(".pesoitempro").forEach(i => {
							i.parentElement.parentElement.querySelector(".importeindividualpro").value = 0
							i.parentElement.parentElement.querySelector(".importetotalpro").value = 0
						})

					}
				}
				getSelector("#check_transporte").addEventListener("click", e => {
					console.log(e.target.checked)
					if(e.target.checked){
						getSelector("#container_transporte").style.display = "";
					}else{
						getSelector("#container_transporte").style.display = "none";
					}
				})
				
				getSelector("#check_estibador").addEventListener("click", e => {
					console.log(e.target.checked)
					if(e.target.checked){
						getSelector("#container_estibador").style.display = "";
					}else{
						getSelector("#container_estibador").style.display = "none";
					}
				})
				document.querySelectorAll(".setStatus").forEach(item => {
					item.addEventListener("click", (e) => {
						fetch(`editarEstadoOrdenCompra.php?codigo=${document.querySelector("#codigoOrdenCompra").value}&estado=${e.target.dataset.estado}`)
						.then(res => res.json())
						.catch(error => console.error("error: ", error))
						.then(res => {
							alert("Se ace´tó la orden de compra!")
							$("#mOrdenCompra").modal("hide");
						});
					})
				});

				document.querySelector(".modal_close").addEventListener("click", () => {
					$("#mOrdenCompra").modal("hide");
				}); var i = 0;
				document.querySelectorAll(".verOrden").forEach(item => {
					document.querySelector("#saveOrdenCompra").reset();
					item.addEventListener("click", (e) => {
						i = 0;

						document.querySelector("#codigoOrdenCompra").value = e.target.dataset.codigo
						fetch(`getDetalleOrdenCompraGuia.php?codigo=${e.target.dataset.codigo}`)
						.then(res => res.json())
						.catch(error => console.error("error: ", error))
						.then(res => {
							document.querySelector("#codigoordcomp").value = res.header.codigoordcomp
							$("#mproveedor").text(res.header.razonsocial)
							$("#mfechaemision").text(res.header.fecha_emision)
							$("#mvalortotal").text(res.header.montofact)
							$("#msucursal").text(res.header.nombre_sucursal)
							$("#mcodref1").text(res.header.codigoref1)
							$("#mcodref2").text(res.header.codigoref2 ? res.header.codigoref2 : "No tiene")
							$("#mgeneradapor").text(res.header.usuario)
							$("#mruc").text(res.header.ruc)

							$("#observacion").val(res.header.observacion)
							$("#numero-guia").val(res.header.numero_guia)
							$("#codigoguia").val(res.header.codigoguia)

							if (res.header.numero_guia) {
								document.querySelector("#th-saldo").style.display = ""
								if (res.header.estadoguia == 2 || res.header.estadoguia == 3) {
									document.querySelector("#btn-finalice").style.display = "none"
									document.querySelector("#btn-guardarGuia-facturacion").style.display = "none"
								}
							} else {
								document.querySelector("#btn-finalice").style.display = "none"
								document.querySelector("#th-saldo").style.display = "none"
							}
							document.querySelector("#detalleTableOrden-facturacion-list").innerHTML = ""
							res.detalle.forEach(r => {
								i++
								let tdExtra = "";
								let validateCant = 0;
								if (res.header.numero_guia) {
									tdExtra = `<td class="cant-extra">${parseInt(r.cantidad) - parseInt(r.cant_recibida)}</td>`
									validateCant = parseInt(r.cantidad) - parseInt(r.cant_recibida)
								} else {
									validateCant = r.cantidad
								}
								$("#detalleTableOrden-facturacion-list").append(`
									<tr>
									<td class="codigo" data-codigo_guiaoc="${r.codigo_guiaoc}" data-codigo="${r.codigo}">${i}</td>
									<td  class="cant_recibida" data-cant_recibida="${r.cantidad}">${r.cantidad}</td>
									<td class="codigoprod" data-codigoprod="${r.codigoprod}">${r.nombre_producto}</td>
									${tdExtra}
									<td style="width: 30px"><input required type="number" oninput="validateCantidad(this)" class="form-control cant-arrived" autocomplete="off" value="${r.cant_recibida}" data-cantidad="${validateCant}" readonly></td>
									</tr>`)
							});
						});
						$("#mOrdenCompra").modal();
					})
				});
				var i = 0;
				document.querySelectorAll(".verOrdenSinOc").forEach(item => {
					document.querySelector("#saveOrdenCompra").reset();
					item.addEventListener("click", (e) => {
						i = 0;

						document.querySelector("#codigoOrdenCompra").value = e.target.dataset.codigo
						fetch(`getDetalleGuiaSinOc.php?codigo=${e.target.dataset.codigo}`)
						.then(res => res.json())
						.catch(error => console.error("error: ", error))
						.then(res => {
							document.querySelector("#codigoordcomp").value = res.header.codigoordcomp
							$("#mproveedor").text(res.header.razonsocial)
							$("#mfechaemision").text(res.header.fecha_emision)
							$("#mvalortotal").text(res.header.montofact)
							$("#mcodref1").text(res.header.numero_guia)
							$("#msucursal").text(res.header.nombre_sucursal)
							$("#mcodref2").text(res.header.codigoref2 ? res.header.codigoref2 : "No tiene")
							$("#mgeneradapor").text(res.header.usuario)
							$("#mruc").text(res.header.ruc)

							$("#observacion").val(res.header.observacion)
							$("#numero-guia").val(res.header.numero_guia)
							$("#codigoguia").val(res.header.codigoguia)

							if (res.header.numero_guia) {
								document.querySelector("#th-saldo").style.display = ""
								if (res.header.estadoguia == 2 || res.header.estadoguia == 3) {
									document.querySelector("#btn-finalice").style.display = "none"
									document.querySelector("#btn-guardarGuia-facturacion").style.display = "none"
								}
							} else {
								document.querySelector("#btn-finalice").style.display = "none"
								document.querySelector("#th-saldo").style.display = "none"
							}
							document.querySelector("#detalleTableOrden-facturacion-list").innerHTML = ""
							res.detalle.forEach(r => {
								i++
								let tdExtra = "";
								let validateCant = 0;
								if (res.header.numero_guia) {
									tdExtra = `<td class="cant-extra">${parseInt(r.cantidad) - parseInt(r.cant_recibida)}</td>`
									validateCant = parseInt(r.cantidad) - parseInt(r.cant_recibida)
								} else {
									validateCant = r.cantidad
								}
								$("#detalleTableOrden-facturacion-list").append(`
									<tr>
									<td class="codigo" data-codigo_guiaoc="${r.codigo_guiaoc}" data-codigo="${r.codigo}">${i}</td>
									<td  class="cant_recibida" data-cant_recibida="${r.cantidad}">${r.cantidad}</td>
									<td class="codigoprod" data-codigoprod="${r.codigoprod}">${r.nombre_producto}</td>
									${tdExtra}
									<td style="width: 30px"><input readonly type="number" oninput="validateCantidad(this)" value="${r.cantidad}" class="form-control cant-arrived" autocomplete="off"  data-cantidad="${validateCant}"></td>
									</tr>`)
							});
						});
						$("#mOrdenCompra").modal();
					})
				});

				function validateCantidad(e) {
					if (parseInt(e.dataset.cantidad) < parseInt(e.value)) {
						e.value = ""
					}
				}
				document.querySelector("#btn-finalice").addEventListener("click", (e) => {
					const data = {
						header: {
							codigoordcomp: $("#codigoordcomp").val(),
							numeroguia: $("#numero-guia").val(),
							codigoacceso: <?= $_SESSION['kt_login_id'] ?>,
							estado: 2,
							observacion: $("#observacion").val(),
							codigoguia: $("#codigoguia").val()

						},
						detalle: []
					}
					let estado = 2;
					if (document.querySelectorAll("#detalleTableOrden-facturacion-list tr")) {
						document.querySelectorAll("#detalleTableOrden-facturacion-list tr").forEach(tr => {
							const cant_recibidda = parseInt(tr.querySelector(".cant-arrived").value);
							const cant_solicitada = parseInt(tr.querySelector(".cant_recibida").dataset.cant_recibida)

							let aux = 0;
							if (tr.querySelector(".cant-extra")) {
								aux = tr.querySelector(".cant-extra").textContent ? parseInt(tr.querySelector(".cant_recibida").textContent) - parseInt(tr.querySelector(".cant-extra").textContent) : 0;

							}
							data.detalle.push({
								codigo: tr.querySelector(".codigo").dataset.codigo,
								codigoprod: tr.querySelector(".codigoprod").dataset.codigoprod,
								cantidad: tr.querySelector(".cant_recibida").dataset.cant_recibida,
								cantidad_recibida: tr.querySelector(".cant-arrived").value ? parseInt(tr.querySelector(".cant-arrived").value) + aux : aux,
								codigo_guiaoc: tr.querySelector(".codigo").dataset.codigo_guiaoc
							})
						})
					}
					var formData = new FormData();
					formData.append("json", JSON.stringify(data))

					fetch(`setOrdenCompra.php`, { method: 'POST', body: formData })
					.then(res => res.json())
					.catch(error => console.error("error: ", error))
					.then(res => {
						$("#mOrdenCompra").modal("hide");
						if (res.success) {
							alert("registro completo!")
						}

					});

				})
				document.querySelector("#saveOrdenCompra").addEventListener("submit", (e) => {
					e.preventDefault();
					const data = {
						header: {
							codigoordcomp: $("#codigoordcomp").val(),
							numeroguia: $("#numero-guia").val(),
							codigoacceso: <?= $_SESSION['kt_login_id'] ?>,
							estado: 3,
							observacion: $("#observacion").val(),
							codigoguia: $("#codigoguia").val()

						},
						detalle: []
					}
					let estado = 3;
					if (document.querySelectorAll("#detalleTableOrden-facturacion-list tr")) {
						document.querySelectorAll("#detalleTableOrden-facturacion-list tr").forEach(tr => {
							const cant_recibidda = parseInt(tr.querySelector(".cant-arrived").value);
							const cant_solicitada = parseInt(tr.querySelector(".cant_recibida").dataset.cant_recibida)
							if (cant_solicitada != cant_recibidda) {
								estado = 1;
							}
							let aux = 0;
							if (tr.querySelector(".cant-extra")) {
								aux = tr.querySelector(".cant-extra").textContent ? parseInt(tr.querySelector(".cant_recibida").textContent) - parseInt(tr.querySelector(".cant-extra").textContent) : 0;

							}
							data.detalle.push({
								codigo: tr.querySelector(".codigo").dataset.codigo,
								codigoprod: tr.querySelector(".codigoprod").dataset.codigoprod,
								cantidad: tr.querySelector(".cant_recibida").dataset.cant_recibida,
								cantidad_recibida: parseInt(tr.querySelector(".cant-arrived").value) + aux,
								codigo_guiaoc: tr.querySelector(".codigo").dataset.codigo_guiaoc
							})
						})
					}
					data.header.estado = estado
					var formData = new FormData();
					formData.append("json", JSON.stringify(data))

					fetch(`setOrdenCompra.php`, { method: 'POST', body: formData })
					.then(res => res.json())
					.catch(error => console.error("error: ", error))
					.then(res => {
						$("#mOrdenCompra").modal("hide");
						if (res.success) {
							alert("registro completo!")
						}

					});
				})
				document.querySelectorAll(".aux_compras").forEach(item => {
					item.addEventListener("click", e => {

						getSelector("#check_transporte").checked = false;
						getSelector("#check_transporte").parentElement.classList.remove("checked")
						getSelector("#container_transporte").style.display = "none";

						getSelector("#check_estibador").checked = false;
						getSelector("#check_estibador").parentElement.classList.remove("checked")
						getSelector("#container_estibador").style.display = "none";


						if (item.dataset.type == "ordencompra") {
							url = `getDetalleOrdenCompraGuia.php?codigo=${e.target.dataset.codigo}`;
						}
						else {
							url = `getDetalleGuiaSinOc.php?codigo=${e.target.dataset.codigo}`
						}
						e.preventDefault();
						var i = 0;
						fetch(url)
						.then(res => res.json())
						.catch(error => console.error("error: ", error))
						.then(res => {
							arrayDetalle = res;
							$("#mproveedor1").text(res.header.razonsocial)
							$("#mfechaemision1").text(res.header.fecha_emision)
							$("#mvalortotal1").text(res.header.montofact)
							$("#mcodref11").text(res.header.numero_guia)
							$("#mcodref21").text(res.header.codigoref2 ? res.header.codigoref2 : "No tiene")
							$("#mgeneradapor1").text(res.header.usuario)
							$("#mruc1").text(res.header.ruc)

							$("#codigo_orden_compra").val(res.header.codigoguia ? res.header.codigoguia : 0)
							$("#codigo_guia_sin_oc").val(res.header.codigo_guia_sin_oc ? res.header.codigo_guia_sin_oc : 0)

							$("#codigoproveedor").val(res.header.codigoproveedor)
							$("#codigosucursal").val(res.header.sucursal)
							$("#msucursal1").text(res.header.nombre_sucursal)


							i = 0;
							document.querySelector("#detalleFacturar-list").innerHTML = ""
							res.detalle.forEach(r => {
								i++
								$("#detalleFacturar-list").append(`
									<tr>
									<td data-codigo="${r.codigoprod}" class="codigoprod">${i}</td>
									<td class="cantidad">${r.cantidad}</td>
									<td>${r.nombre_producto}</td>
									<td >${r.marca}</td>
									<td ><input data-toggle="tooltip"  step="any" data-placement="bottom" title="0" oninput="changepreciocompra(this)" value="${r.pcompra}" required type="number" class="precio-compra form-control"></td>

									<td class="" ><input  step="any" data-toggle="tooltip" data-placement="bottom" title="0" oninput="changeimporte(this)" value="${r.pcompra ? (r.pcompra * r.cantidad).toFixed(4) : ""}" required type="number" class="importe form-control"></td>
									</tr>`);


								let suma = 0;
								getSelectorAll(".importe").forEach(item => {
									if (item.textContent)
										suma += parseFloat(item.value)
								})
								document.querySelector("#importe-total").textContent = suma * 1.18
							});
							$('[data-toggle="tooltip"]').tooltip()
							$('.tooltips').tooltip();
						});
						$("#mFacturaCompra").modal();

					})
				});
				function changedescuento(e){
					if(e.value < 0){
						e.value = 0; 
						return;
					}
					const descuento = $("#descuento").val() ? $("#descuento").val() : 0

					let total = 0;
					let subtotal = 0;
					document.querySelectorAll(".importe").forEach(item => {
						if (item.value) {
							total += item.value * 1.18;
							subtotal +=  parseFloat(item.value);
						}
					});
					$("#importe-total").text((total*(100 - descuento) / 100).toFixed(4))
					$("#subtotal-facturacion").text((subtotal*(100 - descuento) / 100).toFixed(4))
					$("#igv-facturacion").text((subtotal*0.18*(100 - descuento) / 100).toFixed(4))
				}
				function changeimporte(e) {	
					if(e.value < 0){
						e.value = 0; 
						return;
					}
					const descuento = $("#descuento").val() ? $("#descuento").val() : 0
					const aa = e.parentElement.parentElement
					const ss =  e.value / parseInt(aa.querySelector(".cantidad").textContent)
					
					aa.querySelector(".precio-compra").value = parseFloat(ss).toFixed(4)

					let total = 0;
					let subtotal = 0;
					document.querySelectorAll(".importe").forEach(item => {
						if (item.value) {
							total += item.value * 1.18;
							subtotal +=  parseFloat(item.value);
						}
					});
					$("#importe-total").text(total.toFixed(4))
					$("#subtotal-facturacion").text(subtotal.toFixed(4)*(100 - descuento) / 100)
					$("#igv-facturacion").text((subtotal*0.18).toFixed(4))

				}
				function changepreciocompra(e, aux = true) {
					if(e.value < 0){
						e.value = 0; 
						return;
					}
					const descuento = $("#descuento").val() ? $("#descuento").val() : 0
					const aa = e.parentElement.parentElement
					const ss = parseInt(aa.querySelector(".cantidad").textContent) * e.value

					if (aux) {
						aa.querySelector(".importe").value = parseFloat(ss).toFixed(4)
						let total = 0;
						let subtotal = 0;
						document.querySelectorAll(".importe").forEach(item => {
							if (item.value) {
								total += item.value * 1.18;
								subtotal +=  parseFloat(item.value);
							}
						});
						$("#importe-total").text(total.toFixed(4))
						$("#subtotal-facturacion").text(subtotal.toFixed(4)*(100 - descuento) / 100)
						$("#igv-facturacion").text((subtotal*0.18).toFixed(4))

					}

					document.querySelector(".tooltip-inner").textContent = `${e.value} - ${(e.value * 1.18).toFixed(4)}`
					e.dataset.originalTitle = `${e.value} - ${(e.value * 1.18).toFixed(4)}`

				}
				document.querySelector("#saveFacturar").addEventListener("submit", e => {
					e.preventDefault();
					if(getSelector("#moneda").value == "dolares" && getSelector("#tipocambio").value == "" && getSelector("#tipocambio").value != "0"){
						alert("debe agregar el tipo de cambio!!")
						return;
					}
					const tipocambio = getSelector("#tipocambio").value ? getSelector("#tipocambio").value : 1;
					const data = {
						header: {},
						gastos: [],
						detalle: []
					}
					let sumaventa = 0;

					if(getSelector("#check_transporte").checked){
						if($("#tipocomprobante").val() && $("#numerocomprobante").val() && $("#empresatransporte").val() && $("#precio_transporte").val()){
							data.gastos.push(`insert into GastosCompras (tipocomprobante, nrocomprobante, empresa, precio, idcompras, tipo) values ('${$("#tipocomprobante").val()}', '${$("#numerocomprobante").val()}', '${$("#empresatransporte").val()}', ${parseFloat($("#precio_transporte").val())}, ##IDCOMPRAS##, 'transporte')`);
						}else{
							alert("debe llenar todos los datos de transporte");
							return;
						}
					}
					if(getSelector("#check_estibador").checked){
						if($("#tipocomprobanteestibador").val() && $("#numerocomprobanteestibador").val() && $("#empresaestibador").val() && $("#precio_estibador").val()){
							data.gastos.push(`insert into GastosCompras (tipocomprobante, nrocomprobante, empresa, precio, idcompras, tipo) values ('${$("#tipocomprobanteestibador").val()}', '${$("#numerocomprobanteestibador").val()}', '${$("#empresaestibador").val()}', 
								${parseFloat($("#precio_estibador").val())}, ##IDCOMPRAS##, 'estibador')`);
						}else{
							alert("debe llenar todos los datos de transporte");
							return;
						}
					}
					

					data.header = {
						codigocompras: 0,
						tipocomprobante: getSelector("#tipocomprobantefactura").value,
						nrocomprobante: getSelector("#nrocomprobante").value,
						moneda: getSelector("#moneda").value,
						codigoproveedor: getSelector("#codigoproveedor").value,
						codacceso: <?= $_SESSION['kt_login_id'] ?>,
						codigopersonal: "<?php echo $_SESSION['kt_codigopersonal']; ?>",
						subtotal: (parseFloat(getSelector("#importe-total").textContent) / 1.18).toFixed(4),
						igv: (parseFloat(getSelector("#importe-total").textContent) * 0.18).toFixed(4),
						total: getSelector("#importe-total").textContent,
						estadofact: 1,
						totalv: sumaventa,
						descuento: getSelector("#descuento").val() ? getSelector("#descuento").val() : 0,
						codsucursal: getSelector("#codigosucursal").value,
						codigo_orden_compra: getSelector("#codigo_orden_compra").value,
						codigo_guia_sin_oc: getSelector("#codigo_guia_sin_oc").value,
						tipocambio

					}
					getSelectorAll("#detalleFacturar-list tr").forEach(item => {
						let preciodolar = 0;
						let preciosoles = parseFloat(item.querySelector(".precio-compra").value).toFixed(4);
						if(getSelector("#moneda").value == "dolares"){
							preciodolar = preciosoles;
							preciosoles = preciosoles/parseInt(getSelector("#tipocambio").value)
						}
						data.detalle.push({
							codigocompras: 0,
							cantidad: item.querySelector(".cantidad").textContent,
							codigoprod: item.querySelector(".codigoprod").dataset.codigo,
							pventa: 0,
							pcompra: preciosoles,
							igv: (preciosoles * 0.18).toFixed(4),
							totalcompras: (preciosoles * item.querySelector(".cantidad").textContent).toFixed(4),
							preciodolar
						})
					})
					console.log(data)
					var formData = new FormData();
					formData.append("json", JSON.stringify(data))
					fetch(`setFactura.php`, { method: 'POST', body: formData })
					.then(res => res.json())
					.catch(error => console.error("error: ", error))
					.then(res => {
						if (res.success) {
							alert("registro completo!")
							location.reload()
						}
					});
				})
				function validatePventa(e) {
					const pcompra = parseFloat(e.closest("tr").querySelector(".precio-compra").value);
					if (pcompra > e.value) {
						e.value = ""
					}
				}
				function selectmoneda(e){
					if(e.value == "dolares"){
						getSelector(".container_moneda").classList.remove("col-md-4");
						getSelector(".container_moneda").classList.add("col-md-2");
						getSelector(".container_cambio").style.display = "";
					}else{
						getSelector(".container_moneda").classList.add("col-md-4");
						getSelector(".container_moneda").classList.remove("col-md-2");
						getSelector(".container_cambio").style.display = "none";
					}
				}
			</script>