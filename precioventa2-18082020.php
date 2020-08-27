<?php require_once('Connections/Ventas.php'); ?>
<?php
mysql_select_db($database_Ventas, $Ventas);
$querySucursales = "select * from sucursal where estado = 1" ;
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT pv.initial, (select max(pv2.precioventa1) from precio_venta pv2 where pv2.codigoprod =  d.codigoprod) precioventamax , pv.codigo_pv, d.codigodetalleproducto, rc.tipomoneda,rc.valorcambio, d.descxitem, d.cantidad, d.codigoprod, co.nombre_color, p.minicodigo, p.minicodigo, p.nombre_producto, m.nombre as marca, d.vcf, d.totalunidad, d.vcu precio_compra, d.totalunidad, d.vcf, IFNULL(pv.porcpv1, '') as porcpv1, IFNULL(pv.porcpv2, '') as porcpv2, IFNULL(pv.porcpv3, '') as porcpv3, IFNULL(pv.precioventa1, '') as precio_venta1, IFNULL(pv.precioventa2, '') as precio_venta2, IFNULL(pv.precioventa3, '') as precio_venta3
from producto p 
inner join detalle_compras d on d.codigodetalleproducto = (select max(d1.codigodetalleproducto) from detalle_compras d1 where d1.codigoprod = p.codigoprod)
inner join registro_compras rc on rc.codigorc = d.codigocompras
left join color co on co.codigocolor = p.codigocolor
left join marca m on m.codigomarca = p.codigomarca 
left join precio_venta pv on pv.codigo_pv = (select max(pv2.codigo_pv) from precio_venta pv2 where pv2.codigoprod =  d.codigoprod) group by d.codigoprod";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);

 //Enumerar filas de data tablas
$i = 1;

//Titulo e icono de la pagina
$Icono="fa fa-cubes"; 
$Color="font-blue";
$Titulo="Asignar PV/Est - Gerencia";
$NombreBotonAgregar="Agregar"; 
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 330;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//__________________________________________________________________
?>        

<!--  ----------------------------------------------------------------------------------------------------------------------------------->

<?php if ($totalRows_Listado == 0) { // Show if recordset empty ?>
	<div class="alert alert-danger">
		<strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
	</div>
<?php } // Show if recordset empty ?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty ?>
	<div class="modal fade" id="mSetPrecioVenta" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document" style="width: 1300px">
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h2 class="modal-title" id="moperation-titlepv">Asignar precio venta</h2>
				</div>
				<div class="modal-body">
					<form id="saveOrdenCompra">
						<div class="container-fluid">
							<div class="row" style="margin-top:20px">
								<div class="col-xs-12 col-md-12">
									<input type="hidden" id="codproducto">
									<input type="hidden" id="codigodetalleproducto">
									<input type="hidden" id="codigo_pv">
									<input type="hidden" id="inputvcf">
									<input type="hidden" id="inputtotalunidad">
									<table class="table">
										<thead>
											<th>Fecha</th>
											<th>Comprobante</th>
											<th>V. C</th>
											<th>IGV</th>
											<th>P. C</th>
											<th>P. V</th>
											<th class="text-center">% V 1</th>
											<th class="text-center">P V 1</th>
											<th class="text-center">% V 2</th>
											<th class="text-center">P V 2</th>
											<th class="text-center">% V 3</th>
											<th class="text-center">P V 3</th>
										</thead>
										<tbody id="detalleComprax">
											<tr>
												<td id="productopv2"></td>
												<td id="marcapv2"></td>
												<td class="text-right" id="preciocomprapv2"></td>
												<td id="vcuigv"></td>
												<td id="vcutotal"></td>
												<td class="text-right" id="precioventapv2"></td>
												<td >
													<input type="number" oninput="inputdynamic(this)"  step="any" data-index="1" data-type="porcentaje" id="porcentaje1" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)"  step="any" data-index="1" data-type="precio" id="precio1" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)"  step="any" data-index="2" data-type="porcentaje" id="porcentaje2" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)"  step="any" data-index="2" data-type="precio" id="precio2" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)"  step="any" data-index="3" data-type="porcentaje" id="porcentaje3" class="form-control">
												</td>
												<td >
													<input type="number" oninput="inputdynamic(this)"  step="any" data-index="3" data-type="precio" id="precio3" class="form-control">
												</td>
											</tr>

										</tbody>
									</table>
								</div>
							</div>
						</div>
						<button type="submit" id="btn_save_precioventa1" style="display: none" class="btn btn-success">Guardar</button>
						<button type="button" data-dismiss="modal" class="modal_close btn btn-danger">Cerrar</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-striped table-bordered table-hover" id="sample_1">
		<thead>
			<tr>
				<th class="text-center">N&deg; </th>
				<th class="text-center">CODIGO </th>
				<th class="text-center">PRODUCTO </th>
				<th class="text-center">MARCA </th>
				<th class="text-center">COLOR </th>
				<th class="text-center">P COMPRA</th>
				<th class="text-center">P VENTA</th>
				<th class="text-center">ACCION </th>
			</tr>
		</thead>
		<tbody>
			<?php do { 
				$pcompra = $row_Listado['descxitem'] == 0 ? $row_Listado['precio_compra'] : $row_Listado['precio_compra'] * (100 - $row_Listado['descxitem'])/100;

				if ($row_Listado['tipomoneda'] == "dolares") {
					$pcompra = $pcompra * $row_Listado['valorcambio'];
				}
				
				?>
				<tr <?= !$row_Listado['initial'] || $pcompra > $row_Listado['precio_venta1'] ? "style='background-color: #E57373'" : ""?>>
					<?php $pcompra = number_format($pcompra, 2); ?>
					<td> <?php echo $i; ?> </td>
					<td class="text-right"> <?php echo $row_Listado['minicodigo']; ?>                                                           </td>
					<td> <?php echo $row_Listado['nombre_producto']; ?></td>
					<td align="center"> <?= $row_Listado['marca']; ?></td>
					<td align="center"> <?= $row_Listado['nombre_color']; ?></td>
					<td align="right"><?= $pcompra ?></td>
					<td align="right"> <?= $row_Listado['precioventamax']; ?></td>
					<td><a href="#" 
						data-nombreproducto="<?= $row_Listado['nombre_producto'] ?>" 
						data-minicodigo="<?= $row_Listado['minicodigo'] ?>" 
						data-color="<?= $row_Listado['nombre_color']; ?>"
						data-marca="<?= $row_Listado['marca']; ?>" 
						data-vcf="<?= $row_Listado['vcf']; ?>" 
						data-totalunidad="<?= $row_Listado['totalunidad']; ?>" 
						data-codigo_pv="<?= $row_Listado['codigo_pv']; ?>" 
						data-preciocompra="<?= $pcompra ?>" 
						data-codigodetalleproducto="<?= $row_Listado['codigodetalleproducto']; ?>" 
						data-precioventa="<?= $row_Listado['precioventamax']; ?>" 
						data-codproducto="<?= $row_Listado['codigoprod'] ?>" 
						onClick="asignarprecioventa(this)" >Asignar</a></td>
				</tr>
				<?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
			</tbody>
		</table>
		
	<?php } // Show if recordset not empty ?>
	<?php 
//___________________________________________________________________________________________________________________
	include("Fragmentos/footer.php");
	include("Fragmentos/pie.php");

	mysql_free_result($Listado);
	?>
	<script type="text/javascript">
		async function asignarprecioventa(e){
			$("#mSetPrecioVenta").modal();

			const precioCompra = parseFloat(e.dataset.preciocompra);

			btn_save_precioventa1.style.display = "";
			vcuigv.textContent = (precioCompra*0.18).toFixed(2);
			vcutotal.textContent = (precioCompra*1.18).toFixed(2);
			preciocomprapv2.textContent = precioCompra.toFixed(2)
			precioventapv2.textContent = e.dataset.precioventa ? parseFloat(e.dataset.precioventa).toFixed(2) : ""
			codigo_pv.value = e.dataset.codigo_pv
			marcapv2.textContent = ""
			productopv2.textContent = ""
			codproducto.textContent = e.dataset.codproducto
			codigodetalleproducto.textContent = e.dataset.codigodetalleproducto
			
			inputvcf.value = e.dataset.vcf
			inputtotalunidad.value = e.dataset.totalunidad

			getSelector("#moperation-titlepv").textContent = `Asignar precio venta ${e.dataset.minicodigo} ${e.dataset.nombreproducto} ${e.dataset.marca} ${e.dataset.color}`;

			var query = `select IFNULL(rc.tipo_comprobante, "") tipo_comprobante, IFNULL(rc.numerocomprobante, "") numerocomprobante, pv.fecha_act, rc.fecha, pv.vcf, rc.valorcambio, pv.totalunidad, pv.porcpv1, pv.porcpv2, pv.porcpv3, pv.precioventa1, pv.precioventa2, pv.precioventa3 
			from precio_venta pv
			left join registro_compras rc on rc.codigorc = pv.codigocompras
			where codigoprod = ${e.dataset.codproducto} order by pv.codigo_pv desc limit 3`;

			const res = await get_data_dynamic(query).then(r => r);
			getSelectorAll(".rowtoremove").forEach(x => x.remove());
			if (res instanceof Array) {
				let htmlhelp = '';
				res.forEach(i => {
					const valorcambio = i.valorcambio ? i.valorcambio : 1;
					const precioCompra = parseFloat(i.totalunidad*valorcambio);
					htmlhelp += `
					<tr class="rowtoremove">
						<td>${i.fecha_act}</td>
						<td>${i.tipo_comprobante + " " + i.numerocomprobante}</td>
						<td class="text-right">${precioCompra.toFixed(2)}</td>

						<td class="text-right">${(precioCompra*0.18).toFixed(2)}</td>
						<td class="text-right">${(precioCompra*1.18).toFixed(2)}</td>

						<td class="text-right">${parseFloat(i.precioventa3).toFixed(2)}</td>
						<td >
							<input type="number" value="${parseFloat(i.porcpv1).toFixed(2)}" step="any" disabled class="form-control text-right">
						</td>
						<td >
							<input type="number" value="${parseFloat(i.precioventa1).toFixed(2)}" step="any" disabled class="form-control text-right">
						</td>
						<td >
							<input type="number" value="${parseFloat(i.porcpv2).toFixed(2)}" step="any" disabled class="form-control text-right">
						</td>
						<td >
							<input type="number" value="${parseFloat(i.precioventa2).toFixed(2)}" step="any" disabled class="form-control text-right">
						</td>
						<td >
							<input type="number" value="${parseFloat(i.porcpv3).toFixed(2)}" step="any" disabled class="form-control text-right">
						</td>
						<td >
							<input type="number" value="${parseFloat(i.precioventa3).toFixed(2)}" step="any" disabled class="form-control text-right">
						</td>
					</tr>

					`;

				});
				detalleComprax.innerHTML += htmlhelp;
			}
		}
		function inputdynamic(e){
			if(e.value < 0){
				e.value = 0;
				return;
			}
			const preciocompra = parseFloat(preciocomprapv2.textContent) * 1.18;
			
			const type = e.dataset.type;
			const i = e.dataset.index;
			const currentvalue = parseFloat(e.value);
			const towrite = type == "porcentaje" ? "precio" : "porcentaje";

			if(type == "porcentaje")
				getSelector(`#${towrite}${i}`).value = (preciocompra*(100+currentvalue)/100).toFixed(2)
			else
				getSelector(`#${towrite}${i}`).value = (currentvalue*100/preciocompra).toFixed(2)
		}
		
		const guardar = e => {
			e.preventDefault();
			const idpv = parseInt(codigo_pv.value);

			const codacceso = <?= $_SESSION['kt_login_id'] ?>;
			const detalle = [];

			detalle.push(
				`insert into precio_venta (codacceso, tipo_asignar_venta, codigodetalleproducto, codigoprod, vcf, totalunidad, porcpv1, precioventa1, porcpv2, precioventa2, porcpv3, precioventa3, codigocompras, initial)
				values
				(
				${codacceso},
				1,
				${codigodetalleproducto.textContent},
				${codproducto.textContent},
				${preciocomprapv2.textContent},
				${vcutotal.textContent},
				${porcentaje1.value},
				${precio1.value},
				${porcentaje2.value},
				${precio2.value},
				${porcentaje3.value},
				${precio3.value},
				0,
				true
				);`
			)

			var formData = new FormData();
				formData.append("exearray", JSON.stringify(detalle))

				fetch(`setPrecioVenta.php`, { method: 'POST', body: formData })
				.then(res => res.json())
				.catch(error => console.error("error: ", error))
				.then(res => {
					$("#mSetPrecioVenta").modal("hide");
					if (res.success) {
						alert("registro completo!")
						location.reload()
					}
				});
		}
		getSelector("#saveOrdenCompra").addEventListener("submit", guardar)
	</script>