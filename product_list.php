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
            p.codigoprod, p.minicodigo, p.codigo2, p.codigo3, p.nombre_producto, m.nombre as marca, c.nombre_color as Color, pr.nombre_presentacion as Presentacion, ct.nombre as Categoria, k.id_kardex_alm AS kardex from producto p inner join marca m on p.codigomarca=m.codigomarca inner JOIN color c on c.codigocolor=p.codigocolor inner JOIN presentacion pr on pr.codigopresent=p.codigopresent inner join categoria ct on ct.codigocat=p.codigocat 
             left join kardex_alm k on p.codigoprod = k.codigoprod where (p.estado = 0) group by p.codigoprod order by p.nombre_producto
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
                    title: 'codigoprod',
                    data: 'codigoprod'
                },
                {
                    title: 'nombre_producto',
                    data: 'nombre_producto'
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
                    title: 'Categoria',
                    data: 'Categoria',
                },
                {
                    title: 'Codigo',
                    data: 'minicodigo',
                },
                {
                    title: 'codigo2',
                    data: 'codigo2',
                    visible: false
                },
                {
                    title: 'codigo3',
                    data: 'codigo3',
                    visible: false
                },
                {
                    title: 'Editar',
                    render: function(data, type, row) {
                        const nn = row.nombre_producto.replace('"', '').replace(/'|"/gi, "");
                        return `<a onClick="${codsucursal !== 1 ? 'return false;': ''}abre_ventana('Emergentes/product_list_edit.php?codigoprod=${parseInt(row.codigoprod)}',<?php echo $popupAncho ?>,<?php echo $popupAlto ?>)" ${codsucursal !== 1 ? 'disabled': ''}  class="btn btn-success">Editar</a>`
                    }
                },

                {
                    title: 'Eliminar',
                    render: function(data, type, row) {
                        //cuando presioee eliminar me elimine , YA ESA ES TU TAREA ESO LO PUEDES HACER NO SE COMO PERO ES TU CHAMBA, BUENO HASTA AQUI FUE, AVISAME CUANDO ME DEPOSITESok ya mañana seguimos tengo sueño
                        if (row.kardex) {
                            return ""
                        } else {
                            let ss = row.nombre_producto.replace(/'|"/g, "")

                            return `<a ${codsucursal !== 1 ? 'disabled': ''} onClick='${codsucursal !== 1 ? 'return false;': ''}eliminarproducto(${parseInt(row.codigoprod)}, "${ss}")'  class="btn btn-success">Eliminar</a>`
                        }
                    }

                }

            ]
        });
        document.querySelector("#maintable tbody tr:last-child").style.fontWeight = "bold"

    }

    window.onload = e => {
        
        if (codsucursal == 1) {

            getSelector("#btnagregargordis .disabled").classList.remove("disabled")

        }
    }
    const eliminarproducto = async (id, nombre_producto) => {
        const ff = confirm(`Desea eliminar el producto ${nombre_producto}?`)

        //modificale la query segun el estado o borra todo
        if (ff) {
            const query = `dsjndasjdas from producto where codigoprod = ${id}`;

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
</script>