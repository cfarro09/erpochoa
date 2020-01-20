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



mysql_select_db($database_Ventas, $Ventas);
$querySucursales = "select * from sucursal where estado = 1" ;
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);

mysql_select_db($database_Ventas, $Ventas);
//$query_Listado = "select * from vt_listaproducto";

$query_Listado = "select `a`.`codigoprod` AS `codigoprod`,`a`.`nombre_producto` AS `nombre_producto`,`b`.`nombre` AS `Marca`,`pv`.`precioventa1` AS `precio_venta`,`pv`.`precioventa2` AS `precio_venta2`,`pv`.`precioventa3` AS `precio_venta3`,`a`.`minicodigo` AS `minicodigo`, k.precio as precio_compra, (select sum(kx.saldo) from kardex_contable kx where kx.id_kardex_contable in (select max(kz1.id_kardex_contable) from kardex_contable kz1 where kz1.codigoprod = a.codigoprod group by kz1.sucursal)) as saldo from `producto` `a` join `marca` `b` on `a`.`codigomarca` = `b`.`codigomarca` 
left join `precio_venta` `pv` on `pv`.`codigoprod` = `a`.`codigoprod`
left join kardex_contable k on k.codigoprod = a.codigoprod and k.id_kardex_contable = (select max(k1.id_kardex_contable) from kardex_contable k1 where k1.codigoprod = k.codigoprod)
where 
    (`a`.`estado` = 0) 
group by `a`.`codigoprod` order by a.codigoprod";
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
				
				<th  >  STOCK</th>
				<th  >  P. COMPRA</th>
				<th  >  P. VENTA</th>
				<th  >  KARDEX</th>
			</tr>
		</thead>
		<tbody>
			<?php do { ?>
				<tr>
					<td> <?php echo $i; ?> </td>
					<td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['codigoprod']; ?> </a>                                                          </td>
					<td> <?php echo $row_Listado['nombre_producto']; ?></td>
					<td align="center"> <?php echo $row_Listado['Marca']; ?></td>
					<td align="center"> <?php if($row_Listado['saldo']==NULL)
									echo 0;
								else 
									echo $row_Listado['saldo'];
								 ?></td>
					<td align="center"> <?php if($row_Listado['precio_compra']==NULL)
									echo 0;
								else 
									echo $row_Listado['precio_compra']; ?></td>
					<td align="center"> <?php if($row_Listado['precio_venta']==NULL)
									echo 0;
								else 
									echo $row_Listado['precio_venta']; ?></td>

	
					<td><a href="#" data-nombreproducto = "<?= $row_Listado['nombre_producto'] ?>" data-codproducto="<?= $row_Listado['codigoprod'] ?>" class="ver-kardex">kardex</a></td>
					
				</tr>
				<?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
			</tbody>
		</table>
		<div class="modal fade" id="mkardex" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog" role="document" style="width: 1100px">
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
															<th colspan="5" id="headerKardex"></th>
															<th colspan="2" style="background-color: #01aaff; color: white; text-align: center">ENTRADA</th>
															<th colspan="2" style="background-color: #01aaff; color: white; text-align: center">SALIDA</th>
															<th colspan="2" style="background-color: #01aaff; color: white; text-align: center">SALDO</th>
														</tr>
														<tr>
															<th>FECHA</th>
															<th>DETALLE</th>
															<th>TIPO</th>
															<th>N° COMP/GUIA</th>
															<th>P.UND</th>
															<th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
															<th style="background-color: #01aaff; color: white; text-align: center">IMPORTE</th>
															<th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
															<th style="background-color: #01aaff; color: white; text-align: center">IMPORTE</th>
															<th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
															<th style="background-color: #01aaff; color: white; text-align: center">IMPORTE</th>
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

			fetch(`getKardexContableFromProductList.php`, { method: 'POST', body: formData })
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
					<td></td>
					<td></td>
					<td></td>
					<td>0</td>	
					<td></td>
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
							<td>${item.tipocomprobante}</td>
							<td>${item.numero}</td>
							<td>${item.precio}</td>
							<td>${(item.detalle.includes("Compras") || (item.detalle.includes("Ventas") && item.tipocomprobante == "notacredito")) || item.detalle.includes("Entra") ? item.cantidad : ""}</td>
							<td>${(item.detalle.includes("Compras") || (item.detalle.includes("Ventas") && item.tipocomprobante == "notacredito")) || item.detalle.includes("Entra") ? item.preciototal : ""}</td>
							<td>${(item.detalle.includes("Ventas") && item.tipocomprobante != "notacredito") || item.detalle.includes("Sale") ? item.cantidad : ""}</td>
							<td>${(item.detalle.includes("Ventas") && item.tipocomprobante != "notacredito") || item.detalle.includes("Sale") ? item.preciototal : ""}</td>
							<td>${item.saldo}</td>
							<td>${(item.precio * item.saldo / item.cantidad).toFixed(3)}</td>
							</tr>
							`;
						}
						
					});

				}
			});


		});
	</script>