<?php require_once('Connections/Ventas.php'); ?>
<?php


mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "select g.codigoguia, registro_compras.codigorc, registro_compras.subtotal,  g.codigoordcomp, o.codigoref1,g.codigoacceso, g.numeroguia, g.estado as estadoGuia, g.fecha, o.codigo,o.codigoordcomp,o.codigoproveedor, o.fecha_emision, o.estadofact, o.sucursal, p.ruc, p.razonsocial from ordencompra_guia g inner JOIN ordencompra o on g.codigoordcomp=o.codigoordcomp inner JOIN proveedor p on p.codigoproveedor=o.codigoproveedor left join registro_compras on registro_compras.codigo_orden_compra = g.codigoguia where g.estado=2 or g.estado=3";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
$i = 1;


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

$queryguiasinoc = "SELECT c.codigo_guia_sin_oc, registro_compras.subtotal,a.usuario,p.ruc, s.nombre_sucursal, c.codigoref2,c.estado, c.numero_guia, p.razonsocial, p.codigoproveedor as codigoproveedor, c.fecha FROM guia_sin_oc c inner join proveedor p on c.codigoproveedor=p.codigoproveedor left join sucursal s on s.cod_sucursal = c.sucursal left join acceso a on a.codacceso = c.codacceso left join registro_compras on registro_compras.codigo_guia_sin_oc = c.codigo_guia_sin_oc where c.estado = 2";
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
		<tbody><?php do {  
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
				<td><a href="#" data-type="ordencompra" data-codigo="<?= $row_Listado['codigo'] ?>" onclick="visualizar(this)" data-codigorc="<?= $row_Listado['codigorc'] ?>">Ver</a>
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
							<a href="#" data-type="guia_sin_oc" onclick="visualizar(this)" data-codigo="<?= $row_listaguiasinoc['codigo_guia_sin_oc'] ?>" data-codigorc="<?= $row_Listado['codigorc'] ?>" class="verOrdenSinOc">Ver</a>
						</td>
						<?php else: ?>
							<td><a href="#" class="aux_compras" data-type="guia_sin_oc"
								data-codigo="<?= $row_listaguiasinoc['codigo_guia_sin_oc'] ?>">Facturar</a>
							</td>
						<?php endif ?>
						<td>
							<a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante"
							href="Imprimir/orden_compra.php?codigocompras=<?php echo $row_listaguiasinoc['codigo']; ?>&codigo=<?php echo $row_listaguiasinoc['codigoref1']; ?>"
							target="new"><i class="glyphicon glyphicon-credit-card"></i></a>
						</td>
						<td>Guia sin OC</td>
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
								<div class="modal-footer">
									<button type="button" id="btn-finalice" style="display: none"
									class="btn btn-primary">Finalizar</button>
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
							<h2 class="modal-title" id="">Facturar Orden de compra</h2>
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


											<div class="row" style="margin-top: 20px">
												<div class="col-xs-12 col-md-12">
													<div class="row">
														<div class="col-md-2">
															<div class="form-group">
																<label for="field-1" class="control-label">Fecha Emision</label>
																<input type="text" required name="facturafechaemision"
																autocomplete="off" id="facturafechaemision"
																class="form-control form-control-inline input-medium date-picker"
																data-date-format="yyyy-mm-dd" required />
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label for="field-1" class="control-label">Descuento General</label>
																<input type="number" class="form-control"
																oninput="changedescuentogeneral(this)" step="any" id="descuento"
																name="">
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label for="field-1" class="control-label">Tipo Comp</label>
																<select class="form-control" name="tipocomprobantefactura"
																id="tipocomprobantefactura">
																<option value="factura">Factura</option>
																<option value="boleta">Boleta</option>
																<option value="recibo">Recibo</option>
																<option value="otros">Otros</option>
															</select>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label for="field-1" class="control-label">Nro Comprobante</label>
															<input type="text" required class="form-control"
															name="nrocomprobante" id="nrocomprobante">
														</div>
													</div>
													<div class="col-md-2 container_moneda">
														<div class="form-group">
															<label for="field-1" class="control-label">Moneda</label>
															<select class="form-control" onchange="selectmoneda(this)"
															id="moneda" name="moneda" required>
															<option value="soles">S/</option>
															<option value="dolares">$</option>
														</select>
													</div>
												</div>
												<div class="col-md-2 container_cambio" id="container_cambio"
												style="display: none">
												<div class="form-group">
													<label for="field-1" class="control-label">Cambio</label>
													<input type="number" step="any" class="form-control" id="tipocambio"
													oninput="changecambiodolar(this)" name="">
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
										<th class="costeosinchecked" width=" 120px">Desc x Item</th>
										<th class="costeosinchecked" width=" 120px">VCU</th>
										<th class="costeosinchecked" width=" 120px">VCI</th>
										<th class="costeosinchecked" width="120px">DSCTO</th>
										<th width="120px">VCF</th>
										<th class="costeosinchecked" width=" 120px">IGV</th>
										<th class="costeosinchecked" width=" 120px">Total</th>
										<th width="60px" class="costeochecked" style="display: none">Transporte</th>
										<th width="60px" class="costeochecked" style="display: none">Estibador</th>
										<th width="60px" class="costeochecked" style="display: none">Nota Debito</th>
										<th width="60px" class="costeochecked" style="display: none">Nota Credito</th>
										<th width="60px" class="costeochecked" style="display: none">Total</th>
										<th width="60px" class="costeochecked" style="display: none">T. Unidad</th>
									</thead>
									<tbody id="detalleFacturar-list">
									</tbody>
								</table>

							</div>
						</div>
					</div>
					<div class="modal-footer">
						<label for="showcosteo">Mostrar costeo</label>
						<input type="checkbox" onclick="checkcosteo(this)" id="showcosteo">
						<button class="btn btn-success" id="showopcionesextras" type="button" onclick="showopciones()">Opciones</button>
						<button type="submit" id="guardarcosteo" class="btn btn-success">Guardar</button>
						<button type="button" data-dismiss="modal" aria-label="Close"
						class="btn btn-danger">Cerrar</button>
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
				<div style="margin-top: 10px">
					<label class="" for="check_transporte">transporte?</label>
					<input type="checkbox" class="" id="check_transporte">

					<div class="row" style="display: none" id="container_transporte">
						<div class="col-sm-6 text-center">
							<button type="button" disabled class="btn btn-success" data-type="prorrateo"
							id="btn_prorrateo" onclick="setExtra(this)">PRORRATEO X PESO</button>
						</div>
						<div class="col-sm-6 text-center">
							<button type="button" disabled class="btn btn-success" data-type="participacion"
							id="btn_participacion" onclick="setExtra(this)" id="participacion">PRORRATEO POR
						COMPRA</button>
					</div>
				</div>
			</div>

			<div style="margin-top: 10px">
				<label class="" for="check_estibador">Estibador?</label>
				<input type="checkbox" class="" id="check_estibador">

				<div class="row" style="display: none" id="container_estibador">
					<div class="col-sm-6">
						<label class="control-label" for="proveedorestibador">PROVEEDOR</label>
						<select name="proveedor" id="proveedorestibador" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
							<option value="">Seleccione</option>
							<?php do {  ?>
								<option value="<?= $row_Clientes['razonsocial'] . '&&&' .$row_Clientes['ruc']?>">
									<?= $row_Clientes['razonsocial'] . ' ' .$row_Clientes['ruc']?>
								</option>
								<?php
							} while ($row_Clientes = mysql_fetch_assoc($Clientes));
							$rows = mysql_num_rows($Clientes);
							if($rows > 0) {
								mysql_data_seek($Clientes, 0);
								$row_Clientes = mysql_fetch_assoc($Clientes);
							}
							?>
						</select>

						<!-- <input class="form-control" name="" id="rucestibador"> -->
					</div>
						<!-- <div class="col-sm-6">
							<label class="control-label" for="proveedorestibador">Proveedor</label>
							<input class="form-control" name="" id="proveedorestibador">
						</div> -->
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
							<input class="form-control"type="number" value="1" min="1" step="any" id="tipocambioestibador">
						</div>
						<div class="col-sm-3">
							<label class="control-label" for="precio_estibador">Valor de Compra</label>
							<input class="form-control" data-tipocambio="tipocambioestibador" data-type="estibador_costeo"
							oninput="changeprecioestibador(this)" readonly type="number" name=""
							id="precio_estibador">
						</div>
					</div>
				</div>
				<div style="margin-top: 10px">
					<label class="" for="check_notadebito">Nota Debito?</label>
					<input type="checkbox" class="" id="check_notadebito">

					<div class="row" style="display: none" id="container_notadebito">
						<div class="col-sm-6">
							<label class="control-label" for="proveedornotadebito">PROVEEDOR</label>
							<select name="proveedor" id="proveedornotadebito" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
								<option value="">Seleccione</option>
								<?php do {  ?>
									<option value="<?= $row_Clientes['razonsocial'] . '&&&' .$row_Clientes['ruc']?>">
										<?= $row_Clientes['razonsocial'] . ' ' .$row_Clientes['ruc']?>
									</option>
									<?php
								} while ($row_Clientes = mysql_fetch_assoc($Clientes));
								$rows = mysql_num_rows($Clientes);
								if($rows > 0) {
									mysql_data_seek($Clientes, 0);
									$row_Clientes = mysql_fetch_assoc($Clientes);
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<label class="control-label" for="tipocomprobantenotadebito">Tipo Comprobante</label>
							<select class="form-control" name="tipocomprobantenotadebito"
							id="tipocomprobantenotadebito">
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
						<input class="form-control"type="number" value="1" min="1" step="any"  id="tipocambionotadebito">
					</div>
					<div class="col-sm-3">
						<label class="control-label" for="precio_notadebito">Precio</label>
						<input class="form-control" data-type="notadebito" oninput="changeprecioestibador(this)"
						readonly type="number" data-tipocambio="tipocambionotadebito" id="precio_notadebito">
					</div>
				</div>
			</div>


			<div style="margin-top: 10px">
				<label class="" for="check_notacredito">Nota credito?</label>
				<input type="checkbox" class="" id="check_notacredito">

				<div class="row" style="display: none" id="container_notacredito">
					<div class="col-sm-6">
						<label class="control-label" for="proveedornotacredito">PROVEEDOR</label>
						<select name="proveedor" id="proveedornotacredito" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
							<option value="">Seleccione</option>
							<?php do {  ?>
								<option value="<?= $row_Clientes['razonsocial'] . '&&&' .$row_Clientes['ruc']?>">
									<?= $row_Clientes['razonsocial'] . ' ' .$row_Clientes['ruc']?>
								</option>
								<?php
							} while ($row_Clientes = mysql_fetch_assoc($Clientes));
							$rows = mysql_num_rows($Clientes);
							if($rows > 0) {
								mysql_data_seek($Clientes, 0);
								$row_Clientes = mysql_fetch_assoc($Clientes);
							}
							?>
						</select>
					</div>
					<div class="col-sm-3">
						<label class="control-label" for="tipocomprobantenotacredito">Tipo Comprobante</label>
						<select class="form-control" name="tipocomprobantenotacredito"
						id="tipocomprobantenotacredito">
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
					<input class="form-control"type="number" value="1" min="1" step="any"  id="tipocambionotacredito">
				</div>
				<div class="col-sm-3">
					<label class="control-label" for="precio_notacredito">Precio</label>
					<input class="form-control" data-tipocambio="tipocambionotacredito" data-type="notacredito" oninput="changeprecioestibador(this)"
					readonly type="number" name="" id="precio_notacredito">
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
<div class="modal fade" id="mProrrateo" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document" style="width: 700px">
		<div class="modal-content m-auto">
			<div class="modal-header">
				<h2 class="modal-title" id="title_extra">PRORRATEO POR PESO</h2>
			</div>
			<div class="modal-body">
				<form id="formExtra">
					<div class="container-fluid">
						<div class="row">
							<div class="row">
								<div class="col-sm-6">
									<label for="field-1" class="control-label">PROVEEDOR</label>

									<select name="proveedor" id="proveedorpro" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
										<option value="">Seleccione</option>
										<?php do {  ?>
											<option value="<?= $row_Clientes['razonsocial'] . '&&&' .$row_Clientes['ruc']?>">
												<?= $row_Clientes['razonsocial'] . ' ' .$row_Clientes['ruc']?>
											</option>
											<?php
										} while ($row_Clientes = mysql_fetch_assoc($Clientes));
										$rows = mysql_num_rows($Clientes);
										if($rows > 0) {
											mysql_data_seek($Clientes, 0);
											$row_Clientes = mysql_fetch_assoc($Clientes);
										}
										?>
									</select>
									<!-- <input type="text" required class="form-control" name="nrorucpro" id="nrorucpro"> -->
								</div>
								<!-- <div class="col-sm-6">
									<label for="field-1" class="control-label">Proveedor</label>
									<input type="text" required class="form-control" name="proveedorpro"
									id="proveedorpro">
								</div> -->
							</div>

							<div class="row" style="margin-top: 10px">
								<div class="col-sm-3">
									<label class="control-label" for="monedapro">Moneda</label>
									<select class="form-control" name="monedapro" id="monedapro"
									onchange="changemonedapro(this)">
									<option value="soles">S/</option>
									<option value="dolares">$</option>
								</select>
							</div>
							<div class="col-sm-3" id="containerTipoCambio" style="display: none">
								<label class="control-label" for="monedapro">Cambio</label>
								<input type="number" class="form-control" value="1" min="1" step="any" name="tipocambiopro"
								id="tipocambiopro">
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="tipocomprobantepro">Tipo Comprobante</label>
								<select class="form-control " name="tipocomprobantepro" id="tipocomprobantepro" required>
									<option value="factura">Guia</option>
									<option value="factura">Factura</option>
									<option value="boleta">Boleta</option>
									<option value="notaventa">Nota venta</option>
									<option valgit ue="recibo">Recibo</option>
									<option value="otros">Otros</option>
								</select>
							</div>
							<div class="col-sm-3">
								<label class="control-label" for="nrocomprobantepro">Nro Comprobante</label>
								<input class="form-control" name="" id="nrocomprobantepro">
							</div>

							<div class="col-sm-3">
								<label class="control-label" for="preciopro">Precio</label>
								<input class="form-control" oninput="changepeso(this)" type="number" name=""
								id="preciopro">
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
								<th id="varTypeExtra" width="120px">Peso</th>
								<th width="60px">Imp Ind</th>
								<th width="60px">Importe</th>
							</thead>
							<tbody id="detalleProrrateo">
							</tbody>
						</table>
					</div>
					<button class="btn btn-primary" type="submit">Guardar</button>
					<button type="button" data-dismiss="modal" aria-label="Close"
					class="btn btn-danger">Cerrar</button>
				</div>
			</form>
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

	function keyControl(key){
		var control = [0,8];
		var result;
		if(control.indexOf(key.which) >= 0){
			result = true;
		}else{
			result =  false;
		}
		return result;
	}

	function pathKey(eReg, key){
		var letra = String.fromCharCode(key.which);
		return keyControl(key) || eReg.test(letra);
	}


	(function($) {
		$(document).ready(function () {
			$(document).on('keypress', '.sololetras', function (key) {
				return pathKey(/^[a-z]| |[ñÑáéíóúÁÉÍÓÚ]$/i, key);
			});
			$(document).on('keypress', '.sololetras', function (key) {
				return pathKey(/^[a-z]| |[ñÑáéíóúÁÉÍÓÚ]$/i, key);
			});
			$(document).on('keypress', '.solonumeros', function (key) {
				return pathKey(/^[0-9.]/i, key);
			});
			$(document).on('keypress', '.nospace', function (key) {
				return pathKey(/^\S/i, key);
			});
        //agregado por NN 27/09/
        $(document).on('keypress', '.cantidades', function (key) {
        	return pathKey(/^[0-9]|[.]$/i, key);
        });
        $(document).on('keypress', '.correo', function (key) {
        	return pathKey(/^[a-z]|[0-9]|[-_.@]/i, key);
        });
        $(document).on('keypress', '.especiales', function (key) {
        	return pathKey(/^[-a-zA-Z0-9_.ñÑÁÉÍÓÚáéíóú\s]+$/i, key);
        });
        $(document).on('keypress', '.letrasnumeros', function (key) {
        	return pathKey(/^[-a-zA-Z0-9]+$/i, key);
        });
        $(document).on('keypress', '.letrasnumeros', function (key) {
        	return pathKey(/^[-a-zA-Z0-9]+$/i, key);
        });
        $(document).on('keypress', '.address', function (key) {
        	return pathKey(/^[-a-zA-Z0-9_.,#\s]+$/i, key);
        });
        $(document).on('keypress', '.letras_especiales', function (key) {
        	return pathKey(/^[-a-zA-Z_.,#@()\s]+$/i, key);
        });
        $(document).on('keypress', '.letrasnumeros_especiales', function (key) {
        	return pathKey(/^[-a-zA-Z0-9_.,#@\s]+$/i, key);
        });
        $(document).on('keypress', '.letrasnumeros_coma', function (key) {
        	if ($('.letrasnumeros_coma').val().length > 0) {
        		var last_caracter = $('.letrasnumeros_coma').val().substring($('.letrasnumeros_coma').val().length - 1, $('.letrasnumeros_coma').val().length);
        		if (last_caracter == "," && last_caracter == key.key) {
        			return false;
        		}else{
        			return pathKey(/^[a-zA-Z0-9,]+$/i, key);
        		}
        	}else{
        		return pathKey(/^[a-zA-Z0-9]+$/i, key);
        	}

        });
    });
	})(jQuery);
	$(document).on('contextmenu', 'input, select, textarea',function(){ return false; });

</script>
<script type="text/javascript">
	$(document).on('focus', '.focusandclean', function (e) {
		if(e.target.value && parseInt(e.target.value) == 0) {
			e.target.value = ""
		}
	});

	let arrayDetalle;
	let monedadolar = false;
	let typetransporte = "";
	let subtotalGLOBAL = 0;
	function checkcosteo(e) {
		if (e.checked) {
			getSelectorAll(".costeochecked").forEach(e => {
				e.style.display = ""
			})
			getSelectorAll(".costeosinchecked").forEach(e => {
				e.style.display = "none"
			})

		} else {
			getSelectorAll(".costeochecked").forEach(e => {
				e.style.display = "none"
			})

			getSelectorAll(".costeosinchecked").forEach(e => {
				e.style.display = ""
			})
		}
		console.log(e.checked)
	}
	function showopciones() {
		$("#mopcionesextras").modal();
	}
	function changemonedapro(e) {
		if (e.value == "dolares") {
			containerTipoCambio.style.display = ""
		} else {
			containerTipoCambio.style.display = "none"
		}
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
	function changetipomoneda(e){
		if(e.value == "dolares"){
			getSelector(`#${e.dataset.container}`).style.display = ""
		}else{
			getSelector(`#${e.dataset.container}`).style.display = "none"
			getSelector(`#${e.dataset.container.split("container")[1]}`).value = 1
		}
	}
	formExtra.addEventListener("submit", e => {
		e.preventDefault()
		if (getSelector(".importeindividualpro").value && getSelector(".importeindividualpro").value != 0) {
			if("dolares" == monedapro.value && "" == tipocambiopro.value){
				alert("debe ingresar todos los campos")
			}else{
				let tipocambio = 1;
				if(monedapro.value == "dolares"){
					tipocambio = parseFloat(tipocambiopro.value)
				}
				getSelectorAll(".importetotalpro").forEach(i => {
					getSelector(`#detalleFactura_${i.dataset.indexdetalle}`).closest("tr").querySelector(".total_costeo").value = parseFloat(getSelector(`#detalleFactura_${i.dataset.indexdetalle}`).closest("tr").querySelector(".total_costeo").value)*tipocambio + parseFloat(i.value)

					getSelector(`#detalleFactura_${i.dataset.indexdetalle}`).value = i.value

				});
				let tc = getSelector(`#tipocambiopro`).value ? parseFloat(getSelector(`#tipocambiopro`).value) : 0;
				getSelector(".sumatransporte").value = (parseFloat(preciopro.value)*tc).toFixed(4)
				calcularExtras()
				$("#mProrrateo").modal("hide");
			}
			
		} else {
			alert("debe ingresar todos los campos")
		}
	})
	function setExtra(e) {
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
					<td><input type="number" required class="form-control pesoitempro" oninput="changepeso(this)"></td>
					<td><input readonly type="number" class="form-control importeindividualpro"></td>
					<td><input readonly data-indexdetalle="${nro}" type="number" class="form-control importetotalpro"></td>
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
					<td><input type="number" readonly required class="form-control pesoitempro" oninput="changepeso(this)" value="${getSelector("#vcf_" + nro).value}"></td>
					<td><input readonly type="number" class="form-control importeindividualpro"></td>
					<td><input readonly data-indexdetalle="${nro}" type="number" class="form-control importetotalpro"></td>
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
			let suma = 0;
			getSelectorAll(".pesoitempro").forEach(i => {
				suma += parseFloat(i.value) 
				if (i.value == 0 || i.value == "") {
					proccesspeso = false;
				}
			});
			if (proccesspeso) {


				let tc = getSelector(`#tipocambiopro`).value ? parseFloat(getSelector(`#tipocambiopro`).value) : 0;

				const unit = $("#preciopro").val() *tc / suma;
				getSelectorAll(".pesoitempro").forEach(i => {
					debugger
					const cantidad = parseInt(i.closest("tr").querySelector(".cant_recibida").textContent)

					i.parentElement.parentElement.querySelector(".importeindividualpro").value = (unit * i.value / cantidad).toFixed(4)
					i.parentElement.parentElement.querySelector(".importetotalpro").value = (unit * i.value).toFixed(4)
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

	function visualizar(e){
		descuento.setAttribute("readonly", true)
		facturafechaemision.setAttribute("readonly", true)
		tipocomprobantefactura.setAttribute("readonly", true)
		nrocomprobante.setAttribute("readonly", true)
		moneda.setAttribute("readonly", true)
		showopcionesextras.style.display = "none"
		guardarcosteo.style.display = "none"


		$("#mFacturaCompra").modal();
		fetch(`getDetalleCompraCosteo.php?codigorc=${e.dataset.codigorc}&type=${e.dataset.type}&codigo=${e.dataset.codigo}`)
		.then(res => res.json())
		.catch(error => console.error("error: ", error))
		.then(res => {
			if(res && res.header){

				$("#mproveedor1").text(res.headerx.razonsocial)
				$("#mfechaemision1").text(res.headerx.fecha_emision)
				$("#mvalortotal1").text(res.headerx.montofact)
				$("#mcodref11").text(res.headerx.numero_guia)
				$("#mcodref21").text(res.headerx.codigoref2 ? res.headerx.codigoref2 : "No tiene")
				$("#mgeneradapor1").text(res.headerx.usuario)
				$("#mruc1").text(res.headerx.ruc)

				$("#codigo_orden_compra").val(res.headerx.codigoguia ? res.headerx.codigoguia : 0)
				$("#codigo_guia_sin_oc").val(res.headerx.codigo_guia_sin_oc ? res.headerx.codigo_guia_sin_oc : 0)

				$("#codigoproveedor").val(res.headerx.codigoproveedor)
				$("#codigosucursal").val(res.headerx.sucursal)
				$("#msucursal1").text(res.headerx.nombre_sucursal)




				const {fecha_registro, descuentocompras, tipo_comprobante, numerocomprobante, tipomoneda} = res.header;
				facturafechaemision.value = fecha_registro.substring(0,10)
				descuento.value = descuentocompras.substring(0,10)
				$("#tipocomprobantefactura").val(tipo_comprobante)
				moneda.value = tipomoneda
				nrocomprobante.value = numerocomprobante


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
						<td class="costeosinchecked"><input readonly type="text" oninput="changedescuento(this)" value="${r.descxitem}" class="form-control descuento solonumeros focusandclean"></td>
						<td class="costeosinchecked"><input readonly id="preciocompra${i}" data-toggle="tooltip"  step="any" data-placement="bottom" title="0" oninput="changepreciocompra(this)" value="${r.vcu}" required type="text" class="solonumeros focusandclean precio-compra form-control"></td>

						<td class="costeosinchecked"><input readonly step="any" data-toggle="tooltip" data-placement="bottom" title="0" oninput="changeimporte(this)" value="${r.vci}" required type="text" class="solonumeros focusandclean importe form-control"></td>

						<td class="costeosinchecked"><input readonly type="text" value=${r.descmonto} readonly class="form-control descuentocantidad"></td>
						<td><input readonly type="text" readonly class="form-control vcf" id="vcf_${i}" value="${r.vcf}"></td>

						<td class="costeosinchecked"><input readonly type="text" readonly class="form-control igvrow" value="${r.igv}"></td>
						<td class="costeosinchecked"><input readonly type="text" readonly value="${r.totalcompra}" class="form-control valorcompra2"></td>

						<td style="display: none" class="costeochecked"><input readonly value="${r.preciotransporte}" id="detalleFactura_${i}" class="form-control transporte_costeo" readonly></td>
						<td style="display: none" class="costeochecked"><input readonly value="${r.precioestibador}" class="form-control estibador_costeo" readonly></td>
						<td style="display: none" class="costeochecked"><input readonly value="${r.notadebito}" class="form-control notadebito" readonly></td>
						<td style="display: none" class="costeochecked"><input readonly value="${r.precionotacredito}" class="form-control notacredito" readonly></td>
						<td style="display: none" class="costeochecked"><input readonly value="${r.totalconadicionales}" class="form-control total_costeo" readonly></td>
						<td style="display: none" class="costeochecked"><input readonly value="${r.totalunidad}" class="form-control totalunidadcosteo" readonly></td>
						</tr>`);
				});


			}else{
				alert("hubo un error")
			}
		})

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
			descuento.removeAttribute("readonly")
			facturafechaemision.removeAttribute("readonly")
			tipocomprobantefactura.removeAttribute("readonly")
			nrocomprobante.removeAttribute("readonly")
			moneda.removeAttribute("readonly")

			facturafechaemision.value = ""
			descuento.value = ""
			$("#tipocomprobantefactura").val("")
			moneda.value = ""
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
						<td class="costeosinchecked"><input type="text" autocomplete="off" oninput="changedescuento(this)" value="0" class="form-control descuento solonumeros focusandclean"></td>
						<td class="costeosinchecked"><input autocomplete="off" id="preciocompra${i}" data-toggle="tooltip"  step="any" data-placement="bottom" title="0" oninput="changepreciocompra(this)" value="${r.pcompra}" required type="text" class="solonumeros focusandclean precio-compra form-control"></td>

						<td class="costeosinchecked"><input step="any" data-toggle="tooltip" data-placement="bottom" title="0" oninput="changeimporte(this)" autocomplete="off" value="${r.pcompra ? (r.pcompra * r.cantidad).toFixed(4) : ""}" required type="text" class="solonumeros focusandclean importe form-control"></td>

						<td class="costeosinchecked"><input type="text" readonly class="form-control descuentocantidad"></td>
						<td><input type="text" readonly class="form-control vcf" id="vcf_${i}"></td>

						<td class="costeosinchecked"><input type="text" readonly class="form-control igvrow"></td>
						<td class="costeosinchecked"><input type="text" readonly class="form-control valorcompra2"></td>

						<td style="display: none" class="costeochecked"><input id="detalleFactura_${i}" class="form-control transporte_costeo" readonly></td>
						<td style="display: none" class="costeochecked"><input class="form-control estibador_costeo" readonly></td>
						<td style="display: none" class="costeochecked"><input class="form-control notadebito" readonly></td>
						<td style="display: none" class="costeochecked"><input class="form-control notacredito" readonly></td>
						<td style="display: none" class="costeochecked"><input class="form-control total_costeo" readonly></td>
						<td style="display: none" class="costeochecked"><input class="form-control totalunidadcosteo" readonly></td>
						</tr>`);
				});
				$("#detalleFacturar-list").append(`
					<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td id="titlesoles" class="costeosinchecked" style="text-align: right; font-weight: bold;">TOTAL S/</td>
					<td><input type="text" readonly class="form-control sumavcf"></td>
					<td class="costeosinchecked"><input type="text" readonly class=" form-control sumaigvrow"></td>
					<td class="costeosinchecked"><input type="text" readonly class="form-control sumavalorcompra2"></td>

					<td style="display: none" class="costeochecked"><input class="form-control sumatransporte" readonly></td>

					<td style="display: none" class="costeochecked"><input class="form-control sumaestibador_costeo" readonly></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotadebito" readonly></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotacredito" readonly></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumatotal_costeo" readonly></td>
					<td style="display: none" class="dddd"><input class="form-control sumatotalunidadcosteo" readonly></td>
					</tr>`);
				$("#detalleFacturar-list").append(`
					<tr id="rowfacturadolar">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td class="costeosinchecked"></td>
					<td id="titledolar" class="costeosinchecked" style="text-align: right; font-weight: bold;">TOTAL $</td>
					<td><input type="text" readonly class="form-control sumavcfdolar"></td>
					<td class="costeosinchecked"><input type="text" readonly class=" form-control sumaigvrowdolar"></td>
					<td class="costeosinchecked"><input type="text" readonly class="form-control sumavalorcompra2dolar"></td>

					<td style="display: none" class="costeochecked"><input class="form-control transporte_costeo" readonly></td>

					<td style="display: none" class="costeochecked"><input class="form-control sumaestibador_costeodolar" readonly></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotadebitodolar" readonly></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumanotacreditodolar" readonly></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumatotal_costeodolar" readonly></td>
					<td style="display: none" class="costeochecked"><input class="form-control sumatotalunidadcosteodolar" readonly></td>
					</tr>`);
				rowfacturadolar.style.display = "none"
				$('[data-toggle="tooltip"]').tooltip()
				$('.tooltips').tooltip();
			});
btn_prorrateo.disabled = true
btn_participacion.disabled = true
container_cambio.style.display = "none"
monedadolar = false;
tipocambio.value = 0
$("#mFacturaCompra").modal();
})
});
function changecambiodolar(e) {
	if (e.value < 0 || e.value == "") {
		e.value = 0;
		return;
	}
	calcularTotalSinExtras();
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
	totalx += parseFloat(tr.querySelector(".vcf").value ? tr.querySelector(".vcf").value : 0);
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
		// const ss = e.value / parseInt(aa.querySelector(".cantidad").textContent)
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
		})
		getSelector(".sumavcf").value = sumavcf.toFixed(2)
		getSelector(".sumaigvrow").value = sumaigvrow.toFixed(2)
		getSelector(".sumavalorcompra2").value = sumavalorcompra2.toFixed(4)
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
			debugger
			tr.querySelector(`.${e.dataset.type}`).value = (parseFloat(e.value) * parseFloat(i.value) * tc/ total).toFixed(2)
			tr.querySelector(".total_costeo").value = calcularcosteobyfile(tr)
		});
		getSelector(`.suma${e.dataset.type}`).value = (parseFloat(e.value)*tc).toFixed(2)
		calcularExtras()
		// calcularTotalSinExtras()
	}
	function calcularExtras() {
		let sumatotalcosteo = 0;
		let sumaunidadtotal = 0;
		getSelectorAll(".total_costeo").forEach(ix => {
			sumatotalcosteo += parseFloat(ix.value);
			const totalunidad = parseFloat(ix.value) / parseInt(ix.closest("tr").querySelector(".cantidad").textContent)
			sumaunidadtotal += totalunidad
			ix.closest("tr").querySelector(".totalunidadcosteo").value = totalunidad.toFixed(4)
		});
		getSelector(".sumatotal_costeo").value = parseFloat(sumatotalcosteo).toFixed(4)
		getSelector(".sumatotalunidadcosteo").value = parseFloat(sumaunidadtotal).toFixed(4)

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
			precio_estibador.removeAttribute('readonly');
			precio_notadebito.removeAttribute("readonly")
			precio_notacredito.removeAttribute("readonly")
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
			precio_estibador.setAttribute("readonly", true)
			precio_notadebito.setAttribute("readonly", true)
			precio_notacredito.setAttribute("readonly", true)
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
		document.querySelector(".tooltip-inner").textContent = `${e.value} - ${(e.value * 1.18).toFixed(4)}`
		e.dataset.originalTitle = `${e.value} - ${(e.value * 1.18).toFixed(4)}`
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
		tr.querySelector(".total_costeo").value = importe * (100 - descuento) / 100
		tr.querySelector(".totalunidadcosteo").value = (importe * (100 - descuento) / 100)/parseInt(tr.querySelector(".cantidad").textContent)

		if(origin != "importe")
			tr.querySelector(".importe").value = (importe).toFixed(2)

		tr.querySelector(".descuentocantidad").value = (parseFloat(importe) * descuento / 100).toFixed(2)
		tr.querySelector(".vcf").value = (importe * (100 - descuento) / 100).toFixed(2)
		tr.querySelector(".valorcompra2").value = (importe * 1.18 * (100 - descuento) / 100).toFixed(2)
		tr.querySelector(".igvrow").value = (importe * 0.18 * (100 - descuento) / 100).toFixed(2)

	}
	document.querySelector("#saveFacturar").addEventListener("submit", e => {
		e.preventDefault();
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
				const proveedorpro = $("#proveedornotacredito").val().split("&&&")[1];
				const query =
				`insert into transporte_compra 
				(tipo_transporte, tipocomprobante, numerocomprobante, ructransporte, moneda, tipocambio, preciotransp_soles, preciotransp_dolar, codigocompras) 
				values 
				('${typetransporte}', '${tipocomprobantepro.value}', '${nrocomprobantepro.value}', '${proveedorpro}', '${monedapro.value}', 0, ${preciopro.value}, 0, ##IDCOMPRAS##)`
				data.gastos.push(query)
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
				data.gastos.push(query)
			}
		}
		if (getSelector("#check_notadebito").checked) {
			if (!$("#proveedornotadebito").val() || !tipocomprobantenotadebito.value || !numerocomprobantenotadebito.value || !precio_notadebito.value) {
				alert("debe llenar todos los datos de nota de debito");
				return;
			} else {
				const rucnotadebito = $("#proveedorestibador").val().split("&&&")[1];
				const query =
				`insert into notadebito_compra 
				(tipocomprobante, numerocomprobante, rucnd, moneda, tipocambio, preciond_soles, preciond_dolar, codigocompras) 
				values 
				('${tipocomprobantenotadebito.value}', '${numerocomprobantenotadebito.value}', '${rucnotadebito}', '${monedanotadebito.value}', 0, ${precio_notadebito.value}, 0, ##IDCOMPRAS##)`
				data.gastos.push(query)
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
				('${tipocomprobantenotacredito.value}', '${numerocomprobantenotacredito.value}', '${rucnotacredito}', '${monedanotacredito.value}', 0, ${precio_notacredito.value}, 0, ##IDCOMPRAS##)`
				data.gastos.push(query)
			}
		}
		const typepay = moneda.value == "dolares" ? "dolar" : ""
		data.header = {
			codigocompras: 0,
			tipomoneda: moneda.value,
			tipo_comprobante: tipocomprobantefactura.value,
			numerocomprobante: nrocomprobante.value,
			codacceso: <?= $_SESSION['kt_login_id'] ?>,

			ruc_proveedor: mruc1.textContent,

			subtotal: getSelector(".sumavcf"+typepay).value,
			igv: getSelector(".sumaigvrow"+typepay).value,
			total: getSelector(".sumavalorcompra2"+typepay).value,
			codigoproveedor: codigoproveedor.value,
			estadofact: 1,
			codigosuc: codigosucursal.value,
			codigo_orden_compra: codigo_orden_compra.value,
			codigo_guia_sin_oc: codigo_guia_sin_oc.value,
			fecha_registro: facturafechaemision.value,
			valorcambio: tipocambio,
			descuentocompras: descuento.value ? descuento.value : 0

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
		getSelector(".container_cambio").style.display = "none";
		monedadolar = false
	}
}
</script>