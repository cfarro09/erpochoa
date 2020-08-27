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
<div class="row" style="margin-bottom: 6rem;">
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
<table id="maintable"  class="display" width="100%"></table>

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
            SELECT 'Articulos Varios' detalle, r.rucproveedor, p.razonsocial, date(r.fecha_registro) as fecha_registro, CONCAT(r.tipo_comprobante,'-', r.numerocomprobante) as documento, r.tipo_comprobante, s.nombre_sucursal as sucursal, subtotal, total, igv, r.codigomesconta
            FROM registro_compras r
            LEFT JOIN sucursal s on s.cod_sucursal = r.codigosuc 
            LEFT JOIN proveedor p on p.ruc = r.rucproveedor
            WHERE 
                p.razonsocial not like  '%inventario%' and
                 r.fecha_registro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
            UNION
            select 'Estibador' detalle,  p.ruc rucproveedor, p.razonsocial, date(ec.fecharegistro) as fecha_registro, CONCAT(ec.tipocomprobante, ' - ', ec.numerocomprobante) documento, ec.tipocomprobante, s.nombre_sucursal as sucursal, ec.precioestibador_soles subtotal, ec.precioestibador_soles*1.18 total,  ec.precioestibador_soles*0.18 igv, '' codigomesconta from estibador_compra ec  
                LEFT JOIN proveedor p on p.ruc = ec.rucestibador
                left join registro_compras rc on rc.codigorc = ec.codigocompras
                LEFT JOIN sucursal s on s.cod_sucursal = rc.codigosuc 
                where ec.fecharegistro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
			UNION 
			select concat('Transporte - ', tc.tipo_transporte) detalle, p.ruc rucproveedor, p.razonsocial, date(tc.fecharegistro) as fecha_registro, CONCAT(tc.tipocomprobante, ' - ', tc.numerocomprobante) documento, tc.tipocomprobante, s.nombre_sucursal as sucursal, tc.preciotransp_soles subtotal, tc.preciotransp_soles*1.18 total,  tc.preciotransp_soles*0.18 igv, '' codigomesconta from transporte_compra tc 
                LEFT JOIN proveedor p on p.ruc = tc.ructransporte
                left join registro_compras rc on rc.codigorc = tc.codigocompras
                LEFT JOIN sucursal s on s.cod_sucursal = rc.codigosuc 
                where tc.fecharegistro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
			UNION
			select  'Nota Debito' detalle, p.ruc rucproveedor, p.razonsocial, date(nd.fecharegistro) as fecha_registro, CONCAT(nd.tipocomprobante, ' - ', nd.numerocomprobante) documento, nd.tipocomprobante, s.nombre_sucursal as sucursal, nd.preciond_soles subtotal, nd.preciond_soles*1.18 total,  nd.preciond_soles*0.18 igv, '' codigomesconta from notadebito_compra nd 
                LEFT JOIN proveedor p on p.ruc = nd.rucnd
                left join registro_compras rc on rc.codigorc = nd.codigocompras
                LEFT JOIN sucursal s on s.cod_sucursal = rc.codigosuc 
                where nd.fecharegistro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
			UNION
			select 'Nota Credito' detalle, p.ruc rucproveedor, p.razonsocial, date(nc.fecharegistro) as fecha_registro, CONCAT(nc.tipocomprobante, ' - ', nc.numerocomprobante) documento, nc.tipocomprobante, s.nombre_sucursal as sucursal, nc.precionc_soles subtotal, nc.precionc_soles*1.18 total,  nc.precionc_soles*0.18 igv, '' codigomesconta from notacredito_compra nc	
                LEFT JOIN proveedor p on p.ruc = nc.rucnotacredito
                left join registro_compras rc on rc.codigorc = nc.codigocompras
                LEFT JOIN sucursal s on s.cod_sucursal = rc.codigosuc 
                where nc.fecharegistro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
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
            if (x.detalle === "Articulos Varios") { 
                return {
                    ...x,
                    ["codigomesconta"]: (x.codigomesconta ? (x.codigomesconta.split("-").length > 1 ? x.codigomesconta.split("-")[1] : "") : ""),
                    ["tipo"]: tipox
                }
            } else {
                return {
                    ...x,
                    ["codigomesconta"]: "",
                    ["tipo"]: tipox
                }
            }
        })
        $('#maintable').DataTable({
            data: data,
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
                    data: 'detalle'
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