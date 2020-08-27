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

mysql_select_db($database_Ventas, $Ventas);
$query_Clientes = "SELECT codigoproveedor as codigoclienten, razonsocial, ruc FROM proveedor  WHERE estado = 0 order by razonsocial";
$Clientes = mysql_query($query_Clientes, $Ventas) or die(mysql_error());
$row_Clientes = mysql_fetch_assoc($Clientes);
$totalRows_Clientes = mysql_num_rows($Clientes);

?>
<style>
	td.details-control {
		background: url('../resources/details_open.png') no-repeat center center;
		cursor: pointer;
	}

	tr.shown td.details-control {
		background: url('../resources/details_close.png') no-repeat center center;
	}

	.select2-container {
		width: 100% !important;
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
							<option value="">Seleccione</option>
							<option value="insumos">Insumos</option>
							<option value="servicios">Servicios</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">Fecha</label>
						<input type="text" required name="fechaxxx" autocomplete="off" id="fechaxxx" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
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


			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<label for="cuentax" class="control-label">Cuenta</label>
						<select class="form-control" id="cuentax"></select>
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group">
						<label for="proveedorx" id="labelproveedor" class="control-label">Proveedor</label>
						<select required id="proveedorx" onchange="onchangeproveedor(this)" class="form-control"></select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4" id="parentproveedoradicional" style="display: none;">
					<div class="form-group">
						<label for="proveedoradicional" class="control-label">Proveedor Adicional</label>
						<select required id="proveedoradicional" class="form-control"></select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="margin-bottom: 10px">
		<div class="col-sm-12">
			<label class="" style="font-weight: bold">Seleccione </label>
			<select id="prouctosaux" class="form-control select2-allow-clear" onchange="agregarproducto()" name="prouctosaux">

			</select>
		</div>
	</div>
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

<div class="modal fade" id="mProrrateo" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document" style="width: 900px">
		<div class="modal-content m-auto">
			<div class="modal-header">
				<h2 class="modal-title" id="title_extra">PRORRATEO POR PESO</h2>
			</div>
			<div class="modal-body">
				<form id="formExtra">
					<div class="container-fluid">
						<div class="row">
							<div class="row">
								<div class="col-sm-6">
									<label for="field-1" class="control-label">PROVEEDOR</label>

									<select name="proveedor" id="proveedorpro" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar proveedor">
										<option value="">Seleccione</option>
										<?php do {  ?>
											<option value="<?= $row_Clientes['razonsocial'] . '&&&' . $row_Clientes['ruc'] ?>">
												<?= $row_Clientes['razonsocial'] . ' ' . $row_Clientes['ruc'] ?>
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

							<div class="row" style="margin-top: 10px">
								<div class="col-sm-3">
									<label class="control-label" for="monedapro">Moneda</label>
									<select class="form-control" name="monedapro" id="monedapro" onchange="changemonedapro(this)">
										<option value="soles">S/</option>
										<option value="dolares">$</option>
									</select>
								</div>
								<div class="col-sm-3" id="containerTipoCambio" style="display: none">
									<label class="control-label" for="monedapro">Cambio</label>
									<input type="number" class="form-control" value="1" min="1" step="any" name="tipocambiopro" id="tipocambiopro" oninput="changepeso(preciopro)">
								</div>
								<div class="col-sm-3">
									<label class="control-label" for="tipocomprobantepro">Tipo Comprobante</label>
									<select class="form-control " name="tipocomprobantepro" id="tipocomprobantepro" required>
										<option value="factura">Guia</option>
										<option value="factura">Factura</option>
										<option value="boleta">Boleta</option>
										<option value="notaventa">Nota venta</option>
										<option valgit ue="recibo">Recibo</option>
										<option value="otros">Otros</option>
									</select>
								</div>
								<div class="col-sm-3">
									<label class="control-label" for="nrocomprobantepro">Nro Comprobante</label>
									<input class="form-control" name="" id="nrocomprobantepro">
								</div>

								<div class="col-sm-3">
									<label class="control-label" for="preciopro">Valor Compra</label>
									<input class="form-control" step="any" oninput="changepeso(this)" type="number" name="" id="preciopro">
								</div>
							</div>
						</div>
						<div class="row" style="margin-top:20px">
							<table class="table">
								<thead>
									<th>Nº</th>
									<th>Cantidad</th>
									<th>Producto</th>
									<th>Marca</th>
									<th id="varTypeExtra" width="120px">Peso</th>
									<th width="100px">Imp Ind</th>
									<th width="100px">Importe</th>
								</thead>
								<tbody id="detalleProrrateo">
								</tbody>
							</table>
						</div>
						<button class="btn btn-primary" type="submit">Guardar</button>
						<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cerrar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

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
		loadCuentas();
	}
	const loadCuentas = async () => {
		const rescombo = await get_data_dynamic(`SELECT id, CONCAT(descripcion, ' - ', codigo) as descripcion FROM plancontable`);

		cargarselect2("#cuentax", rescombo, 'id', 'descripcion');
	}


	async function onchangeproveedor(e) {
		if (tipocompra.value === 'servicios') {
			
			let res = [];

			if (e.value === "0") {
				const query = "SELECT codigoserv as id, nombre_servicio as descripcion FROM servicios_add where estado=0";
				res = await get_data_dynamic(query).then(r => r);
				res = res.map(x => {
					return {
						descripcion: x.descripcion,
						id: x.id,
						tipo: 'servicios'
					}
				});
			} else if (e.value !== "0") {
				res = [
					{
						id: 0,
						descripcion: 'TRANSPORTE',
						tipo: 'costeo'
					},
					{
						id: 1,
						descripcion: 'ESTIBADOR',
						tipo: 'costeo'
					},
					{
						id: 2,
						descripcion: 'NOTADEBITO',
						tipo: 'costeo'
					},
					{
						id: 3,
						descripcion: 'NOTACREDITO',
						tipo: 'costeo'
					},
				]
			}
			res.unshift({
				descripcion: "Seleccione",
				id: ""
			})
			cargarselect2("#prouctosaux", res, "id", "descripcion", ["tipo"]);
		}

	}

	const loadCosteo = async () => {

		const rescombo = await get_data_dynamic(`
		SELECT prov.ruc, prov.codigoproveedor as codigoclienten, rc.codigorc, CONCAT(prov.razonsocial, ' ', prov.ruc, ' ', rc.tipo_comprobante, ' - ', rc.numerocomprobante, ' - ' , rc.fecha, ' - ', rc.total, ' ', rc.tipomoneda) as descripcion FROM proveedor prov inner join registro_compras rc on rc.codigoproveedor = prov.codigoproveedor WHERE estado = 0 and prov.razonsocial not like  '%inventario%' order by prov.razonsocial`);

		rescombo.unshift({
			codigoclienten: 0,
			descripcion: 'SIN COSTEO'
		});

		cargarselect2("#proveedorx", rescombo, 'codigoclienten', 'descripcion', ["ruc, codigoproveedor", "codigorc"]);
	}

	const agregarproducto = () => {
		if (!prouctosaux.value) {
			return;
		}
		const tipo = prouctosaux.options[prouctosaux.selectedIndex].dataset.tipo;
		const descripcion = prouctosaux.options[prouctosaux.selectedIndex].textContent;
		const id = prouctosaux.value;
		if (document.querySelector(`#id_${id}.${tipo}`)) {
			alert("El insumo/servicio ya fue agregado.");
			return;
		}
		const newtr = document.createElement("tr");
		newtr.dataset.tipo = tipo;
		newtr.dataset.id = id;
		newtr.id = `id_${id}`;
		newtr.className = `rowproducto divparent ${tipo}`;

		newtr.innerHTML += `
			<td width="15%">
				<input required type="number" class="form-control cantidad" data-type="cantidad" value="1" oninput="calcularimporte(this)">
			</td>
			<td width="40%">
				<input disabled value="${descripcion}" required type="text" class="form-control producto">
			</td>
			<td width="15%">
				<input required type="number" step="any" class="form-control precio" data-type="precio" value="0" oninput="calcularimporte(this)">
			</td>
			<td width="15%">
				<input required type="text" class="form-control importe" value="0" disabled>
			</td>
			<td width="15%" class="text-center">
				<button type="button" onclick="eliminarproducto(this)" class="btn red-thunderbird btn-sm"><i class="glyphicon glyphicon-trash"></i></button>
			</td>
		`;
		detalleFormProducto.appendChild(newtr);
	}
	const changetipo = async e => {
		// parentcosteo.style.display = "none";

		let res = [];
		clearselect2("#prouctosaux");
		clearselect2("#proveedorx");

		let resproveedor = [];

		if (e.value === "insumos") {
			labelproveedor.textContent = "Proveedor";
			parentproveedoradicional.style.display = "none";
			const query = "select i.codigoins, i.nombre_insumo, m.nombre, p.nombre_presentacion, c.nombre_color dd from insumo i inner join marca m on i.codigomarca=m.codigomarca inner join presentacion p on p.codigopresent=i.codigopresent inner join color c on c.codigocolor=i.codigocolor";
			res = await get_data_dynamic(query).then(r => r);
			res = res.map(x => {
				return {
					descripcion: `${x.nombre_insumo} ${x.nombre} ${x.nombre_presentacion} ${x.dd}`,
					id: x.codigoins,
					tipo: 'insumos'
				}
			});
			
			loadproveedor(false).then(r => r);
		} else if (e.value === "servicios") {
			labelproveedor.textContent = "Fijar hoja de costeo";
			parentproveedoradicional.style.display = "";
			await loadCosteo();

			const query = "SELECT codigoserv as id, nombre_servicio as descripcion FROM servicios_add where estado=0";
			res = await get_data_dynamic(query).then(r => r);
			res = res.map(x => {
				return {
					descripcion: x.descripcion,
					id: x.id,
					tipo: 'servicios'
				}
			});

		}
		res.unshift({
			descripcion: "Seleccione",
			id: ""
		})
		cargarselect2("#prouctosaux", res, "id", "descripcion", ["tipo"]);
		detalleFormProducto.innerHTML = "";
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

		// const querymaxcode = `SELECT IFNULL(max(contamesincrement), 0) maxcodigo FROM registro_compras WHERE fecha BETWEEN '${firstday}' and LAST_DAY('${firstday}')`;
		// const result = await get_data_dynamic(querymaxcode);
		// const maxcodigo = result[0].maxcodigo;
		// const codeconcat = `${codigomesconta}${maxcodigo}`

		const rucproveedor = proveedoradicional.options[proveedoradicional.selectedIndex].dataset.ruc;
		const codproveedor = proveedorx.value;

		const codigorc = parseInt(proveedorx.options[proveedorx.selectedIndex].dataset.codigorc);

		const codacceso = <?= $_SESSION['kt_login_id'] ?>;
		const sucursal = <?= $codsucursal ?>;
		const subtotalh = subtotalheader.textContent;
		const igvh = igvheader.textContent;
		const totalh = totalheader.textContent;

		const fecha = fechaxxx;
		const cuenta = cuentax.value;

		const tipocomprr = tipocompra.value === "servicios" && proveedorx.value != "0" ? "costeo" : tipocompra.value;

		data.header = `
			insert into insumoservicio (tipo_comprobante, rucproveedor, numerocomprobante, codacceso, subtotal, igv, total, estado, codigosuc, fecha_registro, codigoproveedor, tipo, cuenta) values ('${comprobantex.value}', '${rucproveedor}', '${nrocomprobante.value}', ${codacceso}, ${subtotalh}, ${igvh}, ${totalh}, 'REGISTRADO', ${sucursal}, NOW(), ${codproveedor}, '${tipocomprr}', ${cuenta})
		`;
		getSelectorAll("#detalleFormProducto tr").forEach(x => {
			const producto = x.querySelector(".producto").value;

			const cantidad = x.querySelector(".cantidad").value;
			const idproducto = parseInt(x.dataset.id);
			const tipo = parseInt(x.dataset.tipo);
			const precio = x.querySelector(".precio").value;
			const subtotal = precio * cantidad;
			const total = subtotal * IGV1;
			const igv = subtotal * IGV;
			const query = `
				insert into detalleinsumoservicio(tipo, idproducto, cantidad, vcu, vci, vcf, igv, totalcompra, idinsumoservicio) values ('${tipocomprr}', ${idproducto}, ${cantidad}, ${precio}, ${subtotal}, ${subtotal}, ${igv}, ${total}, ###ID###)
			`;
			data.detalle.push(query);

			if (tipocomprr === "costeo") {
				if (producto === "ESTIBADOR") {
					const queryest =
						`insert into estibador_compra 
						(tipocomprobante, numerocomprobante, rucestibador, moneda, tipocambio, precioestibador_soles, precioestibador_dolar, codigocompras) 
						values 
						('${comprobantex.value}', '${nrocomprobante.value}', '${rucproveedor}', 'soles', 0, ${precio}, 0, ${codigorc})`;
					data.detalle.push(queryest);
				} else if (producto === "NOTADEBITO") {
					const query =
						`insert into notadebito_compra 
					(tipocomprobante, numerocomprobante, rucnd, moneda, tipocambio, preciond_soles, preciond_dolar, codigocompras, porpagar) 
					values 
					('${comprobantex.value}', '${nrocomprobante.value}', '${rucproveedor}', 'soles', 0, ${precio}, 0, ${codigorc}, 1)`;
				} else if (producto === "NOTACREDITO") {
					const query =
						`insert into notacredito_compra 
						(tipocomprobante, numerocomprobante, rucnotacredito, moneda, tipocambio, precionc_soles, precionc_dolar, codigocompras) 
						values 
						('${comprobantex.value}', '${nrocomprobante.value}', '${rucproveedor}', 'soles', 0, ${precio}, 0, ${codigorc})`;
					data.detalle.push(query);
				} else if (producto === "TRANSPORTE") {
					const query =
						`insert into transporte_compra 
						(tipo_transporte, tipocomprobante, numerocomprobante, ructransporte, moneda, tipocambio, preciotransp_soles, preciotransp_dolar, codigocompras) 
						values 
						('', '${comprobantex.value}', '${nrocomprobante.value}', '${rucproveedor}', 'soles', 0, ${precio}, 0, ${codigorc})`
					data.detalle.push(query);
				}
			}
		});

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
	const loadproveedor = async (first = true) => {
		const query = "SELECT ruc, codigoproveedor as codigoclienten, CONCAT(razonsocial, ' ', ruc) as name FROM proveedor  WHERE estado = 0 and razonsocial not like '%INVENTARIO%' order by razonsocial";
		const arraydata = await get_data_dynamic(query);
		cargarselect2("#proveedorx", arraydata, "codigoclienten", "name", ["name", "ruc"])
		if (first)
			cargarselect2("#proveedoradicional", arraydata, "codigoclienten", "name", ["name", "ruc"])
	}
</script>