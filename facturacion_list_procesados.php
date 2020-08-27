<?php require_once('Connections/Ventas.php'); ?>
<?php


mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT g.codigoguia,  CONCAT('Tipo Comp ',rc.tipo_comprobante, ' ',rc.numerocomprobante) as comprobante, rc.codigorc, rc.subtotal,  g.codigoordcomp, o.codigoref1,g.codigoacceso, g.numeroguia, g.estado as estadoGuia, g.fecha, o.codigo,o.codigoordcomp,o.codigoproveedor, o.fecha_emision, o.estadofact, o.sucursal, p.ruc, p.razonsocial 
from ordencompra_guia g 
inner JOIN ordencompra o on g.codigoordcomp=o.codigoordcomp 
inner JOIN proveedor p on p.codigoproveedor=o.codigoproveedor 
inner join registro_compras rc on rc.codigo_orden_compra = g.codigoguia 
where g.estado = 2 or g.estado = 3
order by g.fecha desc";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
//Enumerar filas de data tablas


mysql_select_db($database_Ventas, $Ventas);
$query_Clientes = "SELECT codigoproveedor as codigoclienten, razonsocial, ruc FROM proveedor  WHERE estado = 0 order by razonsocial";
$Clientes = mysql_query($query_Clientes, $Ventas) or die(mysql_error());
$row_Clientes = mysql_fetch_assoc($Clientes);
$totalRows_Clientes = mysql_num_rows($Clientes);

//para asignar precio y cantidad
$query_Listado1 = "SELECT a.codigoprod, a.nombre_producto from producto a INNER JOIN detalle_compras b ON a.codigoprod = b.codigoprod group by a.codigoprod";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);
//Enumerar filas de data tablas

$queryguiasinoc = "SELECT c.codigo_guia_sin_oc, CONCAT('Tipo Comp ',rc.tipo_comprobante, ' ',rc.numerocomprobante) as comprobante, rc.codigorc, rc.subtotal,a.usuario,p.ruc, s.nombre_sucursal, c.codigoref2,c.estado, c.numero_guia, p.razonsocial, p.codigoproveedor as codigoproveedor, c.fecha 
FROM guia_sin_oc c 
inner join proveedor p on c.codigoproveedor=p.codigoproveedor 
left join sucursal s on s.cod_sucursal = c.sucursal 
left join acceso a on a.codacceso = c.codacceso 
inner join registro_compras rc on rc.codigo_guia_sin_oc = c.codigo_guia_sin_oc 
where c.estado = 2
order by c.fecha desc";
$listaguiasinoc = mysql_query($queryguiasinoc, $Ventas) or die(mysql_error());
$row_listaguiasinoc = mysql_fetch_assoc($listaguiasinoc);
$totalRows_listaguiasinoc = mysql_num_rows($listaguiasinoc);



//Titulo e icono de la pagina
$Icono = "fa fa-building-o";
$Color = "font-blue";
$Titulo = "Historial de Compras";
$NombreBotonAgregar = "Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar = "disabled";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho = 700;
$popupAlto = 525;

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
<?php if ($totalRows_Listado == 0 && $totalRows_listaguiasinoc == 0) { // Show if recordset empty 
?>
	<div class="alert alert-danger">
		<strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
	</div>
<?php } // Show if recordset empty 
?>
<?php if ($totalRows_Listado > 0 || $totalRows_listaguiasinoc > 0) { // Show if recordset not empty 
?>

	<style>
		.tohidden {
			display: none;
		}
		thead, th {text-align: center;white-space: pre-wrap;}


	</style>
	<table class="table table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th> N&deg; </th>
				<th> CODIGO</th>
				<th class="none">Total </th>
				<th class="none">SUBTOTAL</th>
				<th class="none"> IGV </th>
				<th> PROVEEDOR </th>
				<th> FECHA </th>
				<th> VER </th>
				<th>TIPO</th>
				<th> IMPRIMIR </th>
			</tr>
		</thead>
		<tbody>
			<?php if ($totalRows_Listado > 0) : ?>
				<?php do {
					$color = "#bde8dc";
					if (isset($row_Listado['subtotal']) && $row_Listado['subtotal']) {
					} else {
						$row_Listado['subtotal'] = 0;
					}
					$color1 = $row_Listado['subtotal'] ? "#45f300" : "#fff100"
				?>
					<tr style="background-color: <?= $color1 ?>">
						<td> <?= $i; ?> </td>
						<td><?= $row_Listado['comprobante'] ?></td>

						<td><?= "S/. " . number_format($row_Listado['subtotal'] * $IGV1, 2); ?> </td>
						<td> <?= "S/. " . number_format($row_Listado['subtotal'], 2); ?></td>
						<td> <?= "S/. " . number_format($row_Listado['subtotal'] * ($IGV1 - 1), 2) ; ?>
						</td>
						<td> <?= $row_Listado['razonsocial']; ?></td>
						<td> <?= $row_Listado['fecha_emision']; ?></td>
						<?php if ($row_Listado['subtotal']) : ?>
							<td>
								<a href="#" data-codigoref="<?= $row_Listado['codigoref1'] ?>" data-comprobante="<?= $row_Listado['comprobante'] ?>" data-type="ordencompra" data-codigo="<?= $row_Listado['codigo'] ?>" data-codigorc="<?= $row_Listado['codigorc'] ?>" onclick="visualizar(this)">Procesado</a>
							</td>
						<?php else : ?>
							<td>
								<a href="#" class="aux_compras" data-type="ordencompra" data-codigo="<?= $row_Listado['codigo'] ?>" data-codigoguia="<?= $row_Listado['codigoguia'] ?>">Pendiente</a></td>
						<?php endif ?>
						<td>
							<a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" href="Imprimir/orden_compra.php?codigocompras=<?= $row_Listado['codigo']; ?>&codigo=<?= $row_Listado['codigoref1']; ?>" target="new"><i class="glyphicon glyphicon-credit-card"></i></a>
						</td>
						<td>Orden Compra</td>
						</td>
					</tr>
				<?php $i++;
				} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
			<?php endif ?>

			<?php if ($totalRows_listaguiasinoc > 0) : do { ?>
					<?php

					if (isset($row_listaguiasinoc['subtotal']) && $row_listaguiasinoc['subtotal']) {
					} else {
						$row_listaguiasinoc['subtotal'] = 0;
					}
					$color = $row_listaguiasinoc['subtotal'] == 0 ? "#f3c200" : "#029128"
					?>
					<tr style="background-color: <?= $color ?>">
						<td> <?= $i; ?> </td>
						<td> <?= $row_listaguiasinoc["comprobante"] . " N° Guia " . $row_listaguiasinoc['numero_guia']; ?></td>
						<td><?php echo "&#36; " . number_format($row_listaguiasinoc['subtotal'], 2); ?> </td>
						<td> <?= "&#36; " . number_format($row_listaguiasinoc['subtotal'] / $IGV1, 2); ?></td>
						<td> <?= "&#36; " . number_format(($row_listaguiasinoc['subtotal'] - number_format($row_listaguiasinoc['subtotal'] / $IGV1, 2)), 2); ?>
						</td>
						<td> <?= $row_listaguiasinoc['razonsocial']; ?></td>
						<td> <?= $row_listaguiasinoc['fecha']; ?></td>
						<?php if ($row_listaguiasinoc['subtotal'] != 0) : ?>
							<td>
								<a href="#" data-type="guia_sin_oc" onclick="visualizar(this)" data-comprobante="<?= $row_listaguiasinoc['comprobante'] ?>" data-codigo="<?= $row_listaguiasinoc['codigo_guia_sin_oc'] ?>" data-codigorc="<?= $row_listaguiasinoc['codigorc'] ?>" class="verOrdenSinOc">Procesado</a>
							</td>
						<?php else : ?>
							<td><a href="#" class="aux_compras" data-type="guia_sin_oc" data-codigo="<?= $row_listaguiasinoc['codigo_guia_sin_oc'] ?>">Facturar</a>
							</td>
						<?php endif ?>
						<td>
							<a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" href="Imprimir/orden_compra.php?codigocompras=<?= $row_listaguiasinoc['codigo']; ?>&codigo=<?= $row_listaguiasinoc['codigoref1']; ?>" target="new"><i class="glyphicon glyphicon-credit-card"></i></a>
						</td>
						<td>Guia sin OC</td>
					</tr>
			<?php $i++;
				} while ($row_listaguiasinoc = mysql_fetch_assoc($listaguiasinoc));
			endif; ?>
		</tbody>
	</table>
	<div class="modal fade" id="mOrdenCompra" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h5 class="modal-title" id="moperation-title"></h5>
				</div>
				<div class="modal-body">
					<!-- <table id="historytable" class="display" width="100%"></table> -->
					<form id="saveOrdenCompra">
						<input type="hidden" id="codigoOrdenCompra">
						<input type="hidden" id="codigoordcomp">
						<input type="hidden" id="codigoguia" value="">
						<div class="container-fluid">

							PROVEEDOR: <span id="mproveedor"></span> <BR>
							RUC : <span id="mruc"></span>
							SUCURSAL: <span id="msucursal"></span> <BR>
							FECHA DE EMISION : <span id="mfechaemision"></span> <br>
							VALOR TOTAL: <span id="mvalortotal"></span><BR>
							CODIGO DE REF 1 : <span id="mcodref1"></span> <br>
							CODIGO REF2: : <span id="mcodref2"></span> <br>
							GENERADA POR: : <span id="mgeneradapor"></span> <br>


							<div class="row" style="margin-top:20px">
								<div class="col-xs-12 col-md-12">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="field-1" class="control-label">Numero Guia</label>
												<input type="text" disabled class="form-control" name="numero-guia" id="numero-guia">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="field-1" class="control-label">Observacion</label>
												<input type="text" disabled class="form-control" name="observacion" id="observacion">
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
		<div class="modal-footer">
			<button type="button" id="btn-finalice" style="display: none" class="btn btn-primary">Finalizar</button>
			<button type="submit" id="btn-guardarGuia-facturacion" class="btn btn-success">Guardar</button>
			<button type="button" data-dismiss="modal" class="modal_close btn btn-danger">Cerrar</button>
		</div>

		</form>
	</div>
	</div>
	</div>
	</div>

	<div class="modal fade" id="mFacturaCompra" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document" style="width: 1300px">
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h2 class="modal-title" id="">Registro de Compras / Por Recibir </h2>
				</div>
				<div class="modal-body">

					<form id="saveFacturar">
						<div class="container-fluid">
							<div class="row">
								<div class="col-xs-12 col-md-12">
									<b>
										<div style="text-align: right">
											FECHA DE REGISTRO: <span id="mfechaemision1"></span> <br>
										</div>
										SUCURSAL: <span id="msucursal1"></span> <BR>
										GENERADA POR: <span id="mgeneradapor1"></span>
										<div style="display: none;">
											RUC: <span id="mruc1"></span><BR>
											<span id="viewcodigoreferencia"></span> <br>
											GUIA: <span id="mcodref11"></span> <br>
											<span id="viewcomprobante"></span> <br>
										</div>
										<span id="auxmodref">DOC REF 2:</span> <span id="mcodref21"></span> <br>

									</b>
									<input type="hidden" id="codigoproveedor">
									<input type="hidden" id="codigosucursal">
									<input type="hidden" id="codigo_orden_compra">
									<input type="hidden" id="codigo_guia_sin_oc">

									<input type="hidden" id="codigorcxx">

									<table id="historytable" class="display" width="100%"></table>
									<div class="row" style="margin-top: 20px; display: none">
										<div class="col-xs-12 col-md-12">
											<div class="row">
												<div class="col-md-2">
													<div class="form-group">
														<label for="field-1" class="control-label">Fecha Emision</label>
														<input type="text" required name="facturafechaemision" autocomplete="off" id="facturafechaemision" class="form-control form-control-inline input-medium date-picker" data-date-format="yyyy-mm-dd" required />
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<label for="field-1" class="control-label">Tipo Comp</label>
														<select class="form-control" name="tipocomprobantefactura" id="tipocomprobantefactura">
															<option value="factura">Factura</option>
															<option value="boleta">Boleta</option>
															<option value="recibo">Recibo</option>
															<option value="recibo">Nota Debito</option>
															<option value="recibo">Nota Credito</option>
															<option value="otros">Otros</option>
														</select>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label for="field-1" class="control-label">Nro Comprobante</label>
														<input type="text" required class="form-control" name="nrocomprobante" id="nrocomprobante">
													</div>
												</div>
												<div class="col-md-2 container_moneda">
													<div class="form-group">
														<label for="field-1" class="control-label">Moneda</label>
														<select class="form-control" onchange="selectmoneda(this)" id="moneda" name="moneda" required>
															<option selected value="soles">S/</option>
															<option value="dolares">$</option>
														</select>
													</div>
												</div>
												<div class="col-md-2 container_cambio" id="container_cambio" style="display: none">
													<div class="form-group">
														<label for="field-1" class="control-label">Cambio</label>
														<input type="number" min="1" value="1" step="any" class="form-control" id="tipocambio" oninput="changecambiodolar(this)" name="">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label for="field-1" class="control-label">Descuento General</label>
														<input type="number" class="form-control" oninput="changedescuentogeneral(this)" step="any" id="descuento" name="">
													</div>
												</div>
											</div>
										</div>
									</div>
									<table class="table">
										<thead>
											<th class="text-center">Nº</th>
											<th class="text-center">Cantidad</th>
											<th class="text-center">Codigo</th>
											<th class="text-center tohidden">Color</th>
											<th class="text-center">Producto</th>
											<th class="text-center tohidden">Desc x Item</th>
											<th class="text-center" class="costeosinchecked">VC</th>
											<th class="text-center" class="costeosinchecked">CARGAS/DSCTS VINCULADOS</th>
											<th class="text-center" class="costeosinchecked">VC<BR> TOTAL</th>
											<th class="text-center" class="costeosinchecked">VC<BR> Un.</th>
											<th class="text-center" class="costeosinchecked">IGV<BR> Un.</th>
											<th class="text-center" class="costeosinchecked">PC<BR> Un.</th>
											<th class="costeosinchecked tohidden">VCI</th>
											<th class="costeosinchecked tohidden">DSCTO</th>
											<th class="tohidden">VCF</th>
											<th class="costeosinchecked tohidden"><?= $nombreigv ?></th>
											<th class="costeosinchecked tohidden">Total</th>
											<th class="costeochecked tohidden" style="display: none">Transporte</th>
											<th class="costeochecked tohidden" style="display: none">Estibador</th>
											<th class="costeochecked tohidden" style="display: none">Nota Debito</th>
											<th class="costeochecked tohidden" style="display: none">Nota Credito</th>
											<th class="costeochecked tohidden" style="display: none">Total</th>
											<th class="costeochecked tohidden" style="display: none">VCUF</th>
										</thead>
										<tbody id="detalleFacturar-list">
										</tbody>
									</table>

								</div>
							</div>
						</div>
						<div class="modal-footer">

							<button class="btn btn-success" id="actualizarextras" type="button" onclick="funactualizarextras()">Actualizar</button>

							<button class="btn btn-success" id="showopcionesextras" type="button" onclick="showopciones()">Opciones</button>
							<button type="submit" id="guardarcosteo" class="btn btn-success">Guardar</button>
							<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cerrar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mopcionesextras" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document" style="width: 700px">
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h2 class="modal-title" id="">Opciones extras</h2>
				</div>
				<div class="modal-body">
					<div style="margin-top: 10px" class="divxx div_transporte">
						<label class="" for="check_transporte">transporte?</label>
						<input type="checkbox" class="" id="check_transporte">

						<div class="row" style="display: none" id="container_transporte">
							<div class="col-sm-6 text-center">
								<button type="button" disabled class="btn btn-success" data-type="prorrateo" id="btn_prorrateo" onclick="setExtra(this)">PRORRATEO X PESO</button>
							</div>
							<div class="col-sm-6 text-center">
								<button type="button" disabled class="btn btn-success" data-type="participacion" id="btn_participacion" onclick="setExtra(this)" id="participacion">PRORRATEO POR
									COMPRA</button>
							</div>
						</div>
					</div>
					<div style="margin-top: 10px" class="divxx div_estibador">
						<label class="" for="check_estibador">Estibador?</label>
						<input type="checkbox" class="" id="check_estibador">

						<div class="row" style="display: none" id="container_estibador">
							<div class="col-sm-6">
								<label class="control-label" for="proveedorestibador">PROVEEDOR</label>
								<select name="proveedor" id="proveedorestibador" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
									<option value="">Seleccione</option>
									<?php do {  ?>
										<option value="<?= $row_Clientes['razonsocial'] . '&&&' . $row_Clientes['ruc'] ?>">
											<?= $row_Clientes['razonsocial'] . ' ' . $row_Clientes['ruc'] ?>
										</option>
									<?php
									} while ($row_Clientes = mysql_fetch_assoc($Clientes));
									$rows = mysql_num_rows($Clientes);
									if ($rows > 0) {
										mysql_data_seek($Clientes, 0);
										$row_Clientes = mysql_fetch_assoc($Clientes);
									}
									?>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="tipocomprobanteestibador">Tipo Comprobante</label>
								<select class="form-control" name="tipocomprobanteestibador" id="tipocomprobanteestibador">
									<option value="factura">Factura</option>
									<option value="boleta">Boleta</option>
									<option value="notaventa">Nota venta</option>
									<option value="recibo">Recibo</option>
									<option value="otros">Otros</option>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="numerocomprobanteestibador">Nro
									Comprobante</label>
								<input class="form-control" name="" id="numerocomprobanteestibador">
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="monedaestibador">Moneda</label>
								<select class="form-control" data-container="containertipocambioestibador" onchange="changetipomoneda(this)" id="monedaestibador" name="monedaestibador" required>
									<option value="soles">S/</option>
									<option value="dolares">$</option>
								</select>
							</div>
							<div class="col-sm-3" id="containertipocambioestibador" style="display: none">
								<label class="control-label" for="tipocambioestibador">Cambio</label>
								<input class="form-control" type="number" value="1" min="1" step="any" id="tipocambioestibador">
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="precio_estibador">Valor de Compra</label>
								<input class="form-control" data-tipocambio="tipocambioestibador" data-type="estibador_costeo" oninput="changeprecioestibador(this)" disabled type="number" name="" id="precio_estibador">
							</div>
						</div>
					</div>
					<div style="margin-top: 10px" class="divxx div_notadebito">
						<label class="" for="check_notadebito">Nota Debito?</label>
						<input type="checkbox" class="" id="check_notadebito">

						<div class="row" style="display: none" id="container_notadebito">
							<div class="col-sm-6">
								<label class="control-label" for="proveedornotadebito">PROVEEDOR</label>
								<select name="proveedor" id="proveedornotadebito" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
									<option value="">Seleccione</option>
									<?php do {  ?>
										<option value="<?= $row_Clientes['razonsocial'] . '&&&' . $row_Clientes['ruc'] ?>">
											<?= $row_Clientes['razonsocial'] . ' ' . $row_Clientes['ruc'] ?>
										</option>
									<?php
									} while ($row_Clientes = mysql_fetch_assoc($Clientes));
									$rows = mysql_num_rows($Clientes);
									if ($rows > 0) {
										mysql_data_seek($Clientes, 0);
										$row_Clientes = mysql_fetch_assoc($Clientes);
									}
									?>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="tipocomprobantenotadebito">Tipo Comprobante</label>
								<select class="form-control" name="tipocomprobantenotadebito" id="tipocomprobantenotadebito">
									<option value="factura">Factura</option>
									<option value="boleta">Boleta</option>
									<option value="notaventa">Nota venta</option>
									<option value="recibo">Recibo</option>
									<option value="otros">Otros</option>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="numerocomprobantenotadebito">Nro Comprobante</label>
								<input class="form-control" name="" id="numerocomprobantenotadebito">
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="monedanotadebito">Moneda</label>
								<select class="form-control" data-container="containertipocambionotadebito" onchange="changetipomoneda(this)" id="monedanotadebito" name="monedanotadebito" required>
									<option value="soles">S/</option>
									<option value="dolares">$</option>
								</select>
							</div>
							<div class="col-sm-3" id="containertipocambionotadebito" style="display: none">
								<label class="control-label" for="tipocambionotadebito">Cambio</label>
								<input class="form-control" type="number" value="1" min="1" step="any" id="tipocambionotadebito">
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="precio_notadebito">Precio</label>
								<input class="form-control" data-type="notadebito" oninput="changeprecioestibador(this)" disabled type="number" data-tipocambio="tipocambionotadebito" id="precio_notadebito">
							</div>
						</div>
					</div>
					<div style="margin-top: 10px" class="divxx div_notacredito">
						<label class="" for="check_notacredito">Nota credito?</label>
						<input type="checkbox" class="" id="check_notacredito">

						<div class="row" style="display: none" id="container_notacredito">
							<div class="col-sm-6">
								<label class="control-label" for="proveedornotacredito">PROVEEDOR</label>
								<select name="proveedor" id="proveedornotacredito" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
									<option value="">Seleccione</option>
									<?php do {  ?>
										<option value="<?= $row_Clientes['razonsocial'] . '&&&' . $row_Clientes['ruc'] ?>">
											<?= $row_Clientes['razonsocial'] . ' ' . $row_Clientes['ruc'] ?>
										</option>
									<?php
									} while ($row_Clientes = mysql_fetch_assoc($Clientes));
									$rows = mysql_num_rows($Clientes);
									if ($rows > 0) {
										mysql_data_seek($Clientes, 0);
										$row_Clientes = mysql_fetch_assoc($Clientes);
									}
									?>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="tipocomprobantenotacredito">Tipo Comprobante</label>
								<select class="form-control" name="tipocomprobantenotacredito" id="tipocomprobantenotacredito">
									<option value="factura">Factura</option>
									<option value="boleta">Boleta</option>
									<option value="notaventa">Nota venta</option>
									<option value="recibo">Recibo</option>
									<option value="otros">Otros</option>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="numerocomprobantenotacredito">Nro Comprobante</label>
								<input class="form-control" name="" id="numerocomprobantenotacredito">
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="monedanotacredito">Moneda</label>
								<select class="form-control" data-container="containertipocambionotacredito" onchange="changetipomoneda(this)" id="monedanotacredito" name="monedanotacredito" required>
									<option value="soles">S/</option>
									<option value="dolares">$</option>
								</select>
							</div>
							<div class="col-sm-3" id="containertipocambionotacredito" style="display: none">
								<label class="control-label" for="tipocambionotacredito">Cambio</label>
								<input class="form-control" type="number" value="1" min="1" step="any" id="tipocambionotacredito">
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="precio_notacredito">Precio</label>
								<input class="form-control" data-tipocambio="tipocambionotacredito" data-type="notacredito" oninput="changeprecioestibador(this)" disabled type="number" name="" id="precio_notacredito">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="modal fade" id="mhistory" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document" style="width: 1000px">
		<div class="modal-content m-auto">
			<div class="modal-header">
				<h2 class="modal-title" id="title_extra">Historial Comprobantes</h2>
			</div>
			<div class="modal-body">
				<table id="historytable" class="display" width="100%"></table>
			</div>
			<div class="modal-footer">
			<button type="button" data-dismiss="modal" aria-label="Close"
							class="btn btn-danger">Cerrar</button>
			</div>
		</div>
	</div>
</div> -->

	<div class="modal fade" id="mProrrateo" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document" style="width: 900px">
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h2 class="modal-title" id="titleprorrateo">PRORRATEO POR PESO</h2>
				</div>
				<div class="modal-body">
					<form id="formExtra">
						<div class="container-fluid">
							<div class="row">
								<div class="row">
									<div class="col-sm-6">
										<label for="field-1" class="control-label">PROVEEDOR</label>

										<select disabled name="proveedor" id="proveedorpro" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
											<option value="">Seleccione</option>
											<?php do {  ?>
												<option value="<?= $row_Clientes['ruc'] ?>">
													<?= $row_Clientes['razonsocial'] . ' ' . $row_Clientes['ruc'] ?>
												</option>
											<?php
											} while ($row_Clientes = mysql_fetch_assoc($Clientes));
											$rows = mysql_num_rows($Clientes);
											if ($rows > 0) {
												mysql_data_seek($Clientes, 0);
												$row_Clientes = mysql_fetch_assoc($Clientes);
											}
											?>
										</select>
									</div>
								</div>

								<div class="row" style="margin-top: 10px">
									<div class="col-sm-3">
										<label class="control-label" for="monedapro">Moneda</label>
										<select disabled class="form-control" name="monedapro" id="monedapro" onchange="changemonedapro(this)">
											<option value="soles">S/</option>
											<option value="dolares">$</option>
										</select>
									</div>
									<div class="col-sm-3" id="containerTipoCambio" style="display: none">
										<label class="control-label" for="monedapro">Cambio</label>
										<input disabled type="number" class="form-control" value="1" min="1" step="any" name="tipocambiopro" id="tipocambiopro" oninput="changepeso(preciopro)">
									</div>
									<div class="col-sm-3">
										<label class="control-label" for="tipocomprobantepro">Tipo Comprobante</label>
										<select disabled class="form-control " name="tipocomprobantepro" id="tipocomprobantepro" required>
											<option value="guia">Guia</option>
											<option value="factura">Factura</option>
											<option value="boleta">Boleta</option>
											<option value="notaventa">Nota venta</option>
											<option valgit ue="recibo">Recibo</option>
											<option value="otros">Otros</option>
										</select>
									</div>
									<div class="col-sm-3">
										<label class="control-label" for="nrocomprobantepro">Nro Comprobante</label>
										<input disabled class="form-control" name="" id="nrocomprobantepro">
									</div>

									<div class="col-sm-3">
										<label class="control-label" for="preciopro">Valor Compra</label>
										<input disabled class="form-control" step="any" oninput="changepeso(this)" type="number" name="" id="preciopro">
									</div>
									<input type="hidden" id="costeocodigorc">
									<input type="hidden" id="idtipocosteo">
								</div>
							</div>
							<div class="row" style="margin-top:20px">
								<table class="table">
									<thead>
										<th>Nº</th>
										<th>Cant</th>
										<th width="200">Producto</th>
										<th width="100">Marca</th>
										<th id="varTypeExtra" width="120px">Peso</th>
										<th width="120px">Imp Ind</th>
										<th width="120px">Importe</th>
									</thead>
									<tbody id="detalleProrrateo">
									</tbody>
								</table>
							</div>
							<button class="btn btn-primary" type="submit">Guardar</button>
							<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cerrar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

<?php } // Show if recordset not empty 
?>

<?php

//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);
?>
<script type="text/javascript">
	function keyControl(key) {
		var control = [0, 8];
		var result;
		if (control.indexOf(key.which) >= 0) {
			result = true;
		} else {
			result = false;
		}
		return result;
	}

	function pathKey(eReg, key) {
		var letra = String.fromCharCode(key.which);
		return keyControl(key) || eReg.test(letra);
	}


	(function($) {
		$(document).ready(function() {
			$(document).on('keypress', '.sololetras', function(key) {
				return pathKey(/^[a-z]| |[ñÑáéíóúÁÉÍÓÚ]$/i, key);
			});
			$(document).on('keypress', '.sololetras', function(key) {
				return pathKey(/^[a-z]| |[ñÑáéíóúÁÉÍÓÚ]$/i, key);
			});
			$(document).on('keypress', '.solonumeros', function(key) {
				return pathKey(/^[0-9.]/i, key);
			});
			$(document).on('keypress', '.nospace', function(key) {
				return pathKey(/^\S/i, key);
			});
			//agregado por NN 27/09/
			$(document).on('keypress', '.cantidades', function(key) {
				return pathKey(/^[0-9]|[.]$/i, key);
			});
			$(document).on('keypress', '.correo', function(key) {
				return pathKey(/^[a-z]|[0-9]|[-_.@]/i, key);
			});
			$(document).on('keypress', '.especiales', function(key) {
				return pathKey(/^[-a-zA-Z0-9_.ñÑÁÉÍÓÚáéíóú\s]+$/i, key);
			});
			$(document).on('keypress', '.letrasnumeros', function(key) {
				return pathKey(/^[-a-zA-Z0-9]+$/i, key);
			});
			$(document).on('keypress', '.letrasnumeros', function(key) {
				return pathKey(/^[-a-zA-Z0-9]+$/i, key);
			});
			$(document).on('keypress', '.address', function(key) {
				return pathKey(/^[-a-zA-Z0-9_.,#\s]+$/i, key);
			});
			$(document).on('keypress', '.letras_especiales', function(key) {
				return pathKey(/^[-a-zA-Z_.,#@()\s]+$/i, key);
			});
			$(document).on('keypress', '.letrasnumeros_especiales', function(key) {
				return pathKey(/^[-a-zA-Z0-9_.,#@\s]+$/i, key);
			});
			$(document).on('keypress', '.letrasnumeros_coma', function(key) {
				if ($('.letrasnumeros_coma').val().length > 0) {
					var last_caracter = $('.letrasnumeros_coma').val().substring($('.letrasnumeros_coma').val().length - 1, $('.letrasnumeros_coma').val().length);
					if (last_caracter == "," && last_caracter == key.key) {
						return false;
					} else {
						return pathKey(/^[a-zA-Z0-9,]+$/i, key);
					}
				} else {
					return pathKey(/^[a-zA-Z0-9]+$/i, key);
				}

			});
		});
	})(jQuery);
	$(document).on('contextmenu', 'input, select, textarea', function() {
		return false;
	});
</script>
<script type="text/javascript">
	$(document).on('focus', '.focusandclean', function(e) {
		if (e.target.value && parseInt(e.target.value) == 0) {
			e.target.value = ""
		}
	});

	let arrayDetalle;
	let windowtype = "costeosingle"
	let monedadolar = false;
	let typetransporte = "";
	let subtotalGLOBAL = 0;

	function checkcosteo(e) {
		if (e.checked) {
			windowtype = "costeoextra"
			if ("dolares" == moneda.value) {
				const cambio = parseFloat(tipocambio.value)
				getSelectorAll(".vcf").forEach(i => {
					i.dataset.vcfsoles = i.value;
					i.value = (parseFloat(i.value) * cambio).toFixed(2);
				})
			}
			getSelectorAll(".costeochecked").forEach(e => {
				e.style.display = ""
			})
			getSelectorAll(".costeosinchecked").forEach(e => {
				e.style.display = "none"
			})

		} else {
			windowtype = "costeosingle"
			if ("dolares" == moneda.value) {
				getSelectorAll(".vcf").forEach(i => {
					i.value = i.dataset.vcfsoles ? i.dataset.vcfsoles : i.value
				})
			}
			getSelectorAll(".costeochecked").forEach(e => {
				e.style.display = "none"
			})

			getSelectorAll(".costeosinchecked").forEach(e => {
				e.style.display = ""
			})
		}
	}

	function showopciones() {
		$("#mopcionesextras").modal();
	}

	function changemonedapro(e) {
		if (e.value == "dolares") {
			containerTipoCambio.style.display = ""
		} else {
			tipocambiopro.value = 1
			containerTipoCambio.style.display = "none"
		}
		changepeso(preciopro)
	}

	function changedescuentogeneral(e) {
		if (e.value < 0 || e.value == "") {
			e.value = 0;
		}
		const tr = getSelector(".descuento").closest("tr");

		getSelectorAll(".descuento").forEach(i => {
			i.value = e.value
			const tr = i.closest("tr");
			calcularFila(tr)
		})
		calcularTotalSinExtras();
		const subtotal = subtotalGLOBAL;
	}

	function changetipomoneda(e) {
		if (e.value == "dolares") {
			getSelector(`#${e.dataset.container}`).style.display = ""
		} else {
			getSelector(`#${e.dataset.container}`).style.display = "none"
			getSelector(`#${e.dataset.container.split("container")[1]}`).value = 1
		}
	}
	formExtra.addEventListener("submit", async (e) => {
		e.preventDefault()
		if (getSelector(".importeindividualpro").value && getSelector(".importeindividualpro").value != 0) {
			if ("dolares" == monedapro.value && "" == tipocambiopro.value) {
				alert("debe ingresar todos los campos")
			} else {

				const datatotrigger = {
					header: "",
					detalle: []
				}

				let tipocambio = 1;
				if (monedapro.value == "dolares") {
					tipocambio = parseFloat(tipocambiopro.value)
				}
				let nametable = "";
				let column = "";
				if (typetransport === "Estibador") {
					nametable = "estibador_compra";
					column = "id_estibador";
				} else if (typetransport === "Nota Debito") {
					nametable = "notadebito_compra";
					column = "id_notadebito"
				} else if (typetransport === "Nota Credito") {
					nametable = "notacredito_compra";
					column = "id_notacredito";
				} else {
					datatotrigger.header = `update transporte_compra set tipo_transporte = '${typetransport}' where id_transporte = ${idtipocosteo.value}`;
				}

				if (nametable) {
					datatotrigger.header = `update ${nametable} set asignado = true where ${column} = ${idtipocosteo.value}`;
				}
				for (let index = 0; index < getSelectorAll(".rowcosteotrans").length; index++) {
					const tr = getSelectorAll(".rowcosteotrans")[index];
					const coddetprod = parseInt(tr.querySelector(".codigodetalleproducto").textContent);
					const impindv = tr.querySelector(".importeindividualpro").value
					const imptotal = tr.querySelector(".importetotalpro").value

					const rows = await get_data_dynamic(`select rc.valorcambio from detalle_compras dc inner join registro_compras rc on rc.codigorc = dc.codigocompras where codigodetalleproducto = ${coddetprod}`);

					const imponn = impindv / rows[0].valorcambio;
					const impott = imptotal / rows[0].valorcambio;

					datatotrigger.detalle.push(`
						update detalle_compras set 
							vcu = (vcu + ${imponn}),
							gastoextras = (gastoextras + ${impott})
						where codigodetalleproducto = ${coddetprod}`);
				}

				let res = await ll_dynamic(datatotrigger);

				if (res.success) {
					alert("Se actualizó!");
					location.reload()
				} else {
					alert("Hubo un problema");
				}

				$("#mProrrateo").modal("hide");
			}

		} else {
			alert("debe ingresar todos los campos")
		}
	})

	function setExtra(e) {
		preciopro.value = "";
		nrocomprobantepro.value = "";
		let nro = 0;
		if (e.dataset.type == "prorrateo") {
			typetransporte = "porpeso";
			title_extra.textContent = "Resgistro Complementario de Compra - Transporte";
			varTypeExtra.textContent = "Peso"
			detalleProrrateo.innerHTML = ""
			arrayDetalle.detalle.forEach(r => {
				nro++;
				$("#detalleProrrateo").append(`
					<tr>
					<td>${nro}</td>
					<td class="cant_recibida" data-cant_recibida="${r.cantidad}">${r.cantidad}</td>
					<td class="nombre_producto" >${r.nombre_producto}</td>
					<td class="marca" >${r.marca}</td>
					<td><input type="number" required step="any" class="form-control pesoitempro" oninput="changepeso(this)"></td>
					<td><input disabled type="number"  class="form-control importeindividualpro"></td>
					<td><input disabled data-indexdetalle="${nro}" type="number" class="form-control importetotalpro"></td>
					</tr>`)
			});
		} else {
			typetransporte = "porparticipacion";
			varTypeExtra.textContent = "VCF"
			title_extra.textContent = "PARTICIPACION POR COMPRAS"
			detalleProrrateo.innerHTML = ""
			arrayDetalle.detalle.forEach(r => {
				nro++;
				$("#detalleProrrateo").append(`
					<tr>
					<td>${nro}</td>
					<td class="cant_recibida" data-cant_recibida="${r.cantidad}">${r.cantidad}</td>
					<td class="nombre_producto" >${r.nombre_producto}</td>
					<td class="marca" >${r.marca}</td>
					<td><input type="number" disabled required class="form-control pesoitempro" oninput="changepeso(this)" value="${getSelector("#vcf_" + nro).value}"></td>
					<td><input disabled type="number" class="form-control importeindividualpro"></td>
					<td><input disabled data-indexdetalle="${nro}" type="number" class="form-control importetotalpro"></td>
					</tr>`)
			});
		}

		$("#mProrrateo").modal();
	}

	function changepeso(e) {
		if (e.value < 0) {
			e.value = 0;
			return;
		}
		let proccesspeso = true;
		if ($("#preciopro").val()) {
			// let suma = 0;
			// let sumacantidad = 0;

			getSelectorAll(".pesoitempro").forEach(i => {
				// suma += parseFloat(i.value)
				if (i.value == 0 || i.value == "") {
					proccesspeso = false;
				}
			});
			// getSelectorAll(".cant_recibida").forEach(i => {
			// 	sumacantidad += parseFloat(i.textContent)
			// });
			if (proccesspeso) {

				let totalx = 0;
				getSelectorAll("#detalleProrrateo tr").forEach(tr => {
					const peso = tr.querySelector(".pesoitempro").value;
					const cantidad = tr.querySelector(".cant_recibida").textContent;
					totalx += peso * cantidad;
				});

				const unit = $("#preciopro").val() / totalx;

				getSelectorAll(".pesoitempro").forEach(i => {
					const cantidad = parseInt(i.closest("tr").querySelector(".cant_recibida").textContent)
					const peso = parseInt(i.closest("tr").querySelector(".pesoitempro").value)

					const totalimporte = unit * cantidad * peso;

					i.parentElement.parentElement.querySelector(".importeindividualpro").value = (totalimporte / cantidad).toFixed(4)
					i.parentElement.parentElement.querySelector(".importetotalpro").value = totalimporte.toFixed(4)
				});

			} else {
				getSelectorAll(".pesoitempro").forEach(i => {
					i.parentElement.parentElement.querySelector(".importeindividualpro").value = 0
					i.parentElement.parentElement.querySelector(".importetotalpro").value = 0
				})
			}
		} else {
			getSelectorAll(".pesoitempro").forEach(i => {
				i.parentElement.parentElement.querySelector(".importeindividualpro").value = 0
				i.parentElement.parentElement.querySelector(".importetotalpro").value = 0
			})

		}
	}
	getSelector("#check_transporte").addEventListener("click", e => {
		console.log(e.target.checked)
		if (e.target.checked) {
			getSelector("#container_transporte").style.display = "";
		} else {
			getSelector("#container_transporte").style.display = "none";
		}
	})

	getSelector("#check_estibador").addEventListener("click", e => {
		console.log(e.target.checked)
		if (e.target.checked) {
			getSelector("#container_estibador").style.display = "";
		} else {
			getSelector("#container_estibador").style.display = "none";
		}
	})

	getSelector("#check_notadebito").addEventListener("click", e => {
		console.log(e.target.checked)
		if (e.target.checked) {
			getSelector("#container_notadebito").style.display = "";
		} else {
			getSelector("#container_notadebito").style.display = "none";
		}
	})

	getSelector("#check_notacredito").addEventListener("click", e => {
		console.log(e.target.checked)
		if (e.target.checked) {
			getSelector("#container_notacredito").style.display = "";
		} else {
			getSelector("#container_notacredito").style.display = "none";
		}
	})
	let typetransport = "";

	async function asignarcosto(e) {
		detalleProrrateo.innerHTML = "";

		const {
			codigorc,
			id,
			tipocomprobante,
			numerocomprobante,
			ruc,
			moneda,
			soles,
			dolares,
			detalle
		} = e.dataset;
		typetransport = detalle;
		costeocodigorc.value = codigorc;
		idtipocosteo.value = id;
		const querydetalle = `select dc.codigodetalleproducto, dc.codigoprod, pro.nombre_producto, m.nombre marca, dc.vcf, dc.cantidad from detalle_compras dc inner join producto pro on pro.codigoprod = dc.codigoprod left join marca m on m.codigomarca = pro.codigomarca where dc.codigocompras = ${codigorc}`;

		let datadetalle = await get_data_dynamic(querydetalle);
		let nro = 0;

		titleprorrateo.textContent = `ASIGNAR COSTEO DE ${detalle.toUpperCase()}`;
		varTypeExtra.textContent = "VCF";
		const total = datadetalle.reduce((total, x) => total + parseInt(x.cantidad), 0);

		const unit = soles / total;

		datadetalle.forEach(r => {

			const totalimporte = unit * r.cantidad;
			const importeindividualpro = totalimporte / r.cantidad;

			nro++;
			$("#detalleProrrateo").append(`
				<tr class="rowcosteotrans">
					<td style="display: none" class="codigodetalleproducto">${r.codigodetalleproducto}</td>
					<td>${nro}</td>
					<td class="cant_recibida" data-cant_recibida="${r.cantidad}">${r.cantidad}</td>
					<td class="nombre_producto" >${r.nombre_producto}</td>
					<td class="marca" >${r.marca}</td>
					<td><input type="number" disabled required class="form-control pesoitempro" oninput="changepeso(this)" value="${r.vcf}"></td>
					<td><input disabled type="number" class="form-control importeindividualpro" value="${importeindividualpro.toFixed(4)}"></td>
					<td><input disabled data-indexdetalle="${nro}" type="number" class="form-control importetotalpro" value="${totalimporte.toFixed(4)}"></td>
				</tr>`)
		});

		$('#proveedorpro').val(ruc).trigger('change');
		monedapro.value = moneda;
		tipocomprobantepro.value = tipocomprobante;
		nrocomprobantepro.value = numerocomprobante;
		preciopro.value = soles;

		$("#mProrrateo").modal();
	}

	async function asignartransporte(e) {
		detalleProrrateo.innerHTML = "";
		const tipocompra = e.dataset.type;
		const codigorc = e.dataset.codigorc;
		const transporteid = e.dataset.transporteid;
		idtipocosteo.value = transporteid;
		costeocodigorc.value = codigorc;
		typetransport = e.dataset.type;

		const query = `
		select tipocomprobante, numerocomprobante, ructransporte, moneda, tipocambio, preciotransp_soles, preciotransp_dolar, codigocompras from transporte_compra where id_transporte = ${transporteid}
        `;

		let data = await get_data_dynamic(query);
		data = data[0];

		const querydetalle = `select dc.codigodetalleproducto, dc.codigoprod, pro.nombre_producto, m.nombre marca, dc.vcf, dc.cantidad from detalle_compras dc inner join producto pro on pro.codigoprod = dc.codigoprod left join marca m on m.codigomarca = pro.codigomarca where dc.codigocompras = ${codigorc}`;

		let datadetalle = await get_data_dynamic(querydetalle);
		let nro = 0;

		if (tipocompra === "porpeso") {
			titleprorrateo.textContent = "Resgistro Complementario de Compra - Transporte";
			varTypeExtra.textContent = "Peso";
			datadetalle.forEach(r => {
				nro++;
				$("#detalleProrrateo").append(`
					<tr>
						<td style="display: none" class="codigodetalleproducto">${r.codigodetalleproducto}</td>
						<td>${nro}</td>
						<td class="cant_recibida" data-cant_recibida="${r.cantidad}">${r.cantidad}</td>
						<td class="nombre_producto" >${r.nombre_producto}</td>
						<td class="marca" >${r.marca}</td>
						<td><input type="number" required step="any" class="form-control pesoitempro" oninput="changepeso(this)"></td>
						<td><input disabled type="number"  class="form-control importeindividualpro"></td>
						<td><input disabled data-indexdetalle="${nro}" type="number" class="form-control importetotalpro"></td>
					</tr>`)
			});
		} else {
			titleprorrateo.textContent = "PARTICIPACION POR COMPRAS";
			varTypeExtra.textContent = "VCF";
			const total = datadetalle.reduce((total, x) => total + parseInt(x.cantidad), 0);

			const unit = data.preciotransp_soles / total;

			datadetalle.forEach(r => {

				const totalimporte = unit * r.cantidad;
				const importeindividualpro = totalimporte / r.cantidad;

				nro++;
				$("#detalleProrrateo").append(`
					<tr class="rowcosteotrans">
						<td>${nro}</td>
						<td style="display: none" class="codigodetalleproducto">${r.codigodetalleproducto}</td>
						<td class="cant_recibida" data-cant_recibida="${r.cantidad}">${r.cantidad}</td>
						<td class="nombre_producto" >${r.nombre_producto}</td>
						<td class="marca" >${r.marca}</td>
						<td><input type="number" disabled required class="form-control pesoitempro" oninput="changepeso(this)" value="${r.vcf}"></td>
						<td><input disabled type="number" class="form-control importeindividualpro" value="${importeindividualpro.toFixed(4)}"></td>
						<td><input disabled data-indexdetalle="${nro}" type="number" class="form-control importetotalpro" value="${totalimporte.toFixed(4)}"></td>
					</tr>`)
			});
		}

		$('#proveedorpro').val(data.ructransporte).trigger('change');
		monedapro.value = data.moneda;
		tipocomprobantepro.value = data.tipocomprobante;
		nrocomprobantepro.value = data.numerocomprobante;
		preciopro.value = data.preciotransp_soles;

		$("#mProrrateo").modal();
	}

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

	function funactualizarextras() {
		const data = {
			id: codigorcxx.value,
			gastos: []
		}
		if (getSelector("#check_transporte").checked) {
			if (!$("#proveedorpro").val() || !tipocomprobantepro.value || !nrocomprobantepro.value || !preciopro.value) {
				alert("debe llenar todos los datos de transporte");
				return;
			} else {
				const proveedorpro = $("#proveedorpro").val().split("&&&")[1];
				const query =
					`insert into transporte_compra 
				(tipo_transporte, tipocomprobante, numerocomprobante, ructransporte, moneda, tipocambio, preciotransp_soles, preciotransp_dolar, codigocompras) 
				values 
				('${typetransporte}', '${tipocomprobantepro.value}', '${nrocomprobantepro.value}', '${proveedorpro}', '${monedapro.value}', 0, ${preciopro.value}, 0, ##IDCOMPRAS##)`
				data.gastos.push(query);
				const dd = new Date().toISOString().substring(0, 10);
				const query1 =
					`insert into plamar 
				(ruc, nro_recibo, monto, descripcion, fecha_inicio, fecha_fin) 
				values 
				('${nrocomprobantepro.value}', '${nrocomprobantepro.value}', ${preciopro.value}, 'transporte', '${dd}', '${dd}')`;
				data.gastos.push(query1);
			}
		}
		if (getSelector("#check_estibador").checked) {
			if (!$("#proveedorestibador").val() || !tipocomprobanteestibador.value || !numerocomprobanteestibador.value || !precio_estibador.value) {
				alert("debe llenar todos los datos de estibador");
				return;
			} else {
				const rucestibaodr = $("#proveedorestibador").val().split("&&&")[1];
				const query =
					`insert into estibador_compra 
				(tipocomprobante, numerocomprobante, rucestibador, moneda, tipocambio, precioestibador_soles, precioestibador_dolar, codigocompras) 
				values 
				('${tipocomprobanteestibador.value}', '${numerocomprobanteestibador.value}', '${rucestibaodr}', '${monedaestibador.value}', 0, ${precio_estibador.value}, 0, ##IDCOMPRAS##)`;
				data.gastos.push(query);
				const dd = new Date().toISOString().substring(0, 10);

				const query1 =
					`insert into plamar 
				(ruc, nro_recibo, monto, descripcion, fecha_inicio, fecha_fin) 
				values 
				('${numerocomprobanteestibador.value}', '${rucestibaodr}', ${precio_estibador.value}, 'estibador', '${dd}', '${dd}')`;
				data.gastos.push(query1);
			}
		}
		if (getSelector("#check_notadebito").checked) {
			if (!$("#proveedornotadebito").val() || !tipocomprobantenotadebito.value || !numerocomprobantenotadebito.value || !precio_notadebito.value) {
				alert("debe llenar todos los datos de nota de debito");
				return;
			} else {
				const rucnotadebito = $("#proveedornotadebito").val().split("&&&")[1];
				const query =
					`insert into notadebito_compra 
				(tipocomprobante, numerocomprobante, rucnd, moneda, tipocambio, preciond_soles, preciond_dolar, codigocompras) 
				values 
				('${tipocomprobantenotadebito.value}', '${numerocomprobantenotadebito.value}', '${rucnotadebito}', '${monedanotadebito.value}', 0, ${precio_notadebito.value}, 0, ##IDCOMPRAS##)`;
				data.gastos.push(query);
				const dd = new Date().toISOString().substring(0, 10);
				const query1 =
					`insert into plamar 
				(ruc, nro_recibo, monto, descripcion, fecha_inicio, fecha_fin) 
				values 
				('${numerocomprobantenotadebito.value}', '${rucnotadebito}', ${precio_notadebito.value}, 'notadebito', '${dd}', '${dd}')`;
				data.gastos.push(query1)
			}
		}
		if (getSelector("#check_notacredito").checked) {
			if (!$("#proveedornotacredito").val() || !proveedornotacredito.value || !tipocomprobantenotacredito.value || !numerocomprobantenotacredito.value || !precio_notacredito.value) {
				alert("debe llenar todos los datos de nota debito");
				return;
			} else {
				const rucnotacredito = $("#proveedornotacredito").val().split("&&&")[1];
				const query =
					`insert into notacredito_compra 
				(tipocomprobante, numerocomprobante, rucnotacredito, moneda, tipocambio, precionc_soles, precionc_dolar, codigocompras) 
				values 
				('${tipocomprobantenotacredito.value}', '${numerocomprobantenotacredito.value}', '${rucnotacredito}', '${monedanotacredito.value}', 0, ${precio_notacredito.value}, 0, ##IDCOMPRAS##)`;
				data.gastos.push(query);
				const dd = new Date().toISOString().substring(0, 10);
				const query1 =
					`insert into plamar 
				(ruc, nro_recibo, monto, descripcion, fecha_inicio, fecha_fin) 
				values 
				('${numerocomprobantenotacredito.value}', '${rucnotacredito}', ${precio_notacredito.value}, 'notacredito', '${dd}', '${dd}')`;
				data.gastos.push(query1)
			}
		}
		var formData = new FormData();
		formData.append("json", JSON.stringify(data))
		fetch(`setupdateextras.php`, {
				method: 'POST',
				body: formData
			})
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				if (res.success) {
					alert("registro completo!")
					location.reload()
				}
			});
	}
	const capitalize = (s) => {
		if (typeof s !== 'string') return ''
		return s.charAt(0).toUpperCase() + s.slice(1)
	}
	async function showhistory(codigorc = 0) {
		if (!codigorc)
			codigorc = codigorcxx.value;
		codigorc = parseInt(codigorc);
		const query = `
			select ec.id_estibador id, ec.asignado, 'Estibador' detalle, CONCAT(ec.tipocomprobante, ' ',ec.numerocomprobante) comprobante, ec.rucestibador ruc, ec.moneda, ec.precioestibador_soles soles, ec.precioestibador_dolar dolares, ec.fecharegistro, 1 valorcambio, prov.razonsocial from estibador_compra ec  
			inner join proveedor prov on prov.ruc = ec.rucestibador
			where ec.codigocompras = ${codigorc}
			union 
			select id_transporte id, tc.tipo_transporte asignado, concat('Transporte - ', tc.tipo_transporte) detalle, CONCAT(tc.tipocomprobante, ' ', tc.numerocomprobante) comprobante, tc.ructransporte ruc, tc.moneda, tc.preciotransp_soles soles, tc.preciotransp_dolar dolares, tc.fecharegistro, 1 valorcambio, prov.razonsocial from transporte_compra tc 
			inner join proveedor prov on prov.ruc = tc.ructransporte
			where tc.codigocompras = ${codigorc}
			union
			select id_notadebito id, nd.asignado, 'Nota Debito' detalle, CONCAT(nd.tipocomprobante, ' ', nd.numerocomprobante) comprobante, nd.rucnd ruc, nd.moneda, nd.preciond_soles soles, nd.preciond_dolar dolares, nd.fecharegistro, 1 valorcambio, prov.razonsocial from notadebito_compra nd 
			inner join proveedor prov on prov.ruc = nd.rucnd
			where nd.codigocompras = ${codigorc}
			union
			select id_notacredito id, nc.asignado, 'Nota Credito' detalle, CONCAT(nc.tipocomprobante, ' ', nc.numerocomprobante) comprobante, nc.rucnotacredito ruc, nc.moneda, nc.precionc_soles soles, nc.precionc_dolar dolares, nc.fecharegistro, 1 valorcambio, prov.razonsocial from notacredito_compra nc
			inner join proveedor prov on prov.ruc = nc.rucnotacredito
			where nc.codigocompras = ${codigorc}
			union
			select 0 id, false asignado, 'Compra de Mercaderia' detalle, CONCAT(rc.tipo_comprobante , ' ',rc.numerocomprobante) comprobante , prov.ruc, rc.tipomoneda moneda, rc.total soles, if(rc.tipomoneda = 'soles', 0, rc.total/	rc.valorcambio) dolares, rc.fecha fecharegistro, rc.valorcambio, prov.razonsocial
			from registro_compras rc
			inner join proveedor prov on prov.codigoproveedor = rc.codigoproveedor
			where rc.codigorc = ${codigorc}
        `;
		let data = await get_data_dynamic(query);

		data.sort(function (a, b) {
			if (a.fecharegistro < b.fecharegistro) {
				return -1;
			}
			if (b.fecharegistro < a.fecharegistro) {
				return 1;
			}
			return 0;
		});

		const soles = data.reduce((total, row) => {
			if (row.detalle === "Compra de Mercaderia") {
				return parseFloat(row.soles)/ 1.18 + total
			} else {
				return parseFloat(row.soles) + total
			}
		}, 0);
		const dolares = data.reduce((total, row) => {
			if (row.detalle === "Compra de Mercaderia") {
				return parseFloat(row.dolares)/ 1.18 + total
			} else {
				return parseFloat(row.dolares) + total
			}
		}, 0);

		data.push({
			fecharegistro: '',
			detalle: '',
			comprobante: '',
			ruc: '',
			valorcambio: '',
			moneda: 'Total',
			detalle: '',
			soles,
			asignado: true,
			dolares,
		});

		const existdolar = data.some(x => x.moneda === "dolares");


		$('#historytable').DataTable({
			data: data,
			ordering: false,
			destroy: true,
			columns: [{
					title: 'Fecha Registro',
					data: 'fecharegistro'
				},
				{
					title: 'RUC',
					render: function(data, type, row) {
						return `<span data-placement="top" data-original-title="${row.razonsocial}" class="tooltips">${row.ruc}</span>`
					}
				},
				{
					title: 'Comprobante',
					render: function(data, type, row) {
						if (row.detalle === "Compra de Mercaderia") {
							return `<div  data-placement="top" data-original-title="${viewcodigoreferencia.textContent}\nGUIA: ${mcodref11.textContent}" class="tooltips">${row.comprobante}</div>`
						} else {
							return row.comprobante;
						}
					},
				},
				{
					title: 'Detalle',
					data: 'detalle'
				},
				{
					title: 'Moneda',
					render: function (_, _, r) {
						return capitalize(r.moneda);
					}
				},
				{
					title: 'VC\n$',
					className: 'dt-body-right',
					visible: existdolar,
					render: function(data, type, row) {
						if (row.detalle === "Compra de Mercaderia") {
							return (parseFloat(row.dolares) / 1.18).toFixed(3);
						} else {
							return (parseFloat(row.dolares)).toFixed(3);
						}
					}
				},
				{
					title: 'T.C.',
					data: 'valorcambio',
					className: 'dt-body-right',
					visible: existdolar
				},
				{
					className: 'dt-body-right',
					title: 'VC\nS/',
					render: function(data, type, row) {
						if (row.detalle === "Compra de Mercaderia") {
							return (parseFloat(row.soles) / 1.18).toFixed(3);
						} else {
							return (parseFloat(row.soles)).toFixed(3);
						}
					}
				},
				{
					title: 'Acciones',
					render: function(data, type, row) {
						if (row.detalle !== 'Compra de Mercaderia' && !row.asignado) {
							if (/transporte/gi.test(row.detalle)) {
								return `
										<a 
											href="#"
											data-codigorc="${codigorc}"
											data-transporteid="${row.id}"
											data-type="porpeso"
											data-moneda="${row.moneda}"
											onClick="asignartransporte(this)"
										>Asignar/Peso</a>
										<a 
											href="#"
											data-type="porparticipacion"
											data-moneda="${row.moneda}"
											data-transporteid="${row.id}"
											data-codigorc="${codigorc}"
											onClick="asignartransporte(this)"
										>Asignar/Compras</a>
									`;
							} else {
								return `
									<a 
										href="#"
										data-codigorc="${codigorc}"
										data-id="${row.id}"
										
										data-tipocomprobante="${row.tipocomprobante}"
										data-numerocomprobante="${row.numerocomprobante}"
										data-ruc="${row.ruc}"
										data-moneda="${row.moneda}"
										data-soles="${row.soles}"
										data-dolares="${row.dolares}"
										data-detalle="${row.detalle}"

										onClick="asignarcosto(this)"
									>Asignar</a>
								`;
							}
						} else {
							return '';
						}
					}
				}
			],
			fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				if (!aData.asignado) {
					$('td', nRow).css('background-color', 'Red');
				}
			}
		});
		$('[data-toggle="tooltip"]').tooltip()
		$('.tooltips').tooltip();
		$("#mhistory").modal();
	}

	async function visualizar(e) {
		descuento.disabled = true;
		facturafechaemision.disabled = true
		tipocomprobantefactura.disabled = true
		nrocomprobante.disabled = true
		moneda.disabled = true
		guardarcosteo.style.display = "none"
		$("#mFacturaCompra").modal();
		codigorcxx.value = e.dataset.codigorc;
		if (e.dataset.comprobante)
			viewcodigoreferencia.textContent = "ORDEN COMPRA " + e.dataset.codigoref;

		viewcomprobante.textContent = e.dataset.comprobante.toUpperCase();


		// showhistory(e.dataset.codigorc).then(r => r)

		await fetch(`getDetalleCompraCosteo.php?codigorc=${e.dataset.codigorc}&type=${e.dataset.type}&codigo=${e.dataset.codigo}`)
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				if (res && res.header) {
					arrayDetalle = res;
					btn_prorrateo.disabled = false
					btn_participacion.disabled = false
					// getSelectorAll(".divxx").forEach(e => e.style.display = "none")
					const countestibador = parseInt(res.header.countestibador)
					const countransporte = parseInt(res.header.counttransporte)
					const countnotadebito = parseInt(res.header.countnotadebito)
					const countnotacredito = parseInt(res.header.countnotacredito)
					tipocambio.disabled = true;
					if (res.header.tipomoneda === 'dolares') {
						container_cambio.style.display = "";
						tipocambio.value = res.header.valorcambio
					} else {
						container_cambio.style.display = "none";
					}
					if (countestibador && countransporte && countnotadebito && countnotacredito) {
						showopcionesextras.style.display = "none";
						actualizarextras.style.display = "none";
					} else {
						showopcionesextras.style.display = "none";
						actualizarextras.style.display = "none";
						if (!countransporte) getSelector(".div_transporte").style.display = ""
						if (!countestibador) {
							precio_estibador.disabled = false;
							getSelector(".div_estibador").style.display = ""
						}
						if (!countnotacredito) {
							precio_notacredito.disabled = false;
							getSelector(".div_notacredito").style.display = ""
						}
						if (!countnotadebito) {
							precio_notadebito.disabled = false;
							getSelector(".div_notadebito").style.display = ""
						}
					}

					$("#mproveedor1").text(res.headerx.razonsocial)
					$("#mfechaemision1").text(res.headerx.fecha_emision)
					// $("#mvalortotal1").text(res.headerx.montofact)
					$("#mcodref11").text(res.headerx.numero_guia)
					if (res.header.codigoref2) {
						$("#mcodref21").text(res.header.codigoref2)
						auxmodref.style.display = "";
					} else {
						auxmodref.style.display = "none";
						mcodref21.style.display = "none";
					}
					$("#mgeneradapor1").text(res.headerx.usuario)
					$("#mruc1").text(res.headerx.ruc)

					$("#codigo_orden_compra").val(res.headerx.codigoguia ? res.headerx.codigoguia : 0)
					$("#codigo_guia_sin_oc").val(res.headerx.codigo_guia_sin_oc ? res.headerx.codigo_guia_sin_oc : 0)

					$("#codigoproveedor").val(res.headerx.codigoproveedor)
					$("#codigosucursal").val(res.headerx.sucursal)
					$("#msucursal1").text(res.headerx.nombre_sucursal)

					let {
						fecha_registro,
						descuentocompras,
						tipo_comprobante,
						numerocomprobante,
						tipomoneda
					} = res.header;
					facturafechaemision.value = fecha_registro.substring(0, 10)
					descuento.value = descuentocompras.substring(0, 10)
					if (tipo_comprobante == "fac")
						tipo_comprobante = "factura"
					else if (tipo_comprobante == "bol")
						tipo_comprobante = "boleta"
					else if (tipo_comprobante == "rec")
						tipo_comprobante = "recibo"
					else if (tipo_comprobante == "otr")
						tipo_comprobante = "otros"
					$("#tipocomprobantefactura").val(tipo_comprobante)
					moneda.value = tipomoneda
					nrocomprobante.value = numerocomprobante


					i = 0;
					document.querySelector("#detalleFacturar-list").innerHTML = "";

					let totalvci = 0;
					let totalgastos = 0;

					let totalddd = 0;
					res.detalle.forEach(r => {

						totalddd += r.vci;
						r.vci *= res.header.valorcambio;
						r.gastoextras = r.gastoextras ? parseFloat(r.gastoextras) * res.header.valorcambio : 0;
						i++;

						totalvci += r.vci;
						totalgastos += r.gastoextras;

						$("#detalleFacturar-list").append(`
						<tr>
						<td data-codigo="${r.codigoprod}" class="codigoprod">${i}</td>
						<td class="cantidad text-right">${r.cantidad}</td>
						<td class="cantidad">${r.minicodigo}</td>
						<td class="cantidad tohidden">${r.nombre_color}</td>
						<td data-placement="bottom" data-original-title="${r.nombre_color}\n${r.marca}"  class="tooltips">${r.nombre_producto}</td>
						
						<td class="costeosinchecked tohidden"><input disabled type="text" oninput="changedescuento(this)" value="${r.descxitem}" class="form-control descuento solonumeros focusandclean"></td>

						<td class="costeosinchecked "><input disabled id="preciocompra${i}" data-toggle="tooltip"  step="any" data-placement="bottom" title="0" oninput="changepreciocompra(this)" value="${(r.vci).toFixed(2)}" required type="text" class="solonumeros focusandclean precio-compra form-control text-right"></td>

						<td>
							<input disabled step="any" value="${r.gastoextras.toFixed(2)}" required type="text" class="solonumeros focusandclean precio-compra form-control text-right">
						</td>
						<td>
							<input disabled step="any" value="${(r.gastoextras + parseFloat(r.vci)).toFixed(2)}" required type="text" class="solonumeros focusandclean precio-compra form-control text-right">
						</td>
						<td>
							<input disabled step="any" value="${((r.gastoextras + parseFloat(r.vci)) / r.cantidad).toFixed(2)}" required type="text" class="solonumeros focusandclean precio-compra form-control text-right">
						</td>
						<td>
							<input disabled step="any" value="${(((r.gastoextras + parseFloat(r.vci))*0.18) / r.cantidad).toFixed(2)}" required type="text" class="solonumeros focusandclean precio-compra form-control text-right">
						</td>
						<td>
							<input disabled step="any" value="${(((r.gastoextras + parseFloat(r.vci))*1.18) / r.cantidad).toFixed(2)}" required type="text" class="solonumeros focusandclean precio-compra form-control text-right">
						</td>
						<td class="costeosinchecked tohidden"><input disabled step="any" data-toggle="tooltip" data-placement="bottom" title="0" oninput="changeimporte(this)" value="${r.vci}" required type="text" class="solonumeros focusandclean importe form-control"></td>

						<td class="costeosinchecked tohidden"><input disabled type="text" value=${r.descmonto} disabled class="form-control descuentocantidad"></td>
						<td class="tohidden"><input disabled type="text" disabled class="form-control vcf" id="vcf_${i}" value="${r.vcf}"></td>

						<td class="costeosinchecked tohidden"><input disabled type="text" disabled class="form-control igvrow" value="${r.igv}"></td>
						<td class="costeosinchecked tohidden"><input disabled type="text" disabled value="${r.totalcompra}" class="form-control valorcompra2"></td>

						<td style="display: none" class="costeochecked tohidden"><input disabled value="${r.preciotransporte}" id="detalleFactura_${i}" class="form-control transporte_costeo" disabled></td>
						<td style="display: none" class="costeochecked tohidden"><input disabled value="${r.precioestibador}" class="form-control estibador_costeo" disabled></td>
						<td style="display: none" class="costeochecked tohidden"><input disabled value="${r.notadebito}" class="form-control notadebito" disabled></td>
						<td style="display: none" class="costeochecked tohidden"><input disabled value="${r.precionotacredito}" class="form-control notacredito" disabled></td>
						<td style="display: none" class="costeochecked tohidden"><input disabled value="${r.totalconadicionales}" class="form-control total_costeo" disabled></td>
						<td style="display: none" class="costeochecked tohidden"><input disabled value="${r.totalunidad}" class="form-control totalunidadcosteo" disabled></td>
						</tr>`);
					});

					$('[data-toggle="tooltip"]').tooltip()
					$('.tooltips').tooltip();

					$("#detalleFacturar-list").append(`
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>TOTAL</td>

							<td class=""><input class="text-right form-control" disabled value="${totalvci.toFixed(2)}"></td>
							<td class=""><input class="text-right form-control" disabled value="${totalgastos.toFixed(2)}"></td>
							<td class=""><input class="text-right form-control" disabled value="${(totalvci + totalgastos).toFixed(2)}"></td>
						</tr>
					`);

					$("#detalleFacturar-list").append(`
					<tr class="tohidden">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td id="titlesoles" class="costeosinchecked" style="text-align: right; font-weight: bold;"></td>
					<td><input type="text" disabled class="form-control sumavcf"></td>
					<td class="costeosinchecked"><input type="text" disabled class=" form-control sumaigvrow"></td>
					<td class="costeosinchecked"><input type="text" disabled class="form-control sumavalorcompra2"></td>

					<td style="display: none" class="costeochecked"><input class="form-control sumatransporte" disabled></td>

					<td style="display: none" class="costeochecked"><input class="form-control sumaestibador_costeo" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotadebito" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotacredito" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumatotal_costeo" disabled></td>
					<td style="display: none" class="dddd"><input class="form-control sumatotalunidadcosteo" disabled></td>
					</tr>`);


					$("#detalleFacturar-list").append(`
					<tr id="rowfacturadolar tohidden" style="display: none">
					<td clas="tohidden"></td>
					<td clas="tohidden"></td>
					<td clas="tohidden"></td>
					<td clas="tohidden"></td>
					<td clas="tohidden"></td>
					<td clas="tohidden"></td>
					<td clas="tohidden" class="costeosinchecked"></td>
					<td clas="tohidden" class="costeosinchecked"></td>
					<td clas="tohidden" class="costeosinchecked"></td>
					<td id="titledolar" class="costeosinchecked" style="text-align: right; font-weight: bold;"></td>
					<td><input type="text" disabled class="form-control sumavcfdolar"></td>
					<td class="costeosinchecked"><input type="text" disabled class=" form-control sumaigvrowdolar"></td>
					<td class="costeosinchecked"><input type="text" disabled class="form-control sumavalorcompra2dolar"></td>

					<td style="display: none" class="costeochecked"><input class="form-control transporte_costeo" disabled></td>

					<td style="display: none" class="costeochecked"><input class="form-control sumaestibador_costeodolar" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotadebitodolar" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotacreditodolar" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumatotal_costeodolar" disabled></td>

					</tr>`);
					if (res.header.tipomoneda === 'dolares') {
						if (getSelector("#rowfacturadolar"))
							rowfacturadolar.style.display = "";
						titlesoles.textContent = "TOTAL $";
						titledolar.textContent = "TOTAL S/";
					} else {
						if (getSelector("#rowfacturadolar"))
							rowfacturadolar.style.display = "none";
						titlesoles.textContent = "TOTAL $";
						titlesoles.textContent = "TOTAL S/";
					}
					calcularTotalSinExtras();
					
				} else {
					alert("hubo un error")
				}
			})
			showhistory(e.dataset.codigorc).then(r => r)
	}

	var i = 0;
	document.querySelectorAll(".verOrden").forEach(item => {
		document.querySelector("#saveOrdenCompra").reset();
		item.addEventListener("click", (e) => {
			i = 0;

			document.querySelector("#codigoOrdenCompra").value = e.target.dataset.codigo
			fetch(`getDetalleCompra.php?codigo=${e.target.dataset.codigo}`)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					document.querySelector("#codigoordcomp").value = res.header.codigoordcomp
					$("#mproveedor").text(res.header.razonsocial)
					$("#mfechaemision").text(res.header.fecha_emision)
					$("#mvalortotal").text(res.header.montofact)
					$("#msucursal").text(res.header.nombre_sucursal)
					$("#mcodref1").text(res.header.codigoref1)
					if (res.header.codigoref2) {
						$("#mcodref21").text(res.header.codigoref2)
						auxmodref.style.display = "";
					} else {
						auxmodref.style.display = "none";
						mcodref21.style.display = "none";
					}
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
						<td style="width: 30px"><input required type="number" oninput="validateCantidad(this)" class="form-control cant-arrived" autocomplete="off" value="${r.cant_recibida}" data-cantidad="${validateCant}" disabled></td>
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

		fetch(`setOrdenCompra.php`, {
				method: 'POST',
				body: formData
			})
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

		fetch(`setOrdenCompra.php`, {
				method: 'POST',
				body: formData
			})
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
			getSelector(".div_estibador").style.display = ""
			getSelector(".div_notacredito").style.display = ""
			getSelector(".div_notadebito").style.display = ""
			getSelector(".div_transporte").style.display = ""

			actualizarextras.style.display = "none"
			descuento.disabled = false;
			facturafechaemision.disabled = false;
			tipocomprobantefactura.disabled = false;
			nrocomprobante.disabled = false;
			moneda.disabled = false;

			facturafechaemision.value = ""
			descuento.value = ""
			$("#tipocomprobantefactura").val("")
			moneda.value = "soles"
			nrocomprobante.value = ""

			showopcionesextras.style.display = ""
			guardarcosteo.style.display = ""

			subtotalGLOBAL = 0;
			getSelector("#check_transporte").checked = false;
			getSelector("#check_transporte").parentElement.classList.remove("checked")
			getSelector("#container_transporte").style.display = "none";

			getSelector("#check_estibador").checked = false;
			getSelector("#check_estibador").parentElement.classList.remove("checked")
			getSelector("#container_estibador").style.display = "none";

			getSelector("#check_notadebito").checked = false;
			getSelector("#check_notadebito").parentElement.classList.remove("checked")
			getSelector("#container_notadebito").style.display = "none";

			getSelector("#check_notacredito").checked = false;
			getSelector("#check_notacredito").parentElement.classList.remove("checked")
			getSelector("#container_notacredito").style.display = "none";


			if (item.dataset.type == "ordencompra") {
				url = `getDetalleOrdenCompraGuia.php?codigo=${e.target.dataset.codigo}&codigoguia=${e.target.dataset.codigoguia}`;
			} else {
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
					// $("#mvalortotal1").text(res.header.montofact)
					$("#mcodref11").text(res.header.numero_guia)
					if (res.header.codigoref2) {
						$("#mcodref21").text(res.header.codigoref2)
						auxmodref.style.display = "";
					} else {
						auxmodref.style.display = "none";
						mcodref21.style.display = "none";
					}

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
						<td class="">${r.minicodigo}</td>
						<td class="">${r.nombre_color}</td>
						<td>${r.nombre_producto}</td>
						<td >${r.marca}</td>
						<td class="costeosinchecked"><input type="text" autocomplete="off" oninput="changedescuento(this)" value="0" class="form-control descuento solonumeros focusandclean"></td>
						<td class="costeosinchecked"><input autocomplete="off" id="preciocompra${i}" data-toggle="tooltip"  step="any" data-placement="bottom" title="0" oninput="changepreciocompra(this)" value="0.00" required type="text" class="solonumeros focusandclean precio-compra form-control text-right"></td>

						<td class="costeosinchecked"><input step="any" data-toggle="tooltip" data-placement="bottom" title="0" oninput="changeimporte(this)" autocomplete="off" value="0.00" required type="text" class="solonumeros focusandclean importe form-control"></td>

						<td class="costeosinchecked"><input type="text" disabled class="form-control descuentocantidad"></td>
						<td><input type="text" disabled class="form-control vcf" id="vcf_${i}"></td>

						<td class="costeosinchecked"><input type="text" disabled class="form-control igvrow"></td>
						<td class="costeosinchecked"><input type="text" disabled class="form-control valorcompra2"></td>

						<td style="display: none" class="costeochecked"><input id="detalleFactura_${i}" class="form-control transporte_costeo" disabled></td>
						<td style="display: none" class="costeochecked"><input class="form-control estibador_costeo" disabled></td>
						<td style="display: none" class="costeochecked"><input class="form-control notadebito" disabled></td>
						<td style="display: none" class="costeochecked"><input class="form-control notacredito" disabled></td>
						<td style="display: none" class="costeochecked"><input class="form-control total_costeo" disabled></td>
						<td style="display: none" class="costeochecked"><input class="form-control totalunidadcosteo" disabled></td>
						</tr>`);
					});
					$("#detalleFacturar-list").append(`
					<tr class="tohidden">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td id="titlesoles" class="costeosinchecked" style="text-align: right; font-weight: bold;">TOTAL S/</td>
					<td><input type="text" disabled class="form-control sumavcf"></td>
					<td class="costeosinchecked"><input type="text" disabled class=" form-control sumaigvrow"></td>
					<td class="costeosinchecked"><input type="text" disabled class="form-control sumavalorcompra2"></td>

					<td style="display: none" class="costeochecked"><input class="form-control sumatransporte" disabled></td>

					<td style="display: none" class="costeochecked"><input class="form-control sumaestibador_costeo" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotadebito" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotacredito" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumatotal_costeo" disabled></td>
					<td style="display: none" class="dddd"><input class="form-control sumatotalunidadcosteo" disabled></td>
					</tr>`);
					$("#detalleFacturar-list").append(`
					<tr id="rowfacturadolar tohidden">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td id="titledolar" class="costeosinchecked" style="text-align: right; font-weight: bold;">TOTAL $</td>
					<td><input type="text" disabled class="form-control sumavcfdolar"></td>
					<td class="costeosinchecked"><input type="text" disabled class=" form-control sumaigvrowdolar"></td>
					<td class="costeosinchecked"><input type="text" disabled class="form-control sumavalorcompra2dolar"></td>

					<td style="display: none" class="costeochecked"><input class="form-control transporte_costeo" disabled></td>

					<td style="display: none" class="costeochecked"><input class="form-control sumaestibador_costeodolar" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotadebitodolar" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotacreditodolar" disabled></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumatotal_costeodolar" disabled></td>

					</tr>`);
					if (rowfacturadolar)
						rowfacturadolar.style.display = "none"
					$('[data-toggle="tooltip"]').tooltip()
					$('.tooltips').tooltip();
				});
			btn_prorrateo.disabled = true
			btn_participacion.disabled = true
			container_cambio.style.display = "none"
			monedadolar = false;
			tipocambio.value = 1
			$("#mFacturaCompra").modal();
		})
	});

	function changecambiodolar(e) {
		if (e.value < 0 || e.value == "") {
			e.value = 0;
			return;
		}
		calcularTotalSinExtras();
		calcularUnidadCosteoyTotalcosteo()


	}

	function changedescuento(e) {
		if (e.value < 0 || e.value == "") {
			e.value = 0;
			return;
		}
		getSelector("#descuento").value = 0;
		const descuento = parseFloat(e.value);

		const aa = e.parentElement.parentElement;

		const ss = parseFloat(aa.querySelector(".importe").value)

		calcularFila(aa)
		calcularTotalSinExtras();
	}

	function calcularcosteobyfile(tr) {
		let totalx = 0
		const vcfinit = tr.querySelector(".vcf").dataset.vcfsoles ? tr.querySelector(".vcf").dataset.vcfsoles : tr.querySelector(".vcf").value
		totalx += parseFloat(vcfinit);
		totalx *= tipocambio.value;
		totalx += parseFloat(tr.querySelector(".transporte_costeo").value ? tr.querySelector(".transporte_costeo").value : 0);
		totalx += parseFloat(tr.querySelector(".estibador_costeo").value ? tr.querySelector(".estibador_costeo").value : 0);
		totalx += parseFloat(tr.querySelector(".notadebito").value ? tr.querySelector(".notadebito").value : 0);
		totalx -= parseFloat(tr.querySelector(".notacredito").value ? tr.querySelector(".notacredito").value : 0);

		// totalx -= tr.querySelector(".descuento").value ? totalx * (parseFloat(tr.querySelector(".descuento").value)) / 100 : 0;
		return parseFloat(totalx).toFixed(4);
	}

	function changeimporte(e) {
		if (e.value < 0) {
			e.value = 0;
			return;
		}
		const aa = e.parentElement.parentElement
		const descuento = parseFloat(aa.querySelector(".descuento").value)

		calcularFila(aa, "importe")
		calcularTotalSinExtras();
		updateColumns();
	}

	function calcularTotalSinExtras() {
		let sumavcf = 0
		let sumaigvrow = 0
		let sumavalorcompra2 = 0
		getSelectorAll(".vcf").forEach(s => {
			const tr = s.closest("tr");
			sumavcf += parseFloat(s.value ? s.value : 0);
			sumaigvrow += parseFloat(tr.querySelector(".igvrow").value ? tr.querySelector(".igvrow").value : 0)
			sumavalorcompra2 += parseFloat(tr.querySelector(".valorcompra2").value ? tr.querySelector(".valorcompra2").value : 0)
			tr.querySelector(".total_costeo").value = calcularcosteobyfile(tr);

			if (windowtype == "costeoextra") {
				const vcfinit = tr.querySelector(".vcf").dataset.vcfsoles ? tr.querySelector(".vcf").dataset.vcfsoles : tr.querySelector(".vcf").value
				tr.querySelector(".vcf").value = parseFloat(vcfinit) * tipocambio.value
				tr.querySelector(".vcf").dataset.vcfsoles = vcfinit
			}
		})
		getSelector(".sumavcf").value = sumavcf.toFixed(2)
		getSelector(".sumaigvrow").value = sumaigvrow.toFixed(2)
		getSelector(".sumavalorcompra2").value = sumavalorcompra2.toFixed(2)
		if (tipocambio) {
			getSelector(".sumavcfdolar").value = (sumavcf * parseFloat(tipocambio.value)).toFixed(2)
			getSelector(".sumaigvrowdolar").value = (sumaigvrow * parseFloat(tipocambio.value)).toFixed(2)
			getSelector(".sumavalorcompra2dolar").value = (sumavalorcompra2 * parseFloat(tipocambio.value)).toFixed(2)
		}

	}

	function changeprecioestibador(e) {
		let total = 0;
		let tc = getSelector(`#${e.dataset.tipocambio}`).value ? parseFloat(getSelector(`#${e.dataset.tipocambio}`).value) : 0;

		getSelectorAll(".vcf").forEach(i => {
			total += parseFloat(i.value)
		});

		getSelectorAll(".vcf").forEach(i => {
			const tr = i.closest("tr");
			tr.querySelector(`.${e.dataset.type}`).value = (parseFloat(e.value) * parseFloat(i.value) * tc / total).toFixed(2)
			tr.querySelector(".total_costeo").value = calcularcosteobyfile(tr)
		});
		getSelector(`.suma${e.dataset.type}`).value = (parseFloat(e.value) * tc).toFixed(2)
		calcularUnidadCosteoyTotalcosteo()
		// calcularTotalSinExtras()
	}

	function calcularUnidadCosteoyTotalcosteo() {
		let sumatotalcosteo = 0;
		let sumaunidadtotal = 0;
		getSelectorAll(".total_costeo").forEach(ix => {
			sumatotalcosteo += parseFloat(ix.value);
			const totalunidad = parseFloat(ix.value) / parseInt(ix.closest("tr").querySelector(".cantidad").textContent)
			sumaunidadtotal += totalunidad
			ix.closest("tr").querySelector(".totalunidadcosteo").value = totalunidad.toFixed(4)
		});
		getSelector(".sumatotal_costeo").value = parseFloat(sumatotalcosteo).toFixed(4)
		// getSelector(".sumatotalunidadcosteo").value = parseFloat(sumaunidadtotal).toFixed(4)

	}

	function updateColumns() {
		let allpreciocompra = true;
		getSelectorAll(".precio-compra").forEach(e => {
			if (e.value == "" || parseFloat(e.value) == 0) {
				allpreciocompra = false;
			}
		});
		if (allpreciocompra) {
			btn_prorrateo.disabled = false
			btn_participacion.disabled = false
			precio_estibador.removeAttribute('disabled');
			precio_notadebito.disabled = false;
			precio_notacredito.disabled = false;
			let total = 0;
			getSelectorAll(".precio-compra").forEach(i => {
				total += parseFloat(i.value)
			});
			getSelectorAll(".precio-compra").forEach(i => {
				i.closest("tr").querySelector(".estibador_costeo").value = parseFloat(precio_estibador.value ? precio_estibador.value : 0) * i.value / total
				i.closest("tr").querySelector(".notadebito").value = parseFloat(precio_notadebito.value ? precio_notadebito.value : 0) * i.value / total
				i.closest("tr").querySelector(".notacredito").value = parseFloat(precio_notacredito.value ? precio_notacredito.value : 0) * i.value / total
			});
		} else {
			btn_prorrateo.disabled = true
			btn_participacion.disabled = true
			precio_estibador.setAttribute("disabled", true)
			precio_notadebito.setAttribute("disabled", true)
			precio_notacredito.setAttribute("disabled", true)
		}
	}

	function changepreciocompra(e, aux = true) {
		if (e.value < 0) {
			e.value = 0;
			return;
		}
		const aa = e.parentElement.parentElement
		const ss = parseInt(aa.querySelector(".cantidad").textContent) * parseFloat(e.value)
		const descuento = parseFloat(aa.querySelector(".descuento").value)

		calcularFila(aa, "preciocompra")
		calcularTotalSinExtras();
		document.querySelector(".tooltip-inner").textContent = `${e.value} - ${(e.value * IGV1).toFixed(4)}`
		e.dataset.originalTitle = `${e.value} - ${(e.value * IGV1).toFixed(4)}`
		updateColumns();

	}

	function calcularFila(tr, origin = false) {

		let importe = parseFloat(tr.querySelector(".importe").value)
		if (origin == "preciocompra") {
			importe = parseFloat(tr.querySelector(".precio-compra").value) * parseInt(tr.querySelector(".cantidad").textContent)
		} else {
			tr.querySelector(".precio-compra").value = (importe / parseInt(tr.querySelector(".cantidad").textContent)).toFixed(4)
		}
		const descuento = parseFloat(tr.querySelector(".descuento").value);

		tr.querySelector(".totalunidadcosteo").value = (importe * (100 - descuento) / 100) / parseInt(tr.querySelector(".cantidad").textContent)

		if (origin != "importe")
			tr.querySelector(".importe").value = (importe).toFixed(2)

		tr.querySelector(".descuentocantidad").value = (parseFloat(importe) * descuento / 100).toFixed(2)
		tr.querySelector(".vcf").value = (importe * (100 - descuento) / 100).toFixed(2)
		tr.querySelector(".valorcompra2").value = (importe * IGV1 * (100 - descuento) / 100).toFixed(2)
		tr.querySelector(".igvrow").value = (importe * IGV * (100 - descuento) / 100).toFixed(2)

		tr.querySelector(".total_costeo").value = calcularcosteobyfile(tr)
	}
	let onlyclick = true;
	document.querySelector("#saveFacturar").addEventListener("submit", e => {
		e.preventDefault();
		if (!onlyclick)
			return
		if (onlyclick)
			onlyclick = false;
		if (getSelector("#moneda").value == "dolares" && getSelector("#tipocambio").value == "" && getSelector("#tipocambio").value != "0") {
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

		if (getSelector("#check_transporte").checked) {
			if (!$("#proveedorpro").val() || !tipocomprobantepro.value || !nrocomprobantepro.value || !preciopro.value) {
				alert("debe llenar todos los datos de transporte");
				return;
			} else {
				const proveedorpro = $("#proveedorpro").val().split("&&&")[1];
				const query =
					`insert into transporte_compra 
				(tipo_transporte, tipocomprobante, numerocomprobante, ructransporte, moneda, tipocambio, preciotransp_soles, preciotransp_dolar, codigocompras) 
				values 
				('${typetransporte}', '${tipocomprobantepro.value}', '${nrocomprobantepro.value}', '${proveedorpro}', '${monedapro.value}', 0, ${preciopro.value}, 0, ##IDCOMPRAS##)`
				data.gastos.push(query);
				const dd = new Date().toISOString().substring(0, 10);
				const query1 =
					`insert into plamar 
				(ruc, nro_recibo, monto, descripcion, fecha_inicio, fecha_fin) 
				values 
				('${nrocomprobantepro.value}', '${nrocomprobantepro.value}', ${preciopro.value}, 'transporte', '${dd}', '${dd}')`;
				data.gastos.push(query1);
			}
		}
		if (getSelector("#check_estibador").checked) {
			if (!$("#proveedorestibador").val() || !tipocomprobanteestibador.value || !numerocomprobanteestibador.value || !precio_estibador.value) {
				alert("debe llenar todos los datos de estibador");
				return;
			} else {
				const rucestibaodr = $("#proveedorestibador").val().split("&&&")[1];
				const query =
					`insert into estibador_compra 
				(tipocomprobante, numerocomprobante, rucestibador, moneda, tipocambio, precioestibador_soles, precioestibador_dolar, codigocompras) 
				values 
				('${tipocomprobanteestibador.value}', '${numerocomprobanteestibador.value}', '${rucestibaodr}', '${monedaestibador.value}', 0, ${precio_estibador.value}, 0, ##IDCOMPRAS##)`;
				data.gastos.push(query);
				const dd = new Date().toISOString().substring(0, 10);

				const query1 =
					`insert into plamar 
				(ruc, nro_recibo, monto, descripcion, fecha_inicio, fecha_fin) 
				values 
				('${numerocomprobanteestibador.value}', '${rucestibaodr}', ${precio_estibador.value}, 'estibador', '${dd}', '${dd}')`;
				data.gastos.push(query1);
			}
		}
		if (getSelector("#check_notadebito").checked) {
			if (!$("#proveedornotadebito").val() || !tipocomprobantenotadebito.value || !numerocomprobantenotadebito.value || !precio_notadebito.value) {
				alert("debe llenar todos los datos de nota de debito");
				return;
			} else {
				const rucnotadebito = $("#proveedornotadebito").val().split("&&&")[1];
				const query =
					`insert into notadebito_compra 
				(tipocomprobante, numerocomprobante, rucnd, moneda, tipocambio, preciond_soles, preciond_dolar, codigocompras, porpagar) 
				values 
				('${tipocomprobantenotadebito.value}', '${numerocomprobantenotadebito.value}', '${rucnotadebito}', '${monedanotadebito.value}', 0, ${precio_notadebito.value}, 0, ##IDCOMPRAS##, 1)`;
				data.gastos.push(query);
				const dd = new Date().toISOString().substring(0, 10);
				const query1 =
					`insert into plamar 
				(ruc, nro_recibo, monto, descripcion, fecha_inicio, fecha_fin) 
				values 
				('${numerocomprobantenotadebito.value}', '${rucnotadebito}', ${precio_notadebito.value}, 'notadebito', '${dd}', '${dd}')`;
				data.gastos.push(query1)
			}
		}
		if (getSelector("#check_notacredito").checked) {
			if (!$("#proveedornotacredito").val() || !proveedornotacredito.value || !tipocomprobantenotacredito.value || !numerocomprobantenotacredito.value || !precio_notacredito.value) {
				alert("debe llenar todos los datos de nota debito");
				return;
			} else {
				const rucnotacredito = $("#proveedornotacredito").val().split("&&&")[1];
				const query =
					`insert into notacredito_compra 
				(tipocomprobante, numerocomprobante, rucnotacredito, moneda, tipocambio, precionc_soles, precionc_dolar, codigocompras) 
				values 
				('${tipocomprobantenotacredito.value}', '${numerocomprobantenotacredito.value}', '${rucnotacredito}', '${monedanotacredito.value}', 0, ${precio_notacredito.value}, 0, ##IDCOMPRAS##)`;
				data.gastos.push(query);
				const dd = new Date().toISOString().substring(0, 10);
				const query1 =
					`insert into plamar 
				(ruc, nro_recibo, monto, descripcion, fecha_inicio, fecha_fin) 
				values 
				('${numerocomprobantenotacredito.value}', '${rucnotacredito}', ${precio_notacredito.value}, 'notacredito', '${dd}', '${dd}')`;
				data.gastos.push(query1)
			}
		}
		const typepay = moneda.value == "dolares" ? "dolar" : "";
		const nowx = new Date()
		let month = nowx.getMonth() + 1;
		month = month < 10 ? "0" + month : "" + month;
		data.header = {
			codigocompras: 0,
			tipomoneda: moneda.value,
			tipo_comprobante: tipocomprobantefactura.value,
			numerocomprobante: nrocomprobante.value,
			codacceso: <?= $_SESSION['kt_login_id'] ?>,
			ruc_proveedor: mruc1.textContent,
			subtotal: getSelector(".sumavcf" + typepay).value,
			igv: getSelector(".sumaigvrow" + typepay).value,
			total: getSelector(".sumavalorcompra2" + typepay).value,
			codigoproveedor: codigoproveedor.value,
			estadofact: 1,
			codigosuc: codigosucursal.value,
			codigo_orden_compra: codigo_orden_compra.value,
			codigo_guia_sin_oc: codigo_guia_sin_oc.value,
			fecha_registro: facturafechaemision.value,
			valorcambio: tipocambio,
			descuentocompras: descuento.value ? descuento.value : 0,
			codigomesconta: `${nowx.getFullYear()}${month}-`,
			firstday: `${nowx.getFullYear()}-${nowx.getMonth() + 1}-1`

		}
		getSelectorAll("#detalleFacturar-list tr").forEach(item => {
			if (item.querySelector(".precio-compra")) {
				let preciodolar = 0;
				let preciosoles = parseFloat(item.querySelector(".precio-compra").value).toFixed(4);
				if (getSelector("#moneda").value == "dolares") {
					preciodolar = preciosoles;
					preciosoles = preciosoles / parseInt(getSelector("#tipocambio").value)
				}
				const peso = item.querySelector(".transporte_costeo").value ? item.querySelector(".transporte_costeo").value : 0;
				const totalunidad = item.querySelector(".totalunidadcosteo").value ? item.querySelector(".totalunidadcosteo").value : 0;
				const preciotransporte = item.querySelector(".transporte_costeo").value ? item.querySelector(".transporte_costeo").value : 0
				data.detalle.push({
					codigoprod: item.querySelector(".codigoprod").dataset.codigo,
					cantidad: item.querySelector(".cantidad").textContent,
					descuento: item.querySelector(".descuento").value,
					vcu: item.querySelector(".precio-compra").value,
					vci: item.querySelector(".importe").value,
					descmonto: item.querySelector(".descuentocantidad").value,
					vcf: item.querySelector(".vcf").value,
					igv: item.querySelector(".igvrow").value,
					totalcompra: item.querySelector(".valorcompra2").value,
					peso,
					preciotransporte,
					precioestibador: item.querySelector(".estibador_costeo").value,
					notadebito: item.querySelector(".notadebito").value,
					precionotacredito: item.querySelector(".notacredito").value,
					totalconadicionales: item.querySelector(".total_costeo").value,
					totalunidad
				})
			}
		})
		var formData = new FormData();
		formData.append("json", JSON.stringify(data))
		fetch(`setFactura.php`, {
				method: 'POST',
				body: formData
			})
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

	function selectmoneda(e) {
		if (e.value == "dolares") {
			titledolar.textContent = "TOTAL S/"
			titlesoles.textContent = "TOTAL $"
			rowfacturadolar.style.display = ""
			getSelector(".container_cambio").style.display = "";
			monedadolar = true;
		} else {
			titledolar.textContent = "TOTAL $"
			titlesoles.textContent = "TOTAL S/"
			rowfacturadolar.style.display = "none"
			tipocambio.value = 1;
			calcularTotalSinExtras();
			calcularUnidadCosteoyTotalcosteo()
			getSelector(".container_cambio").style.display = "none";
			monedadolar = false
		}
	}
</script>