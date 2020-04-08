<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Datos Sueldo";
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
<button class="btn btn-secondary" style="display: none" onclick="agregaroperation()">Agregar</button>
<table id="maintable" class="display table table-bordered" width="100%"></table>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    $(function() {
        initTable();
    });

    const initTable = async () => {
        let res = await get_data_dynamic(`
            select 
                ps.fecharegistro as fecha, p.cedula, concat(p.nombre,' ',p.paterno,' ',p.materno) as nombrepersonal, ps.id, ps.tegresos cargo, '0.00' as abono 
            from datos_sueldo ds 
            inner join personalsueldo ps on 1 = 1
            inner join personal p on p.codigopersonal=ps.personal 
            where ds.nombre = 'essalud'
            UNION 
            select 
                pa.fecha_registro as fecha, concat('Recibo ', pa.id), 'Pago Efectivo Caja', pa.id, '0.00' cargo, pa.monto abono 
            from datos_sueldo ds 
            inner join pagosafp pa on pa.regimen = 0
            where ds.nombre = 'essalud'`);
        let saldo = 0;
        res = res.map(x => {
            saldo += parseFloat(x.abono) - parseFloat(x.cargo);
            return {
                ...x,
                saldo: saldo.toFixed(2)
            }
        })
        $('#maintable').DataTable({
            data: res,
            ordering: false,

            destroy: true,
            columns: [
                {
                    title: 'fecha',
                    data: 'fecha',
                    className: 'dt-body-right'
                },
                {
                    title: 'cedula',
                    data: 'cedula',
                    className: 'dt-body-right'
                },
                 {
                    title: 'Nombres Personal',
                    data: 'nombrepersonal',
                    className: 'dt-body-right'
                },
                {
                    title: 'cargo',
                    data: 'cargo',
                    className: 'dt-body-right'
                },
                {
                    title: 'abono',
                    data: 'abono',
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