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
			<button class="btn btn-success" type="submit" style="margin-top:10px;margin-bottom: 10px; font-size: 20px">CONFIRMAR COMPRA</button>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">

			<div class="row" style="margin-top: 10px">


				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">Tipo Compra</label>
						<select id="tipocompra" onchange="changetipo(this)" required class="form-control">
							<option value="mercaderia">Mercaderia</option>
							<option value="insumos">Insumos</option>
							<option value="servicios">Servicios</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">Comprobante</label>
						<select required id="comprobantex" class="form-control">
							<option value="factura">Factura</option>
							<option value="boleta">Boleta</option>
							<option value="notadebito">Nota Debito</option>
							<option value="notacredito">Nota Credito</option>
							<option value="otros">Otros</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="nrocomprobante" class="control-label">N° Comprobante</label>
						<input required type="text" class="form-control" id="nrocomprobante">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="cuentax" class="control-label">Cuenta</label>
						<input required type="text" class="form-control" id="cuentax">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="proveedorx" class="control-label">Proveedor</label>
						<select required id="proveedorx" class="form-control"></select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="margin-bottom: 10px">
		<div class="col-sm-12">
			<label class="" style="font-weight: bold">Seleccione </label>
			<select id="prouctosaux" class="form-control select2-allow-clear" name="prouctosaux">
				
			</select>
		</div>
	</div>
	<button type="button" onclick="agregarproducto()" class="btn btn-primary">Agregar Producto</button>
	<div class="row" style="margin-top:20px">
		<div class="col-sm-12">
			<table class="table table-bordered" style="width: 100%">

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
		initEvents();
		initControls();
	});
	const initEvents = () => {
		getSelector("#form-generate-compra").addEventListener("submit", guardar);
	}
	const initControls = () => {
		loadproveedor();
	}
	const agregarproducto = () => {
		detalleFormProducto.innerHTML += `
			<tr class="divparent">
				<td width="15%">
					<input required type="number" class="form-control cantidad" data-type="cantidad" value="1" oninput="calcularimporte(this)">
				</td>
				<td width="40%">
					<input required type="text" class="form-control producto">
				</td>
				<td width="15%">
					<input required type="number" class="form-control precio" data-type="precio" value="0" oninput="calcularimporte(this)">
				</td>
				<td width="15%">
					<input required type="text" class="form-control importe" value="0" disabled>
				</td>
				<td width="15%" class="text-center">
					<button type="button" onclick="eliminarproducto(this)" class="btn red-thunderbird btn-sm"><i class="glyphicon glyphicon-trash"></i></button>
				</td>
			</tr>
		`;
	}
	const changetipo = e => {
		clearselect2("#prouctosaux")
		let query = "";
		if(e.value === "mercaderia"){
			query = "";
		}else if(e.value === "mercaderia"){
			query = "";
		}else if(e.value === "servicios"){
			query = "";
		}
		let res = await get_data_dynamic(query).then(r => r);
		cargarselect2("#prouctosaux", res, "id", "descripcion", ["tipo"]);
	}
	const guardar = async e => {
		e.preventDefault();
		const data = {
			header: {},
			detalle: []
		}
		const nowx = new Date();
		const month = nowx.getMonth() + 1;

		const codigomesconta = `${nowx.getFullYear()}${month}-`;
		const firstday = `${nowx.getFullYear()}-${nowx.getMonth() + 1}-1`;

		const querymaxcode = `SELECT IFNULL(max(contamesincrement), 0) maxcodigo FROM registro_compras WHERE fecha BETWEEN '${firstday}' and LAST_DAY('${firstday}')`;
		const result = await get_data_dynamic(querymaxcode);
		const maxcodigo = result[0].maxcodigo;
		const codeconcat = `${codigomesconta}${maxcodigo}`

		const rucproveedor = proveedorx.options[proveedorx.selectedIndex].dataset.ruc;
		const codproveedor = proveedorx.value;
		const codacceso = <?= $_SESSION['kt_login_id'] ?>;
		const sucursal = <?= $codsucursal ?>;
		const subtotalh = subtotalheader.textContent;
		const igvh = igvheader.textContent;
		const totalh = totalheader.textContent;

		data.header = `
			insert into registro_compras(tipo_comprobante, rucproveedor, numerocomprobante, codacceso, subtotal, igv, total, estadofact, codigosuc, fecha_registro, codigoproveedor, codigomesconta, contamesincrement) values ('${comprobantex.value}', '${rucproveedor}', '${nrocomprobante.value}', ${codacceso}, ${subtotalh}, ${igvh}, ${totalh}, 0, ${sucursal}, NOW(), ${codproveedor}, '${codeconcat}', ${maxcodigo})
		`;
		getSelectorAll("#detalleFormProducto tr").forEach(x => {
			const cantidad = x.querySelector(".cantidad").value;
			const producto = x.querySelector(".producto").value;
			const precio = x.querySelector(".precio").value;
			const subtotal = precio * cantidad;
			const total = subtotal * IGV1;
			const igv = subtotal * IGV;
			const query = `
				insert into detalle_compras(codigoprod, cantidad, vcu, vci, vcf, igv, totalcompra, codigocompras, nombreproducto) values (0, ${cantidad}, ${precio}, ${subtotal}, ${subtotal}, ${igv}, ${total}, ###ID###, '${producto}')
			`;
			data.detalle.push(query)
		})

		const jjson = JSON.stringify(data).replace(/select/g, "lieuiwuygyq")
		var formData = new FormData();
		formData.append("json", jjson)

		fetch(`setVenta.php`, {
				method: 'POST',
				body: formData
			})
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				if (res.success) {
					alert("registro completo!")
					location.reload();

				}
			});
	}
	const calcularimporte = e => {
		if (e.value < 0) {
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
		totalheader.textContent = (total * IGV1).toFixed(2);
		subtotalheader.textContent = total.toFixed(2);
		igvheader.textContent = (total * IGV).toFixed(2);
	}
	const eliminarproducto = e => {
		e.closest(".divparent").remove();
		calculartotales();
	}
	const loadproveedor = async () => {
		const query = "SELECT ruc, codigoproveedor as codigoclienten, CONCAT(razonsocial, ' ', ruc) as name FROM proveedor  WHERE estado = 0 order by razonsocial";
		const arraydata = await get_data_dynamic(query);
		cargarselect2("#proveedorx", arraydata, "codigoclienten", "name", ["ruc"])

	}
</script>