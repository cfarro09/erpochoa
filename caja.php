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
                <h2 class="modal-title" id="moperationtitle">Detalle del producto</h2>
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

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    $(function() {
        initTable()
        // onloadSucursales()
    });
    const onloadSucursales = async () => {
        const res = await get_data_dynamic("select nombre_sucursal, cod_sucursal from sucursal where estado = 1");

        cargarselect2("#sucursales", res, "cod_sucursal", "nombre_sucursal")
    }
    const getdetail = async (id, name) => {
        $("#moperation").modal();
        moperationtitle.textContent = "INGRESOS " + name
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
        let ddd = await get_data_dynamic(query);
        setConsolidado(ddd)
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
        const datatotble = []

        const data = {
            total: 0
        }
        res.forEach(iii => {
            const arraypagos = JSON.parse(iii.jsonpagos);
            arraypagos.forEach(ixx => {
                if (ixx.tipopago == "efectivo") {
                    if(!data[iii.fecha_emision])
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
                    total: value.toFixed(2)
                })
        $('#ventastable').DataTable({
            data: datatotble,
            destroy: true,
            columns: [
                {
                    title: 'fecha',
                    data: 'fecha'
                },
                {
                    title: 'total',
                    data: 'total',
                    className: 'dt-body-right'
                },
            ]
        });


    }
</script>