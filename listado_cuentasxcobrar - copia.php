<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Servicios por pagar";
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
if (isset($_GET['codigocliente'])) {
    $codcliente = $_GET['codigocliente'];
}
$query_Listado = "select v.*, CONCAT(c.paterno,  ' ', c.materno, ' ', c.nombre) as ClienteNatural, c.cedula from ventas v left join  cnatural c on c.codigoclienten = v.codigoclienten where v.codigoclienten = $codcliente and porpagar = 1 order by v.codigoventas asc";

// $query_Listado = "select v.*, CONCAT(c.paterno,  ' ', c.materno, ' ', c.nombre) as ClienteNatural, c.cedula from ventas v left join  cnatural c on c.codigoclienten = v.codigoclienten where porpagar = 1";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
$i = 1;
?>
<h3>Cliente <?= $row["ClienteNatural"] . " - " . $row["cedula"] ?></h3>
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
                    <th>Tipo Comp.</th>
                    <th>Cod. Comp</th>
                    <th>CARGO</th>
                    <th>ABONO</th>
                    <th>SALDO</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $acumulado = 0; ?>
                <?php do {
                    $restante = $row["total"] - $row["pagoacomulado"];
                    $acumulado += $row["total"] - $row["pagoacomulado"]
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $row["fecha_emision"] ?></td>
                        <td><?= $row["tipocomprobante"] ?></td>
                        <td><?= $row["codigocomprobante"] ?></td>
                        <td><?= $row["total"] ?></td>
                        <td><?= $row["pagoacomulado"] ?></td>
                        <td><?= $acumulado ?></td>
                        <td>
                            <a href="#" data-porpagar="<?= $row["porpagar"] ?>" data-fecha="<?= $row["fecha_emision"] ?>" data-cliente="<?= $row["ClienteNatural"] ?>" data-codigocomprobante="<?= $row["codigocomprobante"] ?>" data-tipocomprobante="<?= $row["tipocomprobante"] ?>" data-total="<?= $row["total"] ?>" data-restante="<?= $restante ?>" data-pagoefectivo="<?= $row["pagoefectivo"] ?>" data-json='<?= $row["jsonpagos"] ?>' data-id="<?= $row["codigoventas"] ?>" onclick="pagar(this)"><?= $row["porpagar"] == 1 ? "Pagar" : "Movimientos" ?>
                        </a>
                    </td>

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
                    <h2 class="modal-title">PAGAR</h2>
                </div>
                <input type="hidden" id="codigoventa">
                <input type="hidden" id="jsonpagos">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputfecha" class="control-label">Fecha</label>
                                    <input type="text" readonly autocomplete="off" id="inputfecha" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputtipocomprobante" class="control-label">Tipo Comp</label>
                                    <input type="text" readonly autocomplete="off" id="inputtipocomprobante" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputnumerocomprobante" class="control-label">N° Comprobante</label>
                                    <input type="text" readonly autocomplete="off" id="inputnumerocomprobante" class="form-control" />
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputcliente" class="control-label">Cliente</label>
                                    <input type="text" readonly autocomplete="off" id="inputcliente" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputrestante" class="control-label">Falta Pagar</label>
                                    <input type="number" readonly autocomplete="off" id="inputrestante" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputtotal" class="control-label">Total Venta</label>
                                    <input type="number" readonly autocomplete="off" id="inputtotal" class="form-control" required />
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover" >
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
                            <div class="col-sm-4" style="margin-top: 15px; margin-bottom: 15px">
                                <button class="btn btn-success" id="btnaddpay" type="button" onclick="addPayExtra()">Agregar Pago</button>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <td class="text-center" colspan="3"><b>HISTORIAL DE PAGOS</b></td>
                                        </tr>
                                        <tr>
                                            <td>TIPO PAGO</td>
                                            <td>MONTO</td>
                                            <td>DETALLE</td>
                                        </tr>
                                    </thead>
                                    <tbody id="historialbody">
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-12" id="containerpayextra">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="guardar_button" class="btn btn-success">Guardar</button>
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
    function changetypepago(e) {
        guardar_button.style.display = ""
        e.closest(".containerx").querySelectorAll(".inputxxx").forEach(ix => ix.style.display = "none");
        e.closest(".containerx").querySelectorAll("." + e.value).forEach(ix => ix.style.display = "");
    }
    const pagar = e => {
        guardar_button.style.display = "none"
        $("#moperation").modal();
        const pagoefectivo = parseFloat(e.dataset.pagoefectivo);
        historialbody.innerHTML = "";

        if(e.dataset.porpagar == "1"){
            btnaddpay.style.display = "";
        }else{
            btnaddpay.style.display = "none";
        }

        inputfecha.value = e.dataset.fecha;
        inputtipocomprobante.value = e.dataset.codigocomprobante;
        inputnumerocomprobante.value = e.dataset.cliente;
        inputcliente.value = e.dataset.tipocomprobante;

        codigoventa.value = e.dataset.id;
        jsonpagos.value = e.dataset.json;
        inputrestante.value = e.dataset.restante;
        inputtotal.value = e.dataset.total;
        
        detallebody.innerHTML = "";
        fetch(`getDetalleVenta.php?id=${e.dataset.id}`)
        .then(res => res.json())
        .catch(error => console.error("error: ", error))
        .then(res => {
            if(res){
                res.forEach(ix => {
                    detallebody.innerHTML += `
                    <tr>
                    <td>${ix.nombre_producto}</td>
                    <td>${ix.marca}</td>
                    <td>${ix.cantidad}</td>
                    <td>${ix.pventa}</td>
                    <td>${ix.unidad_medida}</td>
                    </tr>
                    `;
                })
            }
        })


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

    function removecontainerpay(e) {
        e.closest(".containerx").remove()
    }

    function addPayExtra() {

        const newxx = document.createElement("div");
        newxx.className = "col-md-12 containerx";
        newxx.style = "border: 1px solid #cdcdcd; padding: 5px; margin-bottom: 5px";

        newxx.innerHTML += `
        <div class="text-right">
        <button type="button" class="btn btn-danger" onclick="removecontainerpay(this)">Cerrar</button>
        </div>

        <div class="col-md-3">
        <div class="form-group">
        <label class="control-label">Tipo Pago</label>
        <select onchange="changetypepago(this)" class="form-control tipopago">
        <option value="">[Seleccione]</option>
        <option value="depositobancario">Deposito Bancario</option>
        <option value="tarjetadebito">Tarjeta Debito</option>
        <option value="tarjetacredito">Tarjeta Credito</option>
        <option value="cheque">Cheque</option>
        <option value="efectivo">Efectivo</option>
        </select>
        </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx depositobancario cheque tarjetacredito tarjetadebito">
        <div class="form-group">
        <label class="control-label">Banco</label>
        <select class="form-control bancoextra">
        <option value="BANCO AZTECA">BANCO AZTECA</option>
        <option value="BANCO BCP">BANCO BCP</option>
        <option value="BANCO CENCOSUD">BANCO CENCOSUD</option>
        <option value="BANCO DE LA NACION">BANCO DE LA NACION</option>
        <option value="BANCO FALABELLA">BANCO FALABELLA</option>
        <option value="BANCO GNB PERÚ">BANCO GNB PERÚ</option>
        <option value="BANCO MI BANCO">BANCO MI BANCO</option>
        <option value="BANCO PICHINCHA">BANCO PICHINCHA</option>
        <option value="BANCO RIPLEY">BANCO RIPLEY</option>
        <option value="BANCO SANTANDER PERU">BANCO SANTANDER PERU</option>
        <option value="BANCO SCOTIABANK">BANCO SCOTIABANK</option>
        <option value="CMAC AREQUIPA">CMAC AREQUIPA</option>
        <option value="CMAC CUSCO S A">CMAC CUSCO S A</option>
        <option value="CMAC DEL SANTA">CMAC DEL SANTA</option>
        <option value="CMAC HUANCAYO">CMAC HUANCAYO</option>
        <option value="CMAC ICA">CMAC ICA</option>
        <option value="CMAC LIMA">CMAC LIMA</option>
        <option value="CMAC MAYNA">CMAC MAYNA</option>
        <option value="CMAC PAITA">CMAC PAITA</option>
        <option value="CMAC SULLANA">CMAC SULLANA</option>
        <option value="CMAC TRUJILLO">CMAC TRUJILLO</option>
        </select>
        </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx depositobancario cheque tarjetacredito tarjetadebito efectivo porcobrar">
        <div class="form-group">
        <label class="control-label">Monto</label>
        <input type="number" step="any" class="form-control montoextra">
        </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx cheque tarjetacredito tarjetadebito">
        <div class="form-group">
        <label class="control-label">Numero</label>
        <input type="number" class="form-control numero">
        </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx depositobancario cheque">
        <div class="form-group">
        <label class="control-label">Cuenta Corriente</label>
        <input type="text" class="form-control cuentacorriente">
        </div>
        </div>


        <div style="display: none" class="col-md-3 inputxxx depositobancario">
        <div class="form-group">
        <label class="control-label">Numero Operacion</label>
        <input type="text"  class="form-control numerooperacion">
        </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx depositobancario cheque">
        <div class="form-group">
        <label class="control-label">Fecha</label>
        <input type="text" class="form-control form-control-inline input-medium date-picker fechaextra" data-date-format="yyyy-mm-dd" readonly autocomplete="off">
        </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx depositobancario">
        <div class="form-group">
        <label class="control-label">Cta Abonado</label>
        <input type="text" class="form-control cuentaabonado">
        </div>
        </div>`;
        containerpayextra.appendChild(newxx);

        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            autoclose: true
        });
    }
    const guardar = e => {
        e.preventDefault();
        let totalpagando = 0;
        let error = "";
        let porpagar = 1;
        let restante = 0;
        let errorrr = "";
        const arraypagos = JSON.parse(jsonpagos.value);
        getSelectorAll(".containerx").forEach(ix => {
            const bancoextra = ix.querySelector(".bancoextra").value;
            const montoextra = ix.querySelector(".montoextra").value ? parseFloat(ix.querySelector(".montoextra").value) : 0;
            const numero = ix.querySelector(".numero").value;
            const cuentacorriente = ix.querySelector(".cuentacorriente").value;
            const numerooperacion = ix.querySelector(".numerooperacion").value;
            const fechaextra = ix.querySelector(".fechaextra").value;
            const cuentaabonado = ix.querySelector(".cuentaabonado").value;
            const tipopago = ix.querySelector(".tipopago").value;

            arraypagos.push({
                bancoextra,
                montoextra,
                numero,
                cuentacorriente,
                numerooperacion,
                fechaextra,
                cuentaabonado,
                tipopago,
                fechaxxx: new Date(new Date().setHours(10)).toISOString().substring(0,10)
            })
            totalpagando += parseFloat(montoextra);
            if (tipopago == "depositobancario" && (!bancoextra || !montoextra || !cuentacorriente || !numerooperacion || !fechaextra || !cuentaabonado)) {
                errorrr = "Llena todos los datos de deposito bancario";
                return;
            } else if (tipopago == "cheque" && (!bancoextra || !montoextra || !numero || !cuentacorriente)) {
                errorrr = "Llena todos los datos de cheque";
                return;
            } else if ((tipopago == "tarjetacredito" || tipopago == "tarjetadebito") && (!bancoextra || !montoextra || !numero)) {
                errorrr = "Llena todos los datos de " + tipopago;
                return;
            } else if (tipopago == "efectivo" && !montoextra) {
                errorrr = "Debe ingresa el monto";
                return;
            }

        });
        if (errorrr) {
            alert(errorrr);
            return;
        }
        if (totalpagando > parseFloat(inputrestante.value)) {
            alert("El monto a pagar excede");
            return
        } else if (totalpagando == parseFloat(inputrestante.value)) {
            porpagar = 0;
        } else {
            restante = parseFloat(inputrestante.value) - totalpagando;
        }
        let acumulado = parseFloat(inputtotal.value) - restante;


        const jssson = JSON.stringify(arraypagos);

        const query = `
        update ventas set jsonpagos = '${jssson}', porpagar = ${porpagar}, pagoacomulado = ${acumulado}
        where codigoventas = ${codigoventa.value}`
        const detalle = [];
        detalle.push(query);
        const formData = new FormData();
        formData.append("exearray", JSON.stringify(detalle))

        fetch(`setPrecioVenta.php`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .catch(error => console.error("error: ", error))
        .then(res => {
            $("#mOrdenCompra").modal("hide");
            if (res.success) {
                alert("registro completo!")
                location.reload()
            }
        });
    }
    formoperacion.addEventListener("submit", guardar)
</script>