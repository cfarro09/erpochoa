<head></head>
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
//------------Inicio Actualizar(Eliminar) Registro----------------
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Eliminar_Registro")) {
	$updateSQL = sprintf(
		"UPDATE proveedor_cuentas SET estado=%s WHERE codprovcue=%s",
		GetSQLValueString($_POST['estado'], "text"),
		GetSQLValueString($_POST['codprovcue'], "int")
	);

	mysql_select_db($database_Ventas, $Ventas);
	$Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

	$updateGoTo = "proveedor_cuentas.php";
	if (isset($_SERVER['QUERY_STRING'])) {
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $updateGoTo));
}
//------------Fin Actualizar(Eliminar) Registro----------------
//------------Inicio Juego de Registro "Listado"----------------
$colname_Listado = "-1";
if (isset($_GET['codigoproveedor'])) {
	$colname_Listado = $_GET['codigoproveedor'];
	$rucxx = $_GET['codigoproveedor'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Listado = sprintf("select *,  
r.abonoochoa as abonor, e.abonoochoa as abonoe, nd.abonoochoa as abonond, nc.abonoochoa as abononc, t.abonoochoa as abonot, 
p.ruc, t.id_transporte, t.tipocomprobante as tipocomprobantet, nc.tipocomprobante as tipocomprobantenc, nd.tipocomprobante as tipocomprobantend, e.tipocomprobante as tipocomprobantee, nc.numerocomprobante as numerocomprobantenc, nd.pagoacumulado, nd.preciond_soles, nd.id_notadebito, nd.numerocomprobante as numerocomprobantend, e.numerocomprobante as numerocomprobantee, t.numerocomprobante as numerocomprobantet, r.numerocomprobante as numerocomprobantec, p.ruc, s.nombre_sucursal 
FROM registro_compras r 
LEFT JOIN transporte_compra t on t.codigocompras = r.codigorc 
LEFT JOIN estibador_compra e on e.codigocompras = r.codigorc 
LEFT JOIN notadebito_compra nd on nd.codigocompras = r.codigorc 
LEFT JOIN notacredito_compra nc on nc.codigocompras = r.codigorc 
LEFT JOIN sucursal s on s.cod_sucursal=r.codigosuc 
LEFT JOIN proveedor p on p.ruc=r.rucproveedor where p.ruc= '%s' or t.ructransporte='%s' or nd.rucnd = '$rucxx' or nc.rucnotacredito = '$rucxx'", GetSQLValueString($colname_Listado, "char"), GetSQLValueString($colname_Listado, "char"));
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);

$colname_Proveedor = "-1";
if (isset($_GET['codigoproveedor'])) {
	$colname_Proveedor = $_GET['codigoproveedor'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Proveedor = sprintf("SELECT * FROM proveedor WHERE ruc = %s", GetSQLValueString($colname_Listado, "char"));
$Proveedor = mysql_query($query_Proveedor, $Ventas) or die(mysql_error());
$row_Proveedor = mysql_fetch_assoc($Proveedor);
$totalRows_Proveedor = mysql_num_rows($Proveedor);
//------------Fin Juego de Registro "Listado"----------------
//Enumerar filas de data tablas
$i = 1;

//Titulo e icono de la pagina
$Icono = "glyphicon glyphicon-credit-card";
$Color = "font-blue";

$VarUrl = "?codigoproveedor=" . $row_Proveedor['codigoproveedor'];
$TituloGeneral = '<div class="page-title"><h1 class="font-red-thunderbird">PROVEEDOR: ' . $row_Proveedor['razonsocial'] . ' - ' . $row_Proveedor['ruc'] . '</h1></div>';
$Titulo = "Cuentas Proveedor";
$NombreBotonAgregar = "Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar = "";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho = 700;
$popupAlto = 475;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//________________________________________________________________________________________________________________
?>
<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { ?>
	<div class="alert alert-danger">
		<strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>


	</div>
<?php } // Show if recordset empty 
?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty 
?>

	<button class="btn btn-success" style="margin: 10px 0" data-toggle="modal" data-target="#mpagar">PAGAR ACUMULADO</button>
	<table width="700" class="table table-striped table-bordered table-hover dt-responsive" id="sample_1">

		<thead>
			<tr>
				<th width="5%"> N&deg; </th>

				<th width="20%"> FECHA REG </th>
				<th width="15%"> TIPO - NUMERO </th>
				<th width="10%"> DETALLE </th>
				<th width="5%"> CARGO </th>
				<th width="20%"> ABONOS </th>
				<th width="5%"> SALDO </th>

				<th width="5%"> VER </th>
			</tr>
		</thead>
		<tbody>

			<?php $acumulado = 0;
			do {

				// $lastcodigoventa = $row_Listado["codigoventas"];
				// $abonoproveedor = $abonoochoa;

				// $auxiliar = number_format($acumulado, 2, '.', '')

			?>
				<?php if ($row_Listado['codigorc'] != NULL && $row_Listado['rucproveedor'] == $colname_Listado) {
					$acumulado += $row_Listado["total"];
					$abonoochoa = $row_Listado["abonor"];
					$lastcodigo = $row_Listado["codigorc"];
					$lastidname = "codigorc";
					$lasttipo = "registro_compras";
					$auxiliar = number_format($acumulado, 2, '.', '') ?>

					<tr>
						<?php $rc = $row_Listado['codigorc']; ?>
						<td> <?php echo $i; ?> </td>
						<td> <?php echo $row_Listado['fecha_registro']; ?></td>
						<td> <?php echo $row_Listado['tipo_comprobante'] . ' - ' . $row_Listado['numerocomprobantec']; ?> </td>
						<td> COMPRA </td>
						<td> <?php echo $row_Listado['total']; ?> </td>
						<td>0 </td>
						<td> <?= $auxiliar ?> </td>
						<td class="text-center">
							<a href="#" data-rc="<?= $row_Listado['codigorc']; ?>" data-codigoproveedor="<?= $row_Listado['rucproveedor']; ?>" onclick="mostrarModalRC(this)">Ver</a>
						</td>
					</tr>

					<?php if ($abonoochoa != null) : ?>
						<?php $arrayabonoproveedor = json_decode($abonoochoa) ?>
						<?php foreach ($arrayabonoproveedor as $abono) : ?>
							<?php
							$acumulado = $acumulado - $abono->montoextra;
							$auxiliar = number_format($acumulado, 2, '.', '');
							$i++;
							?>
							<tr>
								<td><?= $i ?></td>
								<td><?= $abono->fechaxxx ?></td>
								<td>ABONO</td>
								<td><?= $abono->tipopago ?></td>
								<td>0.00</td>
								<td><?= number_format((float) $abono->montoextra, 2, '.', '') ?></td>
								<td><?= $auxiliar ?></td>
								<td></td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>

				<?php } ?>
				<?php if ($row_Listado['id_transporte'] != NULL && $row_Listado['ructransporte'] == $colname_Listado) {
					$acumulado +=  $row_Listado['preciotransp_soles'];
					$abonoochoa = $row_Listado["abonot"];
					$lastcodigo = $row_Listado["id_transporte"];
					$lasttipo = "transporte_compra";
					$lastidname = "id_transporte";
					$auxiliar = number_format($acumulado, 2, '.', '') ?>
					<tr>
						<td> <?php echo $i; ?> </td>
						<td> <?php echo $row_Listado['fecha_registro']; ?></td>
						<td> <?php echo $row_Listado['tipocomprobantet'] . ' - ' . $row_Listado['numerocomprobantet']; ?> </td>
						<td> TRANSPORTE -
							<?PHP echo $row_Listado['tipo_transporte']; ?>
						</td>

						<td> <?php echo round($row_Listado['preciotransp_soles'], 2); ?> </td>
						<td> 0 </td>
						<td> <?= $auxiliar ?> </td>


						<td class="text-center">
							<a href="#" data-trans="<?= $row_Listado['id_transporte']; ?>" data-codigotrans="<?= $row_Listado['ructransporte']; ?>" onclick="mostrarModalTRANS(this)">Ver</a>
						</td>
					</tr>
					<?php if ($abonoochoa != null) : ?>
						<?php $arrayabonoproveedor = json_decode($abonoochoa) ?>
						<?php foreach ($arrayabonoproveedor as $abono) : ?>
							<?php
							$acumulado = $acumulado - $abono->montoextra;
							$auxiliar = number_format($acumulado, 2, '.', '');
							$i++;
							?>
							<tr>
								<td><?= $i ?></td>
								<td><?= $abono->fechaxxx ?></td>
								<td>ABONO</td>
								<td><?= $abono->tipopago ?></td>
								<td>0.00</td>
								<td><?= number_format((float) $abono->montoextra, 2, '.', '') ?></td>
								<td><?= $auxiliar ?></td>
								<td></td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
				<?php } ?>

				<?php if ($row_Listado['id_estibador'] != NULL && $row_Listado['rucestibador'] == $colname_Listado) {
					$acumulado += $row_Listado['precioestibador_soles'];
					$abonoochoa = $row_Listado["abonoe"];
					$lastcodigo = $row_Listado["id_estibador"];
					$lastidname = "id_estibador";
					$lasttipo = "estibador_compra";
					$auxiliar = number_format($acumulado, 2, '.', '') ?>
					<tr>

						<td> <?php echo $i; ?> </td>
						<td> <?php echo $row_Listado['fecha_registro']; ?></td>
						<td> <?php echo $row_Listado['tipocomprobantee'] . ' - ' . $row_Listado['numerocomprobantee']; ?> </td>
						<td> Estibador </td>

						<td> <?php echo round($row_Listado['precioestibador_soles'], 2); ?> </td>
						<td> 0 </td>
						<td> <?= $auxiliar ?> </td>


						<td class="text-center">
							<a href="#" data-toggle="modal" data-target="#ver_e">Ver </a>
						</td>
					</tr>
					<?php if ($abonoochoa != null) : ?>
						<?php $arrayabonoproveedor = json_decode($abonoochoa) ?>
						<?php foreach ($arrayabonoproveedor as $abono) : ?>
							<?php
							$acumulado = $acumulado - $abono->montoextra;
							$auxiliar = number_format($acumulado, 2, '.', '');
							$i++;
							?>
							<tr>
								<td><?= $i ?></td>
								<td><?= $abono->fechaxxx ?></td>
								<td>ABONO</td>
								<td><?= $abono->tipopago ?></td>
								<td>0.00</td>
								<td><?= number_format((float) $abono->montoextra, 2, '.', '') ?></td>
								<td><?= $auxiliar ?></td>
								<td></td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
				<?php } ?>
				<?php if ($row_Listado['id_notadebito'] != NULL && $row_Listado['rucnd'] == $colname_Listado) {
					$acumulado += $row_Listado['preciond_soles'];
					$abonoochoa = $row_Listado["abonond"];
					$lastcodigo = $row_Listado["id_notadebito"];
					$lastidname = "id_notadebito";
					$lasttipo = "notadebito_compra";
					$auxiliar = number_format($acumulado, 2, '.', '') ?>
					<tr>

						<td> <?php echo $i; ?> </td>
						<td> <?php echo $row_Listado['fecha_registro']; ?></td>
						<td> <?php echo $row_Listado['tipocomprobantend'] . ' - ' . $row_Listado['numerocomprobantend']; ?> </td>
						<td> NOTA DEBITO </td>

						<td> <?php echo round($row_Listado['preciond_soles'], 2); ?> </td>
						<td> <?= $row_Listado["pagoacumulado"]  ?> </td>
						<td><?= $row_Listado["preciond_soles"] - $row_Listado["pagoacumulado"] ?></td>


						<td class="text-center">
							<a href="#" data-notad="<?= $row_Listado['id_notadebito']; ?>" data-codigonotad="<?= $row_Listado['rucnd']; ?>" onclick="mostrarModalNOTAD(this, <?= $row_Listado['id_notadebito'] ?>)">Ver</a>
						</td>
					</tr>
					<?php if ($abonoochoa != null) : ?>
						<?php $arrayabonoproveedor = json_decode($abonoochoa) ?>
						<?php foreach ($arrayabonoproveedor as $abono) : ?>
							<?php
							$acumulado = $acumulado - $abono->montoextra;
							$auxiliar = number_format($acumulado, 2, '.', '');
							$i++;
							?>
							<tr>
								<td><?= $i ?></td>
								<td><?= $abono->fechaxxx ?></td>
								<td>ABONO</td>
								<td><?= $abono->tipopago ?></td>
								<td>0.00</td>
								<td><?= number_format((float) $abono->montoextra, 2, '.', '') ?></td>
								<td><?= $auxiliar ?></td>
								<td></td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
				<?php } ?>



				<?php if ($row_Listado['id_notacredito'] != NULL  && $row_Listado['rucnotacredito'] == $colname_Listado) {
					$acumulado += $row_Listado['precionc_soles'];
					$abonoochoa = $row_Listado["abononc"];
					$lastcodigo = $row_Listado["id_notacredito"];
					$lastcodigo = $row_Listado["id_notacredito"];
					$lasttipo = "notacredito_compra";
					$auxiliar = number_format($acumulado, 2, '.', '') ?>
					<tr>

						<td> <?php echo $i; ?> </td>
						<td> <?php echo $row_Listado['fecha_registro']; ?></td>
						<td> <?php echo $row_Listado['tipocomprobantenc'] . ' - ' . $row_Listado['numerocomprobantenc']; ?> </td>
						<td> NOTA CREDITO </td>
						<td> <?php echo round($row_Listado['precionc_soles'], 2); ?> </td>
						<td> 0</td>

						<td> <?= $auxiliar ?></td>


						<td class="text-center">
							<a href="#" data-notac="<?= $row_Listado['id_notacredito']; ?>" data-codigonotac="<?= $row_Listado['rucnotacredito']; ?>" onclick="mostrarModalNOTAC(this)">Ver</a>
						</td>
					</tr>
					<?php if ($abonoochoa != null) : ?>
						<?php $arrayabonoproveedor = json_decode($abonoochoa) ?>
						<?php foreach ($arrayabonoproveedor as $abono) : ?>
							<?php
							$acumulado = $acumulado - $abono->montoextra;
							$auxiliar = number_format($acumulado, 2, '.', '');
							$i++;
							?>
							<tr>
								<td><?= $i ?></td>
								<td><?= $abono->fechaxxx ?></td>
								<td>ABONO</td>
								<td><?= $abono->tipopago ?></td>
								<td>0.00</td>
								<td><?= number_format((float) $abono->montoextra, 2, '.', '') ?></td>
								<td><?= $auxiliar ?></td>
								<td></td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
				<?php } ?>

			<?php $i++;
			} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
		</tbody>
	</table>
<?php } // Show if recordset not empty 
?>

<div class="modal fade" id="mpagar" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document" style="width: 900px">
		<div class="modal-content m-auto">
			<form id="formoperacion" action="">
				<div class="modal-header">
					<h2 class="modal-title">PAGAR</h2>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<input type="hidden" id="lastcodigo" value="<?= $lastcodigo ?>">
							<input type="hidden" id="lasttipo" value="<?= $lasttipo ?>">
							<input type="hidden" id="lastidname" value="<?= $lastidname ?>">
							<input type="hidden" id="abonoproveedor" value='<?= $abonoochoa ?>'>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="acumulado" class="control-label">Saldo Pendiente</label>
									<input type="number" id="saldoacumulado" readonly class="form-control" required value="<?= $acumulado ?>" />
								</div>
							</div>

							<div class="col-sm-4" style="margin-top: 15px; margin-bottom: 15px">
								<button class="btn btn-success" id="btnaddpay" type="button" onclick="addPayExtra()">Agregar Pago</button>
							</div>
							<div class="col-sm-12" id="containerpayextra">

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="guardar_button" class="btn btn-success">Guardar</button>
					<button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
				</div>
			</form>
		</div>
	</div>
</div>


<!-- MODAL DE REGISTRO DE COMPRA  -->

<div role="dialog" tabindex="-1" class="modal fade" id="ver_rc" style="max-width:600px;margin-right:auto;margin-left:auto;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<!-- CABECERA -->
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="text-center modal-title">Registro de Compra <span id="ver_rc_codigorc"></span></h4>
			</div>
			<div class="modal-body">
				<!-- CUERPO DEL MENSAJE -->
				<br class="text-center">COMPROBANTE: <span id="ver_rc_tipocomp"> </span>- <span id="ver_rc_numerocomprobante"></span>
				<br align="right">FECHA:<span id="ver_rc_fecha"></span>
				<br align="right">TOTAL:<span id="ver_rc_total"></span>
				<br align="left">RUC: <span id="ver_rc_ruc"></span>
				<br align="left">PROVEEDOR: <span id="ver_rc_proveedor"></span>
				<br align="left">SUCURSAL: <span id="ver_rc_sucursal"></span>
				<br align="left">GENERADA POR: <span id="ver_rc_usuario"></span>
				<div class="table-responsive-sm">
					<table class="table">
						<thead>
							<tr>
								<td>#</td>
								<td>Cant</td>
								<td>Detalle</td>
								<td>Desc x Item</td>
								<td>Precio UND</td>
							</tr>
						</thead>
						<tbody id="ver_rc_body_tabla">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<!-- PIE -->
				<button class="btn btn-default btn btn-primary btn-lg" type="button" data-dismiss="modal">Cerrar
				</button>
			</div>
		</div>
	</div>
</div>

<!-- MODAL DE TRANSPORTE  -->
<div role="dialog" tabindex="-1" class="modal fade" id="ver_trans" style="max-width:600px;margin-right:auto;margin-left:auto;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<!-- CABECERA -->
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="text-center modal-title">TRANSPORTE <span id="ver_trans_id_transporte"></span></h4>
			</div>
			<div class="modal-body">
				<!-- CUERPO DEL MENSAJE -->
				<!--   <br class="text-center">COMPROBANTE: <span id="ver_trans_tipocomp"> </span>- <span id="ver_trans_numerocomprobante"></span>
      -->
				<br align="right">TIPO:<span id="ver_trans_tipotransp"></span>
				<br align="left">RUC: <span id="ver_trans_ruc"></span>
				<br align="left">PROVEEDOR: <span id="ver_trans_razonsocial"></span>
				<br align="left">FECHA: <span id="ver_trans_fecha"></span>
				<br class="text-center">COMPROBANTE: <span id="ver_trans_tipocomp"> </span>- <span id="ver_trans_numerocomprobante"></span>
				<br class="text-center">MONEDA: <span id="ver_trans_moneda"></span>
				<br class="text-center">TOTAL SOLES: <span id="ver_trans_preciotransp_soles"></span>
				<!--  <br align="right">MONEDA:<span id="ver_trans_moneda"></span>
       <br align="right">TOTAL:<span id="ver_trans_ruc"></span>
       <br align="left">SUCURSAL: <span id="ver_trans_ruc"></span>-->
			</div>
			<div class="modal-footer">
				<!-- PIE -->
				<button class="btn btn-default btn btn-primary btn-lg" type="button" data-dismiss="modal">Cerrar
				</button>
			</div>
		</div>
	</div>
</div>






<!-- MODAL DE NOTA DEBITO  -->

<div role="dialog" tabindex="-1" class="modal fade" id="ver_notad" style="margin-right:auto;margin-left:auto;">
	<div class="modal-dialog" role="document" style="min-width:900px">
		<div class="modal-content">
			<div class="modal-header">
				<!-- CABECERA -->
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="text-center modal-title">NOTA DE DEBITO <span id="ver_notad_id_notadebito"></span></h4>
			</div>
			<input type="hidden" id="idnotadebito">
			<input type="hidden" id="jsonpagos">
			<input type="hidden" id="restantex">
			<div class="modal-body">
				<!-- CUERPO DEL MENSAJE -->
				<!--   <br class="text-center">COMPROBANTE: <span id="ver_trans_tipocomp"> </span>- <span id="ver_trans_numerocomprobante"></span>
    -->
				<br align="left">RUC: <span id="ver_notad_ruc"></span>
				<br align="left">PROVEEDOR: <span id="ver_notad_razonsocial"></span>
				<br align="left">FECHA: <span id="ver_notad_fecha"></span>
				<br class="text-center">COMPROBANTE: <span id="ver_notad_tipocomp"> </span>- <span id="ver_notad_numerocomprobante"></span>
				<br class="text-center">MONEDA: <span id="ver_notad_moneda"></span>
				<br class="text-center">TOTAL SOLES: <span id="ver_notad_preciond_soles"></span>

				<div style="margin-top: 15px">
					<button class="btn btn-success" type="button" id="btnaddpay" onclick="addPayExtra()">Agregar Pago</button>
				</div>

				<div class="col-sm-12" style="margin-top: 10px">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td>TIPO PAGO</td>
								<td>MONTO</td>
								<td>DETALLE</td>
							</tr>
						</thead>
						<tbody id="historialbody">
						</tbody>
					</table>
				</div>
				<div class="col-sm-12" id="containerpayextrax">

				</div>
			</div>
			<div class="modal-footer">

				<button class="btn btn-default btn btn-primary btn-lg" type="button" data-dismiss="modal">Cerrar
				</button>
			</div>
		</div>
	</div>
</div>


<!-- MODAL DE NOTA CREDITO  -->

<div role="dialog" tabindex="-1" class="modal fade" id="ver_notac" style="max-width:600px;margin-right:auto;margin-left:auto;">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<!-- CABECERA -->
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="text-center modal-title">NOTA DE CREDITO <span id="ver_notac_id_notacrebito"></span></h4>
			</div>
			<div class="modal-body">
				<!-- CUERPO DEL MENSAJE -->
				<!--   <br class="text-center">COMPROBANTE: <span id="ver_trans_tipocomp"> </span>- <span id="ver_trans_numerocomprobante"></span>
    -->
				<br align="left">RUC: <span id="ver_notac_ruc"></span>
				<br align="left">PROVEEDOR: <span id="ver_notac_razonsocial"></span>
				<br align="left">FECHA: <span id="ver_notac_fecha"></span>
				<br class="text-center">COMPROBANTE: <span id="ver_notac_tipocomp"> </span>- <span id="ver_notac_numerocomprobante"></span>
				<br class="text-center">MONEDA: <span id="ver_notac_moneda"></span>
				<br class="text-center">TOTAL SOLES: <span id="ver_notac_precionc_soles"></span>

			</div>
			<div class="modal-footer">
				<!-- PIE -->
				<button class="btn btn-default btn btn-primary btn-lg" type="button" data-dismiss="modal">Cerrar
				</button>
			</div>
		</div>
	</div>


	<?php
	//___________________________________________________________________________________________________________________
	include("Fragmentos/footer.php");
	include("Fragmentos/pie.php");

	mysql_free_result($Listado);

	mysql_free_result($Proveedor);
	?>
	<script type="text/javascript">
		function mostrarModalRC(etiqueta) {

			$("#ver_rc").modal();
			ver_rc_ruc.textContent = etiqueta.dataset.codigoproveedor;
			ver_rc_codigorc.textContent = etiqueta.dataset.rc;
			ver_rc_body_tabla.innerHTML = ""
			fetch("traerregistrocompra.php?codigorc=" + etiqueta.dataset.rc)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))

				.then(res => {
					ver_rc_numerocomprobante.textContent = res.header.numerocomprobante
					ver_rc_fecha.textContent = res.header.fecha
					ver_rc_total.textContent = res.header.total
					ver_rc_proveedor.textContent = res.header.razonsocial
					ver_rc_sucursal.textContent = res.header.nombre_sucursal
					ver_rc_usuario.textContent = res.header.usuario
					ver_rc_tipocomp.textContent = res.header.tipo_comprobante


					res.detalle.forEach(row => {
						ver_rc_body_tabla.innerHTML += `
						<tr>
							<td></td>
							<td>${row.cantidad}</td>
							<td>${row.nombre_producto}</td>
							<td>${row.descxitem}</td>
							<td>${row.vcu}</td>
						</tr>
						`;
					});
				});
		}

		function removecontainerpay(e) {
			e.closest(".containerx").remove()
		}

		function changetypepago(e) {
			guardar_button.style.display = ""
			e.closest(".containerx").querySelectorAll(".inputxxx").forEach(ix => ix.style.display = "none");
			e.closest(".containerx").querySelectorAll("." + e.value).forEach(ix => ix.style.display = "");
		}

		function addPayExtra() {
			const newxx = document.createElement("div");
			newxx.className = "col-md-12 containerx";
			newxx.style = "border: 1px solid #cdcdcd; padding: 5px; margin-bottom: 5px";

			newxx.innerHTML += `
				<div class="text-right">
				<button type="button" class="btn btn-danger" onclick="removecontainerpay(this)">Cerrar</button>
				</div>

				<div class="col-md-3">
				<div class="form-group">
				<label class="control-label">Tipo Pago</label>
				<select onchange="changetypepago(this)" class="form-control tipopago">
				<option value="">[Seleccione]</option>
				<option value="depositobancario">Deposito Bancario</option>
				<option value="tarjetadebito">Tarjeta Debito</option>
				<option value="tarjetacredito">Tarjeta Credito</option>
				<option value="cheque">Cheque</option>
				<option value="efectivo">Efectivo</option>
				</select>
				</div>
				</div>

				<div style="display: none" class="col-md-3 inputxxx depositobancario cheque tarjetacredito tarjetadebito">
				<div class="form-group">
				<label class="control-label">Banco</label>
				<select class="form-control bancoextra">
				<option value="BANCO AZTECA">BANCO AZTECA</option>
				<option value="BANCO BCP">BANCO BCP</option>
				<option value="BANCO CENCOSUD">BANCO CENCOSUD</option>
				<option value="BANCO DE LA NACION">BANCO DE LA NACION</option>
				<option value="BANCO FALABELLA">BANCO FALABELLA</option>
				<option value="BANCO GNB PERÚ">BANCO GNB PERÚ</option>
				<option value="BANCO MI BANCO">BANCO MI BANCO</option>
				<option value="BANCO PICHINCHA">BANCO PICHINCHA</option>
				<option value="BANCO RIPLEY">BANCO RIPLEY</option>
				<option value="BANCO SANTANDER PERU">BANCO SANTANDER PERU</option>
				<option value="BANCO SCOTIABANK">BANCO SCOTIABANK</option>
				<option value="CMAC AREQUIPA">CMAC AREQUIPA</option>
				<option value="CMAC CUSCO S A">CMAC CUSCO S A</option>
				<option value="CMAC DEL SANTA">CMAC DEL SANTA</option>
				<option value="CMAC HUANCAYO">CMAC HUANCAYO</option>
				<option value="CMAC ICA">CMAC ICA</option>
				<option value="CMAC LIMA">CMAC LIMA</option>
				<option value="CMAC MAYNA">CMAC MAYNA</option>
				<option value="CMAC PAITA">CMAC PAITA</option>
				<option value="CMAC SULLANA">CMAC SULLANA</option>
				<option value="CMAC TRUJILLO">CMAC TRUJILLO</option>
				</select>
				</div>
				</div>

				<div style="display: none" class="col-md-3 inputxxx depositobancario cheque tarjetacredito tarjetadebito efectivo porcobrar">
				<div class="form-group">
				<label class="control-label">Monto</label>
				<input type="number" step="any" class="form-control montoextra">
				</div>
				</div>

				<div style="display: none" class="col-md-3 inputxxx cheque tarjetacredito tarjetadebito">
				<div class="form-group">
				<label class="control-label">Numero</label>
				<input type="number" class="form-control numero">
				</div>
				</div>

				<div style="display: none" class="col-md-3 inputxxx depositobancario cheque">
				<div class="form-group">
				<label class="control-label">Cuenta Corriente</label>
				<input type="text" class="form-control cuentacorriente">
				</div>
				</div>


				<div style="display: none" class="col-md-3 inputxxx depositobancario">
				<div class="form-group">
				<label class="control-label">Numero Operacion</label>
				<input type="text"  class="form-control numerooperacion">
				</div>
				</div>

				<div style="display: none" class="col-md-3 inputxxx depositobancario">
				<div class="form-group">
				<label class="control-label">Fecha</label>
				<input type="text" class="form-control form-control-inline input-medium date-picker fechaextra" data-date-format="yyyy-mm-dd" readonly autocomplete="off">
				</div>
				</div>

				<div style="display: none" class="col-md-3 inputxxx depositobancario">
				<div class="form-group">
				<label class="control-label">Cta Abonado</label>
				<input type="text" class="form-control cuentaabonado">
				</div>
				</div>`;
			containerpayextra.appendChild(newxx);

			$('.date-picker').datepicker({
				rtl: App.isRTL(),
				autoclose: true
			});
		}

		function mostrarModalTRANS(etiqueta) {

			$("#ver_trans").modal();
			ver_trans_ruc.textContent = etiqueta.dataset.ructransporte;
			ver_trans_tipotransp.textContent = etiqueta.dataset.tipo_transporte;
			ver_trans_razonsocial.textContent = etiqueta.dataset.razonsocial;
			ver_trans_id_transporte.textContent = etiqueta.dataset.trans;
			ver_trans_tipocomp.textContent = etiqueta.dataset.tipocomprobante;
			ver_trans_numerocomprobante.textContent = etiqueta.dataset.numerocomprobante;
			ver_trans_moneda.textContent = etiqueta.dataset.moneda;
			ver_trans_preciotransp_soles.textContent = etiqueta.dataset.preciotransp_soles

			//ver_trans_body_tabla.innerHTML = ""
			fetch("traerregistrocompra.php?codigotrans=" + etiqueta.dataset.trans)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))

				.then(res => {
					ver_trans_ruc.textContent = res.header.ructransporte
					ver_trans_tipotransp.textContent = res.header.tipo_transporte
					ver_trans_tipocomp.textContent = res.header.tipocomprobante
					ver_trans_numerocomprobante.textContent = res.header.numerocomprobante
					ver_trans_razonsocial.textContent = res.header.razonsocial
					ver_trans_moneda.textContent = res.header.moneda
					ver_trans_preciotransp_soles.textContent = res.header.preciotransp_soles
					//ver_trans_usuario.textContent = res.header.usuario
					//ver_trans_tipocomp.textContent = res.header.tipo_comprobante
					console.log(res)
					//console.log(res.numerocomprobante)
				});
			//console.log(etiqueta.dataset.codigoproveedor)

		}

		function mostrarModalNOTAD(etiqueta, idx) {
			idnotadebito.value = idx
			$("#ver_notad").modal();
			//ver_notad_ruc.textContent = etiqueta.dataset.rucnd;
			ver_notad_tipocomp.textContent = etiqueta.dataset.tipocomprobante;
			ver_notad_numerocomprobante.textContent = etiqueta.dataset.numerocomprobante;
			ver_notad_razonsocial.textContent = etiqueta.dataset.razonsocial;
			ver_notad_id_notadebito.textContent = etiqueta.dataset.notad;
			ver_notad_moneda.textContent = etiqueta.dataset.moneda;
			ver_notad_preciond_soles.textContent = etiqueta.dataset.preciond_soles

			//ver_trans_body_tabla.innerHTML = ""
			fetch("traerregistrocompra.php?codigonotad=" + etiqueta.dataset.notad)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))

				.then(res => {
					ver_notad_ruc.textContent = res.header.rucnd
					ver_notad_tipocomp.textContent = res.header.tipocomprobante
					ver_notad_numerocomprobante.textContent = res.header.numerocomprobante
					ver_notad_id_notadebito.textContent = res.header.id_notadebito
					ver_notad_razonsocial.textContent = res.header.razonsocial
					ver_notad_moneda.textContent = res.header.moneda
					ver_notad_preciond_soles.textContent = res.header.preciond_soles




				});
		}
		const guardar = e => {
			e.preventDefault();
			let totalpagando = 0;
			let error = "";
			let porpagar = 1;
			let restante = 0;
			let errorrr = "";
			let pagoxxx = {};
			let acumuladoabono = 0;
			const arraypagoxxx = JSON.parse(abonoproveedor.value ? abonoproveedor.value : "[]")
			getSelectorAll(".containerx").forEach(ix => {
				const bancoextra = ix.querySelector(".bancoextra").value;
				const montoextra = ix.querySelector(".montoextra").value ? parseFloat(ix.querySelector(".montoextra").value) : 0;
				const numero = ix.querySelector(".numero").value;
				const cuentacorriente = ix.querySelector(".cuentacorriente").value;
				const numerooperacion = ix.querySelector(".numerooperacion").value;
				const fechaextra = ix.querySelector(".fechaextra").value;
				const cuentaabonado = ix.querySelector(".cuentaabonado").value;
				const tipopago = ix.querySelector(".tipopago").value;

				acumuladoabono += montoextra;

				arraypagoxxx.push({
					bancoextra,
					montoextra,
					numero,
					cuentacorriente,
					numerooperacion,
					fechaextra,
					cuentaabonado,
					tipopago,
					fechaxxx: new Date(new Date().setHours(10)).toISOString().substring(0, 10)
				})
				totalpagando += parseFloat(montoextra);
				if (tipopago == "depositobancario" && (!bancoextra || !montoextra || !cuentacorriente || !numerooperacion || !fechaextra || !cuentaabonado)) {
					errorrr = "Llena todos los datos de deposito bancario";
					return;
				} else if (tipopago == "cheque" && (!bancoextra || !montoextra || !numero || !cuentacorriente)) {
					errorrr = "Llena todos los datos de cheque";
					return;
				} else if ((tipopago == "tarjetacredito" || tipopago == "tarjetadebito") && (!bancoextra || !montoextra || !numero)) {
					errorrr = "Llena todos los datos de " + tipopago;
					return;
				} else if (tipopago == "efectivo" && !montoextra) {
					errorrr = "Debe ingresa el monto";
					return;
				}

			});
			if (errorrr) {
				alert(errorrr);
				return;
			}
			if (totalpagando > parseFloat(saldoacumulado.value)) {
				alert("El monto a pagar excede");
				return
			} else if (totalpagando == parseFloat(saldoacumulado.value)) {
				porpagar = 0;
			} else {
				restante = parseFloat(saldoacumulado.value) - totalpagando;
			}
			const data = {
				header: "",
				detalle: []
			}
			const jssson = JSON.stringify(arraypagoxxx);

			const query = `
				update ${lasttipo.value} 
					set 
						abonoochoa = '${jssson}',
						montoochoa =  IFNULL(montoochoa, 0) + ${acumuladoabono}
				where ${lastidname.value} = ${lastcodigo.value}`
			data.detalle.push(query)
			console.log(query)
			var formData = new FormData();
			formData.append("json", JSON.stringify(data))

			fetch(`setVenta.php`, {
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

		function mostrarModalNOTAC(etiqueta) {

			$("#ver_notac").modal();
			// ver_notac_ruc.textContent = etiqueta.dataset.rucnotacredito;
			ver_notac_tipocomp.textContent = etiqueta.dataset.tipocomprobante;
			ver_notac_numerocomprobante.textContent = etiqueta.dataset.numerocomprobante;
			ver_notac_razonsocial.textContent = etiqueta.dataset.razonsocial;
			ver_notac_id_notacrebito.textContent = etiqueta.dataset.notac;
			ver_notac_moneda.textContent = etiqueta.dataset.moneda;
			ver_notac_precionc_soles.textContent = etiqueta.dataset.precionc_soles

			//ver_trans_body_tabla.innerHTML = ""
			fetch("traerregistrocompra.php?codigonotac=" + etiqueta.dataset.notac)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))

				.then(res => {
					ver_notac_ruc.textContent = res.header.rucnotacredito
					ver_notac_tipocomp.textContent = res.header.tipocomprobante
					ver_notac_numerocomprobante.textContent = res.header.numerocomprobante
					ver_notac_id_notacrebito.textContent = res.header.id_notacredito
					ver_notac_razonsocial.textContent = res.header.razonsocial
					ver_notac_moneda.textContent = res.header.moneda
					ver_notac_precionc_soles.textContent = res.header.precionc_soles
				});
		}
		formoperacion.addEventListener("submit", guardar)
	</script>