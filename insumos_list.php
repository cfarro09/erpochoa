<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Data Insumos";
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

<script type="text/javascript">
    window.onload = e => {
        const codsucursal = <?= $suc ?>;
        if (codsucursal == 1) {

            getSelector("#btnagregargordis .disabled").classList.remove("disabled")
            getSelector("#btnagregargordis .disabled").classList.remove("disabled")

        }
    }
    const eliminarproducto = async (id, nombre_producto) => {
        const ff = confirm(`Desea eliminar el insumo ${nombre_producto}?`)

        //modificale la query segun el estado o borra todo
        if (ff) {
            const query = `dsjndasjdas from insumo where codigoins = ${id}`;

            const data = {
                header: query,
                detalle: []
            }
            let res = await ll_dynamic(data);
            if (res && res.success) {
                alert("Se eliminó satisfactoriamente")
                initTable()
            } else
                alert(res.msg)
        }
    }
    $(function() {
        initTable()
        onloadSucursales()
    });
    const onloadSucursales = async () => {
        //     const res = await get_data_dynamic("select nombre_sucursal, cod_sucursal from sucursal where estado = 1");

        //    cargarselect2("#sucursales", res, "cod_sucursal", "nombre_sucursal")
    }
    const getdetail = async (idpro, name) => {
        /*    getSelector("#codproducto").value = idpro
            getSelector("#headerKardex").textContent = name
            detalleKardexAlmProd.innerHTML = ""
            $("#mkardex").modal();
            $("#fecha_inicio").val("");
            $("#fecha_termino").val("");

            const res = await get_data_dynamic(`SELECT s.nombre_sucursal name, IF(k.saldo IS NULL or k.saldo = '', '0', k.saldo) as saldo from sucursal s left join kardex_alm k on k.codsucursal = s.cod_sucursal and k.id_kardex_alm = ( SELECT MAX(t2.id_kardex_alm) FROM kardex_alm t2 WHERE k.codigoprod = t2.codigoprod and t2.codsucursal = s.cod_sucursal) and k.codigoprod = ${idpro} where s.cod_sucursal != 10 order by cod_sucursal asc`);

            containersucursales.innerHTML = ""
            res.forEach(s => {
                containersucursales.innerHTML += `
                    <div>${s.name}: ${s.saldo}</div>
                `
            })*/
    }

    const initTable = async () => {
        const query = `
        select 
            p.codigoins, p.nombre_insumo, m.nombre as marca, c.nombre_color as Color, pr.nombre_presentacion as Presentacion 
            from insumo p inner join marca m on p.codigomarca=m.codigomarca 
            inner JOIN color c on c.codigocolor=p.codigocolor inner JOIN presentacion pr on pr.codigopresent=p.codigopresent 
            where (p.estado = 0) group by p.codigoins order by p.codigoins
        `;

        let data = await get_data_dynamic(query);

        $('#maintable').DataTable({
            data: data,

            destroy: true,

            ordering: false,
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
                    title: 'codigoins',
                    data: 'codigoins'
                },
                {
                    title: 'nombre_insumo',
                    data: 'nombre_insumo'
                },
                {
                    title: 'marca',
                    data: 'marca'
                },
                {
                    title: 'Color',
                    data: 'Color'
                },
                {
                    title: 'Presentacion',
                    data: 'Presentacion'
                },
                {
                    title: 'Editar',
                    render: function(data, type, row) {
                        const nn = row.nombre_insumo.replace('"', '').replace("'", "");
                        return `<a onClick="abre_ventana('Emergentes/insumos_list_edit.php?codigoins=${parseInt(row.codigoins)}',<?php echo $popupAncho ?>,<?php echo $popupAlto ?>)"  class="btn btn-success">Editar</a>`
                    }
                },
                {
                    title: 'Eliminar',
                    render: function(data, type, row) {
                        //cuando presioee eliminar me elimine , YA ESA ES TU TAREA ESO LO PUEDES HACER NO SE COMO PERO ES TU CHAMBA, BUENO HASTA AQUI FUE, AVISAME CUANDO ME DEPOSITESok ya mañana seguimos tengo sueño
                        if (row.kardex) {
                            return ""
                        } else {
                            let ss = row.nombre_insumo.replace(/'/g, "")

                            return `<a onClick='eliminarproducto(${parseInt(row.codigoins)}, "${ss}")'  class="btn btn-success">Eliminar</a>`
                        }
                    }

                }
            ]
        });
    }
</script>