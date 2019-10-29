<?php require_once('Connections/Ventas.php'); ?>
<?php

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "select c.codigorc, 0 as totalv, c.tipomoneda, c.tipo_comprobante, p.razonsocial, c.fecha,c.numerocomprobante,a.usuario, c.subtotal, c.total, c.estadofact, s.nombre_sucursal  from registro_compras c left join sucursal s on s.cod_sucursal = c.codigosuc left join proveedor p on p.codigoproveedor = c.codigoproveedor inner join acceso a on a.codacceso=c.codacceso";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas



//Titulo e icono de la pagina
$Icono="fa fa-building-o";
$Color="font-blue";
$Titulo="Historial de Compras";
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

<h2>PRECIO VENTA</h2>
<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty ?>
	<div class="alert alert-danger">
		<strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
	</div>
<?php } // Show if recordset empty ?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty ?>

	<table class="table table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th> N&deg; </th>
				<th>N° COMPR</th>
				<th>TIPO COMPR </th>
				<th style="display: none">TIPO COMPR </th>
				<th style="display: none">TIPO COMPR </th>
				<th>PROVEEDOR</th>
				<th>T COMPRA</th>
				<th>FECHA</th>
				<th>SUCURSAL</th>
				<th>Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1; do { ?>
				<?php //var_dump($row_Listado); die; ?>
				<tr>
					<td><?= $i ?></td>
					<td class="numerocomprobante"><?= $row_Listado['numerocomprobante'] ?></td>
					<td class="tipo_comprobante"><?= $row_Listado['tipo_comprobante'] ?></td>
					<td style="display: none" class="usuario"><?= $row_Listado['usuario'] ?></td>
					<td style="display: none" class="codigorc"><?= $row_Listado['codigorc'] ?></td>
					<td class="razonsocial"><?= $row_Listado['razonsocial'] ?></td>
					<td class="total"><?= $row_Listado['total'] ?></td>
					<td class="fecha"><?= $row_Listado['fecha'] ?></td>
					<td class="nombre_sucursal"><?= $row_Listado['nombre_sucursal'] ?></td>
					<?php if ($row_Listado['estadofact'] == 1): ?>
						<td><a href="#" onclick="managecompra(this)">Asignar</a></td>
						<?php else : ?>
							<td><a href="#" onclick="verprecioventa(this)">Ver</a></td>
						<?php endif ?>

					</tr>
					<?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>

				</tbody>
			</table>
			
			<div class="modal fade" id="mSetPrecioVenta" role="dialog" data-backdrop="static" data-keyboard="false">
				<div class="modal-dialog" role="document" style="width: 1300px">
					<div class="modal-content m-auto">
						<div class="modal-header">
							<h2 class="modal-title" id="moperation-title">Asignar precio venta</h2>
						</div>
						<div class="modal-body">
							<form id="saveOrdenCompra">
								<input type="hidden" id="codigoOrdenCompra">
								<input type="hidden" id="codigoordcomp">
								<input type="hidden" id="codigoguia" value="">
								<div class="container-fluid">

									N° COMPROBANTE: <span id="mnumerocomprobante"></span> <BR>
									TIPO COMPROBANTE: <span id="mtipo_comprobante"></span> <BR>
									PROVEEDOR: <span id="mrazonsocial"></span> <BR>
									TOTAL COMPRA: <span id="mtotal"></span> <BR>
									SUCURSAL: <span id="mnombre_sucursal"></span> <BR>
									FECHA DE EMISION: : <span id="mfecha"></span> <br>
									GENERADA POR: : <span id="musuario"></span> <br><br>


									<input type="hidden" id="codigorc" name="">
									<div class="row" style="margin-top:20px">
										<div class="col-xs-12 col-md-12">

											<table class="table">
												<thead>
													<th>Nº</th>
													<th>Cant</th>
													<th>Producto</th>
													<th>Marca</th>
													<!-- <th>Precio UND</th> -->
													<th>VCU</th>
													<th>IGV</th>
													<th>PCU</th>
													<th class="text-center">% V 1</th>
													<th class="text-center">P V 1</th>
													<th class="text-center">% V 2</th>
													<th class="text-center">P V 2</th>
													<th class="text-center">% V 3</th>
													<th class="text-center">P V 3</th>
												</thead>
												<tbody id="detalleComprax">
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<button class="btn btn-success" type="button" onclick="vercosteo()">VER COSTEO</button>
								<button type="submit" id="btn_save_precioventa1" class="btn btn-success">Guardar</button>
								<button type="button" data-dismiss="modal" class="modal_close btn btn-danger">Cerrar</button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade" id="mFacturaCompra" role="dialog" data-backdrop="static" data-keyboard="false">
				<div class="modal-dialog" role="document" style="width: 1300px">
					<div class="modal-content m-auto">
						<div class="modal-header">
							<h2 class="modal-title" id="">FICHA COSTEO</h2>
						</div>
						<div class="modal-body">
							<form id="saveFacturar">
								<div class="container-fluid">
									<div class="row">
										<div class="col-xs-12 col-md-12">

											<div class="row" style="margin-top: 20px">
												<div class="col-xs-12 col-md-12">
													<div class="row">
														<div class="col-md-2">
															<div class="form-group">
																<label for="field-1" class="control-label">Fecha Emision</label>
																<input type="text" required name="facturafechaemision"
																autocomplete="off" id="facturafechaemision"
																class="form-control form-control-inline input-medium date-picker"
																data-date-format="yyyy-mm-dd" required />
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label for="field-1" class="control-label">Descuento General</label>
																<input type="number" class="form-control" oninput="changedescuentogeneral(this)" step="any" id="descuento" name="">
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label for="field-1" class="control-label">Tipo Comp</label>
																<select class="form-control" name="tipocomprobantefactura" id="tipocomprobantefactura">
																	<option value="factura">Factura</option>
																	<option value="boleta">Boleta</option>
																	<option value="recibo">Recibo</option>
																	<option value="otros">Otros</option>
																</select>
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label for="field-1" class="control-label">Nro Comprobante</label>
																<input type="text" required class="form-control"
																name="nrocomprobante" id="nrocomprobante">
															</div>
														</div>
														<div class="col-md-2 container_moneda">
															<div class="form-group">
																<label for="field-1" class="control-label">Moneda</label>
																<select class="form-control" onchange="selectmoneda(this)" id="moneda" name="moneda" required>
																	<option value="soles">S/</option>
																	<option value="dolares">$</option>
																</select>
															</div>
														</div>
														<div class="col-md-2 container_cambio" id="container_cambio" style="display: none">
															<div class="form-group">
																<label for="field-1" class="control-label">Cambio</label>
																<input type="number" step="any" class="form-control" id="tipocambio" oninput="changecambiodolar(this)" name="">
															</div>
														</div>
													</div>
												</div>
											</div>
											<table class="table">
												<thead>
													<th>Nº</th>
													<th>Cantidad</th>
													<th>Producto</th>
													<th>Marca</th>
													<th class="costeosinchecked" width=" 120px">Desc x Item</th>
													<th class="costeosinchecked" width=" 120px">VCU</th>
													<th class="costeosinchecked" width=" 120px">VCI</th>
													<th class="costeosinchecked" width="120px">DSCTO</th>
													<th width="120px">VCF</th>
													<th class="costeosinchecked" width=" 120px">IGV</th>
													<th class="costeosinchecked" width=" 120px">Total</th>
													<th width="60px" class="costeochecked" style="display: none">Transporte</th>
													<th width="60px" class="costeochecked" style="display: none">Estibador</th>
													<th width="60px" class="costeochecked" style="display: none">Nota Debito</th>
													<th width="60px" class="costeochecked" style="display: none">Nota Credito</th>
													<th width="60px" class="costeochecked" style="display: none">Total</th>
													<th width="60px" class="costeochecked" style="display: none">T. Unidad</th>
												</thead>
												<tbody id="detalleFacturar-list">
												</tbody>
											</table>

										</div>
									</div>
								</div>
								<div class="modal-footer">
									<label for="showcosteo">Mostrar costeo</label>
									<input type="checkbox" onclick="checkcosteo(this)" id="showcosteo">
									<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cerrar</button>
								</div>
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
		<script type="text/javascript">
			function checkcosteo(e) {
				if (e.checked) {
					getSelectorAll(".costeochecked").forEach(e => {
						e.style.display = ""
					})
					getSelectorAll(".costeosinchecked").forEach(e => {
						e.style.display = "none"
					})

				} else {
					getSelectorAll(".costeochecked").forEach(e => {
						e.style.display = "none"
					})

					getSelectorAll(".costeosinchecked").forEach(e => {
						e.style.display = ""
					})
				}
				console.log(e.checked)
			}
			getSelectorAll(".setStatus").forEach(item => {
				item.addEventListener("click", (e) => {
				// getSelector("#check_logistica_edificaciones").checked
				fetch(`editarEstadoOrdenCompra.php?codigo=${getSelector("#codigoOrdenCompra").value}&estado=${e.target.dataset.estado}`)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					alert("Se ace´tó la orden de compra!")
					$("#mOrdenCompra").modal("hide");
				});
			})
			});
			function vercosteo(e){
				descuento.setAttribute("readonly", true)
				facturafechaemision.setAttribute("readonly", true)
				tipocomprobantefactura.setAttribute("readonly", true)
				nrocomprobante.setAttribute("readonly", true)
				moneda.setAttribute("readonly", true)

				$("#mFacturaCompra").modal();
				fetch(`getDetalleCompraCosteo.php?codigorc=${codigorc.value}`)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					if(res && res.header){

						const {fecha_registro, descuentocompras, tipo_comprobante, numerocomprobante, tipomoneda} = res.header;
						facturafechaemision.value = fecha_registro.substring(0,10)
						descuento.value = descuentocompras.substring(0,10)
						$("#tipocomprobantefactura").val(tipo_comprobante)
						moneda.value = tipomoneda
						nrocomprobante.value = numerocomprobante


						i = 0;
						document.querySelector("#detalleFacturar-list").innerHTML = ""
						res.detalle.forEach(r => {
							i++
							$("#detalleFacturar-list").append(`
								<tr>
								<td data-codigo="${r.codigoprod}" class="codigoprod">${i}</td>
								<td class="cantidad">${r.cantidad}</td>
								<td>${r.nombre_producto}</td>
								<td >${r.marca}</td>
								<td class="costeosinchecked"><input readonly type="text" oninput="changedescuento(this)" value="${r.descxitem}" class="form-control descuento solonumeros focusandclean"></td>
								<td class="costeosinchecked"><input readonly id="preciocompra${i}" data-toggle="tooltip"  step="any" data-placement="bottom" title="0" oninput="changepreciocompra(this)" value="${r.vcu}" required type="text" class="solonumeros focusandclean precio-compra form-control"></td>

								<td class="costeosinchecked"><input readonly step="any" data-toggle="tooltip" data-placement="bottom" title="0" oninput="changeimporte(this)" value="${r.vci}" required type="text" class="solonumeros focusandclean importe form-control"></td>

								<td class="costeosinchecked"><input readonly type="text" value=${r.descmonto} readonly class="form-control descuentocantidad"></td>
								<td><input readonly type="text" readonly class="form-control vcf" id="vcf_${i}" value="${r.vcf}"></td>

								<td class="costeosinchecked"><input readonly type="text" readonly class="form-control igvrow" value="${r.igv}"></td>
								<td class="costeosinchecked"><input readonly type="text" readonly value="${r.totalcompra}" class="form-control valorcompra2"></td>

								<td style="display: none" class="costeochecked"><input readonly value="${r.preciotransporte}" id="detalleFactura_${i}" class="form-control transporte_costeo" readonly></td>
								<td style="display: none" class="costeochecked"><input readonly value="${r.precioestibador}" class="form-control estibador_costeo" readonly></td>
								<td style="display: none" class="costeochecked"><input readonly value="${r.notadebito}" class="form-control notadebito" readonly></td>
								<td style="display: none" class="costeochecked"><input readonly value="${r.precionotacredito}" class="form-control notacredito" readonly></td>
								<td style="display: none" class="costeochecked"><input readonly value="${r.totalconadicionales}" class="form-control total_costeo" readonly></td>
								<td style="display: none" class="costeochecked"><input readonly value="${r.totalunidad}" class="form-control totalunidadcosteo" readonly></td>
								</tr>`);
						});
					}else{
						alert("hubo un error")
					}
				})
			}

			function verprecioventa(e){
				$('#mnumerocomprobante').text(e.parentElement.parentElement.querySelector(".numerocomprobante").textContent)
				$('#mtipo_comprobante').text(e.parentElement.parentElement.querySelector(".tipo_comprobante").textContent)
				$('#mrazonsocial').text(e.parentElement.parentElement.querySelector(".razonsocial").textContent)
				$('#mtotal').text(e.parentElement.parentElement.querySelector(".total").textContent)
				$('#mnombre_sucursal').text(e.parentElement.parentElement.querySelector(".nombre_sucursal").textContent)
				$('#mfecha').text(e.parentElement.parentElement.querySelector(".fecha").textContent)
				$('#musuario').text(e.parentElement.parentElement.querySelector(".usuario").textContent)
				const codigorc =  parseInt(e.parentElement.parentElement.querySelector(".codigorc").textContent)
				$("#codigorc").val(codigorc);
				getSelector("#detalleComprax").innerHTML = ""

				let i = 1;
				fetch(`getdetalleprecioventa.php?codigorc=${codigorc}`)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					res.forEach(ix => {
						getSelector("#detalleComprax").innerHTML += `
						<tr class="rowto" data-codigodetalleproducto="${ix.codigodetalleproducto}" data-codigoprod="${ix.codigoprod}">
						<td>${i}</td>
						<td class="cantidad">${ix.cantidad}</td>
						<td>${ix.nombre_producto}</td>
						<td>${ix.marca}</td>

						<td><input type="hidden" class="preciounidad" value="${ix.vcf}"><input readonly required class="form-control preciounidadmas" value="${ix.totalunidad}" readonly></td>
						<td><input readonly required class="form-control" value="${parseFloat(ix.totalunidad)*0.18}" readonly></td>
						<td><input readonly required class="form-control" value="${parseFloat(ix.totalunidad)*1.18}" readonly></td>
						<td><input required readonly data-cantidad="${ix.cantidad}" value="${ix.porcpv1}" oninput="changeporcentaje(this)" data-origin="venta1" class="form-control porcentajeventa1" ></td>
						<td><input required class="form-control precioventa1" readonly value="${ix.precioventa1}"></td>
						<td><input readonly required data-cantidad="${ix.cantidad}" value="${ix.porcpv2}" oninput="changeporcentaje(this)" data-origin="venta2" class="form-control porcentajeventa2" ></td>
						<td><input required class="form-control precioventa2" value="${ix.precioventa1}" readonly></td>
						<td><input readonly required data-cantidad="${ix.cantidad}" value="${ix.porcpv3}" oninput="changeporcentaje(this)" data-origin="venta3" class="form-control porcentajeventa3" ></td>
						<td><input required class="form-control precioventa3" value="${ix.precioventa1}" readonly></td>
						</tr>
						`
						$('[data-toggle="tooltip"]').tooltip()
						$('.tooltips').tooltip();
						i++;
					})

				});
				$("#mSetPrecioVenta").modal();
			}

			function managecompra(e){
				$('#mnumerocomprobante').text(e.parentElement.parentElement.querySelector(".numerocomprobante").textContent)
				$('#mtipo_comprobante').text(e.parentElement.parentElement.querySelector(".tipo_comprobante").textContent)
				$('#mrazonsocial').text(e.parentElement.parentElement.querySelector(".razonsocial").textContent)
				$('#mtotal').text(e.parentElement.parentElement.querySelector(".total").textContent)
				$('#mnombre_sucursal').text(e.parentElement.parentElement.querySelector(".nombre_sucursal").textContent)
				$('#mfecha').text(e.parentElement.parentElement.querySelector(".fecha").textContent)
				$('#musuario').text(e.parentElement.parentElement.querySelector(".usuario").textContent)
				const codigorc =  parseInt(e.parentElement.parentElement.querySelector(".codigorc").textContent)
				$("#codigorc").val(codigorc);
				getSelector("#detalleComprax").innerHTML = ""

				let i = 1;
				fetch(`getDetalleCompra.php?codigorc=${codigorc}`)
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					res.forEach(ix => {
						const pc = (parseFloat(ix.vcf)/parseInt(ix.cantidad)).toFixed(4);

						getSelector("#detalleComprax").innerHTML += `
						<tr class="rowto" data-codigodetalleproducto="${ix.codigodetalleproducto}" data-codigoprod="${ix.codigoprod}">
						<td>${i}</td>
						<td class="cantidad">${ix.cantidad}</td>
						<td>${ix.nombre_producto}</td>
						<td>${ix.nombre}</td>

						<td><input type="hidden" class="preciounidad" value="${ix.vcf}"><input required class="form-control preciounidadmas" value="${ix.totalunidad}" readonly></td>
						<td><input readonly required class="form-control" value="${parseFloat(ix.totalunidad)*0.18}" readonly></td>
						<td><input readonly required class="form-control" value="${parseFloat(ix.totalunidad)*1.18}" readonly></td>

						<td><input required data-cantidad="${ix.cantidad}" oninput="changeporcentaje(this)" data-origin="venta1"data-pc="${parseFloat(ix.totalunidad)*1.18}" class="form-control porcentajeventa1" ></td>
						<td><input required class="form-control precioventa1" readonly></td>
						<td><input required data-cantidad="${ix.cantidad}" oninput="changeporcentaje(this)" data-origin="venta2"data-pc="${parseFloat(ix.totalunidad)*1.18}" class="form-control porcentajeventa2" ></td>
						<td><input required class="form-control precioventa2" readonly></td>
						<td><input required data-cantidad="${ix.cantidad}" oninput="changeporcentaje(this)" data-origin="venta3"data-pc="${parseFloat(ix.totalunidad)*1.18}" class="form-control porcentajeventa3" ></td>
						<td><input required class="form-control precioventa3" readonly></td>
						</tr>
						`
						$('[data-toggle="tooltip"]').tooltip()
						$('.tooltips').tooltip();
						i++;
					})

				});
				$("#mSetPrecioVenta").modal();
			}
			function changeporcentaje(e){
				if(e.value < 0){
					e.value = 0;
					return
				}
				const porcentaje = parseFloat(e.value)
				const origin = e.dataset.origin;
				const pc = parseFloat(e.dataset.pc);
				const cantidad = parseInt(e.dataset.cantidad);
				e.closest("tr").querySelector(`.precio${origin}`).value = (pc*(100 + porcentaje)/100).toFixed(2);
			}
			getSelector("#saveOrdenCompra").addEventListener("submit", e => {
				e.preventDefault();
				const codacceso = <?= $_SESSION['kt_login_id'] ?>;
				const detalle = [];

				detalle.push(`
					update registro_compras set estadofact = 2 where codigorc = ${codigorc.value}
					`)
				getSelectorAll(".rowto").forEach(ee =>  {
					detalle.push(
						`insert into precio_venta (codacceso, tipo_asignar_venta, codigodetalleproducto, codigoprod, vcf, totalunidad, porcpv1, precioventa1, porcpv2, precioventa2, porcpv3, precioventa3, codigocompras)
						values
						(
						${codacceso},
						1,
						${ee.dataset.codigodetalleproducto},
						${ee.dataset.codigoprod},
						${ee.querySelector(".preciounidad").value},
						${ee.querySelector(".preciounidadmas").value},
						${ee.querySelector(".porcentajeventa1").value},
						${ee.querySelector(".precioventa1").value},
						${ee.querySelector(".porcentajeventa2").value},
						${ee.querySelector(".precioventa2").value},
						${ee.querySelector(".porcentajeventa3").value},
						${ee.querySelector(".precioventa3").value},
						${codigorc.value}
						);
						`
						)
				})
				console.log(detalle)
				var formData = new FormData();
				formData.append("exearray", JSON.stringify(detalle))

				fetch(`setPrecioVenta.php`, { method: 'POST', body: formData })
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					$("#mOrdenCompra").modal("hide");
					if (res.success) {
						alert("registro completo!")
						location.reload()
					}
				});
			})


		</script>