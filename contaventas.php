<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Reporte Ventas";
$NombreBotonAgregar = "Agregar";
$EstadoBotonAgregar = "disabled";
$popupAncho = 700;
$popupAlto = 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");

include("Fragmentos/abrirpopupcentro.php");

$codpersonal = $_SESSION['kt_codigopersonal'];
$codsucursal = $_SESSION['cod_sucursal'];
$tmpcodsucursal = $_SESSION['cod_sucursal'];


$querySucursales = "select * from sucursal where estado = 1 or estado = 999";
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$r_suc = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);


?>
<style>ui
    .text-right {
        text-align: right
    }

    .textleft {
        text-align: left
    }

    .textcenter {
        text-align: center
    }

    @media print {
        * {
            display: none;
        }

        #printableTable {
            display: block;
        }
    }
</style>
<input type="hidden" value="<?= $tmpcodsucursal ?>" id="sucursalactive">
<form id="formoperacion">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Sucursal</label>
                <select name="sucursal" required id="sucursalconta" class="form-control ">
                    <?php do {  ?>
                        <option <?= $r_suc['cod_sucursal'] == $_SESSION['cod_sucursal'] ? 'selected' : '' ?> value="<?php echo $r_suc['cod_sucursal'] ?>">
                            <?php echo $r_suc['nombre_sucursal'] ?>
                        </option>
                    <?php
                    } while ($r_suc = mysql_fetch_assoc($sucursales));
                    $rows = mysql_num_rows($sucursales);
                    if ($rows > 0) {
                        mysql_data_seek($sucursales, 0);
                        $r_suc = mysql_fetch_assoc($sucursales);
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Sucursal</label>
                <select class="form-control" id="combopersonal"></select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <select class="form-control" id="tipotmp">
                <option value="detallado">Detallado</option>
                <option value="consolidado">Consolidado</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Fecha Inicio</label>
                <input type="text" required name="fecha_inicio" autocomplete="off" id="fecha_inicio" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="field-1" class="control-label">Fecha termino</label>
                <input type="text" name="fecha_fin" autocomplete="off" id="fecha_fin" required class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5"></div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success btn-block">Buscar</button>
        </div>
    </div>
</form>
<div class="row" style="margin-bottom: 10px">
    <div class="col-sm-6">
        <button class="btn btn-success" onclick='tableToExcel("tableconta", sucursalconta.options[sucursalconta.selectedIndex].text)'>Descargar excel</button>

        <button class="btn btn-success" onclick='printDiv()'>Imprimir</button>
    </div>
</div>
<div class="row">
    <div id="printableTable">
        <table class="table table-bordered" id="tableconta">
            <thead>
                <th class="text-center">Fecha</th>
                <th class="text-center">Numero</th>
                <th class="text-center">Cliente</th>
                <th class="text-center">Efectivo</th>
                <th class="text-center">Cheque</th>
                <th class="text-center">D. Bancario</th>
                <th class="text-center">T. Debito</th>
                <th class="text-center">T. Credito</th>
                <th class="text-center">Por cobrar</th>
                <th class="text-center">Otros</th>
                <th class="text-center">Total</th>
            </thead>
            <tbody id="bodydata">

            </tbody>
        </table>
    </div>

</div>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>
<iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>

<script>
    const onloadPersonal = async () => {
        const res = await get_data_dynamic("SELECT codigopersonal, concat(paterno, ' ', materno, ' ', nombre) as fullname FROM personal WHERE estado = 0");
        res.unshift({
            codigopersonal: 0,
            fullname: "TODO"
        })
        cargarselect2("#combopersonal", res, "codigopersonal", "fullname")
    }
    window.onload = e => {
        onloadPersonal()
    }
    const codigopersonal = <?= $codpersonal ?>;

    const searchconta = async e => {
        e.preventDefault();
        bodydata.innerHTML = "";
        const suc = sucursalconta.value;
        // const per = personalconta.value;
        const f_ini = fecha_inicio.value;
        const f_fin = fecha_fin.value;

        const whereper = combopersonal.value == 0 ? "" : `and v.codigopersonal = ${combopersonal.value}`

        const query = `
            SELECT 
                if(cn.cedula is null, 'juridico', 'natural') as tipo, v.codigoventas, montoabono as abonoproveedor, v.tipocomprobante, v.codigocomprobante, v.jsonpagos,
                if(cn.cedula is null, v.codigoclientej, v.codigoclienten) as codcliente,
                if(cn.cedula is null, cj.razonsocial, CONCAT(cn.paterno, ' ', cn.materno, ' ', cn.nombre)) as fullname, v.fecha_emision,
                IFNULL(cn.cedula, cj.ruc) as identificacion, 
                v.montofact as totalcargo, v.pagoacomulado as totalabono 
            FROM ventas v
            left join cnatural cn on v.codigoclienten = cn.codigoclienten 
            left join cjuridico cj on v.codigoclientej = cj.codigoclientej 
            WHERE 
                v.sucursal = ${suc}
                ${whereper}
                and v.fecha_emision BETWEEN '${f_ini}' AND '${f_fin}'`;

        const res = await get_data_dynamic(query);

        if (tipotmp.value == "detallado") {
            const typelist = {};
            res.forEach(xx => {
                if (!typelist[xx.tipocomprobante])
                    typelist[xx.tipocomprobante] = []
                typelist[xx.tipocomprobante].push(xx)
            })
            let sumatotal = 0;
            let datatt = {};
            for (let [key, datatmp1] of Object.entries(typelist)) {
                key = key == "boleta" ? "boleta de ventas" : key;
                const dtx = setventascontado(datatmp1, true, key);
                sumatotal += dtx.totalgroup;
                if (!datatt) {
                    datatt = dtx.ttp;
                } else {
                    for (const [key, value] of Object.entries(dtx.ttp)) {
                        datatt[key] = datatt[key] ? datatt[key] : 0;
                        datatt[key] = parseFloat(datatt[key]) + parseFloat(value);
                    }
                }
            }
            for (const [key, value] of Object.entries(datatt)) {
                datatt[key] = datatt[key].toFixed(2);
            }
            bodydata.innerHTML += `
                <tr style="font-weight: bold">
                    <td class="text-right" colspan="3">TOTALES </td>
                    <td class="text-right">${datatt["efectivo"] ? datatt["efectivo"] : "0.00" }</td>
                    <td class="text-right">${datatt["cheque"] ? datatt["cheque"] : "0.00" }</td>
                    <td class="text-right">${datatt["depositobancario"] ? datatt["depositobancario"] : "0.00" }</td>
                    <td class="text-right">${datatt["tarjetadebito"] ? datatt["tarjetadebito"] : "0.00" }</td>
                    <td class="text-right">${datatt["tarjetacredito"] ? datatt["tarjetacredito"] : "0.00" }</td>
                    <td class="text-right">${datatt["porcobrar"] ? datatt["porcobrar"] : "0.00" }</td>
                    <td class="text-right">${datatt["comision"] ? datatt["comision"] : "0.00" }</td>
                    <td class="text-right">${sumatotal.toFixed(2)}</td>
                </tr>`;
        } else {
            setConsolidado(res)
        }
    }

    function printDiv() {
        window.frames["print_frame"].document.body.innerHTML = document.getElementById("printableTable").innerHTML;
        window.frames["print_frame"].window.focus();
        window.frames["print_frame"].window.print();
    }
    var tableToExcel = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>',
            base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            },
            format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            }
        return function(table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            window.location.href = uri + base64(format(template, ctx))
        }
    })()

    const setConsolidado = (res) => {
        bodydata.innerHTML = ""
        const data = {
            totales: {}
        }
        res.forEach(iii => {
            const arraypagos = JSON.parse(iii.jsonpagos);
            arraypagos.forEach(ixx => {
                if (!data[iii.fecha_emision])
                    data[iii.fecha_emision] = {
                        total: 0
                    }

                if (!data["totales"]["comision"])
                    data["totales"]["comision"] = 0

                if (!data["totales"]["total"])
                    data["totales"]["total"] = 0

                if (!data[iii.fecha_emision][ixx.tipopago])
                    data[iii.fecha_emision][ixx.tipopago] = 0
                if (!data[iii.fecha_emision]["comision"])
                    data[iii.fecha_emision]["comision"] = 0

                if (!data["totales"][ixx.tipopago])
                    data["totales"][ixx.tipopago] = 0

                data["totales"][ixx.tipopago] += parseFloat(ixx.montoextra)
                data["totales"]["comision"] += parseFloat(ixx.comision)
                data["totales"]["total"] += parseFloat(ixx.montoextra)

                data[iii.fecha_emision]["total"] += parseFloat(ixx.montoextra)
                data[iii.fecha_emision]["comision"] += parseFloat(ixx.comision)

                data[iii.fecha_emision][ixx.tipopago] += ixx.montoextra ? parseFloat(ixx.montoextra) : 0
            })
        })

        for (const [key, value] of Object.entries(data)) {
            if (key != "totales") {
                const tmpdd = data[key]
                bodydata.innerHTML += `
                <tr>
                    <td class="textleft">${key}</td>
                    <td colspan="2"></td>
                    <td class="text-right">${tmpdd["efectivo"] ? tmpdd["efectivo"].toFixed(2) : "" }</td>
                    <td class="text-right">${tmpdd["cheque"] ? tmpdd["cheque"].toFixed(2) : "" }</td>
                    <td class="text-right">${tmpdd["depositobancario"] ? tmpdd["depositobancario"].toFixed(2) : "" }</td>
                    <td class="text-right">${tmpdd["tarjetadebito"] ? tmpdd["tarjetadebito"].toFixed(2) : "" }</td>
                    <td class="text-right">${tmpdd["tarjetacredito"] ? tmpdd["tarjetacredito"].toFixed(2) : "" }</td>
                    <td class="text-right">${tmpdd["porcobrar"] ? tmpdd["porcobrar"].toFixed(2) : "" }</td>
                    <td class="text-right">${tmpdd["comision"] ? tmpdd["comision"].toFixed(2) : "" }</td>
                    <td class="text-right">${tmpdd["total"].toFixed(2)}</td>
                </tr>`;
            }

        }

            const dd = data["totales"]
            bodydata.innerHTML += `
                <tr>
                    <td class="text-right" colspan="3">TOTALES</td>
                    <td class="text-right">${dd["efectivo"] ? dd["efectivo"].toFixed(2) : "" }</td>
                    <td class="text-right">${dd["cheque"] ? dd["cheque"].toFixed(2) : "" }</td>
                    <td class="text-right">${dd["depositobancario"] ? dd["depositobancario"].toFixed(2) : "" }</td>
                    <td class="text-right">${dd["tarjetadebito"] ? dd["tarjetadebito"].toFixed(2) : "" }</td>
                    <td class="text-right">${dd["tarjetacredito"] ? dd["tarjetacredito"].toFixed(2) : "" }</td>
                    <td class="text-right">${dd["porcobrar"] ? dd["porcobrar"].toFixed(2) : "" }</td>
                    <td class="text-right">${dd["comision"] ? dd["comision"].toFixed(2) : "" }</td>
                    <td class="text-right">${dd["total"].toFixed(2)}</td>
                </tr>`;
    }
    const setventascontado = (res, header = false, key) => {
        if (header)
            bodydata.innerHTML += `
                <tr>
                    <td colspan="3" class="text-center" style="font-weight: bold; background-color: #b7e1ff">${key.toUpperCase()}</td>
                </tr>`;

        data = {
            acumulated: [],
            totalgroup: 0,
            ttp: {comision: 0}
        }
        res.forEach(iii => {
            const arraypagos = JSON.parse(iii.jsonpagos);
            let suma = 0;
            const acumulatedtipos = [];
            acumulatedtipos["comision"] = 0
            arraypagos.forEach(ixx => {
                if (ixx.comision) {
                    acumulatedtipos["comision"] += parseFloat(ixx.comision | 0)
                    data.ttp["comision"] += parseFloat(ixx.comision | 0)
                }
                const tii = iii.tipocomprobante.toUpperCase();
                data.acumulated[tii] = parseFloat(iii.totalcargo) + (data.acumulated[tii] ? data.acumulated[tii] : 0);
                acumulatedtipos[ixx.tipopago] = parseFloat(ixx.montoextra) + (acumulatedtipos[ixx.tipopago] ? acumulatedtipos[ixx.tipopago] : 0);
                data.ttp[ixx.tipopago] = parseFloat(ixx.montoextra) + (data.ttp[ixx.tipopago] ? data.ttp[ixx.tipopago] : 0);
                suma += parseFloat(ixx.montoextra);
                data.totalgroup += parseFloat(ixx.montoextra);
            })
            suma = suma.toFixed(2);
            for (const [key, value] of Object.entries(acumulatedtipos)) {
                acumulatedtipos[key] = parseFloat(value).toFixed(2);
            }
            bodydata.innerHTML += `
                <tr>
                    <td class="textleft">${iii.fecha_emision}</td>
                    <td class="textleft"><a href="Imprimir/facturaventa_imprimir.php?id=${iii.codigoventas}">${iii.codigocomprobante}</a></td>
                    <td class="textleft">${iii.fullname}</td>
                    <td class="text-right">${acumulatedtipos["efectivo"] ? acumulatedtipos["efectivo"] : "" }</td>
                    <td class="text-right">${acumulatedtipos["cheque"] ? acumulatedtipos["cheque"] : "" }</td>
                    <td class="text-right">${acumulatedtipos["depositobancario"] ? acumulatedtipos["depositobancario"] : "" }</td>
                    <td class="text-right">${acumulatedtipos["tarjetadebito"] ? acumulatedtipos["tarjetadebito"] : "" }</td>
                    <td class="text-right">${acumulatedtipos["tarjetacredito"] ? acumulatedtipos["tarjetacredito"] : "" }</td>
                    <td class="text-right">${acumulatedtipos["porcobrar"] ? acumulatedtipos["porcobrar"] : "" }</td>
                    <td class="text-right">${acumulatedtipos["comision"]}</td>
                    <td class="text-right">${suma}</td>
                </tr>`;
        });
        for (const [key, value] of Object.entries(data.ttp))
            data.ttp[key] = parseFloat(value).toFixed(2);

        bodydata.innerHTML += `
                <tr style="font-weight: bold">
                    <td class="text-right" colspan="3">TOTAL ${key.toUpperCase()}</td>
                    <td class="text-right">${data.ttp["efectivo"] ? data.ttp["efectivo"] : "0.00" }</td>
                    <td class="text-right">${data.ttp["cheque"] ? data.ttp["cheque"] : "0.00" }</td>
                    <td class="text-right">${data.ttp["depositobancario"] ? data.ttp["depositobancario"] : "0.00" }</td>
                    <td class="text-right">${data.ttp["tarjetadebito"] ? data.ttp["tarjetadebito"] : "0.00" }</td>
                    <td class="text-right">${data.ttp["tarjetacredito"] ? data.ttp["tarjetacredito"] : "0.00" }</td>
                    <td class="text-right">${data.ttp["porcobrar"] ? data.ttp["porcobrar"] : "0.00" }</td>
                    <td class="text-right">${data.ttp["comision"] ? data.ttp["comision"] : "0.00" }</td>
                    <td class="text-right">${data.totalgroup.toFixed(2)}</td>
                </tr>`;
        return data;
    }

    formoperacion.addEventListener("submit", searchconta)
</script>