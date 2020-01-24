<?php $total=0; $validarc=1; ?>
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
			case "float":
			$theValue = ($theValue != "") ? floatval($theValue) : "NULL";
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

if ((isset($_POST["MM_eliminar"])) && ($_POST["MM_eliminar"] == "EliminarProducto")) {  
	$deleteSQL = sprintf("DELETE FROM detalle_compras_oc WHERE  codigodetalleproducto=%s",
		GetSQLValueString($_POST['codigodetalleproducto'], "int"));

	mysql_select_db($database_Ventas, $Ventas);
	$Result1 = mysql_query($deleteSQL, $Ventas) or die(mysql_error());
}



//eliminar venta
if ((isset($_POST["MM_EliminarVenta"])) && ($_POST["MM_EliminarVenta"] == "EliminarVenta")) { 
	$deleteSQL = sprintf("DELETE FROM detalle_compras_oc WHERE codigo=%s",
		GetSQLValueString($_POST['codigo'], "int"));

	mysql_select_db($database_Ventas, $Ventas);
	$Result1 = mysql_query($deleteSQL, $Ventas) or die(mysql_error());
}

//grabar factura venta
if ((isset($_POST["MM_GuardarVenta"])) && ($_POST["MM_GuardarVenta"] == "GuardarVenta")) {  
	if($_POST['codigoproveedor']==NULL){
		?>
<script type="text/javascript">
	alert("INGRESE CODIGO DE PROVEEDOR, NUMERO DE FACTURA Y SUCURSAL, O PRECIO DE COMPRA O VENTA ESTA EN CERO");
</script>
<?php 
	}
	else {

/*
   $insertSQL = sprintf("insert into ordencompra values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			  GetSQLValueString(NULL, "text"),
			  GetSQLValueString(2, "text"),
			  
			  GetSQLValueString(3, "int"),
			  GetSQLValueString(4, "date"),
			  GetSQLValueString(5, "date"),
			  GetSQLValueString(6, "int"),
			  GetSQLValueString(7, "int"),
			  GetSQLValueString(8, "double"),
			  GetSQLValueString(9, "double"),
			  GetSQLValueString(10, "double"),
			  GetSQLValueString(11, "int"),
			  GetSQLValueString(11, "int"),
		   GetSQLValueString(13, "text"),
		   GetSQLValueString(14, "text"));
  */
		   $insertSQL = sprintf("insert into ordencompra values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
		   	GetSQLValueString(NULL, "text"),
		   	GetSQLValueString($_POST['codigo'], "text"),

		   	GetSQLValueString($_POST['codigoproveedor'], "int"),
		   	GetSQLValueString($_POST['fecha_emision'], "date"),
		   	GetSQLValueString($_POST['hora_emision'], "date"),
		   	GetSQLValueString($_POST['codacceso'], "int"),
		   	GetSQLValueString($_POST['codigopersonal'], "int"),
		   	GetSQLValueString(round($_POST['montopagar']/1.18,2), "double"),
		   	GetSQLValueString(round($_POST['montopagar']-$_POST['montopagar']/1.18,2), "double"),
		   	GetSQLValueString($_POST['montopagar'], "double"),
		   	GetSQLValueString(1, "int"),
		   	GetSQLValueString($_POST['codigosuc'], "int"),
		   	GetSQLValueString($_POST['docref1'], "text"),
			GetSQLValueString($_POST['docref2'], "text"),
			GetSQLValueString(1, "int"),
			GetSQLValueString($_POST['direccion'], "text"));

		   mysql_select_db($database_Ventas, $Ventas);
		   $Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());

		   ?>
<script type="text/javascript">
	window.location = "product_list.php";
</script>

<?php } 
	}



	if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "IngresarProducto")) {


		mysql_select_db($database_Ventas, $Ventas);
		$codigoproducto10=$_POST['codigoprod'];
		$query_Productos = "SELECT codigoprod, precio_venta, round(precio_compra/1.18,2) as precio_compra FROM producto_stock WHERE codigoprod = $codigoproducto10 ORDER BY codigoprod desc";
		$Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
		$row_Productos = mysql_fetch_assoc($Productos);
		$totalRows_Productos = mysql_num_rows($Productos);

		$concatenacion=$_POST['CodigoProducto'].$_POST['codigoprod'];
		
		
		$query_Contador_Clientes = "SELECT count(*) AS Contador FROM detalle_compras_oc where concatenacion='$concatenacion'";
		$Contador_Clientes = mysql_query($query_Contador_Clientes, $Ventas) or die(mysql_error());
		$row_Contador_Clientes = mysql_fetch_assoc($Contador_Clientes);
		$totalRows_Contador_Clientes = mysql_num_rows($Contador_Clientes);
		if($row_Contador_Clientes['Contador']==0)
		{

			$concatenacion=$_POST['CodigoProducto'].$_POST['codigoprod'];


			$insertSQL = sprintf("INSERT INTO detalle_compras_oc (codigo, codigoprod, concatenacion, pcompra, igv, totalcompras) VALUES (%s, %s, %s, %s, %s, %s)",
				GetSQLValueString($_POST['CodigoProducto'], "text"),
				GetSQLValueString($_POST['codigoprod'], "int"),
				GetSQLValueString($concatenacion,"text"),
				GetSQLValueString(0, "double"),
				GetSQLValueString($row_Productos['precio_compra'], "double"),
				GetSQLValueString(round($row_Productos['precio_compra']*0.18,2), "double"),
				GetSQLValueString(round($row_Productos['precio_compra']+$row_Productos['precio_compra']*0.18,2), "double"));

			mysql_select_db($database_Ventas, $Ventas);
			$Result1 = mysql_query($insertSQL, $Ventas) or die(mysql_error());
		}
		else
		{
			echo "<script language='JavaScript'>alert('Grabacion Correcta');</script>";  
		}

		$insertGoTo = "ordencompra_add.php?codigo=" . $_GET['codigo'] . "";
		if (isset($_SERVER['QUERY_STRING'])) {
			$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
			$insertGoTo .= $_SERVER['QUERY_STRING'];
		}
		header(sprintf("Location: %s", $insertGoTo));
	}

//actualiza stock
	if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Cantidad")) {
		$updateSQL = sprintf("UPDATE detalle_compras_oc SET cantidad=%s WHERE codigodetalleproducto=%s",
			GetSQLValueString($_POST['cantidad'], "int"),
			GetSQLValueString($_POST['codigodetalleproducto'], "int"));

		mysql_select_db($database_Ventas, $Ventas);
		$Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());
	}
//actualiza precio
	if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Precio_compra")) {

		$updateSQL = sprintf("UPDATE detalle_compras_oc SET pcompra=%s, igv=%s, totalcompras=%s WHERE codigodetalleproducto=%s",
			GetSQLValueString($_POST['pcompra'], "double"),
			GetSQLValueString(round($_POST['pcompra']*0.18,2), "double"),
			GetSQLValueString(($_POST['pcompra']*0.18)+$_POST['pcompra'], "double"),
			GetSQLValueString($_POST['codigodetalleproducto'], "int"));

		mysql_select_db($database_Ventas, $Ventas);
		$Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());
	}


	mysql_select_db($database_Ventas, $Ventas);
	$query_Productos = "SELECT * FROM vt_producto_compra";

	$Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
	$row_Productos = mysql_fetch_assoc($Productos);
	$totalRows_Productos = mysql_num_rows($Productos);

	$colname_Detalle_Compras = "-1";
	if (isset($_GET['codigo'])) {
		$colname_Detalle_Compras = $_GET['codigo'];
	}
	mysql_select_db($database_Ventas, $Ventas);
	$query_Detalle_Compras = sprintf("SELECT a.codigodetalleproducto, a.codigo, a.codigoprod, a.cantidad,b.nombre_producto AS Producto,c.nombre AS Marca, ps.precio_venta, e.nombre_color, sum(a.cantidad*ps.precio_compra) as Importe,ps.stock, a.pcompra FROM detalle_compras_oc a  INNER JOIN producto b ON a.codigoprod =b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN color e ON b.codigocolor = e.codigocolor INNER JOIN producto_stock ps ON ps.codigoprod=b.codigoprod WHERE a.codigo = %s GROUP BY a.codigoprod desc ", GetSQLValueString($colname_Detalle_Compras, "text"));
	$Detalle_Compras = mysql_query($query_Detalle_Compras, $Ventas) or die(mysql_error());
	$row_Detalle_Compras = mysql_fetch_assoc($Detalle_Compras);
	$totalRows_Detalle_Compras = mysql_num_rows($Detalle_Compras);
	$validastock=$row_Detalle_Compras['cantidad'];

	mysql_select_db($database_Ventas, $Ventas);
	$query_Clientes = "SELECT codigoproveedor as codigoclienten, CONCAT(razonsocial, ' ', ruc) as ClienteNatural FROM proveedor  WHERE estado = 0 order by razonsocial";
	$Clientes = mysql_query($query_Clientes, $Ventas) or die(mysql_error());
	$row_Clientes = mysql_fetch_assoc($Clientes);
	$totalRows_Clientes = mysql_num_rows($Clientes);

	$querySucursales = "select * from sucursal where estado = 1 or estado = 999" ;
	$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
	$row_sucursales = mysql_fetch_assoc($sucursales);
	$totalRows_sucursales = mysql_num_rows($sucursales);

	mysql_select_db($database_Ventas, $Ventas);
	$query_Bancos = "SELECT * FROM banco WHERE estado = 0";
	$Bancos = mysql_query($query_Bancos, $Ventas) or die(mysql_error());
	$row_Bancos = mysql_fetch_assoc($Bancos);
	$totalRows_Bancos = mysql_num_rows($Bancos);



//Titulo e icono de la pagina
	$Icono="glyphicon glyphicon-shopping-cart";
	$Color="font-blue";
	$Titulo="Generar Orden de Compras - Principal - Tumbes";
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


<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<table width="100%" border="0">
	<tr>
		<td width="65%" valign="top">
			<table width="100%" border="0">
				<tr>
					<td>




						<form action="<?php echo $editFormAction; ?>" method="POST" name="IngresarProducto">
							<div class="form-group has-success">
								<div class="input-group select2-bootstrap-append">
									<span id="spryselect1">

										<select id="single-append-text" class="form-control select2-allow-clear"
											name="codigoprod">
											<option value=""
												<?php if (!(strcmp("", "compras_add.php"))) {echo "selected=\"selected\"";} ?>>
											</option>
											<?php
												do {  
													?>
											<option value="<?php echo $row_Productos['codigoprod']?>"
												<?php if (!(strcmp($row_Productos['codigoprod'], "compras_add.php"))) {echo "selected=\"selected\"";} ?>>
												<?php echo $row_Productos['nombre_producto']?> -
												<?php echo $row_Productos['Marca']; ?> -
												<?php echo $row_Productos['nombre_color']; ?> -
												<?php echo "$/.". $row_Productos['precio_venta']; ?> -
												<?php echo $row_Productos['minicodigo']; ?> -
												(<?php echo "Stock ".$row_Productos['stock']; ?>)</option>
											<?php
												} while ($row_Productos = mysql_fetch_assoc($Productos));
												$rows = mysql_num_rows($Productos);
												if($rows > 0) {
													mysql_data_seek($Productos, 0);
													$row_Productos = mysql_fetch_assoc($Productos);
												}
												?>
										</select>
										<span class="selectRequiredMsg"></span></span><span class="input-group-btn">
										<button class="btn btn-default" type="submit">
											<span class="glyphicon glyphicon-shopping-cart "></span>
										</button>
										<input name="CodigoProducto" id="CodigoProducto" type="hidden"
											value="<?php echo $_GET['codigo']; ?>" />
										<input type="hidden" name="MM_insert" value="IngresarProducto" />
						</form>







						<?php 
 //Enumerar filas de data tablas
											$i = 1;?>
						</span>
						</div>
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top"><?php if ($totalRows_Detalle_Compras == 0) { // Show if recordset empty ?>
						<div class="alert alert-danger"> <strong>AUN NO SE HA INGRESADO NINGUN PRODUCTO A LA
								VENTA...!</strong> </div>
						<?php } // Show if recordset empty ?>
						<?php if ($totalRows_Detalle_Compras > 0) { // Show if recordset not empty ?>
						<table class="table" border="0">
							<thead>
								<tr>
									<th width="5%"> # </th>
									<th width="7%"> Cant </th>
									<th width="30%"> Producto </th>
									<th width="10%"> Marca </th>
									<th width="10%">Precio Compra S/<?= $nombreigv ?></th>
									<th width="10%"> Importe </th>
									<th width="5%"> </th>
								</tr>
							</thead>
							<tbody>
							<tbody>
								<?php do { ?>
								<tr>
									<td valign="middle"><?php echo $i; ?></td>
									<td valign="top">
										<form action="<?php echo $editFormAction; ?>" method="POST" name="Cantidad"
											id="Cantidad">
											<input name="cantidad" type="text" class="form-control tooltips input-sm"
												id="nrecibo" value="<?php echo $row_Detalle_Compras['cantidad']; ?>"
												maxlength="3" data-placement="top"
												data-original-title="<?php echo $row_Detalle_Compras['stock']; ?>" />
											<input name="codigodetalleproducto" type="hidden" id="codigodetalleproducto"
												value="<?php echo $row_Detalle_Compras['codigodetalleproducto']; ?>" />
											<input type="hidden" name="MM_update" value="Cantidad" />

										</form>
									</td>
									<td><?php echo $row_Detalle_Compras['Producto']; ?></td>
									<td><?php echo $row_Detalle_Compras['Marca']; ?></td>


									<td>
										<form action="<?php echo $editFormAction; ?>" method="POST" name="Precio_compra"
											id="Precio_compra">


											<input name="pcompra" type="text" class="form-control tooltips input-sm"
												id="pcompra" value="<?php echo $row_Detalle_Compras['pcompra']; ?>"
												maxlength="6" data-placement="top"
												data-original-title="<?php echo $row_Detalle_Compras['pcompra'].' - '.round($row_Detalle_Compras['pcompra']*0.18+$row_Detalle_Compras['pcompra'],2); ?>" />

											<input name="codigodetalleproducto" type="hidden" id="codigodetalleproducto"
												value="<?php echo $row_Detalle_Compras['codigodetalleproducto']; ?>" />
											<input type="hidden" name="MM_update" value="Precio_compra" />


										</form>
									</td>


									<td align="center">

										<?php 
															$importe=$row_Detalle_Compras['pcompra']*$row_Detalle_Compras['cantidad'];
															$total=$total+$importe;
	  $validarc=$validarc*$importe;  //validarv=  
	  
	  
	  echo $importe;
	  
	  //echo $row_Detalle_Compras['Importe']; ?>


									</td>
									<td><?php //echo $EliminarProducto; ?>
										<form action="#" method="POST" name="EliminarProducto">
											<button type="submit" class="btn red-thunderbird btn-sm tooltips"
												data-placement="top" data-original-title="Eliminar Producto"><i
													class="glyphicon glyphicon-trash"></i></button>
											<input name="codigodetalleproducto" type="hidden"
												value="<?php echo $row_Detalle_Compras['codigodetalleproducto']; ?>" />
											<input type="hidden" name="MM_eliminar" value="EliminarProducto" />
										</form>




									</td>
								</tr>
								<?php $i++;} while ($row_Detalle_Compras = mysql_fetch_assoc($Detalle_Compras)); ?>
							</tbody>
						</table>
						<?php } // Show if recordset not empty 
	?>
					</td>
				</tr>
			</table>




			<?php if ($totalRows_Detalle_Compras > 0) { // Show if recordset not empty ?>
			<table width="80%" border="0" align="center">
				<tr>
					<td align="center" valign="top">

						<?php //echo $EliminarVenta; ?>
						<form action="#" method="POST" name="EliminarVenta" id="EliminarVenta">
							<button type="submit" class="btn red-thunderbird btn-lg tooltips " data-placement="top"
								data-original-title="Eliminar Venta">Eliminar Venta</button>
							<input name="codigo" type="hidden" value="<?php echo $_GET['codigo']; ?>" />
							<input type="hidden" name="MM_EliminarVenta" value="EliminarVenta" />
						</form>


					</td>

					<td align="center" valign="top">
						<form action="#" method="POST" name="GuardarVenta" id="GuardarVenta">
							<button type="submit" class="btn blue-chambray btn-lg tooltips" data-placement="top"
								data-original-title="Guardar Venta"><i
									class="glyphicon glyphicon-shopping-cart"></i>Generar Compras</button>
							<input name="codigo" type="hidden" value="<?php echo $_GET['codigo']; ?>" />
							<input type="hidden" name="MM_GuardarVenta" value="GuardarVenta" />
							<input name="validarc" type="hidden" id="validarc" value="<?php echo $validarc; ?>" />
							<input name="validarv" type="hidden" id="validarv" value="<?php echo $validarv; ?>" />
							<input name="totalv1" type="hidden" id="totalv1" value="<?php echo $totalv1; ?>" />
							<input name="CodigoProducto" id="CodigoProducto" type="hidden"
								value="<?php echo $_GET['codigo']; ?>" />
							<input name="codacceso" type="hidden" value="<?php echo $_SESSION['kt_login_id']; ?>" />
							<input name="codigopersonal" type="hidden"
								value="<?php echo $_SESSION['kt_codigopersonal']; ?>" />


					</td>

				</tr>
			</table>
			<?php echo $row_Detalle_Compras['nombre_color']; ?>
			<?php } // Show if recordset not empty ?>
		</td>
		<td width="35%" valign="top">


			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN Portlet PORTLET-->
					<div class="portlet box blue-chambray text-center">
						<div class="portlet-title text-center ">
							<div class="caption text-center">
								<h1>$ <?php 
						$total=round($total*0.18+$total,2);
						echo  $total;
						$subtotal=$total/1.18;
						$iva=$total-$subtotal;

						?></h1>
							</div>
							<input type="hidden" name="montopagar" id="montopagar" value="<?php echo($total); ?>" />
						</div>
						<div class="portlet-body">

							<table width="100%" border="0">


								<tr>
									<td colspan="2">
										<table width="100%" border="0">
											<tr>
												<td width="95%" valign="top">
													<div class="form-group" id="ClienteN"><span id="spryselect5">
															<select name="codigoproveedor" id="codigoproveedor"
																class="form-control select2 tooltips" id="single"
																data-placement="top"
																data-original-title="Seleccionar Comprobante">
																<optgroup label="Proveedores"> </optgroup>
																<option value=""></option>
																<?php
													do {  
														?>
																<option
																	value="<?php echo $row_Clientes['codigoclienten']?>">
																	<?php echo $row_Clientes['ClienteNatural']?>
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
															<span class="selectRequiredMsg"></span></span></div>

												</td>
												<td width="5%" valign="top">
													<div class="col-md-2">
														<div class="input-group">
															<div class="input-group-btn">
																<button type="button" class="btn blue dropdown-toggle"
																	data-toggle="dropdown"><i class="icon-users"></i>
																	<i class="fa fa-angle-down"></i>
																</button>
																<ul class="dropdown-menu pull-right">
																	<li class="divider"> </li>
																	<li>
																		<a href="javascript:;"
																			onclick="abre_ventana('Emergentes/proveedor_list_add.php',900,700)"><i
																				class="glyphicon glyphicon-floppy-disk "></i>
																			Agregar Proveedor </a>
																	</li>


																	<li class="divider"> </li>
																</ul>
															</div>
															<!-- /btn-group -->
														</div>
														<!-- /input-group -->
													</div>

												</td>
											</tr>







											<tr>
												<td colspan="2">
													<table width="100%" border="0">
														<tr>
															<td width="95%" valign="top">
																<div class="form-group" id="codigosuc"><span
																		id="spryselect5">
																		<select name="codigosuc" id="codigosuc"
																			class="sucursalXX form-control select2 tooltips"
																			id="single" data-placement="top"
																			data-original-title="Seleccionar Comprobante">
																			<optgroup label="Sucursal"> </optgroup>
																			<?php
																do {  
																	?>
																			<option
																				value="<?php echo $row_sucursales['cod_sucursal']?>">
																				<?php echo $row_sucursales['nombre_sucursal']?>
																			</option>
																			<?php
																} while ($row_sucursales = mysql_fetch_assoc($sucursales));
																$rows = mysql_num_rows($sucursales);
																if($rows > 0) {
																	mysql_data_seek($sucursales, 0);
																	$row_sucursales = mysql_fetch_assoc($sucursales);
																}
																?>
																		</select>
																		<span class="selectRequiredMsg"></span></span>
																</div>

															</td>

														</tr>


													</table>
												</td>
											</tr>
											<tr id="tr_domicilio" style="display: none">
												<td colspan="2">
													<table width="100%" border="0">
														<tr>
															<td width="95%" valign="top">
																<div class="form-group" ><span
																		>
																		<label for="">Domicilio</label>
																<input type="text" name="direccion" class="form-control">
																</div>

															</td>

														</tr>


													</table>
												</td>
											</tr>
											<br>

											<tr>

												<td colspan="2">
													<br>


													<div class="form-group" id="ClienteJ" style="display:none"><span
															id="spryselect6">
															</select>
															<span class="selectRequiredMsg"></span></span></div>
												</td>
											</tr>


											<tr>
												<td>


													<span>
														<input value="" type="text" name="docref1" id="docref1"
															class="form-control tooltips" data-placement="top"
															data-original-title="Documento Referencial 1"
															placeholder="Codigo Referencial 1" />

														<span class="textfieldRequiredMsg"></span></span>
												</td>
												<td>
													<input type="text" name="docref2" id="docref2"
														class="form-control tooltips" data-placement="top"
														data-original-title="Documento Referencial 2"
														placeholder="Codigo Referencial 2">

													<span class="textfieldRequiredMsg"></span></span></td>
											</tr>


											<tr>
												<td>

													<?php $time = time();?>
													<span id="sprytextfield4">
														<input value="<?php echo date("Y-m-d");?>" type="text"
															name="fecha_emision" id="fecha_comprobante"
															class="form-control form-control-inline input-medium date-picker tooltips"
															value="fecha_emision" data-date-format="yyyy-mm-dd"
															data-placement="top"
															data-original-title="Fecha Emisi&oacute;n"
															value="<?php echo date("d-m-Y");?>" required="required" />

														<span class="textfieldRequiredMsg"></span></span>
												</td>
												<td><span id="sprytextfield5">
														<input type="text" name="hora_emision" id="hora_emision"
															class="form-control timepicker timepicker-24 tooltips"
															data-placement="top"
															data-original-title="Hora Emisi&oacute;n"
															required="required">

														<span class="textfieldRequiredMsg"></span></span></td>
											</tr>

										</table>

										<table width="100%" border="0">
											<tr>
												<td colspan="2">
													<hr />
												</td>
											</tr>
											<tr>
												<td valign="top" align="right">
													<h5 class="font-red-mint"><strong>SUB TOTAL</strong></h5>
												</td>
												<td valign="top" align="right">
													<h5 class="font-red-mint"><strong>$
															<?php echo number_format($subtotal,2); ?>
															<input type="text" name="subtotal10" id="subtotal10"
																align="center" readonly="readonly"
																value="<?php echo number_format($subtotal,2);?>"
																size="8" hidden="true" /></strong></h5>
												</td>
											</tr>
											<tr>
												<td valign="top" align="right">
													<h5 class="font-red-mint"><strong>IGV 18%</strong></h5>
												</td>
												<td valign="top" align="right">
													<h5 class="font-red-mint"><strong>$
															<?php echo number_format($iva,2);?>
															<input type="text" name="igv" id="igv" align="center"
																readonly="readonly"
																value="<?php echo number_format($iva,2);?>" size="8"
																hidden="true" /></strong></h5>
												</td>
											</tr>
											<tr>
												<td valign="top" align="right">
													<h5 class="font-red-mint"><strong>TOTAL </strong></h5>
												</td>
												<td valign="top" align="right">
													<h5 class="font-red-mint"><strong>$
															<?php echo number_format($total,2);?>
															<input type="text" name="total10" id="total10"
																align="center" readonly="readonly"
																value="<?php echo number_format($total,2);?>" size="8"
																hidden="true" />
															<input type="text" name="totalv" id="totalv" align="center"
																readonly="readonly"
																value="<?php echo number_format($totalv,2);?>" size="8"
																hidden="true" />
													</h5>
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<!--<button type="submit" class="btn blue-chambray btn-block btn-lg tooltips" data-placement="top" data-original-title="Calcular"><i class="fa fa-calculator"></i>Calcular11</button>
</td>-->
											</tr>
										</table>

										</form>
						</div>
					</div>
					<!-- END Portlet PORTLET-->
				</div>
			</div>
			</form>
		</td>
	</tr>
</table>
<div class="modal fade" id="mSucursal" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content m-auto">
			<div class="modal-header">
				<h5 class="modal-title" id="moperation-title">Registrar Nueva Sucursal</h5>
			</div>
			<form id="form-setSucursal">
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="field-1" class="control-label">DIRECCION</label>
									<input type="text" required class="form-control" name="sucursal" id="sucursalinput">
								</div>
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-primary">Guardar</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", { invalidValue: "0", validateOn: ["blur", "change"] });
	var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", { invalidValue: "0", validateOn: ["blur", "change"] });
	var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", { invalidValue: "0", validateOn: ["blur", "change"] });
	var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", { invalidValue: "0", validateOn: ["blur", "change"] });
	var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", { invalidValue: "0", validateOn: ["blur", "change"] });
	var spryselect6 = new Spry.Widget.ValidationSelect("spryselect6", { invalidValue: "0", validateOn: ["blur", "change"] });
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", { validateOn: ["blur", "change"] });
	var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", { validateOn: ["blur", "change"] });
	var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", { validateOn: ["blur", "change"] });
	var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", { validateOn: ["blur", "change"] });
	var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", { validateOn: ["blur", "change"] });
</script>

<?php 
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");


mysql_free_result($Clientes);
mysql_free_result($Detalle_Compras);
?>

<script type="text/javascript">
	$(".sucursalXX").on("change", function () {

		if ($(".sucursalXX").val() == 10) {
			$("#tr_domicilio").val("");
			$("#tr_domicilio").show("fast/300/slow");
		}else{
			$("#tr_domicilio").hide("fast/300/slow");
		}
	})

	$("#form-setSucursal").on("submit", function (e) {
		e.preventDefault();
		fetch(`setDireccion.php?name=${document.querySelector("#sucursalinput").value}&estado=999`)
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				location.reload();
			});
	})
</script>