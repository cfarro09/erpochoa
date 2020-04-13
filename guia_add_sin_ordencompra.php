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
$Titulo="Generar Guia sin orden de compra - Principal - Tumbes";
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

<form id="form-generate-compra">
	<div class="row">
		<div class="col-sm-12 text-center">
			<button class="btn btn-success" type="submit" id="generateCompra"
			style="margin-top:10px;margin-bottom: 10px; font-size: 20px">ENTRADA DE MERCADERIA</button>
		</div>
	</div>
	<div class="row">
		<div class="row" style="margin-top: 10px">
			<div class="col-md-6">
				<div class="form-group">
					<label for="field-1" class="control-label">Proveedor</label>
					<select name="proveedor" id="proveedor" required class="form-control select2 tooltips" id="single"
					data-placement="top" data-original-title="Seleccionar proveedor">
					<option value=""></option>
					<?php do {  ?>
						<option value="<?php echo $row_Clientes['codigoclienten']?>">
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
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="field-1" class="control-label">Sucursal</label>
				<input type="hidden" name="sucursal" id="sucursal" value="<?=  $_SESSION['cod_sucursal'] ?>">
				<select name="sucursal" id="sucursal" disabled required
				class="sucursal form-control select2 tooltips" data-placement="top"
				data-original-title="Seleccionar sucursal">
				<?php do {  ?>
					<option <?= $row_sucursales['cod_sucursal'] == $_SESSION['cod_sucursal'] ? 'selected' : '' ?>
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
	</div>
</div>
<div class="col-md-3">
	<div class="form-group">
		<label for="field-1" class="control-label">Tipo Doc</label>
		<select class="form-control" id="tipodocsinoc">
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
		<input type="text" class="form-control" required="" id="numeroguia" name="numeroguia">
	</div>
</div>
<div class="col-md-6">
	<div class="form-group">
		<label for="field-1" class="control-label">Documento de Referencia</label>
		<input type="text" class="form-control" id="codigoreferencia2" name="codigoreferencia2">
	</div>
</div>
</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<label class="" style="font-weight: bold">Seleccione un producto</label>
		<select id="codigoprod" class="form-control select2-allow-clear" name="codigoprod">
			<option value="" <?php if (!(strcmp("", "compras_add.php"))) {echo "selected=\"selected\"";} ?>>
			</option>
			<?php
			do {  
				?>
				<option value="<?php echo $row_Productos['codigoprod']?>"
					data-nombre="<?php echo $row_Productos['nombre_producto']?>"
					data-marca="<?php echo $row_Productos['Marca']; ?>"
					data-umedida="<?php echo $row_Productos['umedida']; ?>"
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
		</div>
	</div>
	<div class="row" style="margin-top:20px">
		<div class="col-sm-12">
			<table class="table">
				<thead>
					<th>Nº</th>
					<th>Cantidad</th>
					<th>Producto</th>
					<th>Marca</th>
					<th>U. Medida</th>
					<th style="display: none" id="cant_aux">Cant Aux</th>
					<th>Accion</th>
				</thead>
				<tbody id="detalleFormProducto">
				</tbody>
			</table>
		</div>
	</div>
</form>

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
		} else {
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
	function changeunidadmedida(e) {
		if (e.value == "kilo" || e.value == "tonelada") {
			e.parentElement.parentElement.querySelector(".td-cantidad_aux").style.display = ""
			getSelector("#cant_aux").style.display = ""
		} else {
			e.parentElement.parentElement.querySelector(".td-cantidad_aux").style.display = "none"
			getSelector("#cant_aux").style.display = "none"
		}
	}

	$('#codigoprod').on('change', function () {
		if (getSelector(`.codigo_${this.value}`)) {

		} else {
			const option = this.options[this.selectedIndex]
			const cantrows = document.querySelectorAll("#detalleFormProducto tr").length + 1
			$("#detalleFormProducto").append(`
				<tr class="producto">
				<td data-codigo="${this.value}" class="codigopro codigo_${this.value}" style="display: none">${this.value}</td>
				<td class="indexproducto">${cantrows}</td>
				<td style="width: 80px"><input type="number" required oninput="nonegative(this)" class="cantidad form-control" value="1" ></td>
				<td class="nombre">${option.dataset.nombre}</td>
				<td class="marca">${option.dataset.marca}</td>
				<td class="unidad_medida" style="width: 100px">${option.dataset.umedida}</td>
				<td class="td-cantidad_aux" style="display: none; width: 80px">
				<input type="number" oninput="nonegative(this)" class="cantidad_aux form-control" >
				</td>
				<td>
				<button type="button" onclick="eliminarproducto(this)" class="btn red-thunderbird btn-sm tooltips" data-placement="top"  data-original-title="Eliminar Producto"><i class="glyphicon glyphicon-trash"></i></button>
				</td>
				</tr>
				`)
		}

	});
	function nonegative(e) {
		if (e.value < 0) {
			e.value = 0;
		}

	}
	function eliminarproducto(e) {
		e.closest(".producto").remove()

		var i = 1;
		getSelectorAll(".producto").forEach(p => {
			p.querySelector(".indexproducto").textContent = i;
			i++;
		})
	}
	getSelector("#form-generate-compra").addEventListener("submit", e => {
		e.preventDefault();
		if (getSelectorAll(".producto").length < 1) {
			alert("Debes agregar almenos un producto")
		} else {
			const data = {
				header: {},
				detalle: []
			}
			data.header = {
				tipodoc: $("#tipodocsinoc").val(),
				codigoguia: 0,
				codigo: "<?= $_GET['codigo'] ?>",
				codigoproveedor: getSelector("#proveedor").value,
				codigoacceso: "<?= $_SESSION['kt_login_id']; ?>",
				codigopersonal: "<?php echo $_SESSION['kt_codigopersonal']; ?>",
				subtotal: 0,
				igv: 0,
				montofact: 0,
				estadofact: 1,
				codsucursal: <?= $_SESSION['cod_sucursal'] ?>,
				numeroguia: getSelector("#numeroguia").value,
				codigoreferencia2: getSelector("#codigoreferencia2").value,
				estado: 1
			}
			console.log(data)
			getSelectorAll(".producto").forEach(item => {
				console.log(item.querySelector(".unidad_medida").value)
				data.detalle.push({
					codigoprod: item.querySelector(".codigopro").dataset.codigo,
					unidad_medida: item.querySelector(".unidad_medida").textContent,
					cantidad_aux: item.querySelector(".cantidad_aux").value ? item.querySelector(".cantidad_aux").value : "0",
					cantidad: item.querySelector(".cantidad").value
				})
			})
			console.log(data)
			var formData = new FormData();
			formData.append("json", JSON.stringify(data))

			fetch(`setGuiaSinOc.php`, { method: 'POST', body: formData })
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				if (res.success) {
					location.reload()
					alert("registro completo!")
					getSelector("#form-generate-compra").reset();
					getSelector("#detalleFormProducto").innerHTML = ""
				}
			});

		}
	})
</script>