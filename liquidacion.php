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

$suc = $_SESSION['cod_sucursal'];

?>

<style>
    .dt-buttons {
        margin-top: 0 !important;
        margin-bottom: 15px !important;
    }
</style>
<table id="maintable" class="display" width="100%"></table>


<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    $(function() {
        getdetail()
    });
    
    const getdetail = async () => {
        let despose = [];
        const id = <?= $suc ?>;
        if(id != 11){
            const query1 = `
                SELECT 
                    fecha, cantidad as despose, '' as total, motivo
                FROM despose
                WHERE 
                    sucursal = ${id} and (tipo = 'despose')`;

        
            despose = await get_data_dynamic(query1);
        }else{
            const query1 = `
                SELECT 
                    fecha, tipo, cantidad as total, motivo
                FROM despose
                WHERE 
                    sucursal = ${id} and (tipo = 'despose' or tipo = 'ingresocaja')`;

            despose = await get_data_dynamic(query1);
        }
        setConsolidado(id, despose)
    }

    const proccessIngresosEfectivo = async id => {
        const query = `
            SELECT 
                v.jsonpagos, v.fecha_emision
            FROM ventas v
            WHERE 
                v.sucursal = ${id}`;

        const da = await get_data_dynamic(query);
        const res = {
            datatotble: [],
            total: 0
        }
        const data = {}

        da.forEach(iii => {
            const arraypagos = JSON.parse(iii.jsonpagos);
            arraypagos.forEach(ixx => {
                if (ixx.tipopago == "efectivo") {
                    if (!data[iii.fecha_emision])
                        data[iii.fecha_emision] = 0
                    data[iii.fecha_emision] += ixx.montoextra ? parseFloat(ixx.montoextra) : 0
                    res.total += parseFloat(ixx.montoextra)
                }
            })
        })
        for (const [key, value] of Object.entries(data))
            res.datatotble.push({
                fecha: key,
                total: value.toFixed(2),
                despose: '',
                motivo: ""
            })
        return res
    }

    const setConsolidado = async (id, des) => {
        let qwer = []
        if(id != 11){
            const rr = await proccessIngresosEfectivo(id)
            const datatotble = rr.datatotble;
            
            qwer = [...datatotble, ...des];
            let saldo = 0;
            qwer.sort(function (a, b) {
                if (a.fecha < b.fecha) {
                    return -1;
                }
                if (b.fecha < a.fecha) {
                    return 1;
                }
                return 0;
            });
            
            qwer = qwer.map(x => {
                const despose = x.despose ? parseFloat(x.despose) : 0
                const total = x.total ? parseFloat(x.total) : 0
                saldo = saldo + total - despose
                x.saldo = saldo.toFixed(2)
                return x
            })
        }else{
            let saldo = 0;
            des.forEach(x => {
                if(x.tipo == "ingresocaja"){
                    saldo += parseFloat(x.total)
                    qwer.push({
                        ...x,
                        despose: 0,
                        saldo: saldo.toFixed(2)
                    })
                }else{
                    saldo -= parseFloat(x.total)
                    qwer.push({
                        total: 0,
                        despose: x.total,
                        saldo: saldo.toFixed(2)
                    })
                }
            })
        }
        
        
        $('#maintable').DataTable({
            data: qwer,
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
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            columns: [
                {
                    title: 'fecha',
                    data: 'fecha'
                },
                {
                    title: 'motivo',
                    data: 'motivo'
                },
                {
                    title: 'Ingreso',
                    data: 'total',
                    className: 'dt-body-right'
                },
                {
                    title: 'Egreso',
                    data: 'despose',
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