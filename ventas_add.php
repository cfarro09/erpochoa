<?php $total = 0;
$validarc = 1; ?>
<?php require_once('Connections/Ventas.php'); ?>
<?php


mysql_select_db($database_Ventas, $Ventas);

$query_Clientes = "SELECT codigoclienten, CONCAT(paterno,  ' ', materno, ' ', nombre, ' ',cedula) as ClienteNatural  FROM cnatural  WHERE estado = 0";
$Clientes = mysql_query($query_Clientes, $Ventas) or die(mysql_error());
$row_Clientes = mysql_fetch_assoc($Clientes);
$totalRows_Clientes = mysql_num_rows($Clientes);



//Titulo e icono de la pagina
$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Ventas";
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

$codsucursal = $_SESSION['cod_sucursal'];

$query_Productos = "
select k.codigoprod, k.saldo, p.nombre_producto, m.nombre as Marca, c.nombre_color,  pv.precioventa1 as p1, pv.precioventa2 as p2, pv.precioventa3 as p3, pv.totalunidad
from kardex_contable k
inner join producto p on p.codigoprod = k.codigoprod
inner join marca m on m.codigomarca = p.codigomarca
inner join `color` `c` on(p.codigocolor = c.codigocolor)
inner join precio_venta pv on pv.codigo_pv = (select max(pv2.codigo_pv) from precio_venta pv2 where pv2.codigoprod = k.codigoprod)
where k.sucursal = $codsucursal and saldo > 0
and k.id_kardex_contable in
(select max(id_kardex_contable) from kardex_contable where sucursal = $codsucursal group by codigoprod)";

$Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
$row_Productos = mysql_fetch_assoc($Productos);
$totalRows_Productos = mysql_num_rows($Productos);

//________________________________________________________________________________________________________________
$querySucursales = "select * from sucursal where estado = 1 or estado = 999";
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);

?>

<form id="form-generate-venta">
	<div class="row">
		<div class="col-sm-12 text-center">
			<button class="btn btn-success" type="submit" style="margin-top:10px;margin-bottom: 10px; font-size: 20px">VENTA<br>
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
						<label for="field-1" class="control-label">Cliente</label>
						<select name="cliente" required id="cliente" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar cliente">
							<option value="">Seleccione tipo de comprobante</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="field-1" class="control-label">Sucursal</label>
						<select name="sucursal" required id="sucursal-oc-new" disabled class="form-control ">
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

				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">Tipo Comprobante</label>
						<select required class="form-control" id="tipocomprobante" onchange="setcombocliente(this)">
							<option value="factura">Factura</option>
							<option value="boleta">Boleta</option>
							<option value="recibo">Recibo</option>
							<option value="otros">Otros</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">Codigo Comprobante</label>
						<input type="text" class="form-control" id="codigocomprobante">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">Modalidad Entrega</label>
						<select required class="form-control" id="modalidadentrega">
							<option value="Entrega inmediata S/G">Entrega Inmediata S/G</option>
							<option value="Entrega inmediata C/G">Entrega Inmediata C/G</option>
							<option value="Entrega almacen C/G">Entrega desde Almacen C/G</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">Forma Pago</label>
						<select required onchange="changemodopago(this)" class="form-control" id="formpago">
							<option value="unico">Unico</option>
							<option value="compuesto">Compuesto</option>
						</select>
					</div>
				</div>
				<div class="col-md-4" style="display: none">
					<div class="form-group">
						<label for="field-1" class="control-label">Monto Pagado</label>
						<input type="number" readonly step="any" class="form-control" id="montopagado">
					</div>
				</div>
				<div class="col-md-12 text-center" id="divparentpayextra" style="margin-top: 10px; margin-bottom: 10px; display: none">
					<button class="btn btn-success" type="button" onclick="addPayExtra()">Agregar Pago</button>
				</div>
				<div style="margin-bottom: 10px" id="containerpayextra">
				</div>

			</div>
		</div>
	</div>
	<div class="row" style="display: none">
		<div class="col-sm-12 text-center">
			<button class="btn btn-success" type="submit" id="generateCompra" style="margin-top:10px;margin-bottom: 10px; font-size: 20px">VENTA</button>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<label class="" style="font-weight: bold">Seleccione un producto</label>
			<select id="codigoprod" class="form-control select2-allow-clear" name="codigoprod">
				<option value="" <?php if (!(strcmp("", "compras_add.php"))) {
										echo "selected=\"selected\"";
									} ?>>
				</option>
				<?php
				do {
				?>
					<option value="<?php echo $row_Productos['codigoprod'] ?>" data-preciocompra="<?= $row_Productos['totalunidad'] ?>" data-precioventa="<?= $row_Productos['p2'] ?>" data-stock="<?= $row_Productos['saldo'] ?>" data-nombre="<?php echo $row_Productos['nombre_producto'] ?>" data-marca="<?= $row_Productos['Marca']; ?>">
						<?php echo $row_Productos['nombre_producto'] ?> -
						<?php echo $row_Productos['Marca']; ?> -
						<?php echo $row_Productos['nombre_color']; ?> -
						<?php echo "$/." . $row_Productos['p2']; ?> -
						(<?= "Stock " . $row_Productos['saldo']; ?>)</option>
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
	<div class="row" style="margin-top:20px">
		<div class="col-sm-12">
			<table class="table">
				<thead>
					<th>Nº</th>
					<th>Cantidad</th>
					<th>U. Medida</th>
					<th>Producto</th>
					<th>Marca</th>
					<th>Precio Venta</th>
					<th>Importe</th>
					<th>Accion</th>
				</thead>
				<tbody id="detalleFormProducto">
				</tbody>
			</table>
		</div>
	</div>
	<div class="row" style="background-color:antiquewhite; font-weight: bold; height: 50px; padding-top:15px" id="header-guia">
		<input type="hidden" id="totalpreciocompra">

		<div class="col-sm-4">
			SUBTOTAL: <span id="subtotal-header"></span>
		</div>
		<div class="col-sm-4">
			IGV: <span id="igv-header"></span>
		</div>
		<div class="col-sm-4">
			TOTAL: <span id="total-header"></span>
		</div>
	</div>
</form>
<?php
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");


?>

<script type="text/javascript">
	$(document).ready(function() {
		addPayExtra();
		getSelector(".containerx").firstElementChild.style.display = "none"
		getSelector(".containerx").style.border = "none"
	});

	function changemodopago(e) {
		if (e.value == "unico") {
			getSelector(".montoextra").value = 0;
			divparentpayextra.style.display = "none";
			let ii = 0;
			getSelectorAll(".containerx").forEach(ix => {
				if (ii != 0) {
					ix.remove();
				}
				ii++;
			});
		} else {
			divparentpayextra.style.display = "";
		}
	}

	function calcularmontopagado() {
		let total = 0;
		if (getSelectorAll(".montoextra")) {
			getSelectorAll(".montoextra").forEach(ee => {
				total += ee.value ? parseFloat(ee.value) : 0;
			})
		}
		montopagado.value = total;
	}

	function changeTipoPago(e) {
		if (e.value == "contado")
			getSelectorAll(".tarjetaso").forEach(i => i.style.display = "none")
		else
			getSelectorAll(".tarjetaso").forEach(i => i.style.display = "")
	}

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
		if (getSelector(`.codigo_${this.value}`)) {

		} else {
			const option = this.options[this.selectedIndex]
			const cantrows = document.querySelectorAll("#detalleFormProducto tr").length + 1
			$("#detalleFormProducto").append(`
				<tr class="producto">
				<input type="hidden" class="pcompra" value="${option.dataset.preciocompra}">
				<td data-codigo="${this.value}" class="codigopro codigo_${this.value}" style="display: none">${this.value}</td>
				<td class="indexproducto">${cantrows}</td>
				<td><input type="number" data-type="cantidad" data-stock="${option.dataset.stock}" oninput="changevalue(this)" required class="cantidad tooltips form-control" value="0" style="width: 80px" data-placement="top" data-original-title="Stock: ${option.dataset.stock}"></td>
				<td>
				<select class="form-control unidad_medida" name="unidad_medida" required>
				<option value="unidad">unidad</option>
				<option value="kilo">kilo</option>
				<option value="tonelada">tonelada</option>
				</select>
				</td>
				<td class="nombre">${option.dataset.nombre}</td>
				<td class="marca">${option.dataset.marca}</td>
				<td style="width: 100px"><input type="text" oninput="changevalue(this)" required value="${option.dataset.precioventa}" class="precio tooltips form-control" data-placement="top" data-original-title="P. Compra: ${option.dataset.preciocompra}"></td>
				<td class="importe">0</td>
				<td>
				<button type="button" onclick="eliminarproducto(this)" class="btn red-thunderbird btn-sm tooltips" data-placement="top"  data-original-title="Eliminar Producto"><i class="glyphicon glyphicon-trash"></i></button>
				</td>
				</tr>
				`)
			$('[data-toggle="tooltip"]').tooltip()
			$('.tooltips').tooltip();
		}

	});

	function changevalue(e) {
		if (e.value < 0 || "" == e.value) {
			e.value = 0
		} else {
			if (e.dataset.type == "cantidad") {
				if (parseInt(e.dataset.stock) < parseInt(e.value)) {
					e.value = 0
				}
			}
			const precio = parseFloat(e.closest(".producto").querySelector(".precio").value);
			const cantidad = parseInt(e.closest(".producto").querySelector(".cantidad").value);

			const mu = precio * cantidad
			const res = mu.toFixed(2)

			e.closest(".producto").querySelector(".importe").textContent = res
			let total = 0;
			let totalpc = 0;
			getSelectorAll(".producto").forEach(p => {
				total += parseFloat(p.querySelector(".importe").textContent);
				totalpc += (parseFloat(p.querySelector(".pcompra").value) * parseInt(p.querySelector(".cantidad").value));
			})
			if (total != 0) {
				totalpreciocompra.value = (totalpc * 1.18).toFixed(3);
				total = parseFloat(total)
				getSelector("#subtotal-header").textContent = (total / 1.18).toFixed(3);
				getSelector("#total-header").textContent = (total).toFixed(3);
				getSelector("#igv-header").textContent = (total - total / 1.18).toFixed(3);

				if (formpago.value == "unico") {
					getSelector(".montoextra").value = (total).toFixed(3);
				} else {
					getSelector(".montoextra").value = 0
				}
			} else {
				totalpreciocompra.value = 0;

				getSelector("#subtotal-header").textContent = 0;
				getSelector("#total-header").textContent = 0;
				getSelector("#igv-header").textContent = 0;
			}
		}

	}

	function addPayExtra() {
		const newxx = document.createElement("div");
		newxx.className = "col-md-12 containerx";
		newxx.style = "border: 1px solid #cdcdcd; padding: 5px; margin-bottom: 5px";

		newxx.innerHTML += `
		<div class="text-right">
		<button type="button" class="btn btn-danger" onclick="removecontainerpay(this)">Cerrar</button>
		</div>

		<div class="col-md-2">
		<div class="form-group">
		<label class="control-label">Tipo Pago</label>
		<select onchange="changetypepago(this)" class="form-control tipopago">
		<option value="">[Seleccione]</option>
		<option value="efectivo">Efectivo</option>
		<option value="depositobancario">Deposito Bancario</option>
		<option value="tarjetadebito">Tarjeta Debito</option>
		<option value="tarjetacredito">Tarjeta Credito</option>
		<option value="cheque">Cheque</option>
		<option value="porcobrar">Por cobrar</option>
		</select>
		</div>
		</div>

		<div style="display: none" class="col-md-2 inputxxx depositobancario cheque tarjetacredito tarjetadebito">
		<div class="form-group">
		<label class="control-label">Banco</label>
		<select class="form-control bancoextra">
		<option value="BANCO AZTECA">BANCO AZTECA</option>
		<option value="BANCO BCP">BANCO BCP</option>
		<option value="BANCO CENCOSUD">BANCO CENCOSUD</option>
		<option value="BANCO DE LA NACION">BANCO DE LA NACION</option>
		<option value="BANCO FALABELLA">BANCO FALABELLA</option>
		<option value="BANCO GNB PERÚ">BANCO GNB PERÚ</option>
		<option value="BANCO MI BANCO">BANCO MI BANCO</option>
		<option value="BANCO PICHINCHA">BANCO PICHINCHA</option>
		<option value="BANCO RIPLEY">BANCO RIPLEY</option>
		<option value="BANCO SANTANDER PERU">BANCO SANTANDER PERU</option>
		<option value="BANCO SCOTIABANK">BANCO SCOTIABANK</option>
		<option value="CMAC AREQUIPA">CMAC AREQUIPA</option>
		<option value="CMAC CUSCO S A">CMAC CUSCO S A</option>
		<option value="CMAC DEL SANTA">CMAC DEL SANTA</option>
		<option value="CMAC HUANCAYO">CMAC HUANCAYO</option>
		<option value="CMAC ICA">CMAC ICA</option>
		<option value="CMAC LIMA">CMAC LIMA</option>
		<option value="CMAC MAYNA">CMAC MAYNA</option>
		<option value="CMAC PAITA">CMAC PAITA</option>
		<option value="CMAC SULLANA">CMAC SULLANA</option>
		<option value="CMAC TRUJILLO">CMAC TRUJILLO</option>
		</select>
		</div>
		</div>

		<div style="display: none" class="efectivo col-md-2 inputxxx depositobancario cheque tarjetacredito tarjetadebito porcobrar">
		<div class="form-group">
		<label class="control-label">Monto</label>
		<input type="number" step="any" oninput="calcularmontopagado()" class="form-control montoextra">
		</div>
		</div>

		<div style="display: none" class="col-md-2 inputxxx cheque tarjetacredito tarjetadebito">
		<div class="form-group">
		<label class="control-label">Numero</label>
		<input type="number" class="form-control numero">
		</div>
		</div>

		<div style="display: none" class="col-md-2 inputxxx depositobancario cheque">
		<div class="form-group">
		<label class="control-label">Cuenta Corriente</label>
		<input type="text" class="form-control cuentacorriente">
		</div>
		</div>


		<div style="display: none" class="col-md-2 inputxxx depositobancario">
		<div class="form-group">
		<label class="control-label">Numero Operacion</label>
		<input type="text"  class="form-control numerooperacion">
		</div>
		</div>

		<div style="display: none" class="col-md-2 inputxxx depositobancario">
		<div class="form-group">
		<label class="control-label">Fecha</label>
		<input type="text" class="form-control form-control-inline input-medium date-picker fechaextra" data-date-format="yyyy-mm-dd" readonly autocomplete="off">
		</div>
		</div>

		<div style="display: none" class="col-md-2 inputxxx depositobancario">
		<div class="form-group">
		<label class="control-label">Cta Abonado</label>
		<input type="text" class="form-control cuentaabonado">
		</div>
		</div>
		`;
		containerpayextra.appendChild(newxx);

		$('.date-picker').datepicker({
			rtl: App.isRTL(),
			autoclose: true
		});
	}

	function changetypepago(e) {
		e.closest(".containerx").querySelectorAll(".inputxxx").forEach(ix => ix.style.display = "none");
		e.closest(".containerx").querySelectorAll("." + e.value).forEach(ix => ix.style.display = "");

	}

	function removecontainerpay(e) {
		e.closest(".containerx").remove()
		calcularmontopagado();
	}

	function eliminarproducto(e) {
		e.closest(".producto").remove()
		var i = 1;
		getSelectorAll(".producto").forEach(p => {
			p.querySelector(".indexproducto").textContent = i;
			i++;
		})
		const precio = parseFloat(e.closest(".producto").querySelector(".precio").value);
		const cantidad = parseInt(e.closest(".producto").querySelector(".cantidad").value);

		const mu = precio * cantidad
		const res = mu.toFixed(2)

		e.closest(".producto").querySelector(".importe").textContent = res
		let total = 0;
		let totalpc = 0;
		getSelectorAll(".producto").forEach(p => {
			total += parseFloat(p.querySelector(".importe").textContent);
			totalpc += (parseFloat(p.querySelector(".pcompra").value) * parseInt(p.querySelector(".cantidad").value));
		})
		if (total != 0) {
			totalpreciocompra.value = (totalpc * 1.18).toFixed(3);
			total = parseFloat(total)
			getSelector("#subtotal-header").textContent = (total / 1.18).toFixed(3);
			getSelector("#total-header").textContent = (total).toFixed(3);
			getSelector("#igv-header").textContent = (total - total / 1.18).toFixed(3);

			if (formpago.value == "unico") {
				getSelector(".montoextra").value = (total).toFixed(3);
			} else {
				getSelector(".montoextra").value = 0
			}
		} else {
			totalpreciocompra.value = 0;

			getSelector("#subtotal-header").textContent = 0;
			getSelector("#total-header").textContent = 0;
			getSelector("#igv-header").textContent = 0;
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

	async function setcombocliente(e) {
		clearselect2("#cliente")
		const query = "SELECT codigoclienten as codigo, CONCAT(paterno,  ' ', materno, ' ', nombre, ' ',cedula) as cliente  FROM cnatural  WHERE estado = 0";
		const query2 = "SELECT codigoclientej as codigo, razonsocial as cliente  FROM cjuridico  WHERE estado = 0";
		let queryselected = "";
		if(e.value == "boleta")
			queryselected = query
		else if(e.value == "factura")
			queryselected = query2
		else
			queryselected = query + " UNION " + query2
		
		const res = await get_data_dynamic(queryselected).then(r => r);
		cargarselect2("#cliente", res, "codigo", "cliente");
	}

	function clearselect2(id) {
		getSelector(id).innerHTML = "";
		$(id).val(null).trigger('change');
	}
	const cargarselect2 = (id, arrayres, key, value) =>   {
		getSelector(id).innerHTML = "<option>Seleccione</option>"
		arrayres.forEach(xx => {
			getSelector(id).innerHTML += `<option value="${xx[key]}">${xx[value]}</option>`
		});
		$(id).select2();
	}
	const get_data_dynamic = async (query) => {
		var formData = new FormData();
		formData.append("query", query)
		const response = await fetch("get_data_dynamic2.php", {
			method: 'POST',
			body: formData,
		});
		if (response.ok) {
			try {
				return await response.json();
			} catch (e) {
				alert(e)
			}
		} else {
			alert("hubo un problema")
		}
	};

	getSelector("#form-generate-venta").addEventListener("submit", e => {
		e.preventDefault();
		if (getSelectorAll(".producto").length < 1) {
			alert("Debes agregar almenos un producto")
		} else {
			let totalpagando = 0;
			let pagoacomulado = 0;
			const codigo = makeid(20);
			const data = {};
			let porpagar = 0;
			const pagosextras = [];
			data.detalle = [];
			conpayextra = [];

			const h = {
				tipocomprobante: tipocomprobante.value,
				codigocomprobante: codigocomprobante.value,
				codigoclienten: cliente.value,
				codigoclientej: cliente.value,
				subtotal: getSelector("#subtotal-header").textContent ? getSelector("#subtotal-header").textContent : 0,
				igv: getSelector("#igv-header").textContent ? getSelector("#igv-header").textContent : 0,
				total: getSelector("#total-header").textContent ? getSelector("#total-header").textContent : 0,
				montofact: getSelector("#total-header").textContent ? getSelector("#total-header").textContent : 0,
				fecha_emision: '<?php echo date("Y-m-d"); ?>',
				hora_emision: '<?php echo date("h:i:s"); ?>',
				codigoacceso: "<?= $_SESSION['kt_login_id']; ?>",
				codigopersonal: "<?php echo $_SESSION['kt_codigopersonal']; ?>",
				estadofact: 1,
				codsucursal: <?= $_SESSION['cod_sucursal'] ?>,
				totalc: totalpreciocompra.value,
				// pagoefectivo: montoefectivo.value ? montoefectivo.value : 0
			}
			let errorxxx = "";
			if (!h.total) {
				alert("debe ingresar una cantidad al producto");
				return;
			}

			getSelectorAll(".containerx").forEach(ix => {
				const pay = {
					bancoextra: ix.querySelector(".bancoextra").value,
					montoextra: ix.querySelector(".montoextra").value ? parseFloat(ix.querySelector(".montoextra").value) : 0,
					numero: ix.querySelector(".numero").value,
					cuentacorriente: ix.querySelector(".cuentacorriente").value,
					numerooperacion: ix.querySelector(".numerooperacion").value,
					fechaextra: ix.querySelector(".fechaextra").value,
					cuentaabonado: ix.querySelector(".cuentaabonado").value,
					tipopago: ix.querySelector(".tipopago").value,
				}

				if (pay.tipopago == "depositobancario" && (!pay.bancoextra || !pay.montoextra || !pay.cuentacorriente || !pay.numerooperacion || !pay.fechaextra || !pay.cuentaabonado)) {
					errorxxx = "Llena todos los datos de deposito bancario";
					return;
				} else if (pay.tipopago == "cheque" && (!pay.bancoextra || !pay.montoextra || !pay.numero || !pay.cuentacorriente)) {
					errorxxx = "Llena todos los datos de cheque";
					return;
				} else if ((pay.tipopago == "tarjetacredito" || pay.tipopago == "tarjetadebito") && (!pay.bancoextra || !pay.montoextra || !pay.numero)) {
					errorxxx = "Llena todos los datos de " + pay.tipopago;
					return;
				}
				if (ix.querySelector(".tipopago").value == "porcobrar")
					porpagar = 1;
				else
					pagoacomulado += pay.montoextra

				totalpagando += pay.montoextra;
				pagosextras.push(pay)
			})
			if (errorxxx) {
				alert(errorxxx);
				return;
			}
			if (parseFloat(h.total) != totalpagando) {
				alert("Los montos no coinciden");
				return;
			}

			data.header = `insert into ventas 
			(tipocomprobante, codigocomprobante, codigoclienten, codigoclientej, subtotal, igv, total, fecha_emision, hora_emision, codacceso, codigopersonal, cambio, montofact, estadofact, totalc, pagoefectivo, jsonpagos, porpagar, pagoacomulado, sucursal, modalidadentrega)
			values
			('${h.tipocomprobante}', '${h.codigocomprobante}', ${h.codigoclienten}, ${h.codigoclientej} , ${h.subtotal}, ${h.igv}, ${h.total}, '${h.fecha_emision}', '${h.hora_emision}', ${h.codigoacceso}, ${h.codigopersonal}, 1, ${h.montofact}, ${h.estadofact}, ${h.totalc}, ${totalpagando}, '${JSON.stringify(pagosextras)}', ${porpagar}, ${pagoacomulado} , ${h.codsucursal}, '${modalidadentrega.value}')
			`
			getSelectorAll(".producto").forEach(item => {
				const d = {
					codigoprod: item.querySelector(".codigopro").dataset.codigo,
					cantidad: item.querySelector(".cantidad").value,
					unidad_medida: item.querySelector(".unidad_medida").value,
					concatenacion: "<?= $_GET['codigo'] ?>" + item.querySelector(".codigopro").dataset.codigo,
					pventa: item.querySelector(".precio").value,
					igv: parseFloat(item.querySelector(".precio").value) * 0.18,
					totalventa: (parseInt(item.querySelector(".cantidad").value) * parseFloat(item.querySelector(".precio").value)).toFixed(4)
				}
				data.detalle.push(`
					insert into detalle_ventas (codigoprod, cantidad, unidad_medida, pventa, codcomprobante, pcompra, codigoventa)
					values
					(${d.codigoprod}, ${d.cantidad}, '${d.unidad_medida}', ${d.pventa}, '${h.codigocomprobante}', 0, ###ID###)
					`);

				data.detalle.push(`
					insert into kardex_contable(codigoprod, fecha, codigocompras, numero, detalle, cantidad, precio, saldo, sucursal, preciototal, tipocomprobante, codigoproveedor)
					values
					(${d.codigoprod}, '${h.fecha_emision}', ###ID###, '${h.codigocomprobante}', 'Ventas', ${d.cantidad}, ${d.pventa}, 
					(select saldo from kardex_contable kc where kc.codigoprod = ${d.codigoprod} and kc.sucursal = ${h.codsucursal} order by kc.id_kardex_contable desc limit 1) - ${d.cantidad}
					, ${h.codsucursal}, ${d.totalventa}, '${h.tipocomprobante}', '${h.codigoclienten}')
					`);

				if (modalidadentrega != "Entrega almacen C/G") {
					data.detalle.push(`
						insert into kardex_alm(codigoprod, codigoguia, numero, detalle, cantidad, saldo, codsucursal, tipo, tipodocumento)

						values
						(${d.codigoprod}, ###ID###, '${h.codigocomprobante}', 'Ventas', ${d.cantidad},  
						(select saldo from kardex_alm kc where kc.codigoprod = ${d.codigoprod} and kc.codsucursal = ${h.codsucursal} order by kc.id_kardex_alm desc limit 1) - ${d.cantidad}
						, ${h.codsucursal}, 'venta', '${h.tipocomprobante}')`);
				}

			})
			var formData = new FormData();
			formData.append("json", JSON.stringify(data))

			fetch(`setVenta.php`, {
					method: 'POST',
					body: formData
				})
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					if (res.success) {
						alert("registro completo!")
						getSelector("#form-generate-venta").reset();
						getSelector("#detalleFormProducto").innerHTML = ""
						location.reload()
					}
				});
		}
	})
</script>