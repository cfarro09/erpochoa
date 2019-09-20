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
$query_Listado = "select c.codigocompras, c.totalv, c.tipomoneda, c.tipo_comprobante, p.razonsocial, c.fecha,c.numerofactura,a.usuario, c.subtotal, c.total, c.estadofact, s.nombre_sucursal  from compras c left join sucursal s on s.cod_sucursal = c.codigosuc left join proveedor p on p.codigoproveedor = c.codigoproveedor inner join acceso a on a.codacceso=c.codacceso";

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

<h2>Data de Costeo</h2>

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
					<td class="numerofactura"><?= $row_Listado['numerofactura'] ?></td>
					<td class="tipo_comprobante"><?= $row_Listado['tipo_comprobante'] ?></td>
					<td style="display: none" class="usuario"><?= $row_Listado['usuario'] ?></td>
					<td style="display: none" class="codigocompras"><?= $row_Listado['codigocompras'] ?></td>
					<td class="razonsocial"><?= $row_Listado['razonsocial'] ?></td>
					<td class="total"><?= $row_Listado['total'] ?></td>
					<td class="fecha"><?= $row_Listado['fecha'] ?></td>
					<td class="nombre_sucursal"><?= $row_Listado['nombre_sucursal'] ?></td>
					<td><a href="#" onclick="managecompra(this)" data-set="<?= $row_Listado['totalv'] == 0 ? "asignar" : "ver" ?>" ><?= $row_Listado['totalv'] == 0 ? "Asignar" : "ver" ?></a></td>
				</tr>
				<?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>

			</tbody>
		</table>
		<div class="modal fade" id="mSetPrecioVenta" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog" role="document">
				<div class="modal-content m-auto">
					<div class="modal-header">
						<h5 class="modal-title" id="moperation-title">Asignar precio venta</h5>
					</div>
					<div class="modal-body">
						<form id="saveOrdenCompra">
							<input type="hidden" id="codigoOrdenCompra">
							<input type="hidden" id="codigoordcomp">
							<input type="hidden" id="codigoguia" value="">
							<div class="container-fluid">

								N° COMPROBANTE: <span id="mnumerofactura"></span> <BR>
								TIPO COMPROBANTE: <span id="mtipo_comprobante"></span> <BR>
								PROVEEDOR: <span id="mrazonsocial"></span> <BR>
								TOTAL COMPRA: <span id="mtotal"></span> <BR>
								SUCURSAL: <span id="mnombre_sucursal"></span> <BR>
								FECHA DE EMISION: : <span id="mfecha"></span> <br>
								GENERADA POR: : <span id="musuario"></span> <br>

								<input type="hidden" id="codigocompras" name="">
								<div class="row" style="margin-top:20px">
									<div class="col-xs-12 col-md-12">
										
										<table class="table">
											<thead>
												<th>Nº</th>
												<th>Cant</th>
												<th>Producto</th>
												<th>Marca</th>
												<th>P.Compra</th>
												<th>P.Venta</th>
												<th>Importe</th>
											</thead>
											<tbody id="detalleComprax">
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
	<?php } // Show if recordset not empty ?>

	<?php 

//___________________________________________________________________________________________________________________
	include("Fragmentos/footer.php");
	include("Fragmentos/pie.php");

	mysql_free_result($Listado);
	?>
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
		function managecompra(e){
			$('#mnumerofactura').text(e.parentElement.parentElement.querySelector(".numerofactura").textContent)
			$('#mtipo_comprobante').text(e.parentElement.parentElement.querySelector(".tipo_comprobante").textContent)
			$('#mrazonsocial').text(e.parentElement.parentElement.querySelector(".razonsocial").textContent)
			$('#mtotal').text(e.parentElement.parentElement.querySelector(".total").textContent)
			$('#mnombre_sucursal').text(e.parentElement.parentElement.querySelector(".nombre_sucursal").textContent)
			$('#mfecha').text(e.parentElement.parentElement.querySelector(".fecha").textContent)
			$('#musuario').text(e.parentElement.parentElement.querySelector(".usuario").textContent)
			const codigocompras =  parseInt(e.parentElement.parentElement.querySelector(".codigocompras").textContent)
			
			$("#codigocompras").val(codigocompras);
			getSelector("#detalleComprax").innerHTML = ""

			let i = 1;
			fetch(`getDetalleCompra.php?codigocompras=${codigocompras}`)
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				res.forEach(ix => {
					getSelector("#detalleComprax").innerHTML += `
					<tr>
					<td>${i}</td>
					<td>${ix.cantidad}</td>
					<td>${ix.nombre_producto}</td>
					<td>${ix.nombre}</td>
					<td>${ix.pcompra}</td>
					<td>
					<input data-pcompra="${ix.pcompra}"  onfocusout="validatewithpcompra(this)" data-cantidad="${ix.cantidad}" data-codigodetalleproducto="${ix.codigodetalleproducto}" required data-toggle="tooltip" oninput="validatepventa(this)" data-placement="bottom" title="${ix.precio_venta}" type="number" class="prventax1 form-control">
					</td>
					<td class="importex">0</td>
					</tr>

					`
					$('[data-toggle="tooltip"]').tooltip()
					$('.tooltips').tooltip();
					i++;
				})

			});
			
			$("#mSetPrecioVenta").modal();
		}
		function validatewithpcompra(e){
			if(parseFloat(e.value) < parseFloat(e.dataset.pcompra)){
				e.closest("tr").querySelector(".importex").textContent= ""
				alert("el precio de venta debe ser mayor q al precio de compra")
				e.value = ""
			}
		}
		function validatepventa(e){
			if(e.value < 0){
				e.value = "";
				e.closest("tr").querySelector(".importex").textContent= ""
				alert("no debe ingresar numeros negativos")
			}else if(e.value != ""){
				e.closest("tr").querySelector(".importex").textContent = (parseFloat(e.value)*parseFloat(e.dataset.cantidad)).toFixed(4)
			}else{
				e.closest("tr").querySelector(".importex").textContent = ""
			}
		}
		getSelector("#saveOrdenCompra").addEventListener("submit", e => {
			e.preventDefault();
			const codigocompras = $("#codigocompras").val();
			let total = 0;
			const detalle = [];
			getSelectorAll(".prventax1").forEach(ee =>  {
				total += parseFloat(ee.value)*parseFloat(ee.dataset.cantidad)
				detalle.push({
					codigodetalleproducto: ee.dataset.codigodetalleproducto,
					pventa: ee.value
				})
			})
			

			total = total.toFixed(4)
			var formData = new FormData();
			formData.append("detalle", JSON.stringify(detalle))
			formData.append("codigocompras", codigocompras)
			formData.append("ventatotal", total)

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