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
<div class="row">
    <div class="col-sm-12">
        <div class="col-sm-3">
            <div class="form-group">
                <label class="control-label">Sucursal</label>
                <select class="form-control" id="sucursalxx"></select>
            </div>
        </div>
        <div class="col-sm-3" style="margin-bottom: 50px">
            <div class="form-group">
                <label class="control-label">Tipo</label>
                <select class="form-control" id="tipo">
                    <option value="kardexalmacen">Kardex Almacen</option>
                    <option value="kardexcontable">Kardex Contable</option>
                </select>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <button style="margin-top: 15px" onclick="initTable()" class="btn btn-block btn-primary">BUSCAR</button>
            </div>
        </div>
    </div>
</div>
<table id="maintable" class="display" width="100%" ></table>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    $(function() {
        const querysuc = `select cod_sucursal, nombre_sucursal from sucursal where estado = 1`;
        get_data_dynamic(querysuc).then(r => {
            cargarselect2("#sucursalxx", r, "cod_sucursal", "nombre_sucursal");
        });

        // initTable()
    });
    const initTable = async () => {
        const codsucursal = sucursalxx.value
        let query = "";
        if(tipo.value == "kardexalmacen"){
            query = `
            select k.codigoprod, k.saldo, p.nombre_producto from kardex_alm k
            inner join producto p on p.codigoprod = k.codigoprod
            where k.codsucursal = ${codsucursal} and k.saldo > 0
            and k.id_kardex_alm in
            (select max(id_kardex_alm) from kardex_alm where codsucursal = ${codsucursal} group by codigoprod)
            `;
        }else{
            query = `
            select k.codigoprod, k.saldo, p.nombre_producto from kardex_contable k
            inner join producto p on p.codigoprod = k.codigoprod
            where k.sucursal = ${codsucursal} and k.saldo > 0
            and k.id_kardex_contable in
            (select max(id_kardex_contable) from kardex_contable where sucursal = ${codsucursal} group by codigoprod)
            `
        }
        let data = await get_data_dynamic(query);

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
                    title: 'PRODUCTO',
                    data: 'nombre_producto'
                },
                {
                    title: 'SALDO',
                    data: 'saldo'
                }
            ]
        });
    }
</script>