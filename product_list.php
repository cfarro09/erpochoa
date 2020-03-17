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
	$updateSQL = sprintf(
		"DELETE from producto_imagen WHERE codigoprod=%s",
		GetSQLValueString($_POST['codigoprod'], "int")
	);

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



	$updateSQL = sprintf(
		"UPDATE producto p, producto_stock ps SET p.estado=%s WHERE p.codigoprod=%s and ps.stock=0 and ps.codigoprod=%s",
		GetSQLValueString($_POST['estado'], "text"),
		GetSQLValueString($_POST['codigoprod'], "int"),
		GetSQLValueString($_POST['codigoprod'], "int")
	);

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
$querySucursales = "select * from sucursal where estado = 1";
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);

mysql_select_db($database_Ventas, $Ventas);
//$query_Listado = "select * from vt_listaproducto";
$query_Listado = "select * from lista_producto";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);

//Enumerar filas de data tablas
$i = 1;

//Titulo e icono de la pagina
$Icono = "fa fa-cubes";
$Color = "font-blue";
$Titulo = "Listado de Productos";
$NombreBotonAgregar = "Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar = "";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho = 700;
$popupAlto = 330;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
$codsucursal = $_SESSION['cod_sucursal'];

//__________________________________________________________________
?>

<!--  ----------------------------------------------------------------------------------------------------------------------------------->

<?php if ($totalRows_Listado == 0) { // Show if recordset empty 
?>
	<div class="alert alert-danger">
		<strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>


	</div>
<?php } // Show if recordset empty 
?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty 
?>
	<table class="table table-striped table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th class="text-center"> N&deg; </th>
				<th class="text-center"> CODIGO </th>
				<th class="text-center"> PRODUCTO </th>
				<th class="text-center"> MARCA </th>
				<th class="text-center"> COLOR</th>
				<th class="text-center"> PRESENTACION</th>
				<th class="text-center"> CATEGORIA </th>
				<th class="none"> CODIGO </th>
				<th> </th>
				<th> </th>

			</tr>
		</thead>
		<tbody>
			<?php $i = 1; 
			do { ?>
				<tr>
					<td> <?php echo $i; ?> </td>
					<td><a onClick="abre_ventana('Emergentes/<?php echo $editar ?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho ?>,<?php echo $popupAlto ?>)" data-toggle="modal"> <?php echo $row_Listado['codigoprod']; ?> </a> </td>
					<td> <?php echo $row_Listado['nombre_producto']; ?></td>
					<td > <?php echo $row_Listado['Marca']; ?></td>
					<td> <?php echo $row_Listado['color']; ?></td>
					<td align="center"> <?php echo $row_Listado['presentacion']; ?></td>


					<td> <?php echo $row_Listado['Categoria']; ?></td>

					<td> <?php echo $row_Listado['minicodigo']; ?></td>


					<td>
						<?php if ($codsucursal == 1) : ?>
							<a class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Actualizar Registro" onClick="abre_ventana('Emergentes/<?php echo $editar ?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho ?>,<?php echo $popupAlto ?>)"><i class="fa fa-refresh"></i></a>
						<?php endif ?>
					</td>
					<td>
						<?php if ($row_Listado['foto'] == NULL) { ?>
							<?php if ($codsucursal == 1) : ?>
								<a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Registrar Foto" onClick="abre_ventana('Emergentes/productofoto.php?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho ?>,<?php echo $popupAlto ?>)"><i class="fa fa-cubes"></i></a>
							<?php endif ?>
						<?php } else {
						?>
							<form method="POST" action="<?php echo $editFormAction; ?>" name="Eliminar_Registro" id="Eliminar_Registro" onSubmit="return confirm('�ESTA SEGURO QUE DESEA ELIMINAR ESTA FOTO: <?php echo $row_Listado['codigoprod']; ?>?');">
								<input name="codigoprod" id="codigoprod" type="hidden" value="<?php echo $row_Listado['codigoprod']; ?>">
								<button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Eliminar Registro"><i class="glyphicon glyphicon-trash"></i></button>

								<input type="hidden" name="MM_eliminar" value="Eliminar_Registro" />
							</form>
						<?php } ?>

					</td>


				</tr>
			<?php $i++;
			} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
		</tbody>
	</table>
	
<?php } // Show if recordset not empty 

//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);
?><!--
<script>
	$(document).ready(function() {
		const codsucursal = <?= $codsucursal ?>;
		debugger
		if (codsucursal != 1) {
			btnagregargordis.style.display = "none"
		}
	});

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

		fetch(`getKardexAlmFromProductList.php`, {
				method: 'POST',
				body: formData
			})
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				if (res.length > 0) {
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
						if (item.cantidad != "0") {
							getSelector("#detalleKardexAlmProd").innerHTML += `
							<tr>
							<td>${new Date(item.fecha).toLocaleDateString()}</td>
							<td>${item.detalle}</td>
							<td>${item.tipodocumento}</td>
							<td>${item.numero}</td>
							<td>${item.cantidad}</td>
							<td></td>
							<td>${item.saldo}</td>
							</tr>
							`;
						}

					});

				}
			});


	});
</script>
!-->