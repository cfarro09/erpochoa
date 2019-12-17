<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Lista Ventas";
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

$query_Listado = "SELECT v.*, CONCAT(c.paterno,  ' ', c.materno, ' ', c.nombre) as ClienteNatural, c.cedula, count(dv.codigoventa) as cantidaddev from ventas v 
left join  cnatural c on c.codigoclienten = v.codigoclienten
inner join detalle_ventas dv on dv.codigoventa = v.codigoventas where dv.devolucion = 1 GROUP by dv.codigoventa";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
$i = 1;
?>

<?php if ($totalRows_Listado == 0) : ?>
<div class="alert alert-danger">
    <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
</div>
<?php else : ?>
<table class="table table-bordered table-hover" id="sample_1">
    <thead>
        <tr>
            <th>N°</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Total Venta</th>
            <th>Total Pago</th>
            <th>Tipo Comp.</th>
            <th>Cod. Comp</th>
            <th>Devoluc.</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php do {
                    $restante = $row["total"] - $row["pagoacomulado"];
                    ?>
        <tr>
            <td class="text-center"><?= $i ?></td>
            <td><?= $row["fecha_emision"] ?></td>
            <td><?= $row["ClienteNatural"] ?></td>
            <td><?= $row["total"] ?></td>
            <td><?= $row["pagoacomulado"] ?></td>
            <td><?= $row["tipocomprobante"] ?></td>
            <td><?= $row["codigocomprobante"] ?></td>
            <td><?= $row["cantidaddev"] ?></td>
            <td><a href="#" data-fecha="<?= $row["fecha_emision"] ?>" data-cliente="<?= $row["ClienteNatural"] ?>" data-codigocomprobante="<?= $row["codigocomprobante"] ?>"
                    data-tipocomprobante="<?= $row["tipocomprobante"] ?>" data-total="<?= $row["total"] ?>"
                    data-restante="<?= $restante ?>" data-pagoefectivo="<?= $row["pagoefectivo"] ?>" data-sucursal='<?= $row["sucursal"] ?>'
                    data-json='<?= $row["jsonpagos"] ?>' data-id="<?= $row["codigoventas"] ?>"
                    onclick="pagar(this)">Detalle</a></td>
        </tr>
        <?php
                    $i++;
                } while ($row = mysql_fetch_assoc($Listado)); ?>
    </tbody>
</table>
<?php endif ?>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
    <input type="hidden" id="moperation_sucursal">
        <div class="modal-content m-auto">
            <form id="formoperacion" action="">
                <div class="modal-header">
                    <h2 class="modal-title">Detalle Venta</h2>
                </div>
                <input type="hidden" id="codigoventa">
                <input type="hidden" id="jsonpagos">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputfecha" class="control-label">Fecha</label>
                                    <input type="text" readonly autocomplete="off" id="inputfecha"
                                        class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputtipocomprobante" class="control-label">Tipo Comp</label>
                                    <input type="text" readonly autocomplete="off" id="inputtipocomprobante"
                                        class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputnumerocomprobante" class="control-label">N° Comprobante</label>
                                    <input type="text" readonly autocomplete="off" id="inputnumerocomprobante"
                                        class="form-control" />
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputcliente" class="control-label">Cliente</label>
                                    <input type="text" readonly autocomplete="off" id="inputcliente"
                                        class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputrestante" class="control-label">Falta Pagar</label>
                                    <input type="number" readonly autocomplete="off" id="inputrestante"
                                        class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputtotal" class="control-label">Total Venta</label>
                                    <input type="number" readonly autocomplete="off" id="inputtotal"
                                        class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <td class="text-center" colspan="5"><b>DEALLE PRODUCTOS</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>PRODUCTO</b></td>
                                            <td><b>MARCA</b></td>
                                            <td><b>CANTIDAD</b></td>
                                            <td><b>DETALLE</b></td>
                                            <td><b>DEVOLUCION</b></td>
                                        </tr>
                                    </thead>
                                    <tbody id="detallebody">
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <td class="text-center" colspan="3"><b>HISTORIAL DE PAGOS</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>TIPO PAGO</b></td>
                                            <td><b>MONTO</b></td>
                                            <td><b>DETALLE</b></td>
                                        </tr>
                                    </thead>
                                    <tbody id="historialbody">
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal_close btn btn-danger" data-dismiss="modal"
                        aria-label="Close">Cerrar</button>
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
    function changetypepago(e) {
        e.closest(".containerx").querySelectorAll(".inputxxx").forEach(ix => ix.style.display = "none");
        e.closest(".containerx").querySelectorAll("." + e.value).forEach(ix => ix.style.display = "");

    }

    function validatenumber(e) {
        if (e.value < 0)
            e.value = 0;
        if (e.value) {
            const maxx = parseInt(e.dataset.max);
            const vv = parseInt(e.value)
            if (maxx < vv)
                e.value = maxx
        }

    }

    const aceptardevolucion = e => {
        const r = confirm("Esta seguro de aceptar la devolución");
        if (r) {
            const data = {
                header: "",
                detalle: []
            }
            const codigoprod = e.dataset.codigoprod;
            const cantidaddevolucion = e.dataset.cantidaddevolucion;
            const sucursal = moperation_sucursal.value;
            const iddetalle = e.dataset.iddetalle;
            const idventa = e.dataset.idventa;
            let historialdevolucion = JSON.parse(e.dataset.historial);
            historialdevolucion.push({
                fecha: new Date(),
                cantidad: cantidaddevolucion,
                motivo: "ACEPTADO"
            })
            historialdevolucion = JSON.stringify(historialdevolucion);
            data.detalle.push(
                `update detalle_ventas set devolucion = 0, historialdevolucion = '${historialdevolucion}', cantidad = cantidad - ${cantidaddevolucion} where codigodetalleproducto = ${iddetalle}`
                );

            data.detalle.push(`
                insert into kardex_contable(codigoprod, fecha, codigocompras, numero, detalle, cantidad, precio, saldo, sucursal, preciototal, tipocomprobante, codigoproveedor)
                values
                (${codigoprod}, '${new Date().toISOString()}', ${idventa}, '', 'Devolucion', ${cantidaddevolucion}, 0, 
                (select saldo from kardex_contable kc where kc.codigoprod = ${codigoprod} and kc.sucursal = ${sucursal} order by kc.id_kardex_contable desc limit 1) + ${cantidaddevolucion}, ${sucursal}, 0, '', 0)`);
            var formData = new FormData();
            formData.append("json", JSON.stringify(data))

            fetch(`setVenta.php`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .catch(error => console.error("error: ", error))
                .then(res => {
                    if (res.success) {
                        alert("Devolucion completa!")
                        location.reload()
                    }
                });
        }
    }
    const pagar = e => {
        $("#moperation").modal();
        codigoventa.value = e.dataset.id;
        moperation_sucursal.value = e.dataset.sucursal
        detallebody.innerHTML = "";

        fetch(`getDetalleVenta.php?id=${e.dataset.id}`)
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res) {
                    res.forEach(ix => {
                        let devoler = "";
                        let fecha = "";
                        try{
                            const history = JSON.parse(ix.historialdevolucion);
                            const last = history.pop();
                            fecha = `${new Date(last.fecha).toLocaleDateString()} | ${last.motivo} `
                        }catch(e){
                            debugger
                        }
                        if (ix.devolucion && parseInt(ix.devolucion) > 0) {
                            devoler =
                                `<span class="btn btn-success" data-historial='${ix.historialdevolucion}' data-iddetalle="${ix.codigodetalleproducto}" data-cantidaddevolucion="${ix.cantdevolucion}" data-codigoprod="${ix.codigoprod}" onClick="aceptardevolucion(this)" data-idventa="${e.dataset.id}">${ix.cantdevolucion} Aceptar</span>`
                        } else {
                            devoler =
                                ` - `
                        }
                        detallebody.innerHTML += `
                        <tr>
                        <td>${ix.nombre_producto}</td>
                        <td>${ix.marca}</td>
                        <td>${ix.cantidad}</td>
                        <td>${fecha}</td>
                        <td>${devoler}</td>
                        </tr>
                        `;
                    })
                }
            })

        const pagoefectivo = parseFloat(e.dataset.pagoefectivo);
        historialbody.innerHTML = "";

        inputfecha.value = e.dataset.fecha;
        inputnumerocomprobante.value = e.dataset.codigocomprobante;
        inputcliente.value = e.dataset.cliente;
        inputtipocomprobante.value = e.dataset.tipocomprobante;


        jsonpagos.value = e.dataset.json;
        inputrestante.value = e.dataset.restante;
        inputtotal.value = e.dataset.total;
        if (pagoefectivo) {
            historialbody.innerHTML += `
                <tr>
                <td>Pago Efectivo</td>
                <td>${pagoefectivo}</td>
                <td>-</td>
                </tr>
                `;
        }
        JSON.parse(e.dataset.json).filter(iy => iy.tipopago != "porcobrar").forEach(ix => {
            let textt = "";
            if (ix.tipopago == "depositobancario")
                textt = `Numero Operacion: ${ix.numerooperacion} |
                Fecha: ${ix.fechaextra} |
                Cta. Abonada: ${ix.cuentaabonado} |
                Ente: ${ix.bancoextra} |
                Monto: ${ix.montoextra}`
            else if (ix.tipopago == "cheque")
                textt = `Numero: ${ix.numero} |
                Ente: ${ix.bancoextra} |
                Cta. Cte.: ${ix.cuentacorriente} |
                Monto: ${ix.montoextra}`
            else if (ix.tipopago == "tarjetacredito")
                textt = `Numero: ${ix.numero} |
                Ente: ${ix.bancoextra} |
                Monto: ${ix.montoextra}`
            else if (ix.tipopago == "tarjetadebito")
                textt = `Numero: ${ix.numero} |
                Ente: ${ix.bancoextra} | 
                Monto: ${ix.montoextra}`
            else if (ix.tipopago == "efectivo") {
                textt = `Monto: ${ix.montoextra} `
            }

            historialbody.innerHTML += `
                <tr>
                <td>${ix.tipopago}</td>
                <td>${ix.montoextra}</td>
                <td>${textt}</td>
                </tr>
                `;
        });
    }
</script>