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
	.head-title {
		font-weight: bold;
		text-align: center;
		padding: 10px;
		border-bottom: 1px solid
	}

	.b-left {
		border-left: 1px solid
	}

	.b-right {
		border-right: 1px solid
	}

	.b-bottom {
		border-bottom: 1px solid
	}

	.b-top {
		border-top: 1px solid
	}
</style>
<button class="btn btn-secondary" style="display: none" onclick="agregaroperation()">Agregar</button>

<table id="maintable" class="display table table-bordered" width="100%"></table>

<div class="modal fade" id="mreporte" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document" style="width: 900px">
		<form id="formreporte">
			<div class="modal-content m-auto">
				<div class="modal-header">
					<h2 class="modal-title" id="titlereporte">Cobro cheque</h2>
				</div>
				<input type="hidden" id="idreporte">
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="control-label">Nombre Trabajador</label>
									<input disabled type="text" required class="form-control" id="nombrereporte">
								</div>
							</div>
						</div>
						<div class="row">
							<table id="reporttable" class="display table table-bordered" width="100%"></table>
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
			<input type="hidden" id="idpersonalp">
			<input type="hidden" id="idregimen">
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
										<option value="5">Mayo</option>	
										<option value="6">Junio</option>
										<option value="7">Julio</option>
										<option value="8">Agosto</option>
										<option value="9">Setiembre</option>
										<option value="10">Octubre</option>
										<option value="11">Noviembre</option>
										<option value="12">Diciembre</option>

									</select>
								</div>
								<div class="form-group">
									<label class="control-label">Año</label>
									<select id="aniop" class="form-control">
										<option value="2020">2020</option>
										<option value="2021">2021</option>
										<option value="2022">2022</option>
										<option value="2023">2023</option>
										<option value="2024">2024</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row ">
							<div class="col-sm-4">
								<div class="row ">
									<div class="col-sm-12 head-title">Ingreso</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">R. Basica</label>
											<input disabled type="text" required class="form-control text-right ccingreso" id="rbasica">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">Asig. Familiar</label>
											<input disabled type="text" required class="form-control text-right ccingreso" id="asignfamiliar">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">Vacaciones</label>
											<input type="number" autocomplete="off" step="any" required class="form-control text-right ccingreso" id="vacaciones">
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-4 b-left b-right" style="padding-bottom: 10px">
								<div class="row ">
									<div class="col-sm-12 head-title">Egresos</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">AFP Aportes</label>
											<input disabled type="text" required class="form-control text-right ccegreso" id="afpaportes">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">AFP Comision</label>
											<input disabled type="text" required class="form-control text-right ccegreso" id="afpcomision">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">AFP prima</label>
											<input disabled type="text" required class="form-control text-right ccegreso" id="afpprima">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">SNP</label>
											<input disabled type="text" required class="form-control text-right ccegreso" id="snp">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">Abonos</label>
											<input disabled type="text" required class="form-control text-right ccegreso" id="abono">
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="row">
									<div class="col-sm-12 head-title">Aportes Empleados</div>
									<div class="col-sm-12">
										<div class="form-group" style="margin-bottom: 0!important">
											<label class="control-label">ESSALUD 9%</label>
											<input disabled type="text" required class="form-control text-right" id="essaludp">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row b-top">
							<div class="col-sm-4">
								<div class="form-group" style="margin-bottom: 0!important">
									<br>
									<input disabled type="text" required class="form-control text-right" id="subtotalingreso">
								</div>
							</div>
							<div class="col-sm-4 b-left b-right">
								<div class="form-group" style="margin-bottom: 0!important">
									<br>
									<input disabled type="text" required class="form-control text-right" id="subtotalegreso">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group" style="margin-bottom: 0!important">
									<br>
									<input disabled type="text" required class="form-control text-right" id="subtotalaportes">
								</div>
							</div>
						</div>

						<div class="row" style="margin-top: 20px">
							<div class="col-sm-4">
								<div class="form-group">
									<label class="control-label">Total a pagar</label>
									<input type="text" disabled required class="form-control text-right" id="totalpagar">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="modal_close btn btn-success">Pagar</button>
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
		formplanilla.addEventListener("submit", guardarplanillapago)
		vacaciones.oninput = inputingresos
	});
	const inputingresos = () => {
		let ttt = 0;
		getSelectorAll(".ccingreso").forEach(x => {
			ttt += x.value ? parseFloat(x.value) : 0;
		})
		subtotalingreso.value = ttt.toFixed(2);
		calculartotal()
	}

	const inputegresos = () => {
		let ttt = 0;
		getSelectorAll(".ccegreso").forEach(x => {
			ttt += x.value ? parseFloat(x.value) : 0;
		})
		subtotalegreso.value = ttt.toFixed(2);
	}
	const calculartotal = () => {
		totalpagar.value = subtotalingreso.value - subtotalegreso.value
	}
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
	const reporte = (id, name) => {
		$("#mreporte").modal();
		nombrereporte.value = name;
		titlereporte.textContent = "Reporte Sueldos"
		reportTable(id)
	}
	const calcularsueldo = async (id, name) => {
		formplanilla.reset()
		idpersonalp.value = id;
		$("#mplanilla").modal();
		titleplanilla.textContent = "PLANILLA DE PAGOS";
		nombrepersonalplanilla.value = name;

		vacaciones.value = 0
		let res = await get_data_dynamic(`
			SELECT 
				prof.profesion, p.sueldo_mensual, hijos, ds.id idregimen, ds.nombre regimen, ds.aporte, ds.comision, ds.prima, ds.essalud
			FROM personal p 
			INNER JOIN profesion prof on p.codigoprofesion = prof.codigoprofesion 
			INNER JOIN datos_sueldo ds on ds.id = p.tiporegimen
			where codigopersonal = ${id}`);

		const dd = res[0];
		const sueldo = dd.hijos == "si" ? parseFloat(dd.sueldo_mensual) + 93 : parseFloat(dd.sueldo_mensual);
		const month = new Date().getMonth() + 1;
		regimenp.value = dd.regimen;
		idregimen.value = dd.idregimen;

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

		afpaportes.value = (sueldo * aporte / 100).toFixed(2);
		afpcomision.value = (sueldo * comision / 100).toFixed(2);
		afpprima.value = (sueldo * prima / 100).toFixed(2);

		subtotalaportes.value = (sueldo * 0.09).toFixed(2);
		essaludp.value = (sueldo * 0.09).toFixed(2);

		inputingresos()
		inputegresos()
		calculartotal()
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
	const guardarplanillapago = async e => {
		e.preventDefault();

		const queryvaldiate = `
			select id 
			from personalsueldo 
			where personal = ${idpersonalp.value} and estadosueldo is null`;

		const res1 = await get_data_dynamic(queryvaldiate);

		if (res1.length > 0) {
			alert(`Ya existe un pago en proceso.`);
			return;
		}
		
		const queryvaldiate1 = `
			select id 
			from personalsueldo 
			where personal = ${idpersonalp.value} and mes = ${mesp.value} and anio = ${aniop.value}`;

		const res2 = await get_data_dynamic(queryvaldiate1);
		if (res2.length > 0) {
			alert(`Ya existe un pago para el mes y año seleccionado.`);
			return
		}

		const vvacaciones = vacaciones.value ? vacaciones.value : 0
		const vafpaporte = afpaportes.value ? afpaportes.value : 0
		const vafpcomision = afpcomision.value ? afpcomision.value : 0
		const vafpprima = afpprima.value ? afpprima.value : 0
		const vsnp = snp.value ? snp.value : 0
		const vabono = abono.value ? abono.value : 0

		const query = `
		insert into personalsueldo 
			(personal, diastrabajados, regimen, mes, anio, remuneracion, asigfamiliar, vacaciones, afpaporte, afpcomision, afpprima, snp, abonos, essalud, tingresos, tegresos, totalpagar)
		values
			(${idpersonalp.value}, ${diastrabajadosp.value}, ${idregimen.value}, ${mesp.value}, ${aniop.value}, ${rbasica.value}, ${asignfamiliar.value}, ${vvacaciones}, ${vafpaporte},${vafpcomision},${vafpprima},${vsnp},${vabono},${essaludp.value}, ${subtotalingreso.value}, ${subtotalegreso.value}, ${totalpagar.value})
		`;
		const res = await ff_dynamic(query);
		if (res.succes) {
			alert("Registro Completo")
			$("#mplanilla").modal("hide");
			initTable();
		} else {
			alert(res.msg)
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
							  <button class="btn btn-primary" onclick='reporte(${row.codigopersonal},` + "`" + fullname + "`" + `)'>REP</button>
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
	const reportTable = async (id) => {
		const query = `
			SELECT ps.id, ps.estadosueldo, ps.fecharegistro, concat('Boleta', ' - ', ps.id) as tipo, concat(ps.mes, ' - ', anio) as fecha, ps.totalpagar as abono, 0 as cargo FROM personalsueldo ps
			where ps.personal = ${id}
        `;
		let data = await get_data_dynamic(query);
		const abonos = data.filter(x => x.estadosueldo != null).map(x => {
			return {
				...x,
				abono: 0,
				cargo: x.abono,
				tipo: "PAGO CAJA",
				fecharegistro: x.estadosueldo
			}
		})
		data = [...data, ...abonos];
		$('#reporttable').DataTable({
			data,
			destroy: true,
			columns: [{
					title: 'fecharegistro',
					data: 'fecharegistro',
				},
				{
					title: 'tipo',
					data: 'tipo',
				},
				{
					title: 'fecha pago',
					data: 'fecha',
				},
				
				{
					title: 'cargo',
					data: 'cargo',
				},
				{
					title: 'abono',
					data: 'abono',
				},
				{
					title: 'Acciones',
					render: function(data, type, row) {
						return `
                            <button href="#" class="btn btn-danger" onclick=''>VER</button>
                          `
					}
				},

			]
		});
	}
</script>