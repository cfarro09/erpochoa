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
                <button class="btn btn-primary" onclick="dispose()">Registro Ingreso</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
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
                    <h2 class="modal-title" id="desposetitle">EMPOZE INGRESO</h2>
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
                                            <input type="number" step="any" id="cantidadxx" autocomplete="off" required class="form-control form-control-inline" />
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
                    <button type="submit" class="btn btn-primary" >Guardar</button>
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
    const dispose =  () => {
        formdispose.reset()
        personal.value = 0
        $("#mdespose").modal()
    }
    const guardardespse = async e => {
        e.preventDefault();
        if(personal.value){
            const query = `
            insert into despose 
                (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo) 
            values
                ('${nrecibox.value}', ${cantidadxx.value}, '${fecha.value}', '${byfrom.value}', ${personal.value}, ${msucursal.value}, 'despose')`
            let res = await ff_dynamic(query);
            $("#mdespose").modal("hide")
            getdetail(msucursal.value, namesucursal.value)
        }else{
            alert("debe seleccionar personal")
        }
    }
    const getdetail = async (id, name) => {
        msucursal.value = id
        namesucursal.value = name
        $("#moperation").modal();
        moperationtitle.textContent = "INGRESO CAJA " + name
        const query1 = `
            SELECT 
                fecha, cantidad as despose, '' as total, por as motivo
            FROM despose
            WHERE 
                sucursal = ${id} and tipo = 'despose'`;

        
        let despose = await get_data_dynamic(query1);

        setConsolidado(id, despose)
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
            select 
                s.cod_sucursal, s.nombre_sucursal,
                sum(Case When d.tipo = 'despose' Then d.cantidad Else 0 End) egreso
            from sucursal s 
            left join despose d on d.sucursal = s.cod_sucursal 
            where s.estado = 1
            group by s.cod_sucursal
        `;

        let data = await get_data_dynamic(query);

        const rowtotal = {nombre_sucursal: "CAJA", ingreso: 0, egreso: 0, saldo: 0}

        for (let i = 0; i < data.length; i++) {
            const x = data[i];
            const rr = await proccessIngresosEfectivo(x.cod_sucursal).then(r => r);
            const ingreso = rr.total;
            rowtotal["ingreso"] += parseFloat(ingreso);
            rowtotal["egreso"] += parseFloat(x.egreso);
            rowtotal["saldo"] += parseFloat(ingreso) - parseFloat(x.egreso);
            data[i] = {
                ...x,
                ingreso: ingreso.toFixed(2),
                saldo: (ingreso - x.egreso).toFixed(2)
            }
        }
        rowtotal["ingreso"] = rowtotal["ingreso"].toFixed(2)
        rowtotal["egreso"] = rowtotal["egreso"].toFixed(2)
        rowtotal["saldo"] = rowtotal["saldo"].toFixed(2)
        data.push(rowtotal)
        $('#maintable').DataTable({
            data: data,
            ordering: false,
            destroy: true,
            columns: [
                {
                    title: 'acciones',
                    render: function(data, type, row) {
                        const nn = row.nombre_sucursal.replace('"', '').replace("'", "");
                        return `<a href="#" onclick='getdetail(${parseInt(row.cod_sucursal)}, "${nn}")'>${row.nombre_sucursal}</a>`
                    }
                },
                {
                    title: 'INGRESOS',
                    data: 'ingreso',
                    className: 'dt-body-right'
                },
                {
                    title: 'EGRESOS',
                    data: 'egreso',
                    className: 'dt-body-right'
                },
                {
                    title: 'SALDO',
                    data: 'saldo',
                    className: 'dt-body-right'
                }
            ]
        });
        document.querySelector("#maintable tbody tr:last-child").style.fontWeight = "bold"
    }

    const proccessIngresosEfectivo = async id => {
        const query = `
            SELECT 
                v.jsonpagos, v.fecha_emision
            FROM ventas v
            WHERE 
                v.sucursal = ${id}`;

        const da = await get_data_dynamic(query);
        const res = {
            datatotble: [],
            total: 0
        }
        const data = {}

        da.forEach(iii => {
            const arraypagos = JSON.parse(iii.jsonpagos);
            arraypagos.forEach(ixx => {
                if (ixx.tipopago == "efectivo") {
                    if (!data[iii.fecha_emision])
                        data[iii.fecha_emision] = 0
                    data[iii.fecha_emision] += ixx.montoextra ? parseFloat(ixx.montoextra) : 0
                    res.total += parseFloat(ixx.montoextra)
                }
            })
        })
        for (const [key, value] of Object.entries(data))
            res.datatotble.push({
                fecha: key,
                total: value.toFixed(2),
                despose: '',
                motivo: ""
            })
        return res
    }

    const setConsolidado = async (id, des) => {
        const rr = await proccessIngresosEfectivo(id)
        const datatotble = rr.datatotble;
        
        let qwer = [...datatotble, ...des];
        let saldo = 0;
        qwer.sort(function (a, b) {
            if (a.fecha < b.fecha) {
                return -1;
            }
            if (b.fecha < a.fecha) {
                return 1;
            }
            return 0;
        });
        
        qwer = qwer.map(x => {
            const despose = x.despose ? parseFloat(x.despose) : 0
            const total = x.total ? parseFloat(x.total) : 0
            saldo = saldo + total - despose
            x.saldo = saldo.toFixed(2)
            return x
        })
        
        $('#ventastable').DataTable({
            data: qwer,
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
            columns: [
                {
                    title: 'fecha',
                    data: 'fecha'
                },
                {
                    title: 'motivo',
                    data: 'motivo'
                },
                {
                    title: 'Ingreso',
                    data: 'total',
                    className: 'dt-body-right'
                },
                {
                    title: 'Egreso',
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