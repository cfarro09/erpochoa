<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Liquidación Caja";
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

$query_personalx = "SELECT codigopersonal, concat(paterno, ' ', materno, ' ', nombre) as fullname FROM personal WHERE estado = 0";
$l_per = mysql_query($query_personalx, $Ventas) or die(mysql_error());
$r_per = mysql_fetch_assoc($l_per);
$totalRows_personal = mysql_num_rows($l_per);

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
        <div class="col-md-4">
            <div class="form-group">
                <input type="hidden" name="cod_acceso_seguridad" value="0" id="cod_acceso_seguridad">
                <label for="personalconta" class="control-label">Personal</label>
                <select name="personal" id="personalconta" required class="form-control select2 tooltips" data-placement="top" data-original-title="Seleccionar personal">
                    <?php do { ?>
                        <option <?= $r_per['codigopersonal'] == $codpersonal ? 'selected' : '' ?> value="<?= $r_per['codigopersonal'] ?>">
                            <?= $r_per['fullname'] ?>
                        </option>
                    <?php
                    } while ($r_per = mysql_fetch_assoc($l_per)); ?>
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
<div class="row">
    <table class="table table-bordered">
        <theady>

            <th class="text-center">Fecha</th>
            <th class="text-center">Comprobante</th>
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
        const per = personalconta.value;
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
            WHERE 
                v.codigopersonal = ${per}
                and v.sucursal = ${suc}
                and v.fecha_emision BETWEEN '${f_ini}' AND '${f_fin}'`;
        const queryotrosegresos = `
            SELECT 
                * from serviciosporpagar
            WHERE 
                codpersonal = ${per}
                and codsucursal = ${suc}
                and fecharegistro BETWEEN '${f_ini}' AND '${f_fin}'`;
        const queryabonos = `
            SELECT v.abonoproveedor, v.fecha_emision, if(cj.razonsocial is null, CONCAT(cn.paterno, ' ', cn.materno, ' ', cn.nombre), cj.razonsocial) as namefull 
            FROM ventas v  
            LEFT JOIN cnatural cn on cn.codigoclienten = v.codigoclienten 
            LEFT JOIN cjuridico cj on cj.codigoclientej = v.codigoclientej  
            WHERE 
                v.abonoproveedor is not null
                and v.sucursal = ${suc}
                and v.fecha_emision >= '${f_ini}'`;

        const queryabonosproveedor = `
                SELECT 
                    'compra' as tipo, r.abonoochoa as abono, p.razonsocial
                FROM registro_compras r
                LEFT JOIN proveedor p on p.ruc = r.rucproveedor
                WHERE
                    r.fecha >= '${f_ini}' and r.codigosuc = ${suc} and r.abonoochoa is not null
            UNION
                SELECT 
                    'transporte' as tipo, t.abonoochoa as abono, p.razonsocial
                FROM transporte_compra t
                LEFT JOIN proveedor p on p.ruc = t.ructransporte
                INNER JOIN registro_compras r on r.codigorc = t.codigocompras
                WHERE
                    t.fecharegistro >= '${f_ini}' and r.codigosuc = ${suc} and t.abonoochoa is not null
            UNION
                SELECT 
                    'estibador' as tipo, e.abonoochoa as abono, p.razonsocial
                FROM estibador_compra e
                LEFT JOIN proveedor p on p.ruc = e.rucestibador
                INNER JOIN registro_compras r on r.codigorc = e.codigocompras
                WHERE
                    e.fecharegistro >= '${f_ini}' and r.codigosuc = ${suc} and e.abonoochoa is not null
            UNION
                SELECT 
                    'notadebito' as tipo, nd.abonoochoa as abono, p.razonsocial
                FROM notadebito_compra nd
                LEFT JOIN proveedor p on p.ruc = nd.rucnd
                INNER JOIN registro_compras r on r.codigorc = nd.codigocompras
                WHERE
                    nd.fecharegistro >= '${f_ini}' and r.codigosuc = ${suc} and nd.abonoochoa is not null
            UNION
                SELECT 
                    'notacredito' as tipo, nc.abonoochoa as abono, p.razonsocial
                FROM notacredito_compra nc
                LEFT JOIN proveedor p on p.ruc = nc.rucnotacredito
                INNER JOIN registro_compras r on r.codigorc = nc.codigocompras
                WHERE
                    nc.fecharegistro >= '${f_ini}' and r.codigosuc = ${suc} and nc.abonoochoa is not null
                `;

        const res = await get_data_dynamic(query);
        const resabonos = await get_data_dynamic(queryabonos);
        const abonosproveedor = await get_data_dynamic(queryabonosproveedor);
        const otrosegresos = await get_data_dynamic(queryotrosegresos);
        // console.log(abonosproveedor)
        const ventas_con_credito = res.filter(ii => ii.jsonpagos.includes("porcobrar"));
        const ventas_contado = res.filter(ii => !ii.jsonpagos.includes("porcobrar"));

        // const pagoscontado = 
        setventascontado(ventas_con_credito, true);
        // ventas_contado.forEach(x => pagoscontado.push(x))
        setventascontado(ventas_contado);
        setabonocliente(resabonos)
        setabonoproveedor(abonosproveedor)
        // setotroegresos(otrosegresos)
    }
    const setventascredito = res => {
        const f_ini = fecha_inicio.value;
        const f_fin = fecha_fin.value;
        bodydata.innerHTML = `
            <tr>
                <td colspan="11" class="text-center" style="font-weight: bold; background-color: #b7e1ff">VENTAS/CREDITO</td>
            </tr>`;
        const ventas_con_pagos_contado = [];
        let acumulated = [];
        res.forEach(iii => {

            const arraypagos = JSON.parse(iii.jsonpagos);
            arraypagos.filter(x => x.tipopago == "porcobrar").forEach(ixx => {
                const tii = iii.tipocomprobante.toUpperCase();
                acumulated[tii] = parseFloat(iii.totalcargo) + (acumulated[tii] ? acumulated[tii] : 0);
                bodydata.innerHTML += `
                    <tr>
                        <td class="text-center">${iii.fecha_emision}</td>
                        <td class="text-center">${iii.tipocomprobante.toUpperCase()}</td>
                        <td class="text-center">${iii.codigocomprobante}</td>
                        <td class="text-center">${iii.fullname}</td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center">${ixx.montoextra}</td>
                        <td class="text-center">${ixx.montoextra}</td>
                    </tr>`
            });
            const pagoscontado = arraypagos.filter(x => x.tipopago != "porcobrar");
            if (pagoscontado.length > 0) {
                iii.jsonpagos = JSON.stringify(pagoscontado);
                ventas_con_pagos_contado.push(iii)
            }
        });

        // for (const [key, value] of Object.entries(acumulated)) {
        //     bodydata.innerHTML += `
        //     <tr>
        //         <td class="text-center"></td>
        //         <td class="text-center">${key}</td>
        //         <td class="text-center"></td>
        //         <td class="text-center">${value.toFixed(2)}</td>
        //         <td class="text-center"></td>
        //     </tr>`
        // }
        return ventas_con_pagos_contado;
    }
    
    const setventascontado = (res, header = false) => {
        if(header)
            bodydata.innerHTML += `
                <tr>
                    <td colspan="11" class="text-center" style="font-weight: bold; background-color: #b7e1ff">VENTAS</td>
                </tr>`;
        const acumulated = [];
        
        res.forEach(iii => {
            const arraypagos = JSON.parse(iii.jsonpagos);
            let suma = 0;
            const acumulatedtipos = [];
            arraypagos.forEach(ixx => {
                const tii = iii.tipocomprobante.toUpperCase();
                acumulated[tii] = parseFloat(iii.totalcargo) + (acumulated[tii] ? acumulated[tii] : 0);
                acumulatedtipos[ixx.tipopago] = parseFloat(ixx.montoextra) + (acumulatedtipos[ixx.tipopago] ? acumulatedtipos[ixx.tipopago] : 0);
                suma += parseFloat(ixx.montoextra);
            })
            suma = suma.toFixed(2);
            for (const [key, value] of Object.entries(acumulatedtipos)) 
                acumulatedtipos[key] = parseFloat(value).toFixed(2);
            bodydata.innerHTML += `
                <tr>
                    <td class="text-center">${iii.fecha_emision}</td>
                    <td class="text-center">${iii.tipocomprobante.toUpperCase()}</td>
                    <td class="text-center">${iii.codigocomprobante}</td>
                    <td class="text-center">${iii.fullname}</td>
                    <td class="text-center">${acumulatedtipos["efectivo"] ? acumulatedtipos["efectivo"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["cheque"] ? acumulatedtipos["cheque"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["depositobancario"] ? acumulatedtipos["depositobancario"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["tarjetadebito"] ? acumulatedtipos["tarjetadebito"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["tarjetacredito"] ? acumulatedtipos["tarjetacredito"] : "" }</td>
                    <td class="text-center">${acumulatedtipos["porcobrar"] ? acumulatedtipos["porcobrar"] : "" }</td>
                    <td class="text-center">${suma}</td>
                </tr>`


        });
        // for (const [key, value] of Object.entries(acumulated)) {
        //     bodydata.innerHTML += `
        //     <tr>
        //         <td class="text-center"></td>
        //         <td class="text-center">${key}</td>
        //         <td class="text-center"></td>
        //         <td class="text-center">${value.toFixed(2)}</td>
        //         <td class="text-center"></td>
        //     </tr>`
        // }
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

        res.forEach(iii => {
            console.log(iii);
            const arraypagos = JSON.parse(iii.abonoproveedor);
            arraypagos.filter(x => x.codigopersonal == codigopersonal && (new Date(x.fechaxxx) >= new Date(f_ini) && new Date(x.fechaxxx) <= new Date(f_fin))).forEach(ixx => {
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
    }
    const setabonoproveedor = res => {
        const f_ini = fecha_inicio.value;
        const f_fin = fecha_fin.value;
        bodydata.innerHTML += `
            <tr>
                <td colspan="11" class="text-center" style="font-weight: bold; background-color: #b7e1ff">PAGO A PROVEEDORES</td>
            </tr>
            `;
        const acumulated = [];
        res.forEach(iii => {
            const arraypagos = JSON.parse(iii.abono);
            arraypagos.filter(x => x.codigopersonal == codigopersonal && (new Date(x.fechaxxx) >= new Date(f_ini) && new Date(x.fechaxxx) <= new Date(f_fin))).forEach(ixx => {
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