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
                                            <input type="text" id="cantidad" required class="form-control form-control-inline" />
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
                (nrorecibo, cantidad, fecha, por, personal, sucursal) 
            values
                ('${nrecibox.value}', ${cantidad.value}, '${fecha.value}', '${byfrom.value}', ${personal.value}, ${msucursal.value})`
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
        const query = `
            SELECT 
                if(cn.cedula is null, 'juridico', 'natural') as tipo, v.codigoventas, montoabono as abonoproveedor, v.tipocomprobante, v.codigocomprobante, v.jsonpagos,
                if(cn.cedula is null, v.codigoclientej, v.codigoclienten) as codcliente,
                if(cn.cedula is null, cj.razonsocial, CONCAT(cn.paterno, ' ', cn.materno, ' ', cn.nombre)) as fullname, v.fecha_emision,
                IFNULL(cn.cedula, cj.ruc) as identificacion, 
                v.montofact as totalcargo, v.pagoacomulado as totalabono 
            FROM ventas v
            left join cnatural cn on v.codigoclienten = cn.codigoclienten 
            left join cjuridico cj on v.codigoclientej = cj.codigoclientej 
            WHERE 
                v.sucursal = ${id}`;
        
        const query1 = `
            SELECT 
                fecha, cantidad as despose, '' as total
            FROM despose
            WHERE 
                sucursal = ${id}`;

        let ddd = await get_data_dynamic(query);
        let despose = await get_data_dynamic(query1);

        setConsolidado(ddd, despose)
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
    const setConsolidado = (res, des) => {
        const datatotble = []

        const data = {
            total: 0
        }
        res.forEach(iii => {
            const arraypagos = JSON.parse(iii.jsonpagos);
            arraypagos.forEach(ixx => {
                if (ixx.tipopago == "efectivo") {
                    if (!data[iii.fecha_emision])
                        data[iii.fecha_emision] = 0
                    data[iii.fecha_emision] += ixx.montoextra ? parseFloat(ixx.montoextra) : 0
                    data.total += parseFloat(ixx.montoextra)
                }
            })
        })

        for (const [key, value] of Object.entries(data))
            if (key != "total")
                datatotble.push({
                    fecha: key,
                    total: value.toFixed(2),
                    despose: ''
                })
        
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
        debugger
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
            columns: [{
                    title: 'fecha',
                    data: 'fecha'
                },
                {
                    title: 'total',
                    data: 'total',
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