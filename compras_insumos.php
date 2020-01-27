<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Plan Contable";
$NombreBotonAgregar = "Agregar";
$EstadoBotonAgregar = "disabled";
$popupAncho = 700;
$popupAlto = 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");

include("Fragmentos/abrirpopupcentro.php");

$codsucursal = $_SESSION['cod_sucursal'];

?>
<style>
	td.details-control {
		background: url('../resources/details_open.png') no-repeat center center;
		cursor: pointer;
	}

	tr.shown td.details-control {
		background: url('../resources/details_close.png') no-repeat center center;
	}
</style>
<form id="form-generate-compra">
	<div class="row">
		<div class="col-sm-12 text-center">
			<button class="btn btn-success" type="submit" style="margin-top:10px;margin-bottom: 10px; font-size: 20px">COMPRAR</button>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">

			<div class="row" style="margin-top: 10px">
				<div class="col-md-6">
					<div class="form-group">
						<label for="field-1" class="control-label">Proveedor</label>
						<select id="proveedorx" class="form-control"></select>
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
	<button type="button" onclick="agregarproducto()" class="btn btn-primary">Agregar Producto</button>
	<div class="row" style="margin-top:20px">
		<div class="col-sm-12">
			<table class="table table-bordered" style="width: 100%">
				<col width="30">
				<col width="80">
				<col width="30">
				<col width="30">
				<col width="30">
				<thead>
					<th style="font-weight: bold">CANTIDAD</th>
					<th style="font-weight: bold">PRODUCTO</th>
					<th style="font-weight: bold">VALOR COMPRA</th>
					<th style="font-weight: bold">IMPORTE</th>
					<th class="text-center" style="font-weight: bold">ACCION</th>
				</thead>
				<tbody id="detalleFormProducto">
				</tbody>
			</table>
		</div>
	</div>
	<div class="row" style="background-color:antiquewhite; font-weight: bold; height: 50px; padding-top:15px" id="header-guia">
		<div class="col-sm-4">
			Total: <span id="totalheader"></span>
		</div>
		<div class="col-sm-4">
			SubTotal: <span id="subtotalheader"></span>
		</div>
		<div class="col-sm-4">
			<?= $nombreigv ?>: <span id="igvheader"></span>
		</div>
	</div>
</form>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
	$(function() {
		initControls()
	});
	const initControls = () => {
		loadproveedor();
	}
	const agregarproducto = () => {
		detalleFormProducto.innerHTML += `
			<tr class="divparent">
				<td width="15%">
					<input type="number" class="form-control cantidad" data-type="cantidad" value="1" oninput="calcularimporte(this)">
				</td>
				<td width="40%">
					<input type="text" class="form-control producto">
				</td>
				<td width="15%">
					<input type="number" class="form-control precio" data-type="precio" value="0" oninput="calcularimporte(this)">
				</td>
				<td width="15%">
					<input type="text" class="form-control importe" value="0" disabled>
				</td>
				<td width="15%" class="text-center">
					<button type="button" onclick="eliminarproducto(this)" class="btn red-thunderbird btn-sm"><i class="glyphicon glyphicon-trash"></i></button>
				</td>
			</tr>
		`;
	}
	const calcularimporte = e => {
		if(e.value < 0){
			e.value = 0;
			return
		}
		const row = e.closest(".divparent")

		const cantidad = row.querySelector(".cantidad").value;
		const precio = row.querySelector(".precio").value;

		row.querySelector(".importe").value = cantidad * precio;
		calculartotales();
	}
	const calculartotales = () => {
		let total = 0;
		getSelectorAll("#detalleFormProducto .importe").forEach(x => {
			total += parseFloat(x.value);
		});
		totalheader.textContent = (total*IGV1).toFixed(2);
		subtotalheader.textContent = total.toFixed(2);
		igvheader.textContent = (total*IGV).toFixed(2);
	}
	const eliminarproducto = e => {
		e.closest(".divparent").remove();
		calculartotales();
	}
	const loadproveedor = async () => {
		const query = "SELECT codigoproveedor as codigoclienten, CONCAT(razonsocial, ' ', ruc) as name FROM proveedor  WHERE estado = 0 order by razonsocial";
		const arraydata = await get_data_dynamic(query);
		cargarselect2("#proveedorx", arraydata, "codigoclienten", "name")

	}
</script>