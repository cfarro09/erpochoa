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
<style>
    td.details-control {
        background: url('../resources/details_open.png') no-repeat center center;
        cursor: pointer;
    }

    tr.shown td.details-control {
        background: url('../resources/details_close.png') no-repeat center center;
    }
</style>
<div class="row">
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
<table id="maintable" class="display" width="100%"></table>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <form id="formoperacion" action="">
                <div class="modal-header">
                    <h2 class="modal-title">Registrar Cuenta</h2>
                </div>
                <input type="hidden" id="codigocontable">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Codigo</label>
                                    <input type="text" required class="form-control" id="cuenta">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Descripción</label>
                                    <input type="text" required class="form-control" id="descripcion">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Padre</label>
                                    <select id="padre"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Subcuenta 1</label>
                                    <select id="subcuenta1"></select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Subcuenta 2</label>
                                    <select id="subcuenta2"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="modal_close btn btn-success">Guardar</button>
                    <button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    $(function() {
        // initTable()
    });
    const initTable = async () => {
        const query = `
            SELECT date(r.fecha_registro) as fecha_registro, CONCAT(r.tipo_comprobante,'-', r.numerocomprobante) as documento, r.tipo_comprobante, s.nombre_sucursal as sucursal, r.rucproveedor, p.razonsocial, subtotal, total, igv, r.codigomesconta
            FROM registro_compras r
            LEFT JOIN sucursal s on s.cod_sucursal = r.codigosuc 
            LEFT JOIN proveedor p on p.ruc = r.rucproveedor
            WHERE r.fecha_registro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
        `;
        let data = await get_data_dynamic(query);

        data = data.map(x => {
            let tipox = "";
            switch (x.tipo_comprobante) {
                case "otros":
                    tipox = 0;
                    break;
                case "fac":
                case "factura":
                    tipox = 1;
                    break;
                case "bol":
                case "boleta":
                    tipox = 3;
                    break;
                case "notacredito":
                    tipox = 14
                    break;
            }
            return {
                ...x,
                ["codigomesconta"]: x.codigomesconta ? x.codigomesconta.split("-")[1] : "",
                ["tipo"]: tipox
            }
        })
        $('#maintable').DataTable({
            data: data,
            destroy: true,
            columns: [{
                    title: 'T/D',
                    data: 'tipo'
                },
                {
                    title: 'FECHA',
                    data: 'fecha_registro'
                },
                {
                    title: 'N° DOC',
                    data: 'documento'
                },
                {
                    title: 'N° Reg',
                    data: 'codigomesconta'
                },
                {
                    title: 'RUC',
                    data: 'rucproveedor'
                },
                {
                    title: 'PROVEEDOR',
                    data: 'razonsocial'
                },
                {
                    title: 'ARTICULO',
                    defaultContent: "Articulos Varios"
                },
                {
                    title: 'VALOR COMPRA',
                    data: 'subtotal'
                },
                {
                    title: 'AFECTO',
                    data: 'subtotal'
                },
                {
                    title: 'INAFECTO',
                    defaultContent: ""
                },
                {
                    title: 'IGV',
                    data: 'igv'
                },
                {
                    title: 'TOTAL',
                    data: 'total'
                },
            ]
        });
    }
</script>