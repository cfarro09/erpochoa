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

$codpersonal = $_SESSION['kt_codigopersonal'];
$codsucursal = $_SESSION['cod_sucursal'];

?>
<table id="maintable" class="display" width="100%"></table>

<div class="modal fade" id="mkardex" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog" role="document" style="width: 1100px">
				<div class="modal-content m-auto">
					<div class="modal-header">
						<h5 class="modal-title" id="moperation-title">Almacen Kardex</h5>
					</div>
					<div class="modal-body">
						<input type="hidden" id="codproducto">
						<form id="form-setKardex" action="kardex_almacen.php" method="GET">
							<div class="container-fluid">
								<div class="row" style="margin-top:20px">
									<div class="col-xs-12 col-md-12">
										<div class="row">
											<div class="col-md-12">
												<div class="form-group">
													<label for="field-1" class="control-label">Sucursales</label>
													<select name="codigosuc" required id="codigosuc" class="sucursalXX form-control select2 tooltips" id="single" data-placement="top" >
														<?php
														do {  
															?>
															<option value="<?php echo $row_sucursales['cod_sucursal']?>"><?php echo $row_sucursales['nombre_sucursal']?></option>
															<?php
														} while ($row_sucursales = mysql_fetch_assoc($sucursales));
														$rows = mysql_num_rows($sucursales);
														if($rows > 0) {
															mysql_data_seek($sucursales, 0);
															$row_sucursales = mysql_fetch_assoc($sucursales);
														}
														?>
														<option value="9999">OTROS</option>
													</select>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="field-1" class="control-label">Fecha Inicio</label>
													<input type="text" required name ="fecha_inicio" autocomplete="off" id ="fecha_inicio" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top"  required/>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="field-1" class="control-label">Fecha termino</label>
													<input type="text" name ="fecha_termino" autocomplete="off" id ="fecha_termino" required class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top"  required/>
												</div>
											</div>
											<div class="col-md-12">
												<table class="table table-striped table-bordered table-hover" id="historydata">
													<thead>
														<tr>
															<th colspan="5" id="headerKardex"></th>
															<th colspan="2" style="background-color: #01aaff; color: white; text-align: center">ENTRADA</th>
															<th colspan="2" style="background-color: #01aaff; color: white; text-align: center">SALIDA</th>
															<th colspan="2" style="background-color: #01aaff; color: white; text-align: center">SALDO</th>
														</tr>
														<tr>
															<th>FECHA</th>
															<th>DETALLE</th>
															<th>TIPO</th>
															<th>N° COMP/GUIA</th>
															<th>P.UND</th>
															<th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
															<th style="background-color: #01aaff; color: white; text-align: center">IMPORTE</th>
															<th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
															<th style="background-color: #01aaff; color: white; text-align: center">IMPORTE</th>
															<th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
															<th style="background-color: #01aaff; color: white; text-align: center">IMPORTE</th>
														</tr>
													</thead>
													<tbody id="detalleKardexAlmProd" class="text-center"></tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<button type="submit" class="btn btn-success">Imprimir</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
   
   const searchdetail = () => {
	const codsucursal = $("#codigosuc").val()
			const fecha_inicio = $("#fecha_inicio").val()
			const fecha_termino = $("#fecha_termino").val()
			const codproducto = getSelector("#codproducto").value
			var formData = new FormData();
			formData.append("codsucursal", codsucursal);
			formData.append("fecha_inicio", fecha_inicio ? fecha_inicio : "1999-09-09");
			formData.append("fecha_termino", fecha_termino ? fecha_termino : "2030-03-03");
			formData.append("codproducto", codproducto);

			getSelector("#detalleKardexAlmProd").innerHTML = "<tr><td colspan='6'>No hay registros</td></tr>"

			fetch(`getKardexContableFromProductList.php`, { method: 'POST', body: formData })
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				if(res.length > 0){
					getSelector("#detalleKardexAlmProd").innerHTML = `
					<tr>
					<td></td>
					<td>Inventario inicial</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>0</td>	
					<td></td>
					</tr>
					`
					console.log(res)
					let i = 0;
					res.forEach(item => {
						item.preciototal = parseFloat(item.preciototal).toFixed(2)
						if(item.cantidad != "0"){
							getSelector("#detalleKardexAlmProd").innerHTML += `
							<tr>
							<td>${new Date(item.fecha).toLocaleDateString()}</td>
							<td>${item.detalle}</td>
							<td>${item.tipocomprobante}</td>
							<td>${item.numero}</td>
							<td>${item.precio}</td>
							<td>${(item.detalle.includes("Compras") || (item.detalle.includes("Ventas") && item.tipocomprobante == "notacredito")) || item.detalle.includes("Entra") ? item.cantidad : ""}</td>
							<td>${(item.detalle.includes("Compras") || (item.detalle.includes("Ventas") && item.tipocomprobante == "notacredito")) || item.detalle.includes("Entra") ? item.preciototal : ""}</td>
							<td>${(item.detalle.includes("Ventas") && item.tipocomprobante != "notacredito") || item.detalle.includes("Sale") ? item.cantidad : ""}</td>
							<td>${(item.detalle.includes("Ventas") && item.tipocomprobante != "notacredito") || item.detalle.includes("Sale") ? item.preciototal : ""}</td>
							<td>${item.saldo}</td>
							<td>${(item.precio * item.saldo / item.cantidad).toFixed(2)}</td>
							</tr>
							`;
						}
					});
				}
			});

   }
    $(function() {
        initTable();
        
		getSelector("#form-setKardex").addEventListener("submit", e => {
			e.preventDefault();
			
			searchdetail();
		});
    });
	const verkardex = e => {
			getSelector("#codproducto").value = e.dataset.codproducto
			getSelector("#headerKardex").textContent = e.dataset.nombreproducto
			$("#mkardex").modal();
			$("#fecha_inicio").val("");
			$("#fecha_termino").val("");
			searchdetail()
		}
    const initTable = async () => {
        const query = `
        select 
        	p.codigoprod, p.nombre_producto,m.nombre Marca, IFNULL(pv.precioventa1, 0) precioventa1, IFNULL(k.precio, 0) precio_compra, IFNULL(k.saldo, 0) saldo
        from producto p join marca m on p.codigomarca = m.codigomarca 
        left join precio_venta pv on pv.codigoprod = p.codigoprod
        left join kardex_contable k on k.codigoprod = p.codigoprod and k.id_kardex_contable = (select max(k1.id_kardex_contable) from kardex_contable k1 where k1.codigoprod = k.codigoprod)
        `
        let data = await get_data_dynamic(query);
        
        $('#maintable').DataTable({
            data: data,
            destroy: true,
            columns: [
                {
                    title: 'N°',
                    data: 'codigoprod'
                },
                {
                    title: 'nombre_producto',
                    data: 'nombre_producto'
                },
                {
                    title: 'saldo',
                    data: 'saldo',
					className: 'dt-body-right'
                },
                {
                    title: 'precio_compra',
                    data: 'precio_compra',
					className: 'dt-body-right'
                },
                {
                    title: 'precioventa1',
                    data: 'precioventa1',
					className: 'dt-body-right'
                },
                {
                    title: 'ACCIONES',
                    render: function(data, type, row, meta) {
						const nn = row.nombre_producto.replace(/'|"/gi, '');
                        return `<a href="#" onclick="verkardex(this)" data-nombreproducto="${nn}" data-codproducto="${parseInt(row.codigoprod)}">VER KARDEX</a>`;
                       
                    }
                },
            ]
        });
    }
</script>