<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Datos Sueldo";
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
	.head-title{
        font-weight: bold;
        text-align: center;
    }
</style>
<button class="btn btn-secondary" style="display: none" onclick="agregaroperation()">Agregar</button>

<table id="maintable" class="display table table-bordered" width="100%"></table>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
		<form id="formoperation">
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h2 class="modal-title" id="titleoperation">Cobro cheque</h2>
				</div>
				<input type="hidden" id="idoperation">
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="control-label">Nombre Trabajador</label>
									<input disabled type="text" required class="form-control" id="nombrepersonal">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="control-label">Tipo Regimen</label>
									<select id="tiporegimen" required class="">
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">Hijos</label>
									<select id="hijos" required class="form-control">
										<option value="si">SI</option>
										<option value="no">NO</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">Codigo SSP</label>
									<input type="text" class="form-control" id="ssp">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">N° SEG SOC</label>
									<input type="text" class="form-control" id="segsoc">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Fecha Inicio Contrato</label>
									<input type="text" readonly required name="fechainiciocontrato" autocomplete="off" id="fechainiciocontrato" class="form-control form-control-inline input-medium date-picker" data-date-format="yyyy-mm-dd" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Fecha Inicio Contrato</label>
									<input type="text" readonly name="fechafincontrato" autocomplete="off" id="fechafincontrato" class="form-control form-control-inline input-medium date-picker" data-date-format="yyyy-mm-dd" />
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">Sueldo Mensual</label>
									<input type="number" step="any" required class="form-control" id="sueldomensual">
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">Banco</label>
									<select id="bancooperation"></select>
								</div>
							</div>


							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">CCI</label>
									<input type="text" autocomplete="off" class="form-control" id="cci">
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label class="control-label">N° Cuenta</label>
									<input type="text" autocomplete="off" class="form-control" id="nrocuenta">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="modal_close btn btn-success">Guardar</button>
					<button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>



<div class="modal fade" id="mplanilla" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document" style="width: 900px">
		<form id="formplanilla">
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h2 class="modal-title" id="titleplanilla">Cobro cheque</h2>
				</div>
				<input type="hidden" id="idplanilla">
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="control-label">Nombre Trabajador</label>
									<input disabled type="text" required class="form-control" id="nombrepersonalplanilla">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label">Cargo</label>
									<input disabled type="text" required class="form-control" id="cargop">
								</div>
								<div class="form-group">
									<label class="control-label">Sueldo</label>
									<input disabled type="text" required class="form-control" id="sueldop">
								</div>
								<div class="form-group">
									<label class="control-label">Dias Trabajados</label>
									<input type="number" required class="form-control" id="diastrabajadosp" oninput="calculardias(this)">
								</div>
							</div>
							<div class="col-sm-4"></div>
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label">Tipo Regimen</label>
									<input type="text" disabled required class="form-control" id="regimenp">
								</div>
								<div class="form-group">
									<label class="control-label">Mes</label>
									<select id="mesp" class="form-control">
										<option value="1">Enero</option>
										<option value="2">Febrero</option>
										<option value="3">Marzo</option>
										<option value="4">Abril</option>
									</select>
								</div>
								<div class="form-group">
									<label class="control-label">Año</label>
									<select id="aniop" class="form-control">
										<option value="2020">2020</option>
										<option value="2021">2021</option>
										<option value="2022">2022</option>
										<option value="2023">2023</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="row">
									<div class="col-sm-12 head-title">Ingreso</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">R. Basica</label>
											<input disabled type="text" required class="form-control text-right" id="rbasica">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">Asig. Familiar</label>
											<input disabled type="text" required class="form-control text-right" id="asignfamiliar">
										</div>	
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">Vacaciones</label>
											<input type="text" required class="form-control text-right" id="vacaciones">
										</div>	
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="row">
									<div class="col-sm-12 head-title">Egresos</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">AFP Aportes</label>
											<input disabled type="text" required class="form-control text-right" id="afpaportes">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">AFP Comision</label>
											<input disabled type="text" required class="form-control text-right" id="afpcomision">
										</div>	
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">AFP prima</label>
											<input disabled type="text" required class="form-control text-right" id="afpprima">
										</div>	
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">SNP</label>
											<input disabled type="text" required class="form-control text-right" id="snp">
										</div>	
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">Abonos</label>
											<input disabled type="text" required class="form-control text-right" id="abono">
										</div>	
									</div>
								</div>
							</div>
							<div class="col-sm-4">

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="modal_close btn btn-success">Guardar</button>
					<button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
				</div>
			</div>
		</form>
	</div>
</div>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
	$(function() {
		onloadBancos();
		onloadRegimen()
		initTable();
		formoperation.addEventListener("submit", guardaroperation)
	});

	const onloadBancos = async () => {
		const res = await get_data_dynamic("select codigobanco, nombre_banco from banco");
		res.unshift({
			codigobanco: "",
			nombre_banco: "Seleccionar"
		})
		cargarselect2("#bancooperation", res, "codigobanco", "nombre_banco", false, false)
	}
	const onloadRegimen = async () => {
		const res = await get_data_dynamic("select id, nombre from datos_sueldo");
		res.unshift({
			id: "",
			nombre: "Seleccionar"
		})
		cargarselect2("#tiporegimen", res, "id", "nombre", false, false)
	}
	const agregaroperation = () => {
		formoperation.reset();
		idoperation.value = "0";
		$("#moperation").modal();
		nombreafp.value = ""
		titleoperation.textContent = "Registrar datos de sueldo."
	}
	const calcularsueldo = async (id, name) => {
		formplanilla.reset()
		$("#mplanilla").modal();
		titleplanilla.textContent = "PLANILLA DE PAGOS";
		nombrepersonalplanilla.value = name;


		let res = await get_data_dynamic(`
			SELECT 
				prof.profesion, p.sueldo_mensual, hijos, ds.nombre regimen, ds.aporte, ds.comision, ds.prima, ds.essalud
			FROM personal p 
			INNER JOIN profesion prof on p.codigoprofesion = prof.codigoprofesion 
			INNER JOIN datos_sueldo ds on ds.id = p.tiporegimen
			where codigopersonal = ${id}`);

		const dd = res[0];
		const sueldo = dd.hijos == "si" ? parseFloat(dd.sueldo_mensual) + 93 : parseFloat(dd.sueldo_mensual);
		const month = new Date().getMonth() + 1;
		regimenp.value = dd.regimen
		cargop.value = dd.profesion;
		sueldop.value = sueldo.toFixed(2);
		mesp.value = month;
		aniop.value = new Date().getFullYear();

		rbasica.value = dd.sueldo_mensual;
		asignfamiliar.value = dd.hijos == "si" ? "93.00" : "0.00";

		const aporte = parseFloat(dd.aporte);
		const comision = parseFloat(dd.comision);
		const prima = parseFloat(dd.prima);
		const essalud = parseFloat(dd.essalud);

		afpaportes.value = (sueldo*aporte/100).toFixed(2);
		afpcomision.value = (sueldo*comision/100).toFixed(2);
		afpprima.value = (sueldo*prima/100).toFixed(2);
		// abono.value = sueldo*aporte/100;
	}

	const calculardias = e => {
		const month = mesp.value;
		const year = aniop.value;

		const limtidays = new Date(year, month, 0).getDate();
		if (parseInt(e.value) > limtidays) {
			e.value = limtidays;
		}
	}
	const editar = async (id, name) => {
		$("#moperation").modal();
		titleoperation.textContent = "Datos del Trabajador"
		formoperation.reset()
		idoperation.value = id;
		nombrepersonal.value = name;

		$('#bancooperation').val("").trigger('change');
		$('#tiporegimen').val("").trigger('change');


		let res = await get_data_dynamic(`select tiporegimen, hijos, codigossp, segsoc, fechainiciocontrato, fechafincontrato, sueldo_mensual, banco, cci, nrocuenta from personal where codigopersonal = ${id}`);

		if (res[0].tiporegimen) {
			const dd = res[0];


			$('#tiporegimen').val(dd.tiporegimen).trigger('change');
			$('#bancooperation').val(dd.banco).trigger('change');

			hijos.value = dd.hijos;
			ssp.value = dd.codigossp;
			segsoc.value = dd.segsoc;
			fechainiciocontrato.value = dd.fechainiciocontrato;
			fechafincontrato.value = dd.fechafincontrato;
			sueldomensual.value = dd.sueldo_mensual;

			cci.value = dd.cci;
			nrocuenta.value = dd.nrocuenta;
		}
	}
	const guardaroperation = async e => {
		e.preventDefault();
		let query = "";
		if (idoperation.value != "0") {
			query = `
                update personal set
                    tiporegimen = ${tiporegimen.value},
                    hijos = '${hijos.value}',
                    codigossp = '${ssp.value}',
                    segsoc = '${segsoc.value}',
                    fechainiciocontrato = '${fechainiciocontrato.value}',
                    fechafincontrato = '${fechafincontrato.value}',
                    sueldo_mensual = ${sueldomensual.value},
                    banco = ${bancooperation.value},
                    cci = '${cci.value}',
                    nrocuenta = '${nrocuenta.value}'
                where codigopersonal = ${idoperation.value}
                `;
		}

		const res = await ff_dynamic(query);
		if (res.succes) {
			alert("Registro Completo")
			$("#moperation").modal("hide");
			initTable();
		} else {
			alert(res.msg)
		}
	}
	const initTable = async () => {
		const query = `
          SELECT codigopersonal, cedula, paterno, materno, nombre, tiporegimen FROM personal WHERE estado = '0'
        `;
		let data = await get_data_dynamic(query);

		$('#maintable').DataTable({
			data,
			destroy: true,
			columns: [{
					title: 'cedula',
					data: 'cedula',
				},
				{
					title: 'paterno',
					data: 'paterno',
				},
				{
					title: 'materno',
					data: 'materno',
				},
				{
					title: 'nombre',
					data: 'nombre',
				},
				{
					title: 'Acciones',
					render: function(data, type, row) {
						const fullname = `${row.paterno} ${row.materno} ${row.nombre}`
						if (row.tiporegimen) {
							return `
                              <button class="btn btn-primary" onclick='editar(${row.codigopersonal},` + "`" + fullname + "`" + `)'>AFP/ONP</button>
                              <button class="btn btn-primary" onclick='calcularsueldo(${row.codigopersonal},` + "`" + fullname + "`" + `)'>SUELDO</button>
                          `;
						} else {
							return `
                            <button class="btn btn-danger" onclick='editar(${row.codigopersonal},` + "`" + fullname + "`" + `)'>AFP/ONP</button>
                          `;
						}

					}
				},

			]
		});
	}
</script>