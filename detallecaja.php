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
<div class="row" style="display: none">
    <div class="col-md-4">
        <div class="form-group">
            <label for="field-1" class="control-label">Fecha Inicio</label>
            <input type="text" required name="fecha_inicio" autocomplete="off" id="fecha_inicio" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="field-1" class="control-label">Fecha termino</label>
            <input type="text" name="fecha_fin" autocomplete="off" id="fecha_fin" required class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" required />
        </div>
    </div>
    <div class="col-md-4">
        <button type="button" onclick="initTable()" class="btn btn-primary" style="margin-top: 10px; padding: 10px 40px">Buscar</button>
    </div>
</div>
<span id="namecuenta" style="font-weight: bold; font-size: 20px"></span> <button class="btn btn-primary" id="btndispose" onclick="dispose()" style="margin-left: 20px">Egreso</button>
<table id="maintable" class="display" width="100%"></table>

<div class="modal fade" id="mdespose" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">

        <form id="formdispose">
            <input type="hidden" id="msucursal">
            <input type="hidden" id="namesucursal">
            <div class="modal-content m-auto">
                <div class="modal-header">
                    <h2 class="modal-title" id="desposetitle">REGISTRO DE EGRESO</h2>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N° Recibo</label>
                                            <input type="text" id="nrecibox" required class="form-control" disabled />
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Fecha</label>
                                            <input type="text" disabled required name="fecha" autocomplete="off" id="fecha" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" />
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Motivo</label> 
                                            <select id="motivo" onchange="changemotivo(this)" class="form-control">
                                                <option value="cuentasxpagar">Cuentas x Pagar Transfenrecia</option>
                                                <option value="transcheque">Transferencia con cheque</option>
                                                <option value="Pago Servicios">Pago Servicios</option>
                                                <option value="Sueldo">Sueldo transferencia</option>
                                                <option value="Viatico">Viatico</option>
                                                <option value="Vacaciones">Vacaciones</option>
                                                <option value="Mantenimiento">Mantenimiento</option>
                                                <option value="Otros">Otros</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-6 divparent divsueldo" style="display: none">
                                        <div class="form-group">
                                            <label class="control-label">Empleado</label>
                                            <select id="empleado"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 divparent divsueldo" style="display: none">
                                        <div class="form-group">
                                            <label class="control-label">Banco</label>
                                            <input type="text" disabled id="bancosueldo"  class="form-control form-control-inline" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 divparent divsueldo" style="display: none">
                                        <div class="form-group">
                                            <label class="control-label">Cuenta</label>
                                            <input type="text" id="cuentasueldo" disabled  class="form-control form-control-inline" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 divparent divsueldo" style="display: none">
                                        <div class="form-group">
                                            <label class="control-label">Fecha Pago</label>
                                            <input type="text" id="fechapagosueldo" disabled  class="form-control form-control-inline" />
                                        </div>
                                    </div>

                                    
                                    
                                    <div class="col-md-6 divparent" >
                                        <div class="form-group">
                                            <label class="control-label">Proveedor</label>
                                            <select id="selectproveedor" onchange="changeproveedor(this)" ></select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 divparent" style="display: none">
                                        <div class="form-group">
                                            <label class="control-label">Saldo</label>
                                            <input type="text" disabled class="form-control" id="saldoproveedor">
                                        </div>
                                    </div>

                                    <div class="col-md-6 divparent" style="display: none">
                                        <div class="form-group">
                                            <label class="control-label">N° Cheque</label>
                                            <input type="text" class="form-control" id="numerocheque">
                                        </div>
                                    </div>
                                    <div class="col-md-6 divparent" style="display: none">
                                        <div class="form-group">
                                            <label class="control-label">Fecha Cheque</label>
                                            <input type="text" readonly required name="fechacheque" autocomplete="off" id="fechacheque" class="form-control form-control-inline input-medium date-picker" data-date-format="yyyy-mm-dd" />
                                        </div>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Cantidad</label>
                                            <input type="number" step="any" id="cantidadxx" required class="form-control form-control-inline" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Observacion</label>
                                            <textarea class="form-control" id="byfrom"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Entregado por: </label>
                                            <select id="personal" disabled></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h2 class="modal-title">Cobro cheque</h2>
            </div>
            <input type="hidden" id="codigocontable">
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Monto</label>
                                <input type="text" required class="form-control" id="montoextra" readonly>
                            </div>
                        </div>
                        <div class="col-sm-4" id="divparentpayextra" style="margin-top: 15px">
                            <button class="btn btn-success" type="button" onclick="addpaylocal()">Agregar Pago</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="field-1" class="control-label">Forma Pago</label>
                                <select required onchange="changemodopago(this)" class="form-control" id="formpago">
                                    <option value="unico">Unico</option>
                                    <option value="compuesto">Compuesto</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div style="margin-bottom: 10px" id="containerpayextraG"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="guardar()" class="modal_close btn btn-success">Guardar</button>
                <button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    const suc = <?= $_SESSION['cod_sucursal']  ?>;
    const id = <?= $_GET["id"] ?>;
    const fullname = '<?= $_GET["fullname"] ?>';
    const idpersonal = <?= $_SESSION['kt_codigopersonal']; ?>;
    namecuenta.textContent = fullname;
    $(function() {
        initTable();
        onloadPersonal()
        onloadProveedores()

        formdispose.addEventListener("submit", guardardespse);
        empleado.onchange = changeEmpleadoSueldo;
    });
    const changeEmpleadoSueldo = e => {
        if(e.target.value == ""){
            cantidadxx.value = "";
            fechapagosueldo.value = "";
            cuentasueldo.value = "";
            bancosueldo.value = "";
        }else{
            const dddd = empleado.options[empleado.selectedIndex];
            cantidadxx.value = dddd.dataset.totalpagar;
            fechapagosueldo.value = dddd.dataset.fechapago;
            cuentasueldo.value = dddd.dataset.nrocuenta;
            bancosueldo.value = dddd.dataset.nombre_banco
        }
    }
    const guardardespse = async e => {
        e.preventDefault();
        const dataxx = {
            header: "",
            detalle: []
        }
        if (personal.value) {

            let nrecibo = parseInt(nrecibox.value) + 1;
            dataxx.detalle.push("UPDATE propiedades SET value = (" + nrecibo + ") where `key` = 'ndetallecaja'");
            let proveedor = 0;
            if (motivo.value == "cuentasxpagar" || motivo.value == "transcheque") {
                proveedor = selectproveedor.value;

                const dd = motivo.value == "transcheque" ? "Transf Cheque" : "Cuentas x Pagar";

                const ruc = selectproveedor.options[selectproveedor.selectedIndex].dataset.ruc;
                const querydepbancario = `insert into cuenta_mov (id_cuenta, fecha_trans, tipo_mov, detalle, monto, saldo, iddespose) VALUES (${id}, '${fecha.value}', 'Egreso' ,'${dd} :: Nro ${nrecibox.value} :: FECHA ${fecha.value} :: RUC ${ruc}', -${cantidadxx.value}, 
                (select cm.saldo - ${cantidadxx.value} from cuenta_mov cm where cm.id_cuenta = ${id} order by cm.id_cuenta_mov desc limit 1), ###ID###)`

                dataxx.detalle.push(querydepbancario);
            }else if(motivo.value == "Sueldo"){
                const idps = empleado.options[empleado.selectedIndex].dataset.idps;
                const query = `
                    update personalsueldo 
                        set estadosueldo = NOW()
                    where id = ${idps}`
                dataxx.detalle.push(query);
            }
            const nnc = motivo.value == "transcheque" ? numerocheque.value : "";
            const ffc = motivo.value == "transcheque" ? fechacheque.value : "";

            let mm = motivo.value == "cuentasxpagar" || motivo.value == "transcheque" ? "cuentasxpagar" : motivo.value
            const query = `
                insert into desposeproveedor 
                    (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo, motivo, estado, proveedor, nrocheque, fechacheque)
                values
                ('${nrecibox.value}', ${cantidadxx.value}, '${fecha.value}', '${byfrom.value}', ${personal.value}, ${suc}, 'despose', '${mm}', 'ENVIADO', ${proveedor}, '${nnc}', '${ffc}')`

            dataxx.header = query;

            let res = await ll_dynamic(dataxx);
            alert("DATOS GUARDADOS CORRECTAMENTE");
            await initTable()
            $("#mdespose").modal("hide")
        } else {
            alert("debe seleccionar personal")
        }
    }

    const changemotivo = e => {
        selectproveedor.closest(".divparent").style.display = e.value == "cuentasxpagar" || e.value == "transcheque" ? "" : "none"
        saldoproveedor.closest(".divparent").style.display = e.value == "cuentasxpagar" || e.value == "transcheque" ? "" : "none"

        numerocheque.closest(".divparent").style.display = e.value == "transcheque" ? "" : "none"
        fechacheque.closest(".divparent").style.display = e.value == "transcheque" ? "" : "none";
        getSelectorAll(".divsueldo").forEach(x => {
            x.value = "";
            x.style.display = e.value == "Sueldo" ? "" : "none";
        });
        cantidadxx.disabled = e.value == "Sueldo" ? true: false;

        $('#empleado').val("").trigger('change');
        
    }
    const onloadPersonalSueldo = async () => {
        const ddd = await get_data_dynamic(`
            SELECT 
                ps.id idps, b.nombre_banco ,p.codigopersonal, p.nrocuenta, CONCAT(ps.mes, ' - ' ,ps.anio) fechapago, ps.totalpagar, concat(p.paterno, ' ', p.materno, ' ', p.nombre) as fullname 
            FROM personal p 
            inner join personalsueldo ps on ps.personal = p.codigopersonal and ps.estadosueldo is null
            left join banco b on b.codigobanco = p.banco
            WHERE p.estado = 0 and p.banco is not null and p.cci <> '' and p.nrocuenta <> ''`);

        ddd.unshift({
            codigopersonal: "",
            fullname: "Seleccionar"
        })
        cargarselect2("#empleado", ddd, "codigopersonal", "fullname", ["totalpagar", "fechapago", "idps", "nrocuenta", "nombre_banco"]);
    }
    const changeproveedor = e => {
        saldoproveedor.closest(".divparent").style.display = "";
        if(e.options[e.selectedIndex] && e.value){
                saldoproveedor.value = e.options[e.selectedIndex].dataset.saldo;
        }

    }
    const dispose = async () => {
        formdispose.reset()
        fecha.value = new Date(new Date().setHours(10)).toISOString().substring(0, 10)

        let nrecibo = await get_data_dynamic("select `value` from propiedades where `key` = 'ndetallecaja'");
        nrecibox.value = nrecibo[0].value
        selectproveedor.value = "";
        saldoproveedor.value = "";
        saldoproveedor.closest(".divparent").style.display = "none";
        selectproveedor.closest(".divparent").style.display = "";
        numerocheque.closest(".divparent").style.display = "none";
        fechacheque.closest(".divparent").style.display = "none";
        onloadPersonalSueldo();
        getSelectorAll(".divsueldo").forEach(x => x.style.display = "none")

        personal.value = 0
        $("#mdespose").modal()
        $('#personal').val(idpersonal).trigger('change');
        $('#selectproveedor').val("").trigger('change');
    }
    const onloadPersonal = async () => {
        const res = await get_data_dynamic("SELECT codigopersonal, concat(paterno, ' ', materno, ' ', nombre) as fullname FROM personal WHERE estado = 0");
        res.unshift({
            codigopersonal: "",
            fullname: "Seleccionar"
        })
        cargarselect2("#personal", res, "codigopersonal", "fullname")
    }
    const onloadProveedores = async () => {
        const res = await get_data_dynamic(`
        SELECT p.codigoproveedor,  CONCAT(razonsocial, '-', ruc) fullname, ruc,
        (sum(IFNULL(rc.total, 0)) + sum(IFNULL(e.precioestibador_soles, 0)) + sum(IFNULL(preciotransp_soles, 0)) + sum(IFNULL(preciond_soles, 0)) + sum(IFNULL(precionc_soles, 0))) - (sum(IFNULL(rc.montoochoa, 0)) +  sum(IFNULL(e.montoochoa, 0)) +  sum(IFNULL(t.montoochoa, 0)) + IFNULL((select sum(IFNULL(dess.cantidad, 0)) from desposeproveedor dess where dess.motivo = 'cuentasxpagar' and dess.proveedor = p.codigoproveedor), 0) +  sum(IFNULL(nd.montoochoa, 0)) +  sum(IFNULL(nc.precionc_soles, 0))) as saldo
        FROM proveedor p
        LEFT JOIN registro_compras rc on rc.rucproveedor = p.ruc
        LEFT JOIN transporte_compra t on t.ructransporte = p.ruc
        LEFT JOIN estibador_compra e on e.rucestibador = p.ruc
        LEFT JOIN notadebito_compra nd on nd.rucnd = p.ruc
        LEFT JOIN notacredito_compra nc on nc.rucnotacredito = p.ruc
        WHERE p.estado = '0' and p.ruc <> '00000000000'
        GROUP BY p.ruc`);
        
        res.unshift({
            codigoproveedor: "",
            fullname: "Seleccionar"
        })
        cargarselect2("#selectproveedor", res, "codigoproveedor", "fullname", ["saldo", "ruc"])
    }
    const initTable = async () => {
        const query = `
            select * from cuenta_mov where id_cuenta = ${id} order by id_cuenta_mov asc
        `
        let data = await get_data_dynamic(query);
        data = data.map(x => {
            return {
                ...x,
                cargo: parseFloat(x.monto) < 0 ? (x.monto*-1).toFixed(2) : "0.00",
                monto: parseFloat(x.monto) < 0 ? "0.00" : x.monto,
            }
        })
        $('#maintable').DataTable({
            data,
            ordering: false,
            destroy: true,
            columns: [
                {
                    title: 'TIPO MOV',
                    data: 'tipo_mov'
                },
                {
                    title: 'DETALLE',
                    data: 'detalle'
                },
                {
                    title: 'CARGO',
                    data: 'cargo'
                },
                {
                    title: 'ABONO',
                    data: 'monto'
                },
                {
                    title: 'SALDO',
                    data: 'saldo'
                },
            ]
        });
    }
</script>