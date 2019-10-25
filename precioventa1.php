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
mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "select c.codigorc, 0 as totalv, c.tipomoneda, c.tipo_comprobante, p.razonsocial, c.fecha,c.numerocomprobante,a.usuario, c.subtotal, c.total, c.estadofact, s.nombre_sucursal  from registro_compras c left join sucursal s on s.cod_sucursal = c.codigosuc left join proveedor p on p.codigoproveedor = c.codigoproveedor inner join acceso a on a.codacceso=c.codacceso";

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
?>

<h2>PRECIO VENTA</h2>
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
				<th>N° COMPR</th>
				<th>TIPO COMPR </th>
				<th style="display: none">TIPO COMPR </th>
				<th style="display: none">TIPO COMPR </th>
				<th>PROVEEDOR</th>
				<th>T COMPRA</th>
				<th>FECHA</th>
				<th>SUCURSAL</th>
				<th>Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1; do { ?>
				<?php //var_dump($row_Listado); die; ?>
				<tr>
					<td><?= $i ?></td>
					<td class="numerocomprobante"><?= $row_Listado['numerocomprobante'] ?></td>
					<td class="tipo_comprobante"><?= $row_Listado['tipo_comprobante'] ?></td>
					<td style="display: none" class="usuario"><?= $row_Listado['usuario'] ?></td>
					<td style="display: none" class="codigorc"><?= $row_Listado['codigorc'] ?></td>
					<td class="razonsocial"><?= $row_Listado['razonsocial'] ?></td>
					<td class="total"><?= $row_Listado['total'] ?></td>
					<td class="fecha"><?= $row_Listado['fecha'] ?></td>
					<td class="nombre_sucursal"><?= $row_Listado['nombre_sucursal'] ?></td>
					<td><a href="#" onclick="managecompra(this)" data-totalv="<?= $row_Listado['totalv'] ?>" data-set="<?= $row_Listado['totalv'] == 0 ? "asignar" : "ver" ?>" ><?= $row_Listado['totalv'] == 0 ? "Asignar" : "ver" ?></a></td>
				</tr>
				<?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>

			</tbody>
		</table>
		<div class="modal fade" id="mSetPrecioVenta" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog" role="document" style="width: 1200px">
				<div class="modal-content m-auto">
					<div class="modal-header">
						<h2 class="modal-title" id="moperation-title">Asignar precio venta</h2>
					</div>
					<div class="modal-body">
						<form id="saveOrdenCompra">
							<input type="hidden" id="codigoOrdenCompra">
							<input type="hidden" id="codigoordcomp">
							<input type="hidden" id="codigoguia" value="">
							<div class="container-fluid">

								N° COMPROBANTE: <span id="mnumerocomprobante"></span> <BR>
								TIPO COMPROBANTE: <span id="mtipo_comprobante"></span> <BR>
								PROVEEDOR: <span id="mrazonsocial"></span> <BR>
								TOTAL COMPRA: <span id="mtotal"></span> <BR>
								SUCURSAL: <span id="mnombre_sucursal"></span> <BR>
								FECHA DE EMISION: : <span id="mfecha"></span> <br>
								GENERADA POR: : <span id="musuario"></span> <br><br>


								<input type="hidden" id="codigorc" name="">
								<div class="row" style="margin-top:20px">
									<div class="col-xs-12 col-md-12">

										<table class="table">
											<thead>
												<th>Nº</th>
												<th>Cant</th>
												<th>Producto</th>
												<th>Marca</th>
												<th>Precio UND</th>
												<th>P. UND + C</th>
												<th class="text-center">% V 1</th>
												<th class="text-center">P V 1</th>
												<th class="text-center">% V 2</th>
												<th class="text-center">P V 2</th>
												<th class="text-center">% V 3</th>
												<th class="text-center">P V 3</th>
											</thead>
											<tbody id="detalleComprax">
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<button type="button" id="btn-finalice" style="display: none"
							class="btn btn-primary">Finalizar</button>
							<button type="submit" id="btn_save_precioventa1" class="btn btn-success">Guardar</button>
							<button type="button" data-dismiss="modal" class="modal_close btn btn-danger">Cerrar</button>
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

		getSelectorAll(".setStatus").forEach(item => {
			item.addEventListener("click", (e) => {
				// getSelector("#check_logistica_edificaciones").checked
				fetch(`editarEstadoOrdenCompra.php?codigo=${getSelector("#codigoOrdenCompra").value}&estado=${e.target.dataset.estado}`)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					alert("Se ace´tó la orden de compra!")
					$("#mOrdenCompra").modal("hide");
				});
			})
		});
		function managecompra(e){
			$('#mnumerocomprobante').text(e.parentElement.parentElement.querySelector(".numerocomprobante").textContent)
			$('#mtipo_comprobante').text(e.parentElement.parentElement.querySelector(".tipo_comprobante").textContent)
			$('#mrazonsocial').text(e.parentElement.parentElement.querySelector(".razonsocial").textContent)
			$('#mtotal').text(e.parentElement.parentElement.querySelector(".total").textContent)
			$('#mnombre_sucursal').text(e.parentElement.parentElement.querySelector(".nombre_sucursal").textContent)
			$('#mfecha').text(e.parentElement.parentElement.querySelector(".fecha").textContent)
			$('#musuario').text(e.parentElement.parentElement.querySelector(".usuario").textContent)
			const codigorc =  parseInt(e.parentElement.parentElement.querySelector(".codigorc").textContent)
			const set = e.dataset.set != "asignar" ? "readonly":"";
			$("#codigorc").val(codigorc);
			getSelector("#detalleComprax").innerHTML = ""

			let i = 1;
			fetch(`getDetalleCompra.php?codigorc=${codigorc}`)
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				res.forEach(ix => {
					const pc = (parseFloat(ix.vcf)/parseInt(ix.cantidad)).toFixed(4);

					getSelector("#detalleComprax").innerHTML += `
					<tr class="rowto" data-codigodetalleproducto="${ix.codigodetalleproducto}" data-codigoprod="${ix.codigoprod}">
						<td>${i}</td>
						<td class="cantidad">${ix.cantidad}</td>
						<td>${ix.nombre_producto}</td>
						<td>${ix.nombre}</td>
						<td><input required class="form-control preciounidad" value="${pc}" readonly></td>
						<td><input required class="form-control preciounidadmas" value="${ix.totalunidad}" readonly></td>
						<td><input required data-cantidad="${ix.cantidad}" oninput="changeporcentaje(this)" data-origin="venta1"data-pc="${pc}" class="form-control porcentajeventa1" ></td>
						<td><input required class="form-control precioventa1" readonly></td>
						<td><input required data-cantidad="${ix.cantidad}" oninput="changeporcentaje(this)" data-origin="venta2"data-pc="${pc}" class="form-control porcentajeventa2" ></td>
						<td><input required class="form-control precioventa2" readonly></td>
						<td><input required data-cantidad="${ix.cantidad}" oninput="changeporcentaje(this)" data-origin="venta3"data-pc="${pc}" class="form-control porcentajeventa3" ></td>
						<td><input required class="form-control precioventa3" readonly></td>
					</tr>

					`
					$('[data-toggle="tooltip"]').tooltip()
					$('.tooltips').tooltip();
					i++;
				})

			});

			$("#mSetPrecioVenta").modal();
			if(set == "readonly"){
				getSelector("#btn_save_precioventa1").style.display = "none"

			}else{
				getSelector("#btn_save_precioventa1").style.display = ""
			}

		}
		function changeporcentaje(e){
			if(e.value < 0){
				e.value = 0;
				return
			}
			const porcentaje = parseFloat(e.value)
			const origin = e.dataset.origin;
			const pc = parseFloat(e.dataset.pc);
			const cantidad = parseInt(e.dataset.cantidad);
			e.closest("tr").querySelector(`.precio${origin}`).value = pc*(100 + porcentaje)/100;
		}
		getSelector("#saveOrdenCompra").addEventListener("submit", e => {
			e.preventDefault();
			const codacceso = <?= $_SESSION['kt_login_id'] ?>;
			const detalle = [];
			getSelectorAll(".rowto").forEach(ee =>  {
				detalle.push(
						`insert into precio_venta (codacceso, tipo_asignar_venta, codigodetalleproducto, codigoprod, vcf, totalunidad, porcpv1, precioventa1, porcpv2, precioventa2, porcpv3, precioventa3)
						values
							(
								${codacceso},
								1,
								${ee.dataset.codigodetalleproducto},
								${ee.dataset.codigoprod},
								${ee.querySelector(".preciounidad").value},
								${ee.querySelector(".preciounidadmas").value},
								${ee.querySelector(".porcentajeventa1").value},
								${ee.querySelector(".precioventa1").value},
								${ee.querySelector(".porcentajeventa2").value},
								${ee.querySelector(".precioventa2").value},
								${ee.querySelector(".porcentajeventa3").value},
								${ee.querySelector(".precioventa3").value}
							);
						`
					)
			})

			var formData = new FormData();
			formData.append("exearray", JSON.stringify(detalle))

			fetch(`setPrecioVenta.php`, { method: 'POST', body: formData })
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				$("#mOrdenCompra").modal("hide");
				if (res.success) {
					alert("registro completo!")
					location.reload()
				}
			});
		})
		
		
	</script>