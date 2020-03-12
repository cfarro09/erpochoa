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

<table id="maintable" class="display" width="100%"></table>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <form id="formoperacion" action="">
                <div class="modal-header">
                    <h2 class="modal-title">TRASLADO DE MERCANCIA</h2>
                </div>

                <input type="hidden" id="idsucursalorigen">
                <input type="hidden" id="idsucursaldestino">

                <input type="hidden" id="codigocontable">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">FECHA INICIO</label>
                                    <input type="text" class="form-control" disabled id="fechainicio">
                                    <input type="hidden" id="idcambiox">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">SUC ORIGEN</label>
                                    <input disabled type="text" class="form-control" id="sucursalorigen">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">SUC DESTINO</label>
                                    <input disabled type="text" class="form-control" id="sucursaldestino">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">N° GUIA</label>
                                    <input disabled type="text" class="form-control" id="nguia">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td class="text-center" colspan="6"><b>DETALLE PRODUCTOS</b></td>
                                    </tr>
                                    <tr>
                                        <td><b>PRODUCTO</b></td>
                                        <td><b>CANTIDAD SOL</b></td>
                                        <td><b>CANTIDAD REC</b></td>
                                    </tr>
                                </thead>
                                <tbody id="historialbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnguarddd" class="modal_close btn btn-success">RECIBIR</button>
                    <button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </form>
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
    });
    const initTable = async () => {
        const codsucursal = <?= $_SESSION['cod_sucursal'] ?>;
        const query = `
            select gs.id, gs.sucursalorigen idso, gs.sucursaldestino idsd, s1.nombre_sucursal sucursalorigen, s2.nombre_sucursal sucursaldestino, gs.fechainicio, gs.estado, gs.productos, gs.nroguia from guiasucursal gs
            left join sucursal s1 on s1.cod_sucursal = gs.sucursalorigen
            left join sucursal s2 on s2.cod_sucursal = gs.sucursaldestino
            where gs.sucursaldestino = ${codsucursal}
        `;
        let data = await get_data_dynamic(query);

        $('#maintable').DataTable({
            data: data,
            destroy: true,
            columns: [{
                    title: 'N° GUIA',
                    data: 'nroguia'
                },
                {
                    title: 'FECHA INICIO',
                    data: 'fechainicio'
                },
                {
                    title: 'SUC ORIGEN',
                    data: 'sucursalorigen'
                },
                {
                    title: 'SUC DESTINO',
                    data: 'sucursaldestino'
                },
                {
                    title: 'ESTADO',
                    data: 'estado'
                },
                {
                    title: 'DETALLE',
                    render: function(data, type, row, meta) {
                        return `<button class="btn btn-primary" onclick='detalleguia("${row.estado}", "${row.fechainicio}", ${row.idso},${row.idsd}, "${row.sucursalorigen}", "${row.sucursaldestino}", "${row.nroguia}",${parseInt(row.id)}, ` + '`' + row.productos + "`)'>DETALLE</button>";
                    }
                },
            ]
        });
    }

    function detalleguia(estado, finicio, idsorigen, idsdestino, sorigen, sdestino, nroguia, id, json) {


        nguia.value = nroguia;

        idsucursaldestino.value = idsdestino;
        idsucursalorigen.value = idsorigen;

        sucursaldestino.value = sdestino;
        sucursalorigen.value = sorigen;
        fechainicio.value = finicio;
        idcambiox.value = id;
        const productos = JSON.parse(json)
        console.log(productos);

        historialbody.innerHTML = "";
        productos.forEach(ii => {
            historialbody.innerHTML += `
                <tr class="productos">
                    <td>${ii.nombreproducto}</td>
                    <td>${ii.cantidad}</td>
                    <td><input type="number" required class="form-control cantidad" min="0" data-limit="${ii.cantidad}" data-codigoprod="${ii.codigoprod}" oninput="limitnumber(this)" value="${ii.cantidad}"></td>
                </tr>
            `
        })
        if (estado == "PENDIENTE") {
            btnguarddd.style.display = "";
            getSelectorAll(".productos .cantidad").forEach(ii => ii.disabled = false)
        } else {
            btnguarddd.style.display = "none";
            getSelectorAll(".productos .cantidad").forEach(ii => ii.disabled = true)
        }
        $("#moperation").modal()
    }

    function limitnumber(e) {
        const value = parseFloat(e.value);
        const limit = parseFloat(e.dataset.limit);

        if (value > limit) {
            e.value = limit;
        }
    }
    formoperacion.addEventListener("submit", guardar);

    async function guardar(e) {
        e.preventDefault();

        const data = {};
        data.header = '';
        data.detalle = [];

        let iscompleted = "COMPLETO";
        const personss = "<?= $_SESSION['kt_login_id']; ?>"

        getSelectorAll(".productos").forEach(ii => {
            const cantidad = parseFloat(ii.querySelector(".cantidad").value);
            const limit = parseFloat(ii.querySelector(".cantidad").dataset.limit);
            const codigoprod = parseFloat(ii.querySelector(".cantidad").dataset.codigoprod);

            if (cantidad < limit)
                iscompleted = "INCOMPLETO";


            // Se registra la entrada kardex_contable
            data.detalle.push(`
                INSERT INTO kardex_contable (codigoprod, fecha, codigocompras, numero, tipo_comprobante, detalle, cantidad, precio, saldo, sucursal, preciodolar, preciototal, tipocomprobante,codigoproveedor)
                VALUES
                (
                    ${codigoprod},
                    '${new Date(new Date().setHours(10)).toISOString().substring(0,10)}',
                    ${idcambiox.value},
                    '${nguia.value}',
                    '',
                    'Cambio mercancia - Entra',
                    ${cantidad}, 
                    0,
                    (select ifnull((SELECT saldo from kardex_contable kc where kc.codigoprod = ${codigoprod} and kc.sucursal = ${idsucursaldestino.value} order by kc.id_kardex_contable desc limit 1), 0 )) + ${cantidad},
                    ${idsucursaldestino.value}, 
                    0, 
                    0, 
                    'guia',
                    0
                )
            `)
            // Se registra la entrada en kardex_almacen
            data.detalle.push(`
                        INSERT INTO kardex_alm (codigoprod, codigoguia, numero, detalle, cantidad, saldo, fecha, codsucursal, tipo, tipodocumento)
                        VALUES 
                        (
                            ${codigoprod},
                            ${idcambiox.value},
                            '${nguia.value}',
                            'Cambio mercancia - Entra',
                            ${cantidad},
                            (select ifnull((SELECT saldo from kardex_alm kc where kc.codigoprod = ${codigoprod} and kc.codsucursal = ${idsucursaldestino.value} order by kc.id_kardex_alm desc limit 1), 0 )) + ${cantidad},
                            '${new Date(new Date().setHours(10)).toISOString().substring(0,10)}',
                            ${idsucursaldestino.value},
                            '',
                            'guia'
                        )
                    `)
        })
        data.detalle.push(`
            update guiasucursal set 
                estado = '${iscompleted}',
                personaldestino = ${personss}
            where id = ${idcambiox.value}
        `)
        const jjson = JSON.stringify(data).replace("%select%", "lieuiwuygyq").replace("%SELECT%", "lieuiwuygyq")
        var formData = new FormData();
        formData.append("json", jjson);

        await fetch(`setVenta.php`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res.success) {
                    alert("registro completo!");
                    location.reload()
                }
            });
    }
</script>