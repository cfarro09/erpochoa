<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Data Mercaderia";
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
<style>
    #maintable th {
        text-align: center;
    }
</style>

<div class="modal fade" id="mdetalle" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h5 class="modal-title" id="moperation-title">Detalle Traslado</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Estado</label>
                            <input type="text" readonly class="form-control" name="estadotraslado" id="estadotraslado">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Suc Origen</label>
                            <input type="text" readonly class="form-control" name="sucursalorigen" id="sucursalorigen">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Fecha Salida</label>
                            <input type="text" readonly class="form-control" name="fechasalida" id="fechasalida">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Personal Origen</label>
                            <input type="text" readonly class="form-control" name="personalorigen" id="personalorigen">
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Suc Destino</label>
                            <input type="text" readonly class="form-control" name="sucursaldestino" id="sucursaldestino">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Fecha Llegada</label>
                            <input type="text" readonly class="form-control" name="fechallegada" id="fechallegada">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Personal Destino</label>
                            <input type="text" readonly class="form-control" name="personaldestino" id="personaldestino">
                        </div>
                    </div>
                </div>


                <table id="detalletabla" class="display" width="100%"></table>
                <div class="text-right">
                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<table id="maintable" class="display" width="100%"></table>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
$suc = $_SESSION['cod_sucursal'];
?>

<style>
    .dt-buttons {
        margin-top: 0 !important;
        margin-bottom: 15px !important;
    }
</style>
<script>
    const codsucursal = <?= $suc ?>;
    $(function() {
        initTable()
    });

    async function verdetalle(e) {
        $("#mdetalle").modal();

        const query = `
        select id, ac1.usuario personalorigen, ac2.usuario personaldestino, gs.estado, fechainicio, fechallegada, productos, s1.nombre_sucursal sucursalorigen, s2.nombre_sucursal sucursaldestino, nroguia 
        from guiasucursal gs 
        left join acceso ac1 on ac1.codigopersonal = gs.personalorigen
        left join acceso ac2 on ac2.codigopersonal = gs.personaldestino
        inner join sucursal s1 on s1.cod_sucursal = gs.sucursalorigen 
        inner join sucursal s2 on s2.cod_sucursal = gs.sucursaldestino 
        where gs.id = ${e.dataset.id}
        `;
        
        let trasladosucursal = await get_data_dynamic(query);
        const row = trasladosucursal[0];

        estadotraslado.value = row.estado;

        sucursalorigen.value = row.sucursalorigen;
        sucursaldestino.value = row.sucursaldestino;
        fechasalida.value = row.fechainicio;
        personalorigen.value = row.personalorigen;
        if (row.estado === "COMPLETO" ){
            fechallegada.value = row.fechallegada;
            personaldestino.value = row.personaldestino;
        }else{
            fechallegada.value = "";
            personaldestino.value = "";
        }

        debugger
        $('#detalletabla').DataTable({
            data: JSON.parse(e.dataset.productos),
            destroy: true,
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
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            columns: [{
                    title: 'Producto',
                    data: 'nombreproducto'
                },
                {
                    title: 'Cantidad',
                    data: 'cantidad'
                }
            ]
        });
    }
    const initTable = async () => {
        const query = `
        select id, gs.estado, fechainicio, fechallegada, productos, s1.nombre_sucursal sucursalorigen, s2.nombre_sucursal sucursaldestino, nroguia 
        from guiasucursal gs 
        inner join sucursal s1 on s1.cod_sucursal = gs.sucursalorigen 
        inner join sucursal s2 on s2.cod_sucursal = gs.sucursaldestino
        where gs.sucursalorigen = ${codsucursal} or gs.sucursaldestino = ${codsucursal}
        `;

        let data = await get_data_dynamic(query);

        $('#maintable').DataTable({
            data: data,
            destroy: true,
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
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            columns: [{
                    title: 'Fecha Salida',
                    data: 'fechainicio'
                },
                {
                    title: 'Fecha Llegada',
                    data: 'fechallegada'
                },
                {
                    title: 'Sucursal Origen',
                    data: 'sucursalorigen'
                },
                {
                    title: 'Sucursal Destino',
                    data: 'sucursaldestino'
                },
                {
                    title: 'N° Guia',
                    data: 'nroguia'
                },
                {
                    title: "Estado",
                    data: 'estado'
                },
                {
                    title: 'Visualizar',
                    render: function(data, type, row) {
                        return `
                            <span  data-productos='${row.productos}' data-id='${row.id}'  onclick="verdetalle(this)">Ver Detalle</span>
                        `;
                    }
                }
            ]
        });
    }
</script>