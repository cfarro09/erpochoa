<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas); //ALTER TABLE ventasochoa.ventas ADD despachado INT DEFAULT 0 NULL;
//INSERT INTO propiedades (propiedades_id, `key`, value, datecreated, datechanged) VALUES(3, 'despacho_guia', '1', '2020-01-02 16:54:32', '2020-01-02 17:38:22');


$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Lista Entregas";
$NombreBotonAgregar = "Agregar";
$EstadoBotonAgregar = "disabled";
$popupAncho = 700;
$popupAlto = 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");

// Logica de la pagina 
$codsucursal = $_SESSION['cod_sucursal'];

# Cargar lista ventas con guia
$query = "select v.*, CONCAT(c.paterno, ' ', c.materno, ' ', c.nombre) as ClienteNatural, c.cedula from ventas v left join cnatural c on  c.codigoclienten = v.codigoclienten where modalidadentrega = 'Entrega almacen C/G' or modalidadentrega = 'Entrega inmediata C/G'";
$listado = mysql_query($query, $Ventas) or die(mysql_error());
$row = mysql_fetch_assoc($listado);
$totalRows_Listado = mysql_num_rows($listado);
$i = 1;

if ($totalRows_Listado == 0) : ?>
    <div class="alert alert-danger">
        <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
    </div>
<?php else: ?>
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
                <td><a href="#" data-fecha="<?= $row["fecha_emision"] ?>" data-cliente="<?= $row["ClienteNatural"] ?>"
                        data-codigocomprobante="<?= $row["codigocomprobante"] ?>"
                        data-tipocomprobante="<?= $row["tipocomprobante"] ?>" data-total="<?= $row["total"] ?>"
                        data-restante="<?= $restante ?>" data-pagoefectivo="<?= $row["pagoefectivo"] ?>"
                        data-json='<?= $row["jsonpagos"] ?>' data-id="<?= $row["codigoventas"] ?>"
                        data-despachado='<?= $row["despachado"] ?>' data-sucursal='<?= $codsucursal ?>'
                        onclick="pagar(this)">Detalle</a></td>
            </tr>
            <?php
                        $i++;
                    } while ($row = mysql_fetch_assoc($listado)); ?>
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
                <input type="hidden" id="despachado">
                <input type="hidden" id="jsonpagos">
                <input type="hidden" id="codsucursal">
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
                    <button type="button" class="btn btn-primary" aria-label="Close"
                        onClick="imprimir()">Imprimir Guia</button>
                    <button type="button" class="modal_close btn btn-danger" data-dismiss="modal"
                        aria-label="Close">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
// ========== Cargar Footer ==========
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>

    const pagar = e => {
        $("#moperation").modal();
        codigoventa.value = e.dataset.id;
        detallebody.innerHTML = "";
        codsucursal.value = e.dataset.sucursal;

        fetch(`getDetalleVenta.php?id=${e.dataset.id}`)
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res) {

                    res.forEach(ix => {
                        detallebody.innerHTML += `
                        <tr class="producto">
                            <input type="hidden" class="codigoprod" value="${ix.codigoprod}">
                            <td>${ix.nombre_producto}</td>
                            <td>${ix.marca}</td>
                            <td><span class="cantidad">${ix.cantidad}</span></td>
                            <td>${ix.pventa}</td>
                            <td>${ix.unidad_medida}</td>
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
        despachado.value = e.dataset.despachado;
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

    async function imprimir(){
        const data = {};
        data.header = '';
        data.detalle = [];
        if (despachado.value == 0) {
            const r = confirm("Se emitirá la guia y se descontará del kardex Almancen. ¿Está seguro que desea continuar");
            if (r) {
                // Conseguimos el numero de guia para el despacho
                var formData = new FormData();
                var query = "select value from propiedades where `key` = 'despacho_guia'";
                const res = await get_data_dynamic(query).then(r => r);

                // Descontar producto del almacen de la sucursal
                const h = {
                    fecha: '<?php echo date("Y-m-d"); ?>',
                    sucursal: codsucursal.value,
                    codventa: codigoventa.value,
                    nguia: res[0].value,
                }

                data.header = `UPDATE ventas SET despachado=1 WHERE codigoventas=${h.codventa}`;
                
                getSelectorAll(".producto").forEach(item => {
                    const d = {
                        codigoprod: item.querySelector(".codigoprod").value,
                        cantidad: item.querySelector(".cantidad").innerHTML,
                    }

                    data.detalle.push(`
                        INSERT INTO kardex_alm (codigoprod, codigoguia, numero, detalle, cantidad, saldo, fecha, codsucursal, tipo, tipodocumento)
                        VALUES 
                        (
                            ${d.codigoprod},
                            ${h.codventa},
                            ${h.nguia},
                            'Despacho de Mercancia',
                            ${d.cantidad},
                            (select saldo from kardex_alm kc where kc.codigoprod = ${d.codigoprod} and kc.codsucursal = ${h.sucursal} order by kc.id_kardex_alm desc limit 1) - ${d.cantidad},
                            '${h.fecha}',
                            ${h.sucursal},
                            '',
                            'guia'
                        )
                    `)
                })

                data.detalle.push("UPDATE propiedades SET value = ("+h.nguia+"+1) where `key` = 'despacho_guia'")
                var formData = new FormData();
                formData.append("json", JSON.stringify(data));

                fetch(`setVenta.php`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .catch(error => console.error("error: ", error))
                .then(res => {
                    if (res.success) {
                        alert("registro completo!");
                    }
                });
                console.log(data);
            } else {
                alert('Debes aceptar el descuento para poder imprimir la guia');
                return false;
            }
        }
        window.location.assign('http://erpochoa.cn/Imprimir/guia_imprimir.php?id=5');
        // location.reload()
    }
</script>