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

$suc = $_SESSION['cod_sucursal'];

?>

<style>
    .dt-buttons {
        margin-top: 0 !important;
        margin-bottom: 15px !important;
    }
</style>
<table id="maintable" class="display" width="100%"></table>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h2 class="modal-title" style="display: inline-block; margin-right: 10px" id="moperationtitle">Detalle Ingresos</h2>
                <button class="btn btn-primary" onclick="dispose()">Despose</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12 col-md-12" style="margin-bottom: 200px">
                            <table id="ventastable" class="display" width="100%"></table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mdespose" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 700px">

        <form id="formdispose">
            <input type="hidden" id="msucursal">
            <input type="hidden" id="namesucursal">
            <div class="modal-content m-auto">
                <div class="modal-header">
                    <h2 class="modal-title" id="desposetitle">EMPOZE EGRESO</h2>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N° Recibo</label>
                                            <input type="text" id="nrecibox" required class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Cantidad</label>
                                            <input type="text" id="cantidad" required class="form-control form-control-inline" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Motivo</label>
                                            <select id="motivo" class="form-control">
                                                <option value="Sueldo">Sueldo</option>
                                                <option value="Viatico">Viatico</option>
                                                <option value="Vacaciones">Vacaciones</option>
                                                <option value="Pago Servicios">Pago Servicios</option>
                                                <option value="a">a</option>
                                                <option value="b">b</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Fecha</label>
                                            <input type="text" required name="fecha" autocomplete="off" id="fecha" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Por</label>
                                            <textarea class="form-control" id="byfrom"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Personal</label>
                                            <select id="personal"></select>
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

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    $(function() {
        initTable()
        onloadPersonal()
        // onloadSucursales()
        formdispose.addEventListener("submit", guardardespse)
    });
    const dispose = () => {
        formdispose.reset()
        personal.value = 0
        $("#mdespose").modal()
    }
    const guardardespse = async e => {
        e.preventDefault();
        if (personal.value) {
            const query = `
            insert into despose 
                (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo, motivo) 
            values
                ('${nrecibox.value}', ${cantidad.value}, '${fecha.value}', '${byfrom.value}', ${personal.value}, ${msucursal.value}, 'empoze', '${motivo.value}')`
            let res = await ff_dynamic(query);
            $("#mdespose").modal("hide")
            getdetail(msucursal.value, namesucursal.value)
        } else {
            alert("debe seleccionar personal")
        }
    }
    const getdetail = async (id, name) => {
        msucursal.value = id
        namesucursal.value = name
        $("#moperation").modal();
        moperationtitle.textContent = "EGRESOS CAJA " + name

        const query1 = `
            SELECT 
                fecha, cantidad, tipo, motivo, nrorecibo
            FROM despose
            WHERE 
                sucursal = ${id}
            ORDER by id asc`;

        let despose = await get_data_dynamic(query1);

        setConsolidado(despose)
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
        select cod_sucursal, nombre_sucursal from sucursal where estado = 1
        `;
        let data = await get_data_dynamic(query);

        $('#maintable').DataTable({
            data: data,
            destroy: true,
            columns: [
                // {
                {
                    title: 'acciones',
                    render: function(data, type, row) {
                        const nn = row.nombre_sucursal.replace('"', '').replace("'", "");
                        return `<a href="#" onclick='getdetail(${parseInt(row.cod_sucursal)}, "${nn}")'>${row.nombre_sucursal}</a>`
                    }
                }
            ]
        });
    }
    const setConsolidado = (res) => {
        let saldo = 0;
        res = res.map(x => {

            if (x.tipo == "despose") {
                saldo += parseFloat(x.cantidad)
                return {
                    fecha: x.fecha,
                    ingreso: x.cantidad,
                    nrorecibo: x.nrorecibo,
                    despose: '',
                    saldo: saldo.toFixed(2),
                    motivo: x.motivo
                }
            } else {
                saldo -= parseFloat(x.cantidad)
                return {
                    fecha: x.fecha,
                    ingreso: '',
                    despose: x.cantidad,
                    saldo: saldo.toFixed(2),
                    motivo: x.motivo,
                    nrorecibo: x.nrorecibo
                }
            }
        })

        $('#ventastable').DataTable({
            data: res,
            destroy: true,
            buttons: [{
                    extend: 'print',
                    className: 'btn dark btn-outline'
                },
                {
                    extend: 'copy',
                    className: 'btn red btn-outline'
                },
                {
                    extend: 'pdf',
                    className: 'btn green btn-outline'
                },
                {
                    extend: 'excel',
                    className: 'btn yellow btn-outline '
                },
                {
                    extend: 'csv',
                    className: 'btn purple btn-outline '
                },
                {
                    extend: 'colvis',
                    className: 'btn dark btn-outline',
                    text: 'Columns'
                }
            ],
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            columns: [{
                    title: 'fecha',
                    data: 'fecha'
                },
                {
                    title: 'nrorecibo',
                    data: 'nrorecibo',
                    className: 'dt-body-left'
                },
                {
                    title: 'motivo',
                    data: 'motivo',
                    className: 'dt-body-right'
                },
                {
                    title: 'ingreso',
                    data: 'ingreso',
                    className: 'dt-body-right'
                },
                {
                    title: 'despose',
                    data: 'despose',
                    className: 'dt-body-right'
                },
                {
                    title: 'saldo',
                    data: 'saldo',
                    className: 'dt-body-right'
                },

            ]
        });


    }
</script>