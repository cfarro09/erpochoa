<?php $total = 0;
$validarc = 1; ?>
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



mysql_select_db($database_Ventas, $Ventas);
//$query_Productos = "SELECT * FROM vt_producto_compra";

$query_Productos = "select a.codigoprod AS codigoprod, a.nombre_producto AS nombre_producto, b.codigomarca AS codigomarca,b.nombre AS Marca,
c.nombre_color AS nombre_color,pres.nombre_presentacion AS umedida, a.minicodigo AS minicodigo 
from producto a 
join marca b on a.codigomarca = b.codigomarca 
join color c on a.codigocolor = c.codigocolor 
join presentacion pres on pres.codigopresent = a.codigopresent 
where a.estado = 0 group by a.codigoprod order by a.nombre_producto";

$Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
$row_Productos = mysql_fetch_assoc($Productos);
$totalRows_Productos = mysql_num_rows($Productos);

$colname_Detalle_Compras = "-1";
if (isset($_GET['codigo'])) {
	$colname_Detalle_Compras = $_GET['codigo'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Detalle_Compras = sprintf("SELECT a.codigodetalleproducto, a.codigo, a.codigoprod, a.cantidad,b.nombre_producto AS Producto,c.nombre AS Marca, ps.precio_venta, e.nombre_color, sum(a.cantidad*ps.precio_compra) as Importe,ps.stock, a.pcompra 
FROM detalle_compras_oc a  INNER JOIN producto b ON a.codigoprod =b.codigoprod INNER JOIN marca c ON b.codigomarca = c.codigomarca INNER JOIN color e ON b.codigocolor = e.codigocolor INNER JOIN producto_stock ps ON ps.codigoprod=b.codigoprod WHERE a.codigo = %s GROUP BY a.codigoprod desc ", GetSQLValueString($colname_Detalle_Compras, "text"));
$Detalle_Compras = mysql_query($query_Detalle_Compras, $Ventas) or die(mysql_error());
$row_Detalle_Compras = mysql_fetch_assoc($Detalle_Compras);
$totalRows_Detalle_Compras = mysql_num_rows($Detalle_Compras);
$validastock = $row_Detalle_Compras['cantidad'];

mysql_select_db($database_Ventas, $Ventas);
$query_Clientes = "SELECT codigoproveedor as codigoclienten, CONCAT(razonsocial, ' ', ruc) as ClienteNatural FROM proveedor  WHERE estado = 0 order by razonsocial";
$Clientes = mysql_query($query_Clientes, $Ventas) or die(mysql_error());
$row_Clientes = mysql_fetch_assoc($Clientes);
$totalRows_Clientes = mysql_num_rows($Clientes);



mysql_select_db($database_Ventas, $Ventas);
$query_Bancos = "SELECT * FROM banco WHERE estado = 0";
$Bancos = mysql_query($query_Bancos, $Ventas) or die(mysql_error());
$row_Bancos = mysql_fetch_assoc($Bancos);
$totalRows_Bancos = mysql_num_rows($Bancos);


//Titulo e icono de la pagina
$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Orden Compra - Area Comercial";
$NombreBotonAgregar = "Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar = "disabled";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho = 700;
$popupAlto = 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//________________________________________________________________________________________________________________
$querySucursales = "select * from sucursal where estado = 1 or estado = 999";
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);
?>

<form id="form-generate-compra">
	<div class="row">
		<div class="col-sm-12 text-center">
			<button class="btn btn-success" type="submit" style="margin-top:10px;margin-bottom: 10px; font-size: 20px">ORDEN DE COMPRA<br>
				<H5><STRONG>
						(CONFIRMAR)
					</STRONG></H5>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">

			<div class="row" style="margin-top: 10px">
				<div class="col-md-6">
					<div class="form-group">
						<label for="field-1" class="control-label">Proveedor</label>
						<select name="proveedor" id="proveedor" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
							<option value=""></option>
							<?php do {  ?>
								<option value="<?php echo $row_Clientes['codigoclienten'] ?>">
									<?php echo $row_Clientes['ClienteNatural'] ?>
								</option>
							<?php
							} while ($row_Clientes = mysql_fetch_assoc($Clientes));
							$rows = mysql_num_rows($Clientes);
							if ($rows > 0) {
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
						<select name="sucursal" id="sucursal-oc-new" required class="sucursal form-control select2 tooltips" data-placement="top" data-original-title="Seleccionar sucursal">
							<?php do {  ?>
								<option <?= $row_sucursales['cod_sucursal'] == $_SESSION['cod_sucursal'] ? 'selected' : '' ?> value="<?php echo $row_sucursales['cod_sucursal'] ?>">
									<?php echo $row_sucursales['nombre_sucursal'] ?>
								</option>
							<?php
							} while ($row_sucursales = mysql_fetch_assoc($sucursales));
							$rows = mysql_num_rows($sucursales);
							if ($rows > 0) {
								mysql_data_seek($sucursales, 0);
								$row_sucursales = mysql_fetch_assoc($sucursales);
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-6" style="display: none" id="div_aux"></div>
				<div class="col-md-6" id="div_direccion" style="display: none">
					<div class="form-group">
						<label for="direccion" class="control-label">Direccion</label>
						<input type="text" class="form-control" id="direccion">
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label for="field-1" class="control-label">Documento de Referencia</label>
						<input type="text" class="form-control" required="" id="codigoreferencial1" name="codigoreferencial1">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="field-1" class="control-label">Documento de referencia</label>
						<input type="text" class="form-control" id="codigoreferencia2" name="codigoreferencia2">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="display: none">
		<div class="col-sm-12 text-center">
			<button class="btn btn-success" type="submit" id="generateCompra" style="margin-top:10px;margin-bottom: 10px; font-size: 20px">GENERAR ORDEN DE COMPRA</button>
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
					<th>Valor Compra</th>
					<th>Importe</th>
					<th>Accion</th>
				</thead>
				<tbody id="detalleFormProducto">
				</tbody>
			</table>
			<div class="row" style="margin-bottom: 1rem">
				<div class="col-sm-12">

					<select id="codigoprod" class="form-control select2-allow-clear" name="codigoprod">
						<option value="" <?php if (!(strcmp("", "compras_add.php"))) {
												echo "selected=\"selected\"";
											} ?>>
						</option>
						<?php
						do {
						?>
							<option value="<?php echo $row_Productos['codigoprod'] ?>" data-nombre="<?php echo $row_Productos['nombre_producto'] ?>" data-marca="<?php echo $row_Productos['Marca']; ?>" <?php if (!(strcmp($row_Productos['codigoprod'], "compras_add.php"))) {
																																																			echo "selected=\"selected\"";
																																																		} ?>>
								<?php echo $row_Productos['nombre_producto'] ?> -
								<?php echo $row_Productos['Marca']; ?> -
								<?php echo $row_Productos['nombre_color']; ?> -
								<?php echo $row_Productos['minicodigo']; ?></option>
						<?php
						} while ($row_Productos = mysql_fetch_assoc($Productos));
						$rows = mysql_num_rows($Productos);
						if ($rows > 0) {
							mysql_data_seek($Productos, 0);
							$row_Productos = mysql_fetch_assoc($Productos);
						}
						?>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="background-color:antiquewhite; font-weight: bold; height: 50px; padding-top:15px" id="header-guia">
		<div class="col-sm-4">
			Total: <span id="total-header"></span>
		</div>
		<div class="col-sm-4">
			SubTotal: <span id="subtotal-header"></span>
		</div>
		<div class="col-sm-4">
			<?= $nombreigv ?>: <span id="igv-header"></span>
		</div>
	</div>
</form>

<script type="text/javascript">
	var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {
		invalidValue: "0",
		validateOn: ["blur", "change"]
	});
	var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {
		invalidValue: "0",
		validateOn: ["blur", "change"]
	});
	var spryselect3 = new Spry.Widget.ValidationSelect("spryselect3", {
		invalidValue: "0",
		validateOn: ["blur", "change"]
	});
	var spryselect4 = new Spry.Widget.ValidationSelect("spryselect4", {
		invalidValue: "0",
		validateOn: ["blur", "change"]
	});
	var spryselect5 = new Spry.Widget.ValidationSelect("spryselect5", {
		invalidValue: "0",
		validateOn: ["blur", "change"]
	});
	var spryselect6 = new Spry.Widget.ValidationSelect("spryselect6", {
		invalidValue: "0",
		validateOn: ["blur", "change"]
	});
	var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {
		validateOn: ["blur", "change"]
	});
	var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {
		validateOn: ["blur", "change"]
	});
	var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "none", {
		validateOn: ["blur", "change"]
	});
	var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "none", {
		validateOn: ["blur", "change"]
	});
	var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {
		validateOn: ["blur", "change"]
	});
</script>

<?php
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");


mysql_free_result($Clientes);
mysql_free_result($Detalle_Compras);
?>

<script type="text/javascript">
	$(document).on("keypress", 'form', function(e) {
		var code = e.keyCode || e.which;
		if (code == 13) {
			e.preventDefault();
			return false;
		}
	});

	
	$("#sucursal-oc-new").on("change", function() {

		if ($("#sucursal-oc-new").val() == 10) {
			$("#direccion").val("");
			$("#div_direccion").show("fast/300/slow");
			$("#div_aux").show("fast/300/slow");
		} else {
			$("#div_direccion").hide("fast/300/slow");
			$("#div_aux").hide("fast/300/slow");
		}
	})
	$('#codigoprod').on('change', function() {
		if (!getSelector(`.codigo_${this.value}`)) {
			const option = this.options[this.selectedIndex]
			const cantrows = document.querySelectorAll("#detalleFormProducto tr").length + 1
			$("#detalleFormProducto").append(`
					<tr class="producto">
					<td data-codigo="${this.value}" class="codigopro codigo_${this.value}" style="display: none">${this.value}</td>
					<td class="indexproducto">${cantrows}</td>
					<td><input type="text" oninput="changevalue(this)" required class="cantidad form-control" value="" style="width: 80px" ></td>
					<td style="display: none">
					<select class="form-control unidad_medida" name="unidad_medida" required>
					<option value="unidad">unidad</option>
					<option value="kilo">kilo</option>
					<option value="tonelada">tonelada</option>
					</select>
					</td>
					<td class="nombre">${option.dataset.nombre}</td>
					<td class="marca">${option.dataset.marca}</td>
					<td style="width: 100px"><input type="number" oninput="changevalue(this)" required class="precio form-control" value="0" ></td>
					<td class="importe">0</td>
					<td>
					<button type="button" onclick="eliminarproducto(this)" class="btn red-thunderbird btn-sm tooltips" data-placement="top"  data-original-title="Eliminar Producto"><i class="glyphicon glyphicon-trash"></i></button>
					</td>
					</tr>
					`)
		}
		setTimeout(() => {
			getSelector("#detalleFormProducto tr:last-of-type").querySelector("input.cantidad").focus()
		}, 1000)
	});

	function changevalue(e) {

		if (e.value < 0) {
			e.value = ""
			alert("no puede ingresar cantidades negativas")
		} else {
			const precio = e.closest(".producto").querySelector(".precio").value;
			const cantidad = e.closest(".producto").querySelector(".cantidad").value;
			const mu = precio * cantidad
			const res = mu.toFixed(2)
			e.closest(".producto").querySelector(".importe").textContent = res
			let total = 0;
			getSelectorAll(".producto").forEach(p => {
				total += p.querySelector(".importe").textContent;
			})
			if (total != 0) {
				total = parseFloat(total)
				getSelector("#subtotal-header").textContent = total.toFixed(3);
				getSelector("#total-header").textContent = (total * IGV1).toFixed(3);
				getSelector("#igv-header").textContent = (total * IGV).toFixed(3);
			} else {
				getSelector("#subtotal-header").textContent = 0;
				getSelector("#total-header").textContent = 0;
				getSelector("#igv-header").textContent = 0;
			}
		}

	}

	function makeid(length) {
		var result = '';
		var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for (var i = 0; i < length; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}

	function eliminarproducto(e) {
		e.closest(".producto").remove()

		var i = 1;
		getSelectorAll(".producto").forEach(p => {
			p.querySelector(".indexproducto").textContent = i;
			i++;
		})
	}
	let bobis = -1;
	getSelector("#form-generate-compra").addEventListener("submit", e => {
		if (bobis > 0)
			return;
		bobis = bobis * -1;
		e.preventDefault();
		if (!$("#proveedor").val() || !$("#codigoreferencial1").val())
			return
		if (getSelectorAll(".producto").length < 1) {
			alert("Debes agregar almenos un producto")
		} else {
			const data = {
				header: {},
				detalle: []
			}
			data.header = {
				fecha_emision: '<?php echo date("Y-m-d"); ?>',
				hora_emision: '<?php echo date("h:i:s"); ?>',
				codigoguia: 0,
				codigo: makeid(20),
				codigoproveedor: getSelector("#proveedor").value,
				codigoacceso: "<?= $_SESSION['kt_login_id']; ?>",
				codigopersonal: "<?php echo $_SESSION['kt_codigopersonal']; ?>",
				subtotal: getSelector("#subtotal-header").textContent ? getSelector("#subtotal-header").textContent : 0,
				igv: getSelector("#igv-header").textContent ? getSelector("#igv-header").textContent : 0,
				montofact: getSelector("#total-header").textContent ? getSelector("#total-header").textContent : 0,
				estadofact: 1,
				sucursal: getSelector("#sucursal-oc-new").value,
				codigoref1: getSelector("#codigoreferencial1").value,
				codigoref2: getSelector("#codigoreferencia2").value,
				estado: 1,
				direccion: $("#direccion").val()
			}
			getSelectorAll(".producto").forEach(item => {
				data.detalle.push({
					codigoprod: item.querySelector(".codigopro").dataset.codigo,
					cantidad: item.querySelector(".cantidad").value,
					unidad_medida: item.querySelector(".unidad_medida").value,
					concatenacion: "<?= $_GET['codigo'] ?>" + item.querySelector(".codigopro").dataset.codigo,
					pcompra: item.querySelector(".precio").value,
					igv: parseFloat(item.querySelector(".precio").value) * IGV,
					totalcompras: parseFloat(item.querySelector(".precio").value) * IGV
				})
			})
			var formData = new FormData();
			formData.append("json", JSON.stringify(data))

			fetch(`setOrdenCompraNew.php`, {
					method: 'POST',
					body: formData
				})
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					if (res.success) {
						alert("registro completo!")
						getSelector("#form-generate-compra").reset();
						getSelector("#detalleFormProducto").innerHTML = ""
						location.reload()
					}
				});

		}
	})
</script>