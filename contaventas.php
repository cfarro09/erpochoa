<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Listado Ventas";
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
    <div class="col-sm-4">
        <button class="btn btn-success" onclick='tableToExcel("tableconta", sucursalconta.options[sucursalconta.selectedIndex].text)'>Descargar</button>
    </div>
</div>
<div class="row">
    <table class="table table-bordered" id="tableconta">
        <theady>

            <th class="text-center">Fecha</th>
            <th class="text-center">Numero</th>
            <th class="text-center">Cliente</th>

            <th class="text-center">Efectivo</th>
            <th class="text-center">Cheque</th>
            <th class="text-center">D. Bancario</th>
            <th class="text-center">T. Debito</th>
            <th class="text-center">T. Credito</th>
            <th class="text-center">Por cobrar</th>

            <th class="text-center">Total</th>
        </theady>
        <tbody id="bodydata">

        </tbody>
    </table>
</div>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    const codigopersonal = <?= $codpersonal ?>;
    const searchconta = async e => {
        e.preventDefault();
        bodydata.innerHTML = "";
        const suc = sucursalconta.value;
        // const per = personalconta.value;
        const f_ini = fecha_inicio.value;
        const f_fin = fecha_fin.value;

        const query = `
            SELECT 
                if(cn.cedula is null, 'juridico', 'natural') as tipo, montoabono as abonoproveedor, v.tipocomprobante, v.codigocomprobante, v.jsonpagos,
                if(cn.cedula is null, v.codigoclientej, v.codigoclienten) as codcliente,
                if(cn.cedula is null, cj.razonsocial, CONCAT(cn.paterno, ' ', cn.materno, ' ', cn.nombre)) as fullname, v.fecha_emision,
                IFNULL(cn.cedula, cj.ruc) as identificacion, 
                v.montofact as totalcargo, v.pagoacomulado as totalabono 
            FROM ventas v
            left join cnatural cn on v.codigoclienten = cn.codigoclienten 
            left join cjuridico cj on v.codigoclientej = cj.codigoclientej 
            WHERE v.sucursal = ${suc}
                and v.fecha_emision BETWEEN '${f_ini}' AND '${f_fin}'`;

        const res = await get_data_dynamic(query);

        const typelist = {};

        res.forEach(xx => {
            if (!typelist[xx.tipocomprobante])
                typelist[xx.tipocomprobante] = []
            typelist[xx.tipocomprobante].push(xx)
        })
        for (const [key, datatmp1] of Object.entries(typelist)) {
            setventascontado(datatmp1, true, null, key);
        }
        // $('#tableconta').dataTable()

        // const ventas_con_credito = res.filter(ii => ii.jsonpagos.includes("porcobrar"));
        // const ventas_contado = res.filter(ii => !ii.jsonpagos.includes("porcobrar"));
        // let totales = setventascontado(ventas_con_credito, true);
        // setventascontado(ventas_contado, false, totales);
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


    const setventascontado = (res, header = false, data, key) => {
        if (header)
            bodydata.innerHTML += `
                <tr>
                    <td colspan="11" class="text-center" style="font-weight: bold; background-color: #b7e1ff">${key.toUpperCase()}</td>
                </tr>`;
        if (!data) {
            data = {
                acumulated: [],
                totalgroup: 0,
                ttp: []
            }
        }
        res.forEach(iii => {
            const arraypagos = JSON.parse(iii.jsonpagos);
            let suma = 0;
            const acumulatedtipos = [];
            arraypagos.forEach(ixx => {
                const tii = iii.tipocomprobante.toUpperCase();
                data.acumulated[tii] = parseFloat(iii.totalcargo) + (data.acumulated[tii] ? data.acumulated[tii] : 0);
                acumulatedtipos[ixx.tipopago] = parseFloat(ixx.montoextra) + (acumulatedtipos[ixx.tipopago] ? acumulatedtipos[ixx.tipopago] : 0);
                data.ttp[ixx.tipopago] = parseFloat(ixx.montoextra) + (data.ttp[ixx.tipopago] ? data.ttp[ixx.tipopago] : 0);
                suma += parseFloat(ixx.montoextra);
                data.totalgroup += parseFloat(ixx.montoextra);
            })
            suma = suma.toFixed(2);
            for (const [key, value] of Object.entries(acumulatedtipos))
                acumulatedtipos[key] = parseFloat(value).toFixed(2);

            bodydata.innerHTML += `
                <tr>
                    <td class="text-center">${iii.fecha_emision}</td>
                    <td class="text-center">${iii.codigocomprobante}</td>
                    <td class="text-center">${iii.fullname}</td>
                    <td class="text-center">${acumulatedtipos["efectivo"] ? acumulatedtipos["efectivo"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["cheque"] ? acumulatedtipos["cheque"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["depositobancario"] ? acumulatedtipos["depositobancario"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["tarjetadebito"] ? acumulatedtipos["tarjetadebito"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["tarjetacredito"] ? acumulatedtipos["tarjetacredito"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["porcobrar"] ? acumulatedtipos["porcobrar"] : "" }</td>
                    <td class="text-center">${suma}</td>
                </tr>`;
        });
        for (const [key, value] of Object.entries(data.ttp))
            data.ttp[key] = parseFloat(value).toFixed(2);

        if (true) {
            bodydata.innerHTML += `
                <tr style="font-weight: bold">
                    <td class="text-right" colspan="3">TOTALES</td>
                    <td class="text-center">${data.ttp["efectivo"] ? data.ttp["efectivo"] : "0.00" }</td>
                    <td class="text-center">${data.ttp["cheque"] ? data.ttp["cheque"] : "0.00" }</td>
                    <td class="text-center">${data.ttp["depositobancario"] ? data.ttp["depositobancario"] : "0.00" }</td>
                    <td class="text-center">${data.ttp["tarjetadebito"] ? data.ttp["tarjetadebito"] : "0.00" }</td>
                    <td class="text-center">${data.ttp["tarjetacredito"] ? data.ttp["tarjetacredito"] : "0.00" }</td>
                    <td class="text-center">${data.ttp["porcobrar"] ? data.ttp["porcobrar"] : "0.00" }</td>
                    <td class="text-center">${data.totalgroup.toFixed(2)}</td>
                </tr>`;
            // acumulated[""] = totalgroup;
            for (const [key, value] of Object.entries(data.acumulated)) {
                bodydata.innerHTML += `
                <tr>
                    <td style="font-weight: bold" class="text-right" colspan="9">TOTAL ${key}</td>
                    <td style="font-weight: bold" class="text-center">${value.toFixed(2)}</td>
                </tr>`
            }
        }
        return data;
    }
    const setabonocliente = res => {
        const f_ini = fecha_inicio.value;
        const f_fin = fecha_fin.value;
        bodydata.innerHTML += `
            <tr>
                <td colspan="11" class="text-center" style="font-weight: bold; background-color: #b7e1ff">COBRANZA A CLIENTES</td>
            </tr>
            `;
        const acumulated = [];
        const ttp = [];
        let acumulatedpey = 0;
        res.forEach(iii => {
            const arraypagos = JSON.parse(iii.abonoproveedor);
            arraypagos.filter(x => x.codigopersonal == codigopersonal && (new Date(x.fechaxxx) >= new Date(f_ini) && new Date(x.fechaxxx) <= new Date(f_fin))).forEach(ixx => {
                acumulatedpey += parseFloat(ixx.montoextra);
                ttp[ixx.tipopago] = parseFloat(ixx.montoextra) + (ttp[ixx.tipopago] ? ttp[ixx.tipopago] : 0);
                bodydata.innerHTML += `
                <tr>
                    <td class="text-center">${ixx.fechaxxx}</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">${iii.namefull}</td>
                    ${gethtmlfromtype(ixx)}
                    <td class="text-center">${parseFloat(ixx.montoextra).toFixed(2)}</td>
                </tr>`
            })
        });
        bodydata.innerHTML += `
            <tr style="font-weight: bold">
                <td class="text-right" colspan="4">TOTALES</td>
                <td class="text-center">${ttp["efectivo"] ? ttp["efectivo"] : "0.00" }</td>
                <td class="text-center">${ttp["cheque"] ? ttp["cheque"] : "0.00" }</td>
                <td class="text-center">${ttp["depositobancario"] ? ttp["depositobancario"] : "0.00" }</td>
                <td class="text-center">${ttp["tarjetadebito"] ? ttp["tarjetadebito"] : "0.00" }</td>
                <td class="text-center">${ttp["tarjetacredito"] ? ttp["tarjetacredito"] : "0.00" }</td>
                <td class="text-center">${ttp["porcobrar"] ? ttp["porcobrar"] : "0.00" }</td>
                <td class="text-center">${acumulatedpey.toFixed(2)}</td>
            </tr>`;

    }
    const setabonoproveedor = res => {
        const f_ini = fecha_inicio.value;
        const f_fin = fecha_fin.value;
        bodydata.innerHTML += `
            <tr>
                <td colspan="11" class="text-center" style="font-weight: bold; background-color: #b7e1ff">PAGO A PROVEEDORES</td>
            </tr>
            `;
        let acumulatedpey = 0;
        const acumulated = [];
        const ttp = [];
        res.forEach(iii => {
            const arraypagos = JSON.parse(iii.abono);
            arraypagos.filter(x => x.codigopersonal == codigopersonal && (new Date(x.fechaxxx) >= new Date(f_ini) && new Date(x.fechaxxx) <= new Date(f_fin))).forEach(ixx => {
                acumulatedpey += parseFloat(ixx.montoextra);
                ttp[ixx.tipopago] = parseFloat(ixx.montoextra) + (ttp[ixx.tipopago] ? ttp[ixx.tipopago] : 0);
                bodydata.innerHTML += `
                <tr>
                    <td class="text-center">${ixx.fechaxxx}</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">${iii.razonsocial}</td>
                    ${gethtmlfromtype(ixx)}
                    <td class="text-center">${parseFloat(ixx.montoextra).toFixed(2)}</td>
                </tr>`
            })
        });
        bodydata.innerHTML += `
            <tr style="font-weight: bold">
                <td class="text-right" colspan="4">TOTALES</td>
                <td class="text-center">${ttp["efectivo"] ? ttp["efectivo"] : "0.00" }</td>
                <td class="text-center">${ttp["cheque"] ? ttp["cheque"] : "0.00" }</td>
                <td class="text-center">${ttp["depositobancario"] ? ttp["depositobancario"] : "0.00" }</td>
                <td class="text-center">${ttp["tarjetadebito"] ? ttp["tarjetadebito"] : "0.00" }</td>
                <td class="text-center">${ttp["tarjetacredito"] ? ttp["tarjetacredito"] : "0.00" }</td>
                <td class="text-center">${ttp["porcobrar"] ? ttp["porcobrar"] : "0.00" }</td>
                <td class="text-center">${acumulatedpey.toFixed(2)}</td>
            </tr>`;
    }
    const setotroegresos = res => {
        bodydata.innerHTML += `
            <tr>
                <td colspan="11" class="text-center" style="font-weight: bold; background-color: #b7e1ff">PAGO A PROVEEDORES</td>
            </tr>
            `;
        const acumulated = [];
        res.forEach(iii => {
            bodydata.innerHTML += `
            <tr>
                <td class="text-center">${iii.concepto} - ${iii.numerorecibo}</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center">${iii.precio}</td>
            </tr>`
        })
    }
    const gethtmlfromtype = ii => {
        return `
            <td class="text-center">${ii.tipopago == "efectivo" ? parseFloat(ii.montoextra).toFixed(2) : "" }</td>
            <td class="text-center">${ii.tipopago == "cheque" ? parseFloat(ii.montoextra).toFixed(2) : "" }</td>
            <td class="text-center">${ii.tipopago == "depositobancario" ? parseFloat(ii.montoextra).toFixed(2) : "" }</td>
            <td class="text-center">${ii.tipopago == "tarjetadebito" ? parseFloat(ii.montoextra).toFixed(2) : "" }</td>
            <td class="text-center">${ii.tipopago == "tarjetacredito" ? parseFloat(ii.montoextra).toFixed(2) : "" }</td>
            <td class="text-center">${ii.tipopago == "porcobrar" ? parseFloat(ii.montoextra).toFixed(2) : "" }</td>
        `;
    }
    formoperacion.addEventListener("submit", searchconta)
</script>