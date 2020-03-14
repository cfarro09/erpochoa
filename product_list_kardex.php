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

if ((isset($_POST["MM_eliminar"])) && ($_POST["MM_eliminar"] == "Eliminar_Registro")) {
	$updateSQL = sprintf("DELETE from producto_imagen WHERE codigoprod=%s",
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Eliminar_Registro")) {
	
	
	
	$updateSQL = sprintf("UPDATE producto p, producto_stock ps SET p.estado=%s WHERE p.codigoprod=%s and ps.stock=0 and ps.codigoprod=%s",
		GetSQLValueString($_POST['estado'], "text"),
		GetSQLValueString($_POST['codigoprod'], "int"),
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
$querySucursales = "select * from sucursal where estado = 1" ;
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "select * from vt_listaproducto";
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
	<table class="table table-striped table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th  > N&deg; </th>
				<th  > CODIGO </th>
				<th  > PRODUCTO </th>
				<th  > MARCA </th>
				
				<?php do {  ?>
					<th  class="none"> <?= $row_sucursales['nombre_sucursal'] ?></th>
					<?php 
				} while ($row_sucursales = mysql_fetch_assoc($sucursales));
				$rows = mysql_num_rows($sucursales);
				if($rows > 0) {
					mysql_data_seek($sucursales, 0);
					$row_sucursales = mysql_fetch_assoc($sucursales);
				}
				?>
				<th  > CATEGORIA </th>
				<th > <?= $_SESSION['nombre_sucursal'] ?></th>
				
				<th  class="none"> CODIGO </th>
				
				<th  >  </th>
			</tr>
		</thead>
		<tbody>
			<?php do { ?>
				<tr>
					<td> <?php echo $i; ?> </td>
					<td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['codigoprod']; ?> </a>                                                          </td>
					<td> <?php echo $row_Listado['nombre_producto']; ?></td>
					<td align="center"> <?php echo $row_Listado['Marca']; ?></td>
					
					<?php 
					$sux = $row_Listado['codigoprod'];
					$query_filtro_by_sucursal = "SELECT s.nombre_sucursal, s.cod_sucursal, IF(k.saldo IS NULL or k.saldo = '', '0', k.saldo) as saldo, k.codigoprod, k.fecha from sucursal s left join kardex_alm k on k.codsucursal = s.cod_sucursal and k.id_kardex_alm = ( SELECT MAX(t2.id_kardex_alm) FROM kardex_alm t2 WHERE k.codigoprod = t2.codigoprod and t2.codsucursal = s.cod_sucursal) and k.codigoprod = $sux where s.cod_sucursal != 10 order by cod_sucursal asc
					";
					$auxx1 = mysql_query($query_filtro_by_sucursal, $Ventas) or die(mysql_error());
					$row_aux = mysql_fetch_assoc($auxx1);
					$total = 0 ;
					$totalsede = 0 ;
					do { ?>
						<?php
							if($row_aux['cod_sucursal'] == $_SESSION['cod_sucursal']){
								$totalsede = $row_aux['saldo'];
							}
							$total += $row_aux['saldo'];
						?>
						
						<th  class="none"> <?= $row_aux['saldo']; ?></th>

						<?php 
					} while ($row_aux = mysql_fetch_assoc($auxx1));
					
					$rows = mysql_num_rows($auxx1);
					if($rows > 0) {
						mysql_data_seek($auxx1, 0);
						$row_aux = mysql_fetch_assoc($auxx1);
					}
					?>
					<td> <?php echo $row_Listado['Categoria']; ?></td>
					<td> <?= $totalsede;?></td>

					

					<td> <?php echo $row_Listado['minicodigo'];?></td>


					
					
					<td><a href="#" data-nombreproducto = "<?= $row_Listado['nombre_producto'] ?>" data-codproducto="<?= $row_Listado['codigoprod'] ?>" class="ver-kardex">kardex</a></td>

				</tr>
				<?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
			</tbody>
		</table>
		<div class="modal fade" id="mkardex" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog" role="document" style="width: 700px">
				<div class="modal-content m-auto">
					<div class="modal-header">
						<h5 class="modal-title" id="moperation-title">Almacen Kardex</h5>
					</div>
					<div class="modal-body">
						<input type="hidden" id="codproducto">
						<form id="form-setKardex" action="kardex_almacen.php" method="GET">
							<div class="container-fluid">
								<div class="row" style="margin-top:20px">
									<div class="col-xs-12 col-md-12">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label for="field-1" class="control-label">Sucursales</label>
													<select name="codigosuc" required id="codigosuc" class="sucursalXX form-control select2 tooltips" id="single" data-placement="top" >
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
													<input type="text" required name ="fecha_inicio" autocomplete="off" id ="fecha_inicio" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top"  required/>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="field-1" class="control-label">Fecha termino</label>
													<input type="text" name ="fecha_termino" autocomplete="off" id ="fecha_termino" required class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top"  required/>
												</div>
											</div>
											<div class="col-md-12">
												<table class="table table-striped table-bordered table-hover" id="">
													<thead>
														<tr>
															<th colspan="4" id="headerKardex"></th>
															<th style="background-color: #01aaff; color: white; text-align: center">ENTRADA</th>
															<th style="background-color: #01aaff; color: white; text-align: center">SALIDA</th>
															<th style="background-color: #01aaff; color: white; text-align: center">SALDO</th>
														</tr>
														<tr>
															<th>FECHA</th>
															<th>DETALLE</th>
															<th>TIPO</th>
															<th>N° COMP/GUIA</th>
															<th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
															<th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
															<th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
														</tr>
													</thead>
													<tbody id="detalleKardexAlmProd" class="text-center"></tbody>
												</table>
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
	<?php } // Show if recordset not empty ?>
	<?php 
//___________________________________________________________________________________________________________________
	include("Fragmentos/footer.php");
	include("Fragmentos/pie.php");

	mysql_free_result($Listado);
	?>
	<script>
		document.querySelectorAll(".ver-kardex").forEach(item => {
			item.addEventListener("click", e => {
				getSelector("#codproducto").value = e.target.dataset.codproducto

				getSelector("#headerKardex").textContent = e.target.dataset.nombreproducto

				$("#mkardex").modal();
				$("#fecha_inicio").val("");
				$("#fecha_termino").val("");
			})
		})
		getSelector("#form-setKardex").addEventListener("submit", e => {
			e.preventDefault();
			const codsucursal = $("#codigosuc").val()
			const fecha_inicio = $("#fecha_inicio").val()
			const fecha_termino = $("#fecha_termino").val()
			const codproducto = getSelector("#codproducto").value
			var formData = new FormData();
			formData.append("codsucursal", codsucursal);
			formData.append("fecha_inicio", fecha_inicio);
			formData.append("fecha_termino", fecha_termino);
			formData.append("codproducto", codproducto);

			getSelector("#detalleKardexAlmProd").innerHTML = "<tr><td colspan='6'>No hay registros</td></tr>"

			fetch(`getKardexAlmFromProductList.php`, { method: 'POST', body: formData })
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				if(res.length > 0){
					getSelector("#detalleKardexAlmProd").innerHTML = `
					<tr>
					<td></td>
					<td>Inventario inicial</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>	
					<td>0</td>
					</tr>
					`
					console.log(res)
					let i = 0;
					res.forEach(item => {
						if(item.cantidad != "0"){
							getSelector("#detalleKardexAlmProd").innerHTML += `
							<tr>
							<td>${new Date(item.fecha).toLocaleDateString()}</td>
							<td>${item.detalle}</td>
							<td>${item.tipodocumento}</td>
							<td>${item.numero}</td>

							<td>${item.detalle.toLowerCase().includes("compras") || item.detalle.toLowerCase().includes("entra") ? item.cantidad : ""}</td>
							<td>${item.detalle.toLowerCase().includes("ventas") || item.detalle.toLowerCase().includes("sale") ? item.cantidad : ""}</td>


							<td>${item.saldo}</td>
							</tr>
							`;
						}
						
					});

				}
			});


		});
	</script>