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

 //Enumerar filas de data tablas
$i = 1;


//para asignar precio y cantidad
$query_Listado1 = "SELECT a.codigoprod, a.nombre_producto from producto a INNER JOIN detalle_compras b ON a.codigoprod = b.codigoprod group by a.codigoprod";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);
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

$codsucursalx =  $_SESSION['cod_sucursal'];

$query_Listado = "SELECT c.codigoordcomp, c.codigo, codigoref1, montofact as valor_compra, razonsocial, p.codigoproveedor as codigoproveedor, fecha_emision, c.estado FROM ordencompra c inner join proveedor p on c.codigoproveedor=p.codigoproveedor where c.sucursal = $codsucursalx  group by codigo";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
//________________________________________________________________________________________________________________
?>        

<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty ?>
	<div class="alert alert-danger">
		<strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>


	</div>
<?php } // Show if recordset empty ?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty ?>
	<table class="table table-striped table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th  > N&deg; </th>
				<th  > CODIGO REF1</th>
				<th  > M. TOTAL</th>
				<th  class="none"> COMPRA </th>
				<th  class="none">SUBTOTAL</th>
				<th  class="none"> IVA </th>
				<th  > PROVEEDOR </th>
				<th  > FECHA </th>
				<th  > IMPRIMIR </th>
				<th  > VER </th>
				<th> ESTADO</th>
			</tr>
		</thead>
		<tbody>
			<?php do { //echo '<pre>'.var_dump($row_Listado).'</pre>'; die;?>
			<?php 
			$color = "#FFF";
			$estado = "";
			if($row_Listado['estado'] == '1'){
				$color = "#fdf701";
				$estado = "PENDIENTE";
			}else if($row_Listado['estado'] == '2'){
				$color = "#01fd0b";
				$estado = "APROBADO";
			}else if($row_Listado['estado'] == '3'){
				$color = "##d05656";
				$estado = "RECHAZADO";
			}else if($row_Listado['estado'] == '4'){
				$color = "#d05656";
				$estado = "ANULADO";
			}
			?>


			<tr style="background-color: <?= $color; ?>" >
				<td> <?php echo $i; ?> </td>
				<td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['codigoref1']; ?> </a>                                                          </td>
				<td> <?php 
				$preciocompra=$row_Listado['valor_compra'];
				echo number_format($row_Listado['valor_compra'],2); ?></td>
				<td><?php  echo "&#36; ".number_format($row_Listado['valor_compra'],2); ?> </td>
				<td> <?php echo "&#36; ".number_format($row_Listado['valor_compra']/1.18,2); ?></td>
				<td> <?php echo "&#36; ".number_format(($row_Listado['valor_compra']-number_format($row_Listado['valor_compra']/1.18,2)),2); ?></td>
				<td> <?php echo $row_Listado['razonsocial']; ?></td>
				<td> <?php echo $row_Listado['fecha_emision']; ?></td>

				<td> 


					<a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" href="Imprimir/orden_compra.php?codigocompras=<?php echo $row_Listado['codigo']; ?>&codigo=<?php echo $row_Listado['codigoref1']; ?>" target="new"><i class="glyphicon glyphicon-credit-card" ></i></a>
				</td>

				<td><a href="#" data-estado="<?= $row_Listado['estado'] ?>" data-codigo="<?= $row_Listado['codigo'] ?>" class="verOrden">Ver</a></td>
				<td><?= $estado ?> </td>

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
					<input type="hidden" id="codigoOrdenCompra">
					<div class="container-fluid"><p align="right">
						GENERADA POR: <span id="mgeneradapor"></span> <br>
						FECHA DE EMISION: <span id="mfechaemision"></span> <br>
						SUCURSAL: <span id="msucursal"></span> <p>
						RUC: <span id="mruc"></span><br>
						PROVEEDOR: <span id="mproveedor"></span>	<BR> 
						VALOR TOTAL: <span id="mvalortotal"></span><BR> 
						DOC REF 1: <span id="mcodref1"></span> <br>
						DOC REF 2: <span id="mcodref2"></span> <br>
												


						<div class="row">
							<div class="col-xs-12 col-md-12">

								<table class="table">
									<thead>
										<th>Nº</th>
										<th>Cantidad Solicitada</th>
										<th>Producto</th>
										<th>Valor de Compra</th>

									</thead>
									<tbody id="detalleTableOrden-oc-list">
									</tbody>
								</table>
							</div>
						</div>
					</div>x
					<div class="modal-footer" id="manageButtons">
						<button type="button" data-estado="2" class="btn btn-primary setStatus" id="aceptarOrden-x">Aceptar</button>
						<button type="button"  data-estado="3" class="btn btn-primary setStatus"  id="rechazarOrden">Rechazar</button>
						<button type="button" data-estado="4" class="btn btn-primary setStatus" id="anularOrden">Anular</button>
						<a href="ordencompra_edit.php"  class="btn btn-primary"  id="corregirOrden">Corregir</a>
					</div>
					<button data-dismiss="modal" type="button" class="modal_close btn btn-danger">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<script>
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
			item.addEventListener("click", (e) => {
				i=0;
				document.querySelector("#codigoOrdenCompra").value = e.target.dataset.codigo
				fetch(`getDetalleOrdenCompra.php?codigo=${e.target.dataset.codigo}`)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {

					$("#mproveedor").text(res.header.razonsocial)
					$("#mfechaemision").text(res.header.fecha_emision)
					$("#mvalortotal").text(res.header.montofact)
					$("#mcodref1").text(res.header.codigoref1)
					$("#mcodref2").text(res.header.codigoref2)
					$("#mgeneradapor").text(res.header.usuario)
					$("#mruc").text(res.header.ruc)
					$("#msucursal").text(res.header.nombre_sucursal)
					if(e.target.dataset.estado == "3"){
						document.querySelector("#rechazarOrden").style.display = "none"
						document.querySelector("#aceptarOrden-x").style.display = "none"
						document.querySelector("#anularOrden").style.display = ""
						document.querySelector("#corregirOrden").style.display = ""
						document.querySelector("#corregirOrden").href = `ordencompra_edit.php?codigoproveedor=${res.header.codigoproveedor}&codigoref1=${res.header.codigoref1}&codigoref2=${res.header.codigoref2}&codigo=${e.target.dataset.codigo}`
					}else if(e.target.dataset.estado == "4" || e.target.dataset.estado == "1" ||  e.target.dataset.estado == "2"){
						document.querySelector("#rechazarOrden").style.display = "none"
						document.querySelector("#aceptarOrden-x").style.display = "none"
						document.querySelector("#anularOrden").style.display = "none"
						document.querySelector("#corregirOrden").style.display = "none"
					}
					document.querySelector("#detalleTableOrden-oc-list").innerHTML = ""
					res.detalle.forEach(r => {
						document.querySelector("#corregirOrden").href += `&codigo=${r.codigo}`
						i++
						$("#detalleTableOrden-oc-list").append(`
							<tr></tr>
							<td>${i}</td>
							<td>${r.cantidad}</td>
							<td>${r.Producto}</td>
							<td>${r.pcompra}</td>
							<tr></tr>

							`)
					});
				});
				$("#mOrdenCompra").modal();
			})
		});
	</script>

<?php } // Show if recordset not empty ?>
<?php 
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);
?>