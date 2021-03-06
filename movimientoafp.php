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

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <form id="formoperation">
            <div class="modal-content m-auto">
                <div class="modal-header">
                    <h2 class="modal-title" id="titleoperation">Detalle AFP</h2>
                </div>
                <input type="hidden" id="idoperation">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="detalletable" class="display table table-bordered" width="100%"></table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    $(function() {
        initTable();
    });

    const agregaroperation = () => {
        formoperation.reset();
        idoperation.value = "0";
        $("#moperation").modal();
        nombreafp.value = ""
        titleoperation.textContent = "Registrar datos de sueldo."
    }
    const detalle = async (id) => {
        $("#moperation").modal()
        let res = await get_data_dynamic(`
            select 
                ps.fecharegistro as fecha, p.cedula, concat(p.nombre,' ',p.paterno,' ',p.materno) as nombrepersonal, ps.id, ps.tegresos cargo, '0.00' as abono 
            from datos_sueldo ds 
            inner join personalsueldo ps on ps.regimen = ds.id 
            inner join personal p on p.codigopersonal=ps.personal 
            where ds.id = ${id} 
            UNION 
            select 
                pa.fecha_registro as fecha, concat('Recibo ', pa.id), 'Pago Efectivo Caja', pa.id, '0.00' cargo, pa.monto abono 
            from datos_sueldo ds 
            inner join pagosafp pa on pa.regimen = ds.id 
            where ds.id = ${id}`);
        let saldo = 0;
        res = res.map(x => {
            saldo += parseFloat(x.abono) - parseFloat(x.cargo);
            return {
                ...x,
                saldo: saldo.toFixed(2)
            }
        })
        $('#detalletable').DataTable({
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
    const initTable = async () => {
        const query = `
            select ds.id, ds.nombre, IFNULL(cargo, 0) cargo, IFNULl(abono, 0) abono, (IFNULL(cargo, 0) - IFNULL(abono, 0)) saldo from datos_sueldo ds where ds.nombre <> 'essalud'
            
        `;
        let data = await get_data_dynamic(query);

        $('#maintable').DataTable({
            data,
            destroy: true,
            columns: [{
                    title: 'nombre',
                    data: 'nombre',
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
                {
                    title: 'Acciones',
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-primary" onclick='detalle(${row.id})'>Detalle</button>
                        `;
                    }
                },

            ]
        });
    }
</script>