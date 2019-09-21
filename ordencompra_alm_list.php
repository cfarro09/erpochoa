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
	$updateSQL = sprintf(
		"UPDATE producto SET estado=%s WHERE codigoprod=%s",
		GetSQLValueString($_POST['estado'], "text"),
		GetSQLValueString($_POST['codigoprod'], "int")
	);

	mysql_select_db($database_Ventas, $Ventas);
	$Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

	$updateGoTo = "ordencompra_alm_list.php";
	if (isset($_SERVER['QUERY_STRING'])) {
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $updateGoTo));
}
mysql_select_db($database_Ventas, $Ventas);

 //Enumerar filas de data tablas
$i = 1;


//para asignar precio y cantidad
$query_Listado1 = "SELECT a.codigoprod, a.nombre_producto from producto a INNER JOIN detalle_compras b ON a.codigoprod = b.codigoprod group by a.codigoprod";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);
 //Enumerar filas de data tablas

$querySucursales = "select * from sucursal where estado = 1" ;
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);


//Titulo e icono de la pagina
$Icono="fa fa-building-o";
$Color="font-blue";
$Titulo="Almacen";
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


$codsucursalx =  $_SESSION['cod_sucursal'];
$query_Listado = "SELECT s.cod_sucursal, s.nombre_sucursal, c.codigoordcomp, g.estado as estadoGuia ,c.codigo, codigoref1, montofact as valor_compra, razonsocial, p.codigoproveedor as codigoproveedor, fecha_emision FROM ordencompra c inner join proveedor p on c.codigoproveedor=p.codigoproveedor inner JOIN sucursal s on s.cod_sucursal=c.sucursal left join ordencompra_guia g on g.codigoordcomp =c.codigoordcomp where c.estado=2 and c.sucursal = $codsucursalx group by codigo order by fecha_emision desc";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
//________________________________________________________________________________________________________________
?>        

<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty?>
	<div class="alert alert-danger">
		<strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
	</div>
<?php } // Show if recordset empty?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty?>
	<table class="table table-striped table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th  > N&deg; </th>
				<th  > DOC REF1</th>
				<th  > M. TOTAL</th>
				<th  class="none"> COMPRA </th>
				<th  class="none">SUBTOTAL</th>
				<th  class="none"> IVA </th>
				<th  > PROVEEDOR </th>
				<th  > FECHA </th>
				<th  > VER </th>
				<th  > ESTADO </th>

				<th  > IMPRIMIR </th>
			</tr>
		</thead>
		<tbody>
			<?php do { ?>
				<?php

				$color = "#bde8dc";
				$estado = "";
				if ($row_Listado['estadoGuia'] == '1') {
					$color = "#fdf701";
					$estado = "PENDIENTE";
				} elseif ($row_Listado['estadoGuia'] == '2') {
					$color = "#01fd0b";
					$estado = "FINALIZADO PENDIENTE";
				} elseif ($row_Listado['estadoGuia'] == '3') {
					$color = "#01fd0b";
					$estado = "ACEPTADO";
				}
				else
				{
					$color = "#fdf701";
					$estado = "POR RECIBIR";
				}
				?>
				<tr style="background-color: <?= $color ?>">
					<td> <?php echo $i; ?> </td>
					<td><a href="#" data-estado="<?= $row_Listado['estado'] ?>" data-codigo="<?= $row_Listado['codigo'] ?>" class="verOrden"> <?php echo $row_Listado['codigoref1']; ?> </a></td>
					<td> <?php
					$preciocompra=$row_Listado['valor_compra'];
					echo number_format($row_Listado['valor_compra'], 2); ?></td>
					<td><?php  echo "&#36; ".number_format($row_Listado['valor_compra'], 2); ?> </td>
					<td> <?php echo "&#36; ".number_format($row_Listado['valor_compra']/1.18, 2); ?></td>
					<td> <?php echo "&#36; ".number_format(($row_Listado['valor_compra']-number_format($row_Listado['valor_compra']/1.18, 2)), 2); ?></td>
					<td> <?php echo $row_Listado['razonsocial']; ?></td>
					<td> <?php echo $row_Listado['fecha_emision']; ?></td>
					<td><a href="#" data-estado="<?= $row_Listado['estado'] ?>" data-codigo="<?= $row_Listado['codigo'] ?>" class="verOrden">Ver</a></td>
					<td><?= $estado ?> </td>
					<td> 
						<a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" href="Imprimir/orden_compra.php?codigocompras=<?php echo $row_Listado['codigo']; ?>&codigo=<?php echo $row_Listado['codigoref1']; ?>" target="new"><i class="glyphicon glyphicon-credit-card" ></i></a>
					</td>
				</td>
			</tr>
			<?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
		</tbody>
	</table>
	<div class="modal fade" id="mOrdenCompra" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document" >
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h5 class="modal-title" id="moperation-title"></h5>
				</div>
				<div class="modal-body">
					<form id="saveOrdenCompra-alm-list">
						<input type="hidden" id="codigoOrdenCompra">
						<input type="hidden" id="codigoordcomp">
						<input type="hidden" id="codsucursaluuu">
						<input type="hidden" id="codigoguia" value="">
						<div class="container-fluid"><p align="right">
							GENERADA POR:  <span id="mgeneradapor"></span> <br>
							FECHA DE EMISION: <span id="mfechaemision"></span> <br>
							SUCURSAL: <span id="msucursal"></span> </p>
							RUC: <span id="mruc"></span><br>
							PROVEEDOR: <span id="mproveedor"></span>  <BR> 
							VALOR TOTAL: <span id="mvalortotal"></span><BR> 
							CODIGO DE REF 1: <span id="mcodref1"></span> <br>
							CODIGO REF2: <span id="mcodref2"></span> <br>

							<div class="row" style="margin-top:20px">
								<div class="col-xs-12 col-md-12">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label for="field-1" class="control-label">Tipo Doc</label>
												<select class="form-control" id="tipodocalmacen">
													<option value="guia">Guia</option>
													<option value="factura">Factura</option>
													<option value="boleta">Boleta</option>
													<option value="otros">Otros</option>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="field-1" class="control-label">Numero Guia</label>
												<input type="text" required class="form-control" name="numero-guia" id="numero-guia">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="field-1" class="control-label">Observacion</label>
												<input type="text" class="form-control" name="observacion" id="observacion">
											</div>
										</div>
									</div>
									<table class="table">
										<thead>
											<th>Nº</th>
											<th>Cant Sol</th>
											<th>Unidad Medida</th>
											<th>Producto</th>
											<th id="th-saldo" style="display: none">Saldo</th>
											<th>Cantidad Recibida</th>
										</thead>
										<tbody id="detalleTableOrden-alm-list">
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<button type="button" id="btn-finalice-alm-list" style="display: none" class="btn btn-primary">Finalizar</button>
						<button type="submit" id="btn-guardarGuia-alm-list" class="btn btn-success">Guardar</button>
						<button type="button" data-dismiss="modal" class="btn btn-danger">Cerrar</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="mkardex" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document" >
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h5 class="modal-title" id="moperation-title">Almacen Kardex</h5>
				</div>
				<div class="modal-body">
					<form id="form-setKardex" action="kardex_almacen.php" method="GET">
						<div class="container-fluid">
							<div class="row" style="margin-top:20px">
								<div class="col-xs-12 col-md-12">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="field-1" class="control-label">Sucursales</label>
												<select name="codigosuc" id="codigosuc" class="sucursalXX form-control select2 tooltips" id="single" data-placement="top" >
													<?php
													do {  
														?>
														<option value="<?php echo $row_sucursales['cod_sucursal']?>"><?php echo $row_sucursales['nombre_sucursal']?></option>
														<?php
													} while ($row_sucursales = mysql_fetch_assoc($sucursales));
													$rows = mysql_num_rows($sucursales);
													if($rows > 0) {
														mysql_data_seek($sucursales, 0);
														$row_sucursales = mysql_fetch_assoc($sucursales);
													}
													?>
													<option value="9999">OTROS</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="field-1" class="control-label">Fecha Inicio</label>
												<input type="text" name ="fecha_inicio" id ="fecha_inicio" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top"  required/>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="field-1" class="control-label">Fecha termino</label>
												<input type="text" name ="fecha_termino" id ="fecha_termino" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top"  required/>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<button type="submit" class="btn btn-success">Imprimir</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php } // Show if recordset not empty?>
<script type="text/javascript">

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
	});var i=0;
	document.querySelectorAll(".verOrden").forEach(item => {
		
		document.querySelector("#saveOrdenCompra-alm-list").reset();
		item.addEventListener("click", (e) => {
			i=0;
			document.querySelector("#codigoOrdenCompra").value = e.target.dataset.codigo
			fetch(`getDetalleOcGuia.php?codigo=${e.target.dataset.codigo}`)
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				document.querySelector("#codigoordcomp").value = res.header.codigoordcomp
				$("#mproveedor").text(res.header.razonsocial)
				$("#mfechaemision").text(res.header.fecha_emision)
				$("#mvalortotal").text(res.header.montofact)
				$("#mcodref1").text(res.header.codigoref1)
				
				
				$("#numero-guia").val("")
				$("#observacion").val("")

				
				$("#codsucursaluuu").val(res.header.sucursal)
				$("#mcodref2").text(res.header.codigoref2 ? res.header.codigoref2 : "No tiene")
				$("#mgeneradapor").text(res.header.usuario)
				$("#mruc").text(res.header.ruc)
				$("#msucursal").text(res.header.nombre_sucursal + " " + (res.header.direccionOrden ? " :"+ res.header.direccionOrden : ""))

				$("#codigoguia").val(res.header.codigoguia)

				document.querySelector("#btn-guardarGuia-alm-list").style.display = ""
				let finaliced = false;
				if(res.header.numeroguia){
					document.querySelector("#th-saldo").style.display = ""
					document.querySelector("#btn-finalice-alm-list").style.display = ""
					if(res.header.estadoguia == "2" || res.header.estadoguia == "3"){
						document.querySelector("#btn-finalice-alm-list").style.display = "none"
						document.querySelector("#btn-guardarGuia-alm-list").style.display = "none"
						finaliced = true;
					}
				}else{
					document.querySelector("#btn-finalice-alm-list").style.display = ""
					document.querySelector("#btn-finalice-alm-list").style.display = ""
					document.querySelector("#th-saldo").style.display = "none"
				}
				document.querySelector("#detalleTableOrden-alm-list").innerHTML = ""
				res.detalle.forEach(r => {
					i++
					let tdExtra = "";
					let validateCant = 0;
					if(res.header.numeroguia){
						tdExtra = `<td class="cant-extra">${parseInt(r.cantidad) - parseInt(r.cant_recibida)}</td>`
						validateCant = parseInt(r.cantidad) - parseInt(r.cant_recibida)
					}else{
						validateCant = r.cantidad
					}
					let input = "";
					
					input = `<input required type="number" oninput="validateCantidad(this)" value="	" class="form-control cant-arrived" autocomplete="off"  data-cantidad="${validateCant}">`
					if(finaliced){
						input = ""
					}
					$("#detalleTableOrden-alm-list").append(`
						<tr>
						<td class="codigo" data-codigo_guiaoc="${r.detalle_cod_oc_guia ? r.detalle_cod_oc_guia : ""}" data-codigo="${r.codigo}">${i}</td>
						<td  class="cant_recibida" data-cant_recibida="${r.cantidad}">${r.cantidad}</td>
						<td  >${r.unidad_medida}</td>
						<td class="codigoprod" data-codigoprod="${r.codigoprod}">${r.nombre_producto}</td>
						${tdExtra}
						<td style="width: 30px">${input}</td>
						</tr>`)
				});
			});
			$("#mOrdenCompra").modal();
		})
	});
	function validateCantidad(e){
		if(e.value < 0){
			alert("no debe ingresar numeros negativos")
			e.value = ""
		}else{
			if(parseInt(e.dataset.cantidad) < parseInt(e.value)){
				e.value = ""
			}
		}
	}
	document.querySelector("#btn-finalice-alm-list").addEventListener("click", (e) => {
		const data = {
			header: {
				codigoordcomp: $("#codigoordcomp").val(),
				numeroguia: $("#numero-guia").val(),
				codigoacceso: <?= $_SESSION['kt_login_id'] ?>,
				estado: 2,
				observacion: $("#observacion").val(),
				codigoguia: $("#codigoguia").val(),
				codsucursal: parseInt($("#codsucursaluuu").val())
			},
			detalle: []
		}
		let estado  = 2;
		if(document.querySelectorAll("#detalleTableOrden-alm-list tr")){
			document.querySelectorAll("#detalleTableOrden-alm-list tr").forEach(tr => {
				const cant_recibidda = parseInt(tr.querySelector(".cant-arrived").value);
				const cant_solicitada = parseInt(tr.querySelector(".cant_recibida").dataset.cant_recibida)
				
				let  aux = 0;
				if(tr.querySelector(".cant-extra")){
					aux = tr.querySelector(".cant-extra").textContent ? parseInt(tr.querySelector(".cant_recibida").textContent) - parseInt(tr.querySelector(".cant-extra").textContent): 0;

				}
				data.detalle.push({
					codigo: tr.querySelector(".codigo").dataset.codigo,
					codigoprod: tr.querySelector(".codigoprod").dataset.codigoprod,
					cantidad: tr.querySelector(".cant_recibida").dataset.cant_recibida,
					cantidad_recibida: tr.querySelector(".cant-arrived").value ? parseInt(tr.querySelector(".cant-arrived").value) + aux : aux,
					saldo: parseInt(tr.querySelector(".cant_recibida").dataset.cant_recibida) - parseInt(tr.querySelector(".cant-arrived").value) + aux,
					codigo_guiaoc: tr.querySelector(".codigo").dataset.codigo_guiaoc
				})
			})
		}
		var formData = new FormData();
		formData.append("json", JSON.stringify(data))

		fetch(`setOrdenCompra.php`,{method: 'POST', body: formData})
		.then(res => res.json())
		.catch(error => console.error("error: ", error))
		.then(res => {
			$("#mOrdenCompra").modal("hide");
			if(res.success){
				alert("registro completo!")
			}
			
		});

	})
	document.querySelector("#saveOrdenCompra-alm-list").addEventListener("submit", (e) => {
		e.preventDefault();
		const data = {
			header: {
				tipodocalmacen: $("#tipodocalmacen").val(),
				codigoordcomp: $("#codigoordcomp").val(),
				numeroguia: $("#numero-guia").val(),
				codigoacceso: <?= $_SESSION['kt_login_id'] ?>,
				estado: 3,
				observacion: $("#observacion").val(),
				codigoguia: $("#codigoguia").val(),
				codsucursal: parseInt($("#codsucursaluuu").val())
			},
			detalle: []
		}
		let estado  = 3;
		if(document.querySelectorAll("#detalleTableOrden-alm-list tr")){
			document.querySelectorAll("#detalleTableOrden-alm-list tr").forEach(tr => {
				const cant_recibidda = parseInt(tr.querySelector(".cant-arrived").value);
				const cant_solicitada = parseInt(tr.querySelector(".cant_recibida").dataset.cant_recibida)
				
				let  aux = 0;
				if(tr.querySelector(".cant-extra")){
					aux = tr.querySelector(".cant-extra").textContent ? parseInt(tr.querySelector(".cant_recibida").textContent) - parseInt(tr.querySelector(".cant-extra").textContent): 0;

				}
				if(cant_solicitada != (cant_recibidda + aux)){
					estado  = 1;
				}
				data.detalle.push({
					codigo: tr.querySelector(".codigo").dataset.codigo,
					codigoprod: tr.querySelector(".codigoprod").dataset.codigoprod,
					cantidad: tr.querySelector(".cant_recibida").dataset.cant_recibida,
					cantidad_recibida: parseInt(tr.querySelector(".cant-arrived").value) + aux,
					cantidad_kardex: parseInt(tr.querySelector(".cant-arrived").value),
					codigo_guiaoc: tr.querySelector(".codigo").dataset.codigo_guiaoc,
					saldo: parseInt(tr.querySelector(".cant_recibida").dataset.cant_recibida) - parseInt(tr.querySelector(".cant-arrived").value) + aux
				})
			})
		}
		data.header.estado = estado
		var formData = new FormData();
		formData.append("json", JSON.stringify(data))

		fetch(`setOrdenCompra.php`,{method: 'POST', body: formData})
		.then(res => res.json())
		.catch(error => console.error("error: ", error))
		.then(res => {
			$("#mOrdenCompra").modal("hide");
			if(res.success){
				alert("registro completo!")
			}
			
		});
	})
	
</script>

<?php

//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);
?>