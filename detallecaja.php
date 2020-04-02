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
<div class="row" style="display: none">
    <div class="col-md-4">
        <div class="form-group">
            <label for="field-1" class="control-label">Fecha Inicio</label>
            <input type="text" required name="fecha_inicio" autocomplete="off" id="fecha_inicio" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="field-1" class="control-label">Fecha termino</label>
            <input type="text" name="fecha_fin" autocomplete="off" id="fecha_fin" required class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
        </div>
    </div>
    <div class="col-md-4">
        <button type="button" onclick="initTable()" class="btn btn-primary" style="margin-top: 10px; padding: 10px 40px">Buscar</button>
    </div>
</div>
<span id="namecuenta" style="font-weight: bold; font-size: 20px"></span> <button class="btn btn-primary" id="btndispose" onclick="dispose()" style="margin-left: 20px">Egreso</button>
<table id="maintable" class="display" width="100%"></table>

<div class="modal fade" id="mdespose" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">

        <form id="formdispose">
            <input type="hidden" id="msucursal">
            <input type="hidden" id="namesucursal">
            <div class="modal-content m-auto">
                <div class="modal-header">
                    <h2 class="modal-title" id="desposetitle">REGISTRO DE EGRESO</h2>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N° Recibo</label>
                                            <input type="text" id="nrecibox" required class="form-control" disabled />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Fecha</label>
                                            <input type="text" disabled required name="fecha" autocomplete="off" id="fecha" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Cantidad</label>
                                            <input type="number" step="any" id="cantidadxx" required class="form-control form-control-inline" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Motivo</label> <select id="motivo" class="form-control">
                                                <option value="cuentasxpagar">Cuentas x Pagar Transfenrecia</option>
                                                <option value="Pago Servicios">Pago Servicios</option>
                                                <option value="Sueldo">Sueldo transferencia</option>
                                                <option value="Viatico">Viatico</option>
                                                <option value="Vacaciones">Vacaciones</option>
                                                <option value="Mantenimiento">Mantenimiento</option>
                                                <option value="Otros">Otros</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 divparent">
                                        <div class="form-group">
                                            <label class="control-label">Cuenta</label>
                                            <select id="cuentabancaria" class="form-control"></select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Observacion</label>
                                            <textarea class="form-control" id="byfrom"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Entregado por: </label>
                                            <select id="personal" disabled></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h2 class="modal-title">Cobro cheque</h2>
            </div>
            <input type="hidden" id="codigocontable">
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Monto</label>
                                <input type="text" required class="form-control" id="montoextra" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4" id="divparentpayextra" style="margin-top: 15px">
                            <button class="btn btn-success" type="button" onclick="addpaylocal()">Agregar Pago</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="field-1" class="control-label">Forma Pago</label>
                                <select required onchange="changemodopago(this)" class="form-control" id="formpago">
                                    <option value="unico">Unico</option>
                                    <option value="compuesto">Compuesto</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div style="margin-bottom: 10px" id="containerpayextraG"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="guardar()" class="modal_close btn btn-success">Guardar</button>
                <button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    const id = <?= $_GET["id"] ?>;
    const fullname = '<?= $_GET["fullname"] ?>';
    const idpersonal = <?= $_SESSION['kt_codigopersonal']; ?>;
    namecuenta.textContent = fullname;
    $(function() {
        initTable();
        onloadPersonal()
    });
    const dispose = async () => {
        formdispose.reset()
        fecha.value = new Date(new Date().setHours(10)).toISOString().substring(0, 10)

        let nrecibo = await get_data_dynamic("select `value` from propiedades where `key` = 'ndetallecaja'");
        nrecibox.value = nrecibo[0].value

        personal.value = 0
        cuentabancaria.closest(".divparent").style.display = "none"
        $("#mdespose").modal()
        $('#personal').val(idpersonal).trigger('change');
    }
    const onloadPersonal = async () => {
        const res = await get_data_dynamic("SELECT codigopersonal, concat(paterno, ' ', materno, ' ', nombre) as fullname FROM personal WHERE estado = 0");
        res.unshift({
            codigopersonal: "",
            fullname: "Seleccionar"
        })
        cargarselect2("#personal", res, "codigopersonal", "fullname")
    }
    const initTable = async () => {
        const query = `
            select * from cuenta_mov where id_cuenta = ${id} order by id_cuenta_mov asc
        `
        let data = await get_data_dynamic(query);
        
        $('#maintable').DataTable({
            data,
            ordering: false,
            destroy: true,
            columns: [
                {
                    title: 'TIPO MOV',
                    data: 'tipo_mov'
                },
                {
                    title: 'DETALLE',
                    data: 'detalle'
                },
                {
                    title: 'CARGO',
                    defaultContent: ''
                },
                {
                    title: 'ABONO',
                    data: 'monto'
                },
                {
                    title: 'SALDO',
                    data: 'saldo'
                },
            ]
        });
    }
</script>