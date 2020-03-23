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

<table id="maintable" class="display" width="100%"></table>

<div class="modal fade" id="mkardex" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h2 class="modal-title" id="moperation-title">Detalle del producto</h2>
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
                                            <input type="text" required name="fecha_inicio" autocomplete="off" id="fecha_inicio" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Fecha termino</label>
                                            <input type="text" name="fecha_termino" autocomplete="off" id="fecha_termino" required class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-hover" id="">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" id="headerKardex"></th>
                                                    <th style="background-color: #01aaff; color: white; text-align: center">ENTRADA</th>
                                                    <th style="background-color: #01aaff; color: white; text-align: center">SALIDA</th>
                                                    <th style="background-color: #01aaff; color: white; text-align: center">SALDO</th>
                                                </tr>
                                                <tr>
                                                    <th>FECHA</th>
                                                    <th>DETALLE</th>
                                                    <th>TIPO</th>
                                                    <th>N° COMP/GUIA</th>
                                                    <th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
                                                    <th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
                                                    <th style="background-color: #01aaff; color: white; text-align: center">CANTIDAD</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleKardexAlmProd" class="text-center"></tbody>
                                        </table>
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
    $(function() {
        initTable()
        onloadSucursales()
    });
    const onloadSucursales = async () => {
        const res = await get_data_dynamic("select nombre_sucursal, cod_sucursal from sucursal where estado = 1");

        cargarselect2("#sucursales", res, "cod_sucursal", "nombre_sucursal")
    }
    const getdetail = async (idpro, name) => {
        getSelector("#codproducto").value = idpro
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
        })
    }

    const initTable = async () => {
        const query = `
        select 
            p.codigoprod ,p.nombre_producto, m.nombre marca, IF(k.saldo IS NULL or k.saldo = '', '0', k.saldo) as saldo,
             IF(kc.saldo IS NULL or kc.saldo = '', '0', kc.saldo - kc.saldo) as xentregar,
             sum(Case When k5.detalle like '%compras%' or k5.detalle like '%entra%' Then k5.cantidad Else 0 End) entradas,
             sum(Case When k5.detalle like '%venta%' or k5.detalle like '%sale%' Then k5.cantidad Else 0 End) salidas
        from producto p 
        left join marca m on m.codigomarca = p.codigomarca
        left join kardex_alm k5 on k5.codigoprod = p.codigoprod and codsucursal = <?= $suc ?>
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
                        const nn = row.nombre_producto.replace('"', '').replace("'", "");
                        return `<button class="btn btn-primary" onclick='getdetail(${parseInt(row.codigoprod)}, "${nn}")'>detalle</button>`
                    }
                }
            ]
        });
    }

    getSelector("#form-setKardex").addEventListener("submit", e => {
        e.preventDefault();
        const codsucursal = $("#sucursales").val()
        const fecha_inicio = $("#fecha_inicio").val()
        const fecha_termino = $("#fecha_termino").val()
        const codproducto = getSelector("#codproducto").value
        var formData = new FormData();
        formData.append("codsucursal", codsucursal);
        formData.append("fecha_inicio", fecha_inicio);
        formData.append("fecha_termino", fecha_termino);
        formData.append("codproducto", codproducto);

        getSelector("#detalleKardexAlmProd").innerHTML = "<tr><td colspan='6'>No hay registros</td></tr>"

        fetch(`getKardexAlmFromProductList.php`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res.length > 0) {
                    getSelector("#detalleKardexAlmProd").innerHTML = `
					<tr>
					<td></td>
					<td>Inventario inicial</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>	
					<td>0</td>
					</tr>
					`
                    let i = 0;
                    res.forEach(item => {
                        if (item.cantidad != "0") {
                            getSelector("#detalleKardexAlmProd").innerHTML += `
							<tr>
							<td>${new Date(item.fecha).toLocaleDateString()}</td>
							<td>${item.detalle}</td>
							<td>${item.tipodocumento}</td>
							<td>${item.numero}</td>

							<td>${item.detalle.toLowerCase().includes("compras") || item.detalle.toLowerCase().includes("entra") ? item.cantidad : ""}</td>
							<td>${item.detalle.toLowerCase().includes("ventas") || item.detalle.toLowerCase().includes("sale") ? item.cantidad : ""}</td>


							<td>${item.saldo}</td>
							</tr>
							`;
                        }

                    });

                }
            });


    });
</script>