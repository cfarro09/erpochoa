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

$query_Productos = "SELECT pre.nombre_presentacion, k.codigoprod, p.minicodigo, k.saldo, p.nombre_producto, m.nombre as Marca, c.nombre_color, pv.initial, pv.precioventa1 as p1, pv.precioventa2 as p2, pv.precioventa3 as p3, pv.totalunidad
from kardex_alm k
inner join producto p on p.codigoprod = k.codigoprod
inner join marca m on m.codigomarca = p.codigomarca
inner join `color` `c` on(p.codigocolor = c.codigocolor)
left join `presentacion` `pre` on (pre.codigopresent = p.codigopresent)
inner join precio_venta pv on pv.codigo_pv = (select max(pv2.codigo_pv) from precio_venta pv2 where pv2.codigoprod = k.codigoprod)
where k.codsucursal = $codsucursal and saldo > 0
and k.id_kardex_alm in
(select max(id_kardex_alm) from kardex_alm where codsucursal = $codsucursal group by codigoprod)";

$Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
$row_Productos = mysql_fetch_assoc($Productos);
$totalRows_Productos = mysql_num_rows($Productos);

//________________________________________________________________________________________________________________
$querySucursales = "select * from sucursal where estado = 1 or estado = 999";
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);

?>
<div class="modal fade" id="mguia" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document" style="width: 900px">
		<div class="modal-content m-auto">
			<form id="formdataguia" action="">
				<div class="modal-header">
					<h2 class="modal-title">Datos de la Guia Inmediata</h2>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12">
								<div class="col-md-3">
									<div class="form-group">
										<label for="puntollegada" class="control-label">Punto de Llegada</label>
										<input required type="text" class="form-control" id="puntollegada">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="quienrecibe" class="control-label">Quien Recibe</label>
										<input required type="text" class="form-control" id="quienrecibe">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="quienrecoge" class="control-label">Quien recoge</label>
										<input required type="text" class="form-control" id="quienrecoge">
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="col-md-3">
									<div class="form-group">
										<label for="quienrecoge" class="control-label">Nombre Transportista</label>
										<input required type="text" class="form-control" id="nombretransportista">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="quienrecoge" class="control-label">RUC transportista</label>
										<input required type="text" class="form-control" id="ructransportista">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="quienrecoge" class="control-label">Marca U. Transporte</label>
										<input required type="text" class="form-control" id="marcatransporte">
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label for="quienrecoge" class="control-label">N° Placa</label>
										<input required type="text" class="form-control" id="nroplaca">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="quienrecoge" class="control-label">N° Licencia conducir</label>
										<input required type="text" class="form-control" id="nlicencia">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="quienrecoge" class="control-label">Certificado Inscripcion</label>
										<input required type="text" class="form-control" id="certinscripcion">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" aria-label="Close" id="btnguardarguiainmediata">Guardar Guia</button>
					<button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
				</div>
			</form>
		</div>
	</div>
</div>


<form id="form-generate-venta">
	<div class="row">
		<div class="col-sm-12 text-center">
			<button class="btn btn-success" type="submit" style="margin-top:10px;margin-bottom: 10px; font-size: 20px" id="buttonsaveventa">VENTA<br>
				<H5><STRONG>
						(CONFIRMAR)
					</STRONG></H5>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="row" style="margin-top: 10px">
				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">Comprobante</label>
						<select required class="form-control" id="tipocomprobante" onchange="setcombocliente(this)">
							<option value="factura">Factura</option>
							<option value="boleta">Boleta</option>
							<option value="notadebito">Nota Debito</option>
							<option value="notacredito">Nota Credito</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">N° Comprobante</label>
						<input type="text" class="form-control" disabled id="codigocomprobante">
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
				<div class="col-md-6">
					<a class="btn sbold blue " onclick="abre_ventana('Emergentes/cliente_natural_list_add.php',700,570)" data-toggle="modal"> C. Natural <i class="fa fa-plus"></i></a>
					<a class="btn sbold blue " onclick="abre_ventana('Emergentes/cliente_juridico_list_add.php',700,430)" data-toggle="modal"> C. Juridico <i class="fa fa-plus"></i></a>
					<a class="btn sbold green " onclick="getclientes()"> Actualizar </a>

					<div class="form-group">
						<label for="field-1" class="control-label">Cliente</label>
						<select name="cliente" required id="cliente" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar cliente">
						</select>
					</div>

					
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label for="field-1" class="control-label">Entrega</label>
						<select required class="form-control" id="modalidadentrega">
							<option value="Entrega inmediata S/G">Inmediata M/D</option>
							<!-- <option value="Entrega inmediata C/G">Inmediata C/G</option> -->
							<option value="Entrega almacen C/G">Despacho desde Almacen</option>
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
	<input type="hidden" id="tmpcodigoventas">
	<div class="row" style="display: none">
		<div class="col-sm-12 text-center">
			<button class="btn btn-success" type="submit" id="generateCompra" style="margin-top:10px;margin-bottom: 10px; font-size: 20px">VENTA</button>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<label class="" style="font-weight: bold">Seleccione un producto</label>
			<select id="codigoprod" class="form-control select2-allow-clear" name="codigoprod">
				<option value="" 
					<?php if (!(strcmp("", "compras_add.php"))) { echo "selected=\"selected\""; } ?>>
				</option>
				<?php
				do {
				?>
					<option 
						value="<?= $row_Productos['codigoprod'] ?>" 
						data-initial="<?= $row_Productos['initial'] ?>"
						data-preciocompra="<?= $row_Productos['totalunidad'] ?>"
						data-precioventa="<?= $row_Productos['p3'] ?>"

						data-higherpcompra="<?= $row_Productos['totalunidad']*1.18 > $row_Productos['p3'] ? "true" : "false" ?>"

						data-stock="<?= $row_Productos['saldo'] ?>"
						data-namexx="<?= $row_Productos['nombre_presentacion'] ?>"
						data-nombre="<?= $row_Productos['nombre_producto'] ?>" 
						data-marca="<?= $row_Productos['Marca']; ?>"
					>
						<?= $row_Productos['nombre_producto'] ?> -
						<?= $row_Productos['Marca']; ?> -
						<?= $row_Productos['nombre_color']; ?> -
						<?= $row_Productos['minicodigo']; ?> -
						<?= "$/." . $row_Productos['p3']; ?> -
						(<?= "Stock " . $row_Productos['saldo']; ?>)
					</option>
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
			<?= $nombreigv ?>: <span id="igv-header"></span>
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
	let htmlcuentaabonado = "";

	let htmloptionsbancos = "";

	const onloadxx = async () => {
		htmlcuentaabonado = await getcuentaabonados()
		htmloptionsbancos = await getbancos()
		addPayExtra();
		setcombocliente({
			value: "factura"
		})
		getSelector(".containerx").firstElementChild.style.display = "none"
		getSelector(".containerx").style.border = "none";

		changencomprobantebytype();
	}
	$(document).ready(onloadxx());
	
	async function getbancos() {
		let htmloptionsbancos = "";
		const query = 'select nombre_banco from banco where estado = 0';
		const arraycuentaabonado = await get_data_dynamic(query);
		arraycuentaabonado.forEach(x => {
			htmloptionsbancos += `
				<option value="${x.nombre_banco}">${x.nombre_banco}</option>
			`;
		});
		return htmloptionsbancos;
	}

	async function getcuentaabonados() {
		let htmlcuentaabonado1 = "";
		const query = 'SELECT c.id_cuenta, concat(b.nombre_banco, " - ", c.tipo, " - CTA ", c.numero_cuenta, " - ", c.moneda) as description FROM `cuenta` c inner JOIN banco b on c.idcodigobanco=b.codigobanco';
		const arraycuentaabonado = await get_data_dynamic(query);
		arraycuentaabonado.forEach(x => {
			htmlcuentaabonado1 += `
				<option value="${x.id_cuenta}">${x.description}</option>
			`;
		});
		return htmlcuentaabonado1;
	}

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
			getSelectorAll(".montoextra").forEach(x => x.disabled = true)
		} else {
			divparentpayextra.style.display = "";
			getSelectorAll(".montoextra").forEach(x => x.disabled = false)
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
			const initial = option.dataset.initial === "0" || option.dataset.higherpcompra === "true";
			;

			const cantrows = document.querySelectorAll("#detalleFormProducto tr").length + 1
			$("#detalleFormProducto").append(`
				<tr class="producto">
				<input type="hidden" class="pcompra" value="${option.dataset.preciocompra}">
				<td data-codigo="${this.value}" class="codigopro codigo_${this.value}" style="display: none">${this.value}</td>
				<td class="indexproducto">${cantrows}</td>

				<td><input data-type="cantidad" data-typeprod="${option.dataset.namexx}" data-stock="${option.dataset.stock}"  required class="cantidad cantformventas tooltips form-control" style="width: 80px" data-placement="top" data-original-title="Stock: ${option.dataset.stock}"></td>
				
				<td class="unidad_medida">${option.dataset.namexx}</td>

				<td class="nombre" style="color: ${initial ? 'red' : 'black'}">${option.dataset.nombre}</td>
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
		getSelector(".producto:last-child .cantformventas").addEventListener("keydown", validatecantidad)

	});

	function validatecantidad(e) {
		const ll = e.key + ""
		if (`${e.target.value}${ll}`.split(".").length > 2) {
			e.preventDefault();
			return
		}
		if (ll != "Backspace") {
			const regex = /KG|M2/.test(e.target.dataset.typeprod) ? /^[0-9]*\.?[0-9]*$/ : /^\d+$/
			if (!e.key.match(regex)) {
				e.preventDefault();
				return
			}
			e.target.value = `${e.target.value}${ll}`;
			e.preventDefault();
			changevalue(e.target)
		}
	}

	function changevalue(e) {
		if (e.value < 0 || "" == e.value) {
			e.value = ""
		} else {
			if (e.dataset.type == "cantidad") {
				if (parseInt(e.dataset.stock) < parseFloat(e.value)) {
					e.value = "1"
				}
			}
			const precio = parseFloat(e.closest(".producto").querySelector(".precio").value);
			const cantidad = parseFloat(e.closest(".producto").querySelector(".cantidad").value)

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
				totalpreciocompra.value = (totalpc * IGV1).toFixed(3);
				total = parseFloat(total)
				getSelector("#subtotal-header").textContent = (total / IGV1).toFixed(3);
				getSelector("#total-header").textContent = (total).toFixed(3);
				getSelector("#igv-header").textContent = (total - total / IGV1).toFixed(3);

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
		const ff =getSelector("#total-header").textContent ? parseFloat(getSelector("#total-header").textContent) : 0
		getSelectorAll(".comision").forEach(x => {
			x.value = ff*0.05
		})
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
		<label class="control-label">Medio de Pago</label>
		<select onchange="changetypepago(this)" class="form-control tipopago">
		<option value="">[Seleccione]</option>
		
		<option value="efectivo">Efectivo</option>
		<option value="cheque">Cheque</option>
		<option value="depositobancario">Deposito Bancario</option>
		<option value="tarjetadebito">Tarjeta Debito</option>
		<option value="tarjetacredito">Tarjeta Credito</option>
		<option value="porcobrar">Por cobrar</option>
		
		</select>
		</div>
		</div>

		<div style="display: none" class="col-md-2 inputxxx cheque tarjetacredito tarjetadebito">
		<div class="form-group">
		<label class="control-label">Banco</label>
		<select class="form-control bancoextra">
		<option value="BANCO AZTECA">BANCO AZTECA</option>
		${htmloptionsbancos}
		</select>
		</div>
		</div>



		<div style="display: none" class="col-md-2 inputxxx cheque tarjetacredito tarjetadebito">
		<div class="form-group">
		<label class="control-label">Numero</label>
		<input type="number" class="form-control numero">
		</div>
		</div>

		<div style="display: none" class="col-md-2 inputxxx cheque">
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

		<div style="display: none" class="col-md-2 inputxxx depositobancario cheque">
		<div class="form-group">
		<label class="control-label">Fecha</label>
		<input type="text" class="form-control form-control-inline input-medium date-picker fechaextra" data-date-format="yyyy-mm-dd" readonly autocomplete="off">
		</div>
		</div>

		<div style="display: none" class="col-md-2 inputxxx tarjetadebito tarjetacredito depositobancario">
		<div class="form-group">
		<label class="control-label">Cta Abonado</label>
		<select class="form-control cuentaabonado">
		${htmlcuentaabonado}
		</select>
		</div>
		</div>

		<div style="display: none" class="efectivo col-md-2 inputxxx depositobancario cheque tarjetacredito tarjetadebito porcobrar">
			<div class="form-group">
				<label class="control-label">Monto</label>
				<input type="number" step="any" oninput="calcularmontopagado()" class="form-control montoextra">
			</div>
		</div>

		<div style="display: none" class="col-md-2 inputxxx tarjetacredito tarjetadebito">
			<div class="form-group">
				<label class="control-label">Comisión</label>
				<input type="text" step="any" class="form-control comision">
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

		getSelectorAll(".montoextra").forEach(x => x.disabled = formpago.value == "unico" ? true : false)

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
			totalpreciocompra.value = (totalpc * IGV1).toFixed(3);
			total = parseFloat(total)
			getSelector("#subtotal-header").textContent = (total / IGV1).toFixed(3);
			getSelector("#total-header").textContent = (total).toFixed(3);
			getSelector("#igv-header").textContent = (total - total / IGV1).toFixed(3);

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
	async function getclientes() {
		let queryselected = "";
		const query = "SELECT 'natural' as tipo, codigoclienten as codigo, CONCAT(paterno,  ' ', materno, ' ', nombre, ' ',cedula) as cliente  FROM cnatural  WHERE estado = 0";
		const query2 = "SELECT 'juridico' as tipo,  codigoclientej as codigo, razonsocial as cliente  FROM cjuridico  WHERE estado = 0 union SELECT 'natural' as tipo, codigoclienten as codigo, CONCAT(paterno,  ' ', materno, ' ', nombre, ' ', ruc) as cliente  FROM cnatural  WHERE estado = 0 and ruc is not null ";

		if (tipocomprobante.value == "boleta") {
			queryselected = query
			
		} else if (tipocomprobante.value == "factura") {
			queryselected = query2
		} else {
			queryselected = query + " UNION " + query2;
		}
		
		const res = await get_data_dynamic(queryselected).then(r => r);
		cargarselect2("#cliente", res, "codigo", "cliente", ["tipo"]);
	}
	async function setcombocliente(e) {
		clearselect2("#cliente")
		const query = "SELECT 'natural' as tipo, codigoclienten as codigo, CONCAT(paterno,  ' ', materno, ' ', nombre, ' ',cedula) as cliente  FROM cnatural  WHERE estado = 0";
		const query2 = "SELECT 'juridico' as tipo,  codigoclientej as codigo, razonsocial as cliente  FROM cjuridico  WHERE estado = 0 union SELECT 'natural' as tipo, codigoclienten as codigo, CONCAT(paterno,  ' ', materno, ' ', nombre, ' ', ruc) as cliente  FROM cnatural  WHERE estado = 0 and ruc is not null ";
		let queryselected = "";
		if (e.value == "boleta") {
			queryselected = query
			codigoprod.closest(".col-sm-12").style.display = "";
			
		} else if (e.value == "factura") {
			queryselected = query2
			codigoprod.closest(".col-sm-12").style.display = "";
			
		} else {
			if (e.value == "notacredito") {
				codigoprod.closest(".col-sm-12").style.display = "";

				detalleFormProducto.innerHTML = `
					<tr class="producto" data-type="notacredito">
						<input type="hidden" class="pcompra" value="1">
						
						<td data-codigo="${this.value}" class="codigopro codigo_${this.value}" style="display: none">${this.value}
						</td>
						<td class="indexproducto">1</td>
						<td>
							<input type="number" data-type="cantidad" data-stock="0" oninput="changevalue(this)" required class="cantidad tooltips form-control" value="1" disabled style="width: 80px">
						</td>
						<td style="display: none">
							<select class="form-control unidad_medida" name="unidad_medida"  required>
								<option selected value="unidad">unidad</option>
								<option value="kilo">kilo</option>
								<option value="tonelada">tonelada</option>
							</select>
						</td>
						<td colspan="3">
							<input type="text" placeholder="Detalle" class="form-control" id="detallenotaaux">
						</td>
						<td style="width: 100px"><input type="text" oninput="changevalue(this)" required value="0" class="precio form-control">
						</td>
						<td class="importe">0</td>
						<td>
						</td>
					</tr>`;
			} else {
				codigoprod.closest(".col-sm-12").style.display = "none";
				detalleFormProducto.innerHTML = `
				<tr class="producto" data-type="notadebito">
					<input type="hidden" class="pcompra" value="1">
					
					<td data-codigo="${this.value}" class="codigopro codigo_${this.value}" style="display: none">${this.value}
					</td>
					<td class="indexproducto">1</td>
					<td>
						<input type="number" data-type="cantidad" data-stock="0" oninput="changevalue(this)" required class="cantidad tooltips form-control" value="1" disabled style="width: 80px">
					</td>
					<td style="display: none">
						<select class="form-control unidad_medida" name="unidad_medida"  required>
							<option selected value="unidad">unidad</option>
							<option value="kilo">kilo</option>
							<option value="tonelada">tonelada</option>
						</select>
					</td>
					<td colspan="3">
						<input type="text" placeholder="Detalle" class="form-control" id="detallenotaaux">
					</td>
					<td style="width: 100px"><input type="text" oninput="changevalue(this)" required value="0" class="precio form-control">
					</td>
					<td class="importe">0</td>
					<td>
					</td>
				</tr>`;
			}
			queryselected = query + " UNION " + query2;
		}
		const res = await get_data_dynamic(queryselected).then(r => r);
		cargarselect2("#cliente", res, "codigo", "cliente", ["tipo"]);

		if (e.value == "notadebito" || e.value == "notacredito") {
			modalidadentrega.value = "Entrega inmediata S/G";
			modalidadentrega.disabled = true;
		} else
			modalidadentrega.disabled = false;

		changencomprobantebytype();
	}
	async function changencomprobantebytype() {
		const codsucursald = <?= $_SESSION['cod_sucursal'] ?>;
		const querycodcc = `(select IFNULL(max(v1.codigocomprobante), 0) + 1 as codcc from ventas v1 where v1.tipocomprobante = '${tipocomprobante.value}' and v1.sucursal = ${codsucursald})`
		const rcodigocomp = await get_data_dynamic(querycodcc).then(r => r);
		codigocomprobante.value = rcodigocomp[0].codcc;
	}

	function imprimir_factura(id) {
		var url = `Imprimir/facturaventa_imprimir.php?id=${id}`;
		window.location = url;
	}
	let h = {};
	getSelector("#form-generate-venta").addEventListener("submit", async e => {

		buttonsaveventa.disabled = true;
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

			const tipocliente = cliente.options[cliente.selectedIndex].dataset.tipo;
			h = {
				tipocomprobante: tipocomprobante.value,
				codigocomprobante: codigocomprobante.value,
				codigoclienten: tipocliente == "natural" ? cliente.value : "null",
				codigoclientej: tipocliente == "juridico" ? cliente.value : "null",
				subtotal: getSelector("#subtotal-header").textContent ? getSelector("#subtotal-header").textContent : 0,
				igv: getSelector("#igv-header").textContent ? getSelector("#igv-header").textContent : 0,
				total: getSelector("#total-header").textContent ? getSelector("#total-header").textContent : 0,
				montofact: getSelector("#total-header").textContent ? getSelector("#total-header").textContent : 0,
				fecha_emision: new Date(new Date().setHours(10)).toISOString().substring(0, 10),
				hora_emision: new Date().toTimeString().substring(0, 8),
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
					fechaxxx: new Date(new Date().setHours(10)).toISOString().substring(0, 10),
					comision: ix.querySelector(".comision").value
				}

				if (pay.tipopago == "depositobancario" && (!pay.montoextra || !pay.numerooperacion || !pay.fechaextra || !pay.cuentaabonado)) {
					errorxxx = "Llena todos los datos de deposito bancario";
					return;
				} else if (pay.tipopago == "cheque" && (!pay.bancoextra || !pay.montoextra || !pay.numero || !pay.cuentacorriente)) {
					errorxxx = "Llena todos los datos de cheque";
					return;
				} else if ((pay.tipopago == "tarjetacredito" || pay.tipopago == "tarjetadebito") && (!pay.bancoextra || !pay.montoextra || !pay.numero || !pay.cuentaabonado)) {
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
			if(getSelector(".tipopago").value == ""){
				alert("Debe ingresar un medio de pago");
				return;
			}

			if (errorxxx) {
				alert(errorxxx);
				return;
			}
			if (parseFloat(h.total) != totalpagando) {
				alert("Los montos no coinciden");
				return;
			}
			pagosextras.filter(x => x.tipopago == "depositobancario" || x.tipopago == "tarjetacredito" || x.tipopago == "tarjetadebito").forEach(x => {
				const querydepbancario = `insert into cuenta_mov (id_cuenta, fecha_trans, tipo_mov, detalle, monto, saldo) VALUES (${x.cuentaabonado}, '${x.fechaextra}', 'DEPOSITO', 'ABONO ${x.tipopago.toUpperCase()}', ${x.montoextra}, 
				(select cm.saldo from cuenta_mov cm where cm.id_cuenta = ${x.cuentaabonado} order by cm.id_cuenta_mov desc limit 1) + ${x.montoextra})`

				data.detalle.push(querydepbancario)
			})

			const proveedor_detalleaux = $('#cliente').select2('data')[0].text;

			data.header = `insert into ventas 
			(tipocomprobante, codigocomprobante, codigoclienten, codigoclientej, subtotal, igv, total, fecha_emision, hora_emision, codacceso, codigopersonal, cambio, montofact, estadofact, totalc, pagoefectivo, jsonpagos, porpagar, pagoacomulado, sucursal, modalidadentrega)
			values
			('${h.tipocomprobante}', ${codigocomprobante.value}, ${h.codigoclienten}, ${h.codigoclientej} , ${h.subtotal}, ${h.igv}, ${h.total}, '${h.fecha_emision}', '${h.hora_emision}', ${h.codigoacceso}, ${h.codigopersonal}, 1, ${h.montofact}, ${h.estadofact}, ${h.totalc}, 0, '${JSON.stringify(pagosextras)}', ${porpagar}, ${pagoacomulado} , ${h.codsucursal}, '${modalidadentrega.value}')
			`

			getSelectorAll(".producto").forEach(item => {
				const d = {
					codigoprod: item.querySelector(".codigopro").dataset.codigo,
					cantidad: item.querySelector(".cantidad").value,
					unidad_medida: item.querySelector(".unidad_medida").textContent,
					concatenacion: "<?= $_GET['codigo'] ?>" + item.querySelector(".codigopro").dataset.codigo,
					pventa: item.querySelector(".precio").value,
					igv: parseFloat(item.querySelector(".precio").value) * IGV,
					totalventa: (item.querySelector(".cantidad").value * parseFloat(item.querySelector(".precio").value)).toFixed(4)
				}
				if (tipocomprobante.value == "notadebito") {
					data.detalle.push(`
					insert into detalle_ventas (codigoprod, cantidad, unidad_medida, pventa, codcomprobante, pcompra, codigoventa, detalleauxiliar)
					values
					(0, ${d.cantidad}, '${d.unidad_medida}', ${d.pventa}, '${h.codigocomprobante}', 0, ###ID###, '${detallenotaaux.value}')
					`);
				} else if (tipocomprobante.value == "notacredito") {
					if (item.dataset.type == "notacredito") {
						data.detalle.push(`
							insert into detalle_ventas (codigoprod, cantidad, unidad_medida, pventa, codcomprobante, pcompra, codigoventa, detalleauxiliar)
							values
							(0, ${d.cantidad}, '${d.unidad_medida}', ${d.pventa}, '${h.codigocomprobante}', 0, ###ID###, '${detallenotaaux.value}')
							`);
					} else {
						data.detalle.push(`
							insert into detalle_ventas (codigoprod, cantidad, unidad_medida, pventa, codcomprobante, pcompra, codigoventa, detalleauxiliar)
							values (${d.codigoprod}, ${d.cantidad}, '${d.unidad_medida}', ${d.pventa}, '${h.codigocomprobante}', 0, ###ID###, '${detallenotaaux.value}')
						`);

						data.detalle.push(`
							insert into kardex_contable(codigoprod, fecha, codigocompras, numero, detalle, cantidad, precio, saldo, sucursal, preciototal, tipocomprobante, codigoproveedor)
							values
							(${d.codigoprod}, '${h.fecha_emision}', ###ID###, '${h.codigocomprobante}', 'Ventas', ${d.cantidad}, ${d.pventa}, 
							(select saldo from kardex_contable kc where kc.codigoprod = ${d.codigoprod} and kc.sucursal = ${h.codsucursal} order by kc.id_kardex_contable desc limit 1) + ${d.cantidad}
							, ${h.codsucursal}, ${d.totalventa}, '${h.tipocomprobante}', '${h.codigoclienten}')
						`);
						debugger
						if (modalidadentrega.value != "Entrega almacen C/G") {
							data.detalle.push(`
							insert into kardex_alm(codigoprod, codigoguia, numero, detalle, cantidad, saldo, codsucursal, tipo, tipodocumento, detalleaux)
							values
							(${d.codigoprod}, ###ID###, '${h.codigocomprobante}', 'Ventas', ${d.cantidad},  
							(select saldo from kardex_alm kc where kc.codigoprod = ${d.codigoprod} and kc.codsucursal = ${h.codsucursal} order by kc.id_kardex_alm desc limit 1) + ${d.cantidad}
							, ${h.codsucursal}, 'venta', '${h.tipocomprobante}', '${proveedor_detalleaux}')`);
						}
					}
				} else {
					data.detalle.push(`
						insert into detalle_ventas (codigoprod, cantidad, unidad_medida, pventa, codcomprobante, pcompra, codigoventa)
						values (${d.codigoprod}, ${d.cantidad}, '${d.unidad_medida}', ${d.pventa}, '${h.codigocomprobante}', 0, ###ID###)
					`);

					data.detalle.push(`
						insert into kardex_contable(codigoprod, fecha, codigocompras, numero, detalle, cantidad, precio, saldo, sucursal, preciototal, tipocomprobante, codigoproveedor)
						values
						(${d.codigoprod}, '${h.fecha_emision}', ###ID###, '${h.codigocomprobante}', 'Ventas', ${d.cantidad}, ${d.pventa}, 
						(select saldo from kardex_contable kc where kc.codigoprod = ${d.codigoprod} and kc.sucursal = ${h.codsucursal} order by kc.id_kardex_contable desc limit 1) - ${d.cantidad}
						, ${h.codsucursal}, ${d.totalventa}, '${h.tipocomprobante}', '${h.codigoclienten}')
					`);

					if (modalidadentrega.value != "Entrega almacen C/G") {
						data.detalle.push(`
						insert into kardex_alm(codigoprod, codigoguia, numero, detalle, cantidad, saldo, codsucursal, tipo, tipodocumento, detalleaux)
						values
						(${d.codigoprod}, ###ID###, '${h.codigocomprobante}', 'Ventas', ${d.cantidad},  
						(select saldo from kardex_alm kc where kc.codigoprod = ${d.codigoprod} and kc.codsucursal = ${h.codsucursal} order by kc.id_kardex_alm desc limit 1) - ${d.cantidad}
						, ${h.codsucursal}, 'venta', '${h.tipocomprobante}', '${proveedor_detalleaux}')`);
					}
				}
			})
			var formData = new FormData();
			const jjson = JSON.stringify(data).replace(/select/g, "lieuiwuygyq")
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


						var opcion = confirm("¿Desea imprimir factura?");
						if (opcion)
							imprimir_factura(res.id);

						if (modalidadentrega.value == "Entrega inmediata C/G") {
							$("#mguia").modal()
							tmpcodigoventas.value = res.id;
						} else {
							setTimeout(() => {
								location.reload();	
							}, 1500);
							
						}
					}
				});
		}
	})

	async function guardarguiainmediata(e) {
		e.preventDefault()
		const data = {};
		data.header = '';
		data.detalle = [];

		const idguia = uuidv4();

		var query = "select value from propiedades where `key` = 'despacho_guia_" + h.codsucursal + "'";
		const resguia = await get_data_dynamic(query).then(r => r);

		const h1 = {
			id: idguia,
			fecha: h.fecha_emision,
			sucursal: h.codsucursal,
			codventa: tmpcodigoventas.value,
			nguia: resguia[0].value,

			puntollegada: puntollegada.value,
			quienrecibe: quienrecibe.value,
			quienrecoge: quienrecoge.value,
			nombretransportista: nombretransportista.value,
			ructransportista: ructransportista.value,
			marcatransporte: marcatransporte.value,
			nroplaca: nroplaca.value,
			nlicencia: nlicencia.value,
			certinscripcion: certinscripcion.value,


			productos: []
		}
		let isdespachado = 1;
		getSelectorAll(".producto").forEach(item => {
			const d = {
				codigoprod: item.querySelector(".codigopro").textContent,
				cantidad: item.querySelector(".cantidad").value,
				canttotal: item.querySelector(".cantidad").value,
				nombre_producto: item.querySelector(".nombre").textContent.replace(/"|'/gi, ''),
				unidad_medida: item.querySelector(".unidad_medida").textContent.trim(),
				pventa: item.querySelector(".precio").value,
			};
			h1.productos.push(d)
		});
		const dataguia = [];
		dataguia.push(h1);
		data.header = `
                UPDATE ventas SET 
                    despachado = ${isdespachado}, 
                    nroguia = ${h1.nguia},
                    dataguia = '${JSON.stringify(dataguia)}'
                WHERE codigoventas=${h1.codventa}`;

		data.detalle.push("UPDATE propiedades SET value = (" + h1.nguia + "+1) where `key` = 'despacho_guia_" + h.codsucursal + "'");

		const jjson = JSON.stringify(data).replace(/select/g, "lieuiwuygyq")
		var formData = new FormData();
		formData.append("json", jjson);

		const res = await fetch(`setVenta.php`, {
				method: 'POST',
				body: formData
			})
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => res);
		if (res.success) {
			alert("registro completo!");
			var url = `Imprimir/guia_imprimir.php?idventas=${parseInt(h1.codventa)}&idguia=${h1.id}`;
			window.location = url;
			getSelector("#formdataguia").reset();
			getSelector("#form-generate-venta").reset();
			getSelector("#detalleFormProducto").innerHTML = "";
			buttonsaveventa.disabled = false;

			puntollegada.value = "";
			quienrecibe.value = "";
			quienrecoge.value = "";
			nombretransportista.value = "";
			ructransportista.value = "";
			marcatransporte.value = "";
			nroplaca.value = "";
			nlicencia.value = "";
			certinscripcion.value = "";


			$("#mguia").modal("hide")
			setTimeout(() => {
				location.reload()
			}, 1500);
			// location.reload();
		}
	}

	formdataguia.addEventListener('submit', guardarguiainmediata)
</script>