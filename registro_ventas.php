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
<div class="row" style="margin-bottom: 70px;">
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
    $(function() {
        // initTable();
    });

    const initTable = async () => {
        const query = `
        select v.fecha_emision, v.tipocomprobante, v.codigocomprobante, cn.cedula, cj.ruc, CONCAT(cn.paterno, ' ', cn.materno, ' ', cn.nombre) as ClienteNatural, cj.razonsocial,v.subtotal, v.igv, v.total from ventas v left join cnatural cn on cn.codigoclienten = v.codigoclienten left join cjuridico cj on cj.codigoclientej = v.codigoclientej
            WHERE v.fecha_emision BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
        `;
        let data = await get_data_dynamic(query);
        data = data.map(x => {
            return {
                ...x,
                ["igv"]: x.tipocomprobante == "boleta" ? 0 : parseFloat(x.igv).toFixed(2),
                ["subtotal"]: x.tipocomprobante == "boleta" ? parseFloat(x.total).toFixed(2) : parseFloat(x.subtotal).toFixed(2),
                ["total"]: parseFloat(x.total).toFixed(2),
                ["identificacion"]: x.ClienteNatural ? x.ClienteNatural : x.razonsocial,
                ["documento"]: x.cedula ? x.cedula : x.ruc,
            }
        })
        $('#maintable').DataTable({
            data,
            destroy: true,
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
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
            columns: [{
                    title: 'FECHAEMISION',
                    data: 'fecha_emision',
                    width: "10%"
                },
                {
                    title: 'tipocomprobante',
                    data: 'tipocomprobante',
                    width: "10%"
                },
                {
                    title: 'codigocomprobante',
                    data: 'codigocomprobante',
                    width: "10%"
                },
                {
                    title: 'documento',
                    data: 'documento',
                    width: "30%"
                },
                {
                    title: 'identificacion',
                    data: 'identificacion',
                    width: "40%"
                },
                {
                    title: 'BASE IMPONIBLE DE LA OPERACION GRAVADA',
                    data: 'subtotal',
                    width: "10%",
                    className: 'dt-body-right'

                },
                {
                    title: 'IMPUESTO GENERAL A LAS VENTAS Y/O IPM',
                    data: 'igv',
                    width: "10%",
                    className: 'dt-body-right'

                },
                {
                    title: 'IMPROTE TOTAL DEL COMPROBANTE DE PAGO',
                    data: 'total',
                    width: "10%",
                    className: 'dt-body-right'

                },

            ]
        });
    }
</script>