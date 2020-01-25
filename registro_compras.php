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
        initTable()
    });
    const initTable = async () => {
        const query = `
            SELECT date(r.fecha_registro) as fecha_registro, CONCAT(r.tipo_comprobante,'-', r.numerocomprobante) as documento, s.nombre_sucursal as sucursal, r.rucproveedor, p.razonsocial,
            subtotal, total, igv 
            FROM registro_compras r
            LEFT JOIN sucursal s on s.cod_sucursal = r.codigosuc 
            LEFT JOIN proveedor p on p.ruc = r.rucproveedor
        `;
        const data1 = await get_data_dynamic(query);
        $('#maintable').DataTable({
            data: data1,
            columns: [
                {
                    title: 'FECHA',
                    data: 'fecha_registro'
                },
                {
                    title: 'N° DOC',
                    data: 'documento'
                },
                {
                    title: 'SUCURSAL',
                    data: 'sucursal'
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
                    title: 'SUBTOTAL',
                    data: 'subtotal'
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