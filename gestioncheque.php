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
        SELECT v.tipocomprobante,v.jsonpagos, v.total, v.codigocomprobante, c.cedula, c.nombre, c.paterno, j.ruc, j.razonsocial FROM ventas v left join cnatural c on c.codigoclienten=v.codigoclienten left join cjuridico j on j.codigoclientej=v.codigoclientej where v.jsonpagos like "%cheque%" or v.abonoproveedor like "%cheque%"`
        //     WHERE r.fecha_registro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
        // `;
        let data = await get_data_dynamic(query);
        const arrayxx = [];
        data = data.forEach(x => {
            const list = JSON.parse(x.jsonpagos);
            list.forEach(y => {
                arrayxx.push({
                    ...x,
                ["fecha_emision"]: y.fechaextra,
                ["montoextra"]: y.montoextra,
                ["documento"]: x.cedula ? x.cedula : x.ruc,
                ["identificacion"]: x.cedula ? `${x.paterno} ${x.nombre}` : x.razonsocial
                })
            })
        })
        $('#maintable').DataTable({
            data: arrayxx,
            destroy: true,
            columns: [
                {
                    title: 'fechaemision',
                    data: 'fecha_emision'
                },
                {
                    title: 'documento',
                    data: 'documento'
                },
                {
                    title: 'identificacion',
                    data: 'identificacion'
                },
                {
                    title: 'tipocomprobante',
                    data: 'tipocomprobante'
                },
                {
                    title: 'codigocomprobante',
                    data: 'codigocomprobante'
                },
                {
                    title: 'montoextra',
                    data: 'montoextra'
                },
            ]
        });
    }
</script>