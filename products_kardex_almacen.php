<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Kardex de Almacen";
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

<div class="modal fade" id="mkardex" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h2 class="modal-title" id="moperation-title-kardex">Detalle del producto</h2>
            </div>
            <div class="modal-body">
                <input type="hidden" id="codproducto">
                <form id="form-setKardex" action="kardex_almacen.php" method="GET">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-md-12" style="margin-bottom: 10px">
                                <div id="containersucursales"></div>
                            </div>
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Sucursales</label>
                                            <select name="codigosuc" required id="sucursales" class=" select2">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Fecha Inicio</label>
                                            <input type="text"  name="fecha_inicio" autocomplete="off" id="fecha_inicio" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin-bottom: 7rem">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Fecha termino</label>
                                            <input type="text" name="fecha_termino" autocomplete="off" id="fecha_termino" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <table id="maintabledetail" class="display" width="100%"></table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Imprimir</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar|</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    const sucursalxx = <?= $suc ?>;
    $(function() {
        initTable()
        onloadSucursales()
    });
    const onloadSucursales = async () => {
        const res = await get_data_dynamic("select nombre_sucursal, cod_sucursal from sucursal where estado = 1");

        cargarselect2("#sucursales", res, "cod_sucursal", "nombre_sucursal");

        
        $('#sucursales').val(sucursalxx).trigger('change');
    }
    const getdetail = async (idpro, name) => {
        getSelector("#codproducto").value = idpro
        getSelector("#moperation-title-kardex").textContent = `Detalle de ${name}`
        // detalleKardexAlmProd.innerHTML = ""
        $("#mkardex").modal();
        $("#fecha_inicio").val("");
        $("#fecha_termino").val("");

        const res = await get_data_dynamic(`SELECT s.nombre_sucursal name, IF(k.saldo IS NULL or k.saldo = '', '0', k.saldo) as saldo from sucursal s left join kardex_alm k on k.codsucursal = s.cod_sucursal and k.id_kardex_alm = ( SELECT MAX(t2.id_kardex_alm) FROM kardex_alm t2 WHERE k.codigoprod = t2.codigoprod and t2.codsucursal = s.cod_sucursal) and k.codigoprod = ${idpro} where s.cod_sucursal != 10 order by cod_sucursal asc`);

        containersucursales.innerHTML = ""
        res.forEach(s => {
            containersucursales.innerHTML += `
                <div>${s.name}: ${s.saldo}</div>
            `
        });
        getdetailproduct();
    }

    const initTable = async () => {
        const query = `
        select 
            p.codigoprod, p.minicodigo, p.codigo2, p.codigo3 ,p.nombre_producto, m.nombre marca, IF(k.saldo IS NULL or k.saldo = '', '0', k.saldo) as saldo,
             Case when IFNULL(kc.saldo, 0) = 0 then 0 else (IFNULL(k.saldo, 0) - IFNULL(kc.saldo, 0)) end as xentregar,
             sum(Case When k5.detalle like '%compras%' or k5.detalle like '%entra%' or k5.detalle like '%anulacion%' Then k5.cantidad Else 0 End) entradas,
             sum(Case When k5.detalle like '%venta%' or k5.detalle like '%sale%' or k5.detalle like '%despacho%' Then k5.cantidad Else 0 End) salidas
        from producto p 
        left join marca m on m.codigomarca = p.codigomarca
        left join kardex_alm k5 on k5.codigoprod = p.codigoprod and k5.codsucursal = <?= $suc ?>
        left join kardex_alm k on k.id_kardex_alm = (SELECT MAX(k2.id_kardex_alm) from  kardex_alm k2 where k2.codigoprod = p.codigoprod and k2.codsucursal = <?= $suc ?>)
        left join kardex_contable kc on kc.id_kardex_contable = (SELECT MAX(kc2.id_kardex_contable) from kardex_contable kc2 where kc2.codigoprod = p.codigoprod and kc2.sucursal = <?= $suc ?>)

        group by p.codigoprod
        `;

        let data = await get_data_dynamic(query);

        $('#maintable').DataTable({
            data: data,
            destroy: true,
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
                    title: 'minicodigo',
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
                    title: 'entradas',
                    data: 'entradas'
                },
                {
                    title: 'salidas',
                    data: 'salidas'
                },
                {
                    title: 'saldo',
                    data: 'saldo',
                    className: 'dt-body-right'
                },
                {
                    title: 'xentregar',
                    data: 'xentregar',
                    className: 'dt-body-right'
                },
                {
                    title: 'acciones',
                    render: function(data, type, row) {
                        const nn = row.nombre_producto.replace(/'|"/gi, '');
                        return `<button class="btn btn-primary" onclick='getdetail(${parseInt(row.codigoprod)}, "${nn}")'>detalle</button>`

                    }
                }
            ]
        });
    }

    getSelector("#form-setKardex").addEventListener("submit", e => {
        e.preventDefault();
        getdetailproduct();
    });
    
    function getdetailproduct (){
        maintabledetail.innerHTML = "";
        const codsucursal = $("#sucursales").val()
        const fecha_inicio = $("#fecha_inicio").val() ? $("#fecha_inicio").val() : "1999-09-09";
        const fecha_termino = $("#fecha_termino").val() ? $("#fecha_termino").val() : "2030-03-03";
        
        const codproducto = getSelector("#codproducto").value
        var formData = new FormData();
        formData.append("codsucursal", codsucursal);
        formData.append("fecha_inicio", fecha_inicio);
        formData.append("fecha_termino", fecha_termino);
        formData.append("codproducto", codproducto);

        //getSelector("#detalleKardexAlmProd").innerHTML = "<tr><td colspan='6'>No hay registros</td></tr>"

        fetch(`getKardexAlmFromProductList.php`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res.length > 0) {
                    const listdata = [];
                    let i = 0;
                    res.forEach(item => {
                        item.isproveedor = item.isproveedor ? parseInt(item.isproveedor) : 0;
                        if (item.cantidad != "0") {
                            listdata.push({
                                fecha: new Date(item.fecha).toLocaleDateString(),
                                detalle: item.isproveedor ? "INVENTARIO" : item.detalle, //isproveedor is isinventario
                                tipodocumento: (item.isproveedor ? "" : item.tipodocumento) + (item.detalle == "anulacion" ? " - anulada" : ""),
                                numero: item.isproveedor ? "" : item.numero,
                                entrada: /compra|entra|inventario|anulacion/gi.test(item.detalle) ? item.cantidad : "",
                                salida: /ventas|sale|despacho/gi.test(item.detalle)? item.cantidad : "",
                                proveedor: item.isproveedor ? "INVENTARIO" : item.detalleaux,
                                saldo: item.saldo
                            });
                        }

                    });
                    $('#maintabledetail').DataTable({
                        data: listdata,
                        ordering: false,
                        dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
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
                        columns: [
                            {
                                title: 'Fecha',
                                data: 'fecha',
                            },
                            {
                                title: 'Detalle',
                                data: 'proveedor',
                            },
                            {
                                title: 'Detalle',
                                data: 'detalle',
                                visible: false
                            },
                            {
                                title: 'Tipo Doc',
                                data: 'tipodocumento',
                            },
                            {
                                title: 'N°',
                                data: 'numero',
                            },
                            {
                                title: 'Entrada',
                                data: 'entrada',
                                className: 'dt-body-right'
                            },
                            {
                                title: 'Salida',
                                data: 'salida',
                                className: 'dt-body-right'
                            },
                            {
                                title: 'Saldo',
                                data: 'saldo',
                                className: 'dt-body-right'
                            },
                        ]
                    });
                }
            });


    
    }
</script>