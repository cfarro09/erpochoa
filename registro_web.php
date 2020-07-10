<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Ventas por la Web";
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



<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    $(function() {
        // initTable()
    });
    const initTable = async () => {
        /*  const query = `
              SELECT date(r.fecha_registro) as fecha_registro, CONCAT(r.tipo_comprobante,'-', r.numerocomprobante) as documento, r.tipo_comprobante, s.nombre_sucursal as sucursal, r.rucproveedor, p.razonsocial, subtotal, total, igv, r.codigomesconta
              FROM registro_compras r
              LEFT JOIN sucursal s on s.cod_sucursal = r.codigosuc 
              LEFT JOIN proveedor p on p.ruc = r.rucproveedor
              WHERE r.fecha_registro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
          `;*/
        /* const query = `
             SELECT name as nombre_cliente, contact as celular, email, address as direccion, item as detalle, amount as total, 
             status as estado, modoentrega, formapago, modoentregadetalle
             FROM orden_compra_web
         `;*/
        const query = `
            SELECT date(dateOrdered) as fecha_registro, name as nombre_cliente, contact as celular, email, address as direccion, item as detalle, amount as total, 
            status as estado, modoentrega, formapago, modoentregadetalle
            FROM orden_compra_web where  dateOrdered BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
        `;
        let data = await get_data_dynamic(query);

        $('#maintable').DataTable({
            data: data,
            destroy: true,
            columns: [{
                    title: 'FECHA',
                    data: 'fecha_registro'
                },

                {
                    title: 'CLIENTE',
                    data: 'nombre_cliente'
                },
                {
                    title: 'CELULAR',
                    data: 'celular'
                },
                {
                    title: 'EMAIL',
                    data: 'email'
                },
                {
                    title: 'TOTAL',
                    data: 'total'
                },
                {
                    title: 'DETALLE',
                    defaultContent: "Articulos Varios"
                },
                {
                    title: 'ENTREGA',
                    data: 'modoentrega'
                },
                {
                    title: 'ESTADO',
                    data: 'estado'
                },
                {
                    title: 'PAGO',
                    data: 'formapago'
                },
            ]
        });
    }
</script>