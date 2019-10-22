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
													<select class="form-control select2-allow-clear"
														name="tipocomprobantefactura" id="tipocomprobantefactura">
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
											<div class="col-md-2 container_cambio" id="container_cambio" style="display: none">
												<div class="form-group">
													<label for="field-1" class="control-label">Cambio</label>
													<input type="number" step="any" class="form-control" id="tipocambio" oninput="changecambiodolar(this)"
														name="">
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
										<th class="costeosinchecked width=" 120px">Desc x Item</th>
										<th class="costeosinchecked width=" 120px">VCU</th>
										<th class="costeosinchecked width=" 120px">VCI</th>
										<th class="costeosinchecked" width="120px">DSCTO</th>
										<th width="120px">VCF</th>
										<th class="costeosinchecked width=" 120px">IGV</th>
										<th class="costeosinchecked width=" 120px">Total</th>
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
						<button class="btn btn-success" type="button" onclick="showopciones()">Opciones</button>
						<button type="submit" class="btn btn-success">Guardar</button>
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
								id="btn_participacion" onclick="setExtra(this)" id="participacion">PRORRATEO POR COMPRA</button>
						</div>
					</div>
				</div>

				<div style="margin-top: 10px">
					<label class="" for="check_estibador">Estibador?</label>
					<input type="checkbox" class="" id="check_estibador">

					<div class="row" style="display: none" id="container_estibador">
						<div class="col-sm-6">
							<label class="control-label" for="rucestibador">RUC</label>
							<input class="form-control" name="" id="rucestibador">
						</div>
						<div class="col-sm-6">
							<label class="control-label" for="proveedorestibador">Proveedor</label>
							<input class="form-control" name="" id="proveedorestibador">
						</div>
						<div class="col-sm-3">
							<label class="control-label" for="tipocomprobanteestibador">Tipo Comprobante</label>
							<select class="form-control select2-allow-clear" name="tipocomprobanteestibador"
								id="tipocomprobanteestibador">
								<option value="">Select</option>
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
							<select class="form-control" id="monedaestibador"
								name="monedaestibador" required>
								<option value="soles">S/</option>
								<option value="dolares">$</option>
							</select>
						</div>
						<div class="col-sm-3">
							<label class="control-label" for="precio_estibador">Valor de Compra</label>
							<input class="form-control" data-type="estibador_costeo"
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
							<label class="control-label" for="rucnotadebito">RUC</label>
							<input class="form-control" name="" id="rucnotadebito">
						</div>
						<div class="col-sm-6">
							<label class="control-label" for="proveedornotadebito">Proveedor</label>
							<input class="form-control" name="" id="proveedornotadebito">
						</div>
						<div class="col-sm-3">
							<label class="control-label" for="tipocomprobantenotadebito">Tipo Comprobante</label>
							<select class="form-control select2-allow-clear" name="tipocomprobantenotadebito"
								id="tipocomprobantenotadebito">
								<option value="">Select</option>
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
							<select class="form-control" id="monedanotadebito"
								name="monedanotadebito" required>
								<option value="soles">S/</option>
								<option value="dolares">$</option>
							</select>
						</div>
						<div class="col-sm-3">
							<label class="control-label" for="precio_notadebito">Precio</label>
							<input class="form-control" data-type="notadebito" oninput="changeprecioestibador(this)"
								readonly type="number" name="" id="precio_notadebito">
						</div>
					</div>
				</div>


				<div style="margin-top: 10px">
					<label class="" for="check_notacredito">Nota credito?</label>
					<input type="checkbox" class="" id="check_notacredito">

					<div class="row" style="display: none" id="container_notacredito">
						<div class="col-sm-6">
							<label class="control-label" for="rucnotacredito">RUC</label>
							<input class="form-control" name="" id="rucnotacredito">
						</div>
						<div class="col-sm-6">
							<label class="control-label" for="proveedornotacredito">Proveedor</label>
							<input class="form-control" name="" id="proveedornotacredito">
						</div>
						<div class="col-sm-3">
							<label class="control-label" for="tipocomprobantenotacredito">Tipo Comprobante</label>
							<select class="form-control select2-allow-clear" name="tipocomprobantenotacredito"
								id="tipocomprobantenotacredito">
								<option value="">Select</option>
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
							<select class="form-control" id="monedanotacredito"
								name="monedanotacredito" required>
								<option value="soles">S/</option>
								<option value="dolares">$</option>
							</select>
						</div>
						<div class="col-sm-3">
							<label class="control-label" for="precio_notacredito">Precio</label>
							<input class="form-control" data-type="notacredito" oninput="changeprecioestibador(this)"
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
									<label for="field-1" class="control-label">Nro RUC</label>
									<input type="text" required class="form-control" name="nrorucpro" id="nrorucpro">
								</div>
								<div class="col-sm-6">
									<label for="field-1" class="control-label">Proveedor</label>
									<input type="text" required class="form-control" name="proveedorpro"
										id="proveedorpro">
								</div>
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
									<label class="control-label" for="monedapro">Tipo Cambio</label>
									<input type="number" class="form-control" step="any" name="tipocambiopro"
										id="tipocambiopro">
								</div>
								<div class="col-sm-3">
									<label class="control-label" for="tipocomprobantepro">Tipo Comprobante</label>
									<select class="form-control select2-allow-clear" name="tipocomprobantepro"
										id="tipocomprobantepro">
										<option value="">Select</option>
										<option value="factura">Guia</option>
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
	let arrayDetalle;
	let monedadolar = false;
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
		calcularTotales();
		const subtotal = subtotalGLOBAL;
	}
	formExtra.addEventListener("submit", e => {
		e.preventDefault()
		if (getSelector(".importeindividualpro").value && getSelector(".importeindividualpro").value != 0) {
			getSelectorAll(".importetotalpro").forEach(i => {

				getSelector(`#detalleFactura_${i.dataset.indexdetalle}`).closest("tr").querySelector(".total_costeo").value = parseFloat(getSelector(`#detalleFactura_${i.dataset.indexdetalle}`).closest("tr").querySelector(".total_costeo").value) + parseFloat(i.value)

				getSelector(`#detalleFactura_${i.dataset.indexdetalle}`).value = i.value

			});

			calcularTotales()
			$("#mProrrateo").modal("hide");
		} else {
			alert("debe ingresar todos los campos")
		}
	})
	function setExtra(e) {
		let nro = 0;
		if (e.dataset.type == "prorrateo") {
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
			varTypeExtra.textContent = "Valor Compra"
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
						<td><input type="number" readonly required class="form-control pesoitempro" oninput="changepeso(this)" value="${getSelector("#preciocompra" + nro).value}"></td>
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
				suma += parseFloat(i.value) * parseInt(i.closest("tr").querySelector(".cant_recibida").textContent)
				if (i.value == 0 || i.value == "") {
					proccesspeso = false;
				}
			});
			if (proccesspeso) {
				const unit = $("#preciopro").val() / suma;
				getSelectorAll(".pesoitempro").forEach(i => {
					const cantidad = parseFloat(i.parentElement.parentElement.querySelector(".cant_recibida").textContent)
					i.parentElement.parentElement.querySelector(".importeindividualpro").value = (unit * i.value).toFixed(4)
					i.parentElement.parentElement.querySelector(".importetotalpro").value = (unit * i.value * cantidad).toFixed(4)
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
							<td class="costeosinchecked"><input type="number" oninput="changedescuento(this)" value="0" class="form-control descuento"></td>
							<td class="costeosinchecked"><input id="preciocompra${i}" data-toggle="tooltip"  step="any" data-placement="bottom" title="0" oninput="changepreciocompra(this)" value="${r.pcompra}" required type="number" class="precio-compra form-control"></td>
							
							<td class="costeosinchecked"><input step="any" data-toggle="tooltip" data-placement="bottom" title="0" oninput="changeimporte(this)" value="${r.pcompra ? (r.pcompra * r.cantidad).toFixed(4) : ""}" required type="number" class="importe form-control"></td>
							
							<td class="costeosinchecked"><input type="text" readonly class="form-control descuentocantidad"></td>
							<td><input type="text" readonly class="form-control vcf"></td>

							<td class="costeosinchecked"><input type="text" readonly class="form-control igvrow"></td>
							<td class="costeosinchecked"><input type="text" readonly class="form-control valorcompra2"></td>

							<td style="display: none" class="costeochecked"><input id="detalleFactura_${i}" class="form-control transporte_costeo" readonly></td>
							<td style="display: none" class="costeochecked"><input class="form-control estibador_costeo" readonly></td>
							<td style="display: none" class="costeochecked"><input class="form-control notadebito" readonly></td>
							<td style="display: none" class="costeochecked"><input class="form-control notacredito" readonly></td>
							<td style="display: none" class="costeochecked"><input class="form-control total_costeo" readonly></td>
							<td style="display: none" class="costeochecked"><input class="form-control totalunidadcosteo" readonly></td>
							</tr>`);

						// let suma = 0;
						// getSelectorAll(".importe").forEach(item => {
						// 	if (item.textContent)
						// 		suma += parseFloat(item.value)
						// })
						// document.querySelector("#importe-total").textContent = suma * 1.18
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
								<td class="costeosinchecked" style="text-align: right; font-weight: bold;">TOTAL $</td>
								<td><input type="text" readonly class="form-control sumavcf"></td>
								<td class="costeosinchecked"><input type="text" readonly class=" form-control sumaigvrow"></td>
								<td class="costeosinchecked"><input type="text" readonly class="form-control sumavalorcompra2"></td>

								<td style="display: none" class="costeochecked"><input class="form-control transporte_costeo" readonly></td>

								<td style="display: none" class="costeochecked"><input class="form-control sumaestibador_costeo" readonly></td>
								<td style="display: none" class="costeochecked"><input class="form-control sumanotadebito" readonly></td>
								<td style="display: none" class="costeochecked"><input class="form-control sumanotacredito" readonly></td>
								<td style="display: none" class="costeochecked"><input class="form-control sumatotal_costeo" readonly></td>
								<td style="display: none" class="costeochecked"><input class="form-control sumatotalunidadcosteo" readonly></td>
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
								<td class="costeosinchecked" style="text-align: right; font-weight: bold;">TOTAL S/</td>
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
	function changecambiodolar(e){
		if (e.value < 0 || e.value == "") {
			e.value = 0;
			return;
		}
		calcularTotales();
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
		calcularTotales();
	}
	function calculartotalcosteo(tr) {
		let totalx = 0
		totalx += parseFloat(tr.querySelector(".importe").value ? tr.querySelector(".importe").value : 0);
		totalx += parseFloat(tr.querySelector(".transporte_costeo").value ? tr.querySelector(".transporte_costeo").value : 0);
		totalx += parseFloat(tr.querySelector(".estibador_costeo").value ? tr.querySelector(".estibador_costeo").value : 0);
		totalx += parseFloat(tr.querySelector(".notadebito").value ? tr.querySelector(".notadebito").value : 0);
		totalx += parseFloat(tr.querySelector(".notacredito").value ? tr.querySelector(".notacredito").value : 0);
		
		totalx -= tr.querySelector(".descuento").value ? totalx * (parseFloat(tr.querySelector(".descuento").value)) / 100 : 0;
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

		calcularFila(aa)


		calcularTotales();
		updateColumns();
	}
	function calcularTotales() {
		let sumavcf = 0
		let sumaigvrow = 0
		let sumavalorcompra2 = 0
		getSelectorAll(".vcf").forEach(s => {
			const tr = s.closest("tr");
			sumavcf += parseFloat(s.value ? s.value : 0);
			sumaigvrow += parseFloat(tr.querySelector(".igvrow").value ? tr.querySelector(".igvrow").value : 0)
			sumavalorcompra2 += parseFloat(tr.querySelector(".valorcompra2").value ? tr.querySelector(".valorcompra2").value : 0)
		})
		getSelector(".sumavcf").value = sumavcf.toFixed(4)
		getSelector(".sumaigvrow").value = sumaigvrow.toFixed(4)
		getSelector(".sumavalorcompra2").value = sumavalorcompra2.toFixed(4)
		if(tipocambio){
			getSelector(".sumavcfdolar").value = (sumavcf*parseFloat(tipocambio.value)).toFixed(4)
			getSelector(".sumaigvrowdolar").value = (sumaigvrow*parseFloat(tipocambio.value)).toFixed(4)
			getSelector(".sumavalorcompra2dolar").value = (sumavalorcompra2*parseFloat(tipocambio.value)).toFixed(4)
		}
	}
	function changeprecioestibador(e) {
		let total = 0;
		getSelectorAll(".precio-compra").forEach(i => {
			total += parseFloat(i.value) * parseInt(i.closest("tr").querySelector(".cantidad").textContent)
		});

		getSelectorAll(".precio-compra").forEach(i => {
			const tr = i.closest("tr");
			tr.querySelector(`.${e.dataset.type}`).value = parseFloat(e.value * parseInt(tr.querySelector(".cantidad").textContent) * i.value / total).toFixed(2)
			tr.querySelector(".total_costeo").value = calculartotalcosteo(tr)
		});
		getSelector(`.suma${e.dataset.type}`).value = parseFloat(e.value).toFixed(2)
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
		// calcularTotales()
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

		calcularFila(aa, true)
		calcularTotales();
		document.querySelector(".tooltip-inner").textContent = `${e.value} - ${(e.value * 1.18).toFixed(4)}`
		e.dataset.originalTitle = `${e.value} - ${(e.value * 1.18).toFixed(4)}`
		updateColumns();

	}
	function calcularFila(tr, preciocompra = false) {
		
		let importe = parseFloat(tr.querySelector(".importe").value)
		if (preciocompra) {
			importe = parseFloat(tr.querySelector(".precio-compra").value) * parseInt(tr.querySelector(".cantidad").textContent)
		} else {
			tr.querySelector(".precio-compra").value = (importe / parseInt(tr.querySelector(".cantidad").textContent)).toFixed(4)
		}
		const descuento = parseFloat(tr.querySelector(".descuento").value);
		tr.querySelector(".total_costeo").value = importe * (100 - descuento) / 100

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
			if ($("#tipocomprobante").val() && $("#numerocomprobante").val() && $("#empresatransporte").val() && $("#precio_transporte").val()) {
				data.gastos.push(`insert into GastosCompras (tipocomprobante, nrocomprobante, empresa, precio, idcompras, tipo) values ('${$("#tipocomprobante").val()}', '${$("#numerocomprobante").val()}', '${$("#empresatransporte").val()}', ${parseFloat($("#precio_transporte").val())}, ##IDCOMPRAS##, 'transporte')`);
			} else {
				alert("debe llenar todos los datos de transporte");
				return;
			}
		}
		if (getSelector("#check_estibador").checked) {
			if ($("#tipocomprobanteestibador").val() && $("#numerocomprobanteestibador").val() && $("#empresaestibador").val() && $("#precio_estibador").val()) {

				data.gastos.push(`insert into GastosCompras (tipocomprobante, nrocomprobante, empresa, precio, idcompras, tipo) values ('${$("#tipocomprobanteestibador").val()}', '${$("#numerocomprobanteestibador").val()}', '${$("#empresaestibador").val()}', 
				${parseFloat($("#precio_estibador").val())}, ##IDCOMPRAS##, 'estibador')`);

			} else {
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
			if (getSelector("#moneda").value == "dolares") {
				preciodolar = preciosoles;
				preciosoles = preciosoles / parseInt(getSelector("#tipocambio").value)
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
	function selectmoneda(e) {
		if (e.value == "dolares") {
			rowfacturadolar.style.display = ""
			getSelector(".container_cambio").style.display = "";
			monedadolar = true;
		} else {
			rowfacturadolar.style.display = "none"
			getSelector(".container_cambio").style.display = "none";
			monedadolar = false
		}
	}
</script>