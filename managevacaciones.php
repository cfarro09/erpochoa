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
$i = 1;
//________________________________________________________________________________________________________________
?>

<form id="form-generate-compra">
	<div class="row">
		<div class="col-sm-12">
			<?php if ($totalRows_personal > 0): ?>
				<table class="table table-bordered table-hover" id="sample_1">
					<thead>
						<tr>
							<th> N&deg; </th>
							<th> Cedula</th>
							<th> Nombre </th>
							<th> Paterno </th>
							<th> Fecha Ingreso </th>
							<th> Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						mysql_select_db($database_Ventas, $Ventas);
						$query_personalx = "SELECT * FROM personal WHERE estado = 0";
						$listado_personalx = mysql_query($query_personalx, $Ventas) or die(mysql_error());
						$row_personalx = mysql_fetch_assoc($listado_personalx);
						?>
						<?php do {  ?>
							<tr>
								<td><?= $i ?>></td>
								<td><?= $row_personalx['cedula'] ?></td>
								<td><?= $row_personalx['nombre'] ?></td>
								<td><?= $row_personalx['paterno'] ?></td>
								<td><?= $row_personalx['fecha_ingreso'] ?></td>
								<td>
									<span class="btn btn-success" style="margin-right: 5px" data-type="vacaciones" data-fullname="<?= $row_personalx['nombre'].' '.$row_personalx['paterno'] ?>" onclick="setManagePersonal(this)" data-codigopersonal="<?= $row_personalx['codigopersonal'] ?>">Vacaciones</span>
									<span class="btn btn-success" style="margin-right: 5px" data-type="permisos" onclick="setManagePersonal(this)" data-codigopersonal="<?= $row_personalx['codigopersonal'] ?>">Permisos</span>
								</tr>
								<?php $i++;} while ($row_personalx = mysql_fetch_assoc($listado_personalx)); ?>
							</tbody>
						</table>
					<?php endif ?>
				</div>
			</div>
		</form>
		<div class="modal fade" id="mVacaciones" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog" role="document">
				<div class="modal-content m-auto">
					<div class="modal-header">
						<h2 class="modal-title" id="moperation-title-vacaciones">Ingresar Vacaciones</h2>
					</div>
					<div class="modal-body">

						<form id="form-vacaciones">
							<div class="container-fluid">
								<div class="row">
									<div class="col-xs-12 col-md-12">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="field-1" class="control-label">Fecha Inicio</label>
													<input  type="text" onchange="changeinputdate(this)"  readonly autocomplete="off"
													name="fecha_inicio" id="fecha_inicio"
													class="form-control date-picker" data-date-format="yyyy-mm-dd" required/>
												</div>
											</div>
											<input type="hidden" id="codigopersonal">
											<input type="hidden" id="type-manage">
											<div class="col-md-6">
												<div class="form-group">
													<label for="field-1" class="control-label">Fecha Fin</label>
													<input  type="text" onchange="changeinputdate(this)"  readonly 	autocomplete="off"
													name="fecha_fin" id="fecha_fin"
													class="form-control date-picker" data-date-format="yyyy-mm-dd" required/>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="" class="control-label">Dias</label>
													<input  type="text"  id="periodo" readonly class="form-control"	autocomplete="off" required/>
												</div>
											</div>
											<div class="col-sm-12" style="margin-bottom: 10px">
												<label for="">Descripcion</label>
												<textarea name="observacion" class="form-control" id="observacion-vacaciones" required cols="30" rows="3"></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							<button type="submit" class="btn btn-success">Guardar</button>
							<button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cerrar</button>
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

				function setManagePersonal(e){
					const text = e.dataset.type == "vacaciones" ? "Vacaciones de " + e.dataset.fullname: "Permiso de " + e.dataset.fullname
					$("#codigopersonal").val(e.dataset.codigopersonal)
					$("#type-manage").val(e.dataset.type)
					getSelector("#moperation-title-vacaciones").textContent = text
					$("#mVacaciones").modal()
				}
				getSelector("#form-vacaciones").addEventListener("submit", e => {
					e.preventDefault();
					if($("#fecha_inicio").val() == "" || $("#fecha_fin").val() == ""){
						alert("ingrese las fechas")
					}else{
						const data = {
							codigopersonal: $("#codigopersonal").val(),
							fecha_inicio: $("#fecha_inicio").val(),
							fecha_fin: $("#fecha_fin").val(),
							type_manage: $("#type-manage").val(),
							periodo: $("#periodo").val(),
							codigoacceso: "<?= $_SESSION['kt_login_id']; ?>",
							observacion: $("#observacion-vacaciones").val()
						}
						console.log(data)
						var formData = new FormData();
						formData.append("json", JSON.stringify(data))

						fetch(`setManagePersonal.php`, { method: 'POST', body: formData })
						.then(res => res.json())
						.catch(error => console.error("error: ", error))
						.then(res => {
							if (res.success) {
								alert("registro completo!")
								$("#fecha_fin").val("")
								$("#fecha_inicio").val("")
								$("#periodo").val("")
								$("#observacion-vacaciones").val("")
								$("#mVacaciones").modal("hide")
							}
						});
					}
				})
				function changeinputdate(e){
					if(e.id == "fecha_inicio"){
						const fecha_fin = $("#fecha_fin").val()
						const fecha_inicio = e.value

						if(fecha_fin){
							const date_inicio = new Date(fecha_inicio)
							const date_fin = new Date(fecha_fin)
							if(date_inicio >= date_fin){
								e.value = ""
							}else{
								var timeDiff = date_fin.getTime() - date_inicio.getTime();
								var DaysDiff = timeDiff / (1000 * 3600 * 24);

								$("#periodo").val(DaysDiff)
							}
						}
					}else{
						const fecha_inicio = $("#fecha_inicio").val()
						const fecha_fin = e.value
						if(fecha_inicio){
							const date_inicio = new Date(fecha_inicio)
							const date_fin = new Date(fecha_fin)
							if(date_inicio >= date_fin){
								e.value = ""
							}else{
								var timeDiff = date_fin.getTime() - date_inicio.getTime();
								var DaysDiff = timeDiff / (1000 * 3600 * 24);

								$("#periodo").val(DaysDiff)
							}
						}
					}
				}
			</script>