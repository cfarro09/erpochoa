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
        <div class="col-md-3"></div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success btn-block">Buscar</button>
        </div>
    </div>
</form>
<div class="row">
    <table class="table table-bordered">
        <theady>
            <th class="text-center">Proveedor/Cliente</th>
            <th class="text-center">Detalle</th>
            <th class="text-center">Contado</th>
            <th class="text-center">Credito</th>
            <th class="text-center">Importe</th>
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
            if(cn.cedula is null, cj.razonsocial, CONCAT(cn.paterno, ' ', cn.materno, ' ', cn.nombre)) as fullname,
            IFNULL(cn.cedula, cj.ruc) as identificacion, 
            v.montofact as totalcargo, v.pagoacomulado as totalabono 
        FROM ventas v
        left join cnatural cn on v.codigoclienten = cn.codigoclienten 
        left join cjuridico cj on v.codigoclientej = cj.codigoclientej 
        WHERE 
            v.codigopersonal = ${per}
            and v.sucursal = ${suc}
            and v.fecha_emision BETWEEN '${f_ini}' AND '${f_fin}';
        `;
        
        const res = await get_data_dynamic(query);
        
        setventascredito(res.filter(ii => ii.jsonpagos.includes("porcobrar")));
        
        setventascontado(res.filter(ii => !ii.jsonpagos.includes("porcobrar")));
    }
    const setventascredito = res => {
        bodydata.innerHTML = `
            <tr>
                <td colspan="5" class="text-center" style="font-weight: bold; background-color: #b7e1ff">VENTAS/CREDITO</td>
            </tr>`;
        let acumulated = [];
        res.forEach(iii => {
            const tii = iii.tipocomprobante.toUpperCase();
            acumulated[tii] =  parseFloat(iii.totalcargo) + (acumulated[tii] ? acumulated[tii] : 0);
            bodydata.innerHTML += `
            <tr>
                <td class="text-center">${iii.tipocomprobante.toUpperCase()}-${iii.codigocomprobante}-${iii.fullname}</td>
                <td class="text-center">VENTA CREDITO</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center">${iii.totalcargo}</td>
            </tr>
            `
        });
        for (const [key, value] of Object.entries(acumulated)) {
            bodydata.innerHTML += `
            <tr>
                <td class="text-center"></td>
                <td class="text-center">${key}</td>
                <td class="text-center"></td>
                <td class="text-center">${value.toFixed(2)}</td>
                <td class="text-center"></td>
            </tr>
            `
        }
    }
    const setventascontado = res => {
        bodydata.innerHTML += `
            <tr>
                <td colspan="5" class="text-center" style="font-weight: bold; background-color: #b7e1ff">VENTAS/CONTADO</td>
            </tr>`;
        const acumulated = [];
        res.forEach(iii => {
            const tii = iii.tipocomprobante.toUpperCase();
            acumulated[tii] =  parseFloat(iii.totalcargo) + (acumulated[tii] ? acumulated[tii] : 0);
            bodydata.innerHTML += `
            <tr>
                <td class="text-center">${iii.tipocomprobante.toUpperCase()}-${iii.codigocomprobante}-${iii.fullname}</td>
                <td class="text-center">VENTA CONTADO</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center">${iii.totalcargo}</td>
            </tr>
            `
        });
        for (const [key, value] of Object.entries(acumulated)) {
            bodydata.innerHTML += `
            <tr>
                <td class="text-center"></td>
                <td class="text-center">${key}</td>
                <td class="text-center"></td>
                <td class="text-center">${value.toFixed(2)}</td>
                <td class="text-center"></td>
            </tr>
            `
        }
    }
    const openmodalplan = async () => {
        const res = await get_data_dynamic("select id, codigo, descripcion, padre from plancontable");
        let parentsresult = res;
        const parents = res;

        containerplan.innerHTML = "";

        parents.forEach(ix => {
            containerplan.innerHTML += gethtml(ix, ix.padre == null ? true : false)
        })

        parents.reverse().filter(ix => ix.padre != null).forEach(ix => {
            const tmphtml = getSelector(`#plan_${ix.id}`);
            getSelector(`#plan_${ix.id}`).remove()
            getSelector(`#plan_${ix.padre} .hijos`).innerHTML += tmphtml.innerHTML;

            // parentsresult.filter(xx =>  xx.id == ix.padre).map(oo => {
            //     if(!oo.hijos)
            //         oo.hijos = [];
            //     oo.hijos.push(ix)
            //     return oo;
            // });
            // parentsresult = parentsresult.filter(xx => xx.id != ix.id);
        });

        cargarselect2("#padre", res, 'id', 'descripcion')
    }
    const gethtml = (ix, parent = false) => {
        const ss = parent ? 'font-weight: bold;' : '';
        return `
            <div class="padre" id="plan_${ix.id}">
                <div style="${ss} margin-bottom: 5px">${ix.codigo.toUpperCase()} - ${ix.descripcion.toUpperCase()}</div>
                <div style="margin-left: 20px" class="hijos"></div>
            </div>
        `
    }
    const openmodal = async () => {
        const res = await get_data_dynamic("select id, CONCAT(codigo, ' ', descripcion) as descripcion from plancontable")
        cargarselect2("#padre", res, 'id', 'descripcion')
    }
    const guardar = e => {
        e.preventDefault();
        const data = {
            header: "",
            detalle: []
        }
        const padrex = padre.value == "Seleccione" ? "null" : padre.value;
        data.header = `insert into plancontable (codigo, descripcion, padre) values ('${cuenta.value.toUpperCase()}', '${descripcion.value.toUpperCase()}', ${padrex})`;

        const formData = new FormData();
        formData.append("json", JSON.stringify(data))

        fetch(`setVenta.php`, {
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
                } else if (res.msg) {
                    alert(res.msg.includes("uplicate") ? "El codigo y la descripción que ingresó está duplicado." : "hubo un error, vuelva a intentarlo");
                }
            });
    }
    formoperacion.addEventListener("submit", searchconta)
</script>