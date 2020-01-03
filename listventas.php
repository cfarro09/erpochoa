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

$query_Listado = "
    select v.*, CONCAT(cn.paterno,  ' ', cn.materno, ' ', cn.nombre) as ClienteNatural, cn.cedula,
    cj.razonsocial, cj.ruc 
    from ventas v 
    left join  cnatural cn on cn.codigoclienten = v.codigoclienten
    left join  cjuridico cj on cj.codigoclientej = v.codigoclientej";

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
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php do {
                    $restante = $row["total"] - $row["pagoacomulado"];
                    ?>
        <tr>
            <td><?= $i ?></td>
            <td><?= $row["fecha_emision"] ?></td>
            <td><?= $row["ClienteNatural"] != null ? $row["ClienteNatural"] : $row["razonsocial"]  ?></td>
            <td><?= $row["total"] ?></td>
            <td><?= $row["pagoacomulado"] ?></td>
            <td><?= $row["tipocomprobante"] ?></td>
            <td><?= $row["codigocomprobante"] ?></td>
            <td><a href="#" data-fecha="<?= $row["fecha_emision"] ?>" data-cliente="<?= $row["ClienteNatural"] != null ? $row["ClienteNatural"] : $row["razonsocial"]  ?>"
                    data-codigocomprobante="<?= $row["codigocomprobante"] ?>"
                    data-tipocomprobante="<?= $row["tipocomprobante"] ?>" data-total="<?= $row["total"] ?>"
                    data-restante="<?= $restante ?>" data-pagoefectivo="<?= $row["pagoefectivo"] ?>"
                    data-modoentrega="<?= $row['modalidadentrega'] ?>"
                    data-json='<?= $row["jsonpagos"] ?>' data-id="<?= $row["codigoventas"] ?>"
                    data-nroguia='<?= $row["nroguia"] ?>' data-sucursal='<?= $codsucursal ?>'
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
        <div class="modal-content m-auto">
            <form id="formoperacion" action="">
                <div class="modal-header">
                    <h2 class="modal-title">Detalle Venta</h2>
                </div>
                <input type="hidden" id="codigoventa">
                <input type="hidden" id="jsonpagos">
                <input type="hidden" id="modoentrega">
                <input type="hidden" id="nroguia">
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
                                            <td><b>P VENTA</b></td>
                                            <td><b>UNIDAD MEDIDA</b></td>
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
                            <div class="col-sm-12" id="">
                                <label>Motivo Devolución</label>
                                <textarea class="form-control" id="motivodevolucion"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <button type="button" class="btn btn-primary" style="float:left" aria-label="Close" id="btnimprimirfactura"
                                onClick="imprimir_factura()">Imprimir Factura</button>
                            <button type="button" class="btn btn-primary" style="float:left" aria-label="Close" id="btnimprimirguia"
                                onClick="imprimir_guia()">Imprimir Guia</button>
                        </div>
                        <div class="col-sm-6 col-md-8 mr-auto">
                            <button type="button" class="btn btn-primary" aria-label="Close"
                                onClick="devolver()">Guardar</button>
                            <button type="button" class="modal_close btn btn-danger" data-dismiss="modal"
                                aria-label="Close">Cerrar</button>
                        </div>
                    </div>
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
    const devolver = () => {
        const data = {
            header: "",
            detalle: []
        }
        let dataxx = false;
        getSelectorAll(".inputdevo").forEach(ix => {
            debugger
            if (ix.value && parseInt(ix.value) != 0) {
                dataxx = true;
                const iddetalle = ix.dataset.iddetalle;
                let historialdevolucion = ix.dataset.historial;
                const idventa = ix.dataset.idventa;

                if (historialdevolucion && historialdevolucion != "null") { historialdevolucion = JSON.parse(historialdevolucion)
                    historialdevolucion.push({
                        fecha: new Date(),
                        cantidad: ix.value,
                        motivo: motivodevolucion.value
                    })
                    historialdevolucion = JSON.stringify(historialdevolucion)
                } else {
                    historialdevolucion = JSON.stringify([{
                        fecha: new Date(),
                        cantidad: ix.value,
                        motivo: motivodevolucion.value
                    }])
                }
                data.detalle.push(
                    `update detalle_ventas set devolucion = 1, cantdevolucion = ${ix.value}, historialdevolucion = '${historialdevolucion}' where codigodetalleproducto = ${iddetalle}` )
            }

        });
        if (dataxx) {
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
                        alert("registro completo!")
                        location.reload()
                    }
                });
        }

    }

    const cancelardevolucion = e => {
        const r = confirm("Esta seguro de cancelar la devolución");
        if (r) {
            const data = {
                header: "",
                detalle: []
            }

            const iddetalle = e.dataset.iddetalle;
            let historialdevolucion = JSON.parse(e.dataset.historial);
            historialdevolucion.push({
                fecha: new Date(),
                cantidad: e.value,
                motivo: "CANCELADO"
            })
            historialdevolucion = JSON.stringify(historialdevolucion);
            data.detalle.push(
                `update detalle_ventas set devolucion = 0, historialdevolucion = '${historialdevolucion}' where codigodetalleproducto = ${iddetalle}`
                )
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
                        alert("Cancelacion completo!")
                        location.reload()
                    }
                });
        }
    }
    async function pagar(e) {
        $("#moperation").modal();
        codigoventa.value = e.dataset.id;
        motivodevolucion.value = "";
        detallebody.innerHTML = "";
        modoentrega.value = e.dataset.modoentrega;
        
        if (modoentrega.value == 'Entrega inmediata C/G') {
            var query = `select nroguia from ventas where codigoventas = ${codigoventa.value}`;
            const res = await get_data_dynamic(query).then(r => r);
            nroguia.value = res[0].nroguia;
            $('#btnimprimirguia').show();
        } else {
            $('#btnimprimirguia').hide();
        }

        fetch(`getDetalleVenta.php?id=${e.dataset.id}`)
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res) {

                    res.forEach(ix => {
                        let devoler = "";
                        if (ix.devolucion && parseInt(ix.devolucion) > 0) {
                            devoler =
                                `<span class="btn btn-danger" data-historial='${ix.historialdevolucion}' data-iddetalle="${ix.codigodetalleproducto}" onClick="cancelardevolucion(this)" data-idventa="${e.dataset.id}">${ix.cantdevolucion} Cancelar</span>`
                        } else {
                            devoler =
                                `<input type="number" data-historial='${ix.historialdevolucion}' data-iddetalle="${ix.codigodetalleproducto}" data-idventa="${e.dataset.id}" class="form-control inputdevo" oninput="validatenumber(this)" data-max="${ix.cantidad}" style="width: 100px">`
                        }
                        detallebody.innerHTML += `
                        <tr>
                        <td>${ix.nombre_producto}</td>
                        <td>${ix.marca}</td>
                        <td>${ix.cantidad}</td>
                        <td>${ix.pventa}</td>
                        <td>${ix.unidad_medida}</td>
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

    async function imprimir_factura()
    {
        var url = `Imprimir/facturaventa_imprimir.php?id=`+parseInt(codigoventa.value,10);
        console.log(url);
        window.location=url;
        $("#moperation").modal('hide');
    }

    const get_data_dynamic = async (query) => {
		var formData = new FormData();
		formData.append("query", query)
		const response = await fetch("get_data_dynamic2.php", {
			method: 'POST',
			body: formData,
		});
		if (response.ok) {
			try {
				return await response.json();
			} catch (e) {
				alert(e)
			}
		} else {
			alert("hubo un problema")
		}
	};

    async function imprimir_guia()
    {
        if (!nroguia.value) {
            const data = {};
            data.header = '';
            data.detalle = [];
            var query = "select value from propiedades where `key` = 'despacho_guia'";
            const res = await get_data_dynamic(query).then(r => r);
            var nguia = res[0].value;

            data.detalle.push(`UPDATE ventas SET despachado=1, nroguia=${nguia} WHERE codigoventas=${codigoventa.value}`);
            data.detalle.push("UPDATE propiedades SET value = ("+nguia+"+1) where `key` = 'despacho_guia'");
            console.log(data);
            var formData = new FormData();
            formData.append("json", JSON.stringify(data));

            await fetch(`setVenta.php`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res.success) {
                    console.log("registro completo!");
                }
            });
        }
        console.log(nroguia.value);
        var url = `Imprimir/guia_imprimir.php?id=`+parseInt(codigoventa.value,10);
        console.log(url);
        window.location=url;
        $("#moperation").modal('hide');
    }
</script>