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
<form id="searchform">
    <div style="display: inline-flex">
        <select required style="width: 200px;" class="form-control" id="searchby">
            <option value="descinclude">Nombre Incluye</option>
            <option value="minicodigo">Codigo</option>
        </select>
        <input required style="width: 500px; margin-left: 20px" class="form-control" id="tosearch">

        <button style="margin-left: 20px" class="btn btn-success" type="submit">Buscar</button>
    </div>
</form>

<div id="anunciooo" style="margin-top: 20px; font-weight: bold;"></div>

<table id="maintable" class="display" width="100%"></table>

<div class="modal" id="modalxentregar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titlexentregar"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalbodyxentregar">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Comporbante</th>
                            <th scope="col">Cliente</th>
                        </tr>
                    </thead>
                    <tbody id="tablepagosdd">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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
                                            <input type="text" name="fecha_inicio" autocomplete="off" id="fecha_inicio" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
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
        // initTable();
        onloadSucursales()
    });
    searchform.addEventListener("submit", initTable);

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
    let xentregarpp = {};
    async function initTable(e) {
        xentregarpp = {}
        e.preventDefault();
        anunciooo.textContent = "BUSCANDO, ESPERE...";
        let where = "";

        if (searchby.value === "descstart") {
            where = ` where p.nombre_producto like '%${tosearch.value}' `;
        } else if (searchby.value === "descinclude") {
            where = ` where p.nombre_producto like '%${tosearch.value}%' `;
        } else if (searchby.value === "minicodigo") {
            where = ` where p.minicodigo like '%${tosearch.value}%' or p.codigo2 like '%${tosearch.value}%' or p.codigo3 like '%${tosearch.value}%'`;
        }

        const query = `
        select 
            p.codigoprod, p.minicodigo, p.codigo2, p.codigo3 ,p.nombre_producto, m.nombre marca, k.saldo,
             sum(Case When k5.detalle like '%compras%' or k5.detalle like '%entra%' or k5.detalle like '%anulacion%' Then k5.cantidad Else 0 End) entradas,
             sum(Case When k5.detalle like '%venta%' or k5.detalle like '%sale%' or k5.detalle like '%despacho%' Then k5.cantidad Else 0 End) salidas
        from producto p 
        left join marca m on m.codigomarca = p.codigomarca
        left join kardex_alm k5 on k5.codigoprod = p.codigoprod and k5.codsucursal = <?= $suc ?>
        left join kardex_alm k on k.id_kardex_alm = (SELECT MAX(k2.id_kardex_alm) from  kardex_alm k2 where k2.codigoprod = p.codigoprod and k2.codsucursal = <?= $suc ?>)
        ${where}
        group by p.codigoprod
        `;

        let data = await get_data_dynamic(query);

        data = await Promise.all(data.map(async x1 => {
            const codigoproductoofi = parseInt(x1.codigoprod);
            let cantt = 0;
            const queryrr = `
            select v.dataguia, dv.cantidad, v.codigocomprobante, v.tipocomprobante, 
            case when v.codigoclientej is null then (select concat(c.cedula, ' ', c.nombre, ' ', c.paterno) from cnatural c where c.codigoclienten=v.codigoclienten) else (select concat(j.ruc, ' ', j.razonsocial) from cjuridico j where j.codigoclientej = v.codigoclientej) end
            as clienttt
            from detalle_ventas dv 
            inner join ventas v on v.codigoventas = dv.codigoventa 
            where 
                sucursal = <?= $suc ?> and 
                dv.codigoprod = ${codigoproductoofi} and 
                (v.dataguia like '%"codigoprod":"${codigoproductoofi}"%' or modalidadentrega = 'Entrega almacen C/G' or modalidadentrega = 'Entrega inmediata C/G');`;

            let dataventas = await get_data_dynamic(queryrr);

            let tooltip = "";
            if (dataventas && dataventas.length > 0) {
                dataventas.forEach(x => {
                    cantt += parseFloat(x.cantidad);
                    let cantidadactual = parseFloat(x.cantidad);
                    if (x.dataguia) {
                        const dataguiaarr = JSON.parse(x.dataguia);
                        dataguiaarr.forEach(cc => {
                            if (cc.productos) {
                                const pp = cc.productos.find(y1 => y1.codigoprod == codigoproductoofi)
                                cantt -= parseFloat(pp.cantidad);
                                cantidadactual -= parseFloat(pp.cantidad);
                            }
                        })
                    }
                    if (cantidadactual > 0)
                        tooltip += `${cantidadactual}###${x.tipocomprobante} ${x.codigocomprobante}###${x.clienttt}#$#`;
                })
            }
            xentregarpp[codigoproductoofi] = tooltip;
            return {
                ...x1,
                xentregar: cantt,
                tooltip
            }
        }))

        $('#maintable').DataTable({
            data: data,
            destroy: true,
            columns: [{
                    title: 'Cod Fab',
                    data: 'minicodigo',
                },
                {
                    title: 'Nombre Producto',
                    data: 'nombre_producto'
                },
                {
                    title: 'Marca',
                    data: 'marca'
                },
                {
                    title: 'Codigo 2',
                    data: 'codigo2',
                    visible: false
                },
                {
                    title: 'Codigo 3',
                    data: 'codigo3',
                    visible: false
                },
                {
                    title: 'Entradas',
                    data: 'entradas'
                },
                {
                    title: 'Salidas',
                    data: 'salidas'
                },
                {
                    title: 'Saldo',
                    data: 'saldo',
                    className: 'dt-body-right',
                    render: function(data, type, row) {
                        return row.saldo ? row.saldo : "0.00"
                    }
                },
                {
                    title: 'Por Entregar',
                    className: 'dt-body-right',
                    render: function(data, type, row) {
                        if (row.xentregar && row.xentregar !== "0"){
                            return `<a href="#" onclick="onclickxx(${parseInt(row.codigoprod)})" class="tooltips" data-placement="top" data-original-title="${row.tooltip}">${row.xentregar}</a>`
                        } else {
                            return `${row.xentregar}`
                        }

                    }
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
        $('[data-toggle="tooltip"]').tooltip()
        $('.tooltips').tooltip();
        anunciooo.textContent = "";
    }

    const onclickxx = (codigprod) => {

        $("#modalxentregar").modal();
        let htmlrow = "";
        xentregarpp[codigprod].split("#$#").forEach((x) => {
            if (x) {
                htmlrow += "<tr>";
                x.split("###").forEach((o, index) => {
                    htmlrow += `<td class="${index === 0 ? 'text-right': ''}">${o}</td>`;
                });
                htmlrow += "</tr>";
            }
        });
        tablepagosdd.innerHTML = htmlrow;
    }

    getSelector("#form-setKardex").addEventListener("submit", e => {
        e.preventDefault();
        getdetailproduct();
    });

    function getdetailproduct() {
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
                                fecha: new Date(item.fecha),
                                detalle: item.isproveedor ? "INVENTARIO" : item.detalle, //isproveedor is isinventario
                                tipodocumento: (item.isproveedor ? "" : item.tipodocumento) + (item.detalle == "anulacion" ? " - anulada" : ""),
                                numero: item.isproveedor ? "" : item.numero,
                                entrada: /compra|entra|inventario|anulacion/gi.test(item.detalle) ? item.cantidad : "",
                                salida: /ventas|sale|despacho/gi.test(item.detalle) ? item.cantidad : "",
                                proveedor: item.isproveedor ? "INVENTARIO" : item.detalleaux,
                                saldo: item.saldo
                            });
                        }
                    });


                    listdata.sort(function(a, b) {
                        if (a.fecha > b.fecha) {
                            return -1;
                        }
                        if (b.fecha < a.fecha) {
                            return 1;
                        }
                        return 0;
                    });

                    $('#maintabledetail').DataTable({
                        data: listdata,
                        ordering: false,
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
                                title: 'Fecha',
                                render: function(data, type, row) {
                                    return row.fecha.toLocaleDateString();
                                }
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