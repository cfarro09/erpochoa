<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Efectivo Caja";
$NombreBotonAgregar = "Agregar";
$EstadoBotonAgregar = "disabled";
$popupAncho = 700;
$popupAlto = 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");

include("Fragmentos/abrirpopupcentro.php");

$suc = $_SESSION['cod_sucursal'];

?>

<style>
    .dt-buttons {
        margin-top: 0 !important;
        margin-bottom: 15px !important;
    }
</style>
<table id="maintable" class="display" width="100%"></table>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h2 class="modal-title" style="display: inline-block; margin-right: 10px" id="moperationtitle">Detalle Ingresos</h2>

                <button class="btn btn-primary" id="btndisposeingreso" onclick="disposeingreso()">Ingreso</button>
                <button class="btn btn-primary" id="btndispose" onclick="dispose()">Egreso</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <table id="ventastable" class="display" width="100%"></table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="typedespose">
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
                                            <label class="control-label">Cantidad</label>
                                            <input type="number" step="any" id="cantidadxx" required class="form-control form-control-inline" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Motivo</label> <select id="motivo" class="form-control">
                                                <option value="cajatumbes">Remesa en Efectivo a Caja Central</option>
                                                <option value="Deposito en cuenta">Deposito en cuenta</option>
                                                <option value="Pago Servicios">Pago Servicios</option>
                                                <option id="optionsalary" value="Sueldo">Sueldo</option>
                                                <option value="Viatico">Viatico</option>
                                                <option value="Vacaciones">Vacaciones</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 divparent">
                                        <div class="form-group">
                                            <label class="control-label">Fecha Sueldo</label>
                                            <input type="text" step="any"  disabled id="inputfechasueldo" required class="form-control form-control-inline" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 divparent">
                                        <div class="form-group">
                                            <label class="control-label">Cuenta</label>
                                            <select id="cuentabancaria" class="form-control"></select>
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
                    <button type="submit" id="guardardespose" class="btn btn-primary">Guardar</button>
                    <button type="button" id="imprimirdespose" onclick="imprimirdesposeX()" class="btn btn-primary">Imprimir</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>

        </form>



    </div>
</div>



<div class="modal fade" id="mdesposeingreso" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 700px">

        <form id="formdisposeingreso">
            <input type="hidden" id="msucursal">
            <input type="hidden" id="namesucursal">
            <div class="modal-content m-auto">
                <div class="modal-header">
                    <h2 class="modal-title" id="desposetitleingreso">REGISTRO DE INGRESO</h2>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N° Recibo</label>
                                            <input type="text" disabled id="nreciboxingreso" required class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Fecha</label>
                                            <input type="text" disabled required name="fecha" autocomplete="off" id="fechaingreso" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Recibido de:</label>
                                            <select id="clienteingreso"></select>
                                        </div>
                                    </div>
                                </div>
                               

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Cantidad</label>
                                            <input type="number" step="any" id="cantidadxxingreso" autocomplete="off" required class="form-control form-control-inline" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Por</label>
                                            <textarea class="form-control" required id="byfromingreso"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Recibido por:</label>
                                            <select disabled id="personalingreso"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" id="guardardesposeingreso" class="btn btn-primary">Guardar</button>
                    <button type="button" id="imprimirdesposeingreso" onclick="imprimirdesposeii()" class="btn btn-primary">Imprimir</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>

        </form>



    </div>
</div>
<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    const suc = <?= $suc  ?>;
    let salary = 0;
    let fechasueldo = "";
    const idpersonal = <?= $_SESSION['kt_codigopersonal']; ?>;
    $(function() {
        initTable()
        onloadPersonal()
        onloadCliente()
        onloadCuentas()
        if (suc == 1) {
            btndispose.style.display = "none"
            btndisposeingreso.style.display = "none"


        } else {
            btndispose.style.display = ""
            btndisposeingreso.style.display = ""
        }
        formdispose.addEventListener("submit", guardardespse)
        formdisposeingreso.addEventListener("submit", guardardespseingreso)
    });
    const changeMotivo = e => {
        cuentabancaria.closest(".divparent").style.display = e.target.value == "Deposito en cuenta" ? "" : "none";
        inputfechasueldo.closest(".divparent").style.display = e.target.value == "Sueldo" ? "" : "none";
        cantidadxx.value = 0;
        cantidadxx.disabled = false;
        if(e.target.value == "Sueldo"){
            cantidadxx.value = salary;
            cantidadxx.disabled = true;
            inputfechasueldo.value = fechasueldo;
        }
    }
    motivo.onchange = changeMotivo;

    const dispose = async () => {
        limpiardespisexx();
        formdispose.reset()
        fecha.value = new Date(new Date().setHours(10)).toISOString().substring(0, 10)
        await validateSalary();
        let nrecibo = await get_data_dynamic("select `value` from propiedades where `key` = 'negresos'");
        nrecibox.value = nrecibo[0].value
        typedespose.value = "negresos";
        personal.value = 0
        cuentabancaria.closest(".divparent").style.display = "none"
        inputfechasueldo.closest(".divparent").style.display = "none"

        guardardespose.style.display = "";
        imprimirdespose.style.display = "none";

        $("#mdespose").modal()
        $('#personal').val(idpersonal).trigger('change');
    }
    const validateSalary = async () => {
        const query = `
            select totalpagar, CONCAT(mes, ' - ', anio) fechasueldo from personalsueldo 
            where personal = ${idpersonal} and estadosueldo is null
        `;
        let res = await get_data_dynamic(query);
        
        if(res.length > 0){
            optionsalary.disabled = false;
            salary = res[0].totalpagar
            fechasueldo = res[0].fechasueldo
        }else{
            optionsalary.disabled = true;
        }
    }
    const disposeingreso = async () => {
        limpiardespise();
        formdisposeingreso.reset()
        fechaingreso.value = new Date(new Date().setHours(10)).toISOString().substring(0, 10);
        let nrecibo = await get_data_dynamic("select `value` from propiedades where `key` = 'ningresos'");
        nreciboxingreso.value = nrecibo[0].value
        typedespose.value = "ningresos";

        personalingreso.value = 0;

        guardardesposeingreso.style.display = "";
        imprimirdesposeingreso.style.display = "none";

        $("#mdesposeingreso").modal();
        
        $('#clienteingreso').val("").trigger('change');
        $('#personalingreso').val(idpersonal).trigger('change');
    }
    let clickguardardespseingreso = false;
    const guardardespseingreso = async e => {
        e.preventDefault();

        if (!clickguardardespseingreso) {
            clickguardardespseingreso = true;
        } else{
            return;
        }

        if (personalingreso.value) {

            let nrecibo = parseInt(nreciboxingreso.value) + 1;

            await ff_dynamic("UPDATE propiedades SET value = (" + nrecibo + ") where `key` = 'ningresos'")
            
            const tipo = clienteingreso.options[clienteingreso.selectedIndex].dataset.tipo

            const query = `
            insert into despose 
                (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo, codigocliente, tipocliente) 
            values
                ('${nreciboxingreso.value}', ${cantidadxxingreso.value}, '${fechaingreso.value}', '${byfromingreso.value}', ${personalingreso.value}, ${msucursal.value}, 'ingreso', ${clienteingreso.value}, '${tipo}')`
            let res = await ff_dynamic(query);
            clickguardardespseingreso = false;
            alert("DATOS GUARDADOS CORRECTAMENTE");
            $("#mdesposeingreso").modal("hide")
            await initTable()
            await getdetail(msucursal.value, namesucursal.value)
        } else {
            alert("debe seleccionar personal")
        }
    }
    let clickguardardespse = false;
    const guardardespse = async e => {
        e.preventDefault();

        if (!clickguardardespse) {
            clickguardardespse = true;
        } else{
            return;
        }

        // ll_dynamic
        const dataxx = {
            header: "",
            detalle: []
        }
        if (personal.value) {

            let nrecibo = parseInt(nrecibox.value) + 1;
            dataxx.detalle.push("UPDATE propiedades SET value = (" + nrecibo + ") where `key` = 'negresos'");

            if (motivo.value == "Deposito en cuenta") {
                const querydepbancario = `insert into cuenta_mov (id_cuenta, fecha_trans, tipo_mov, detalle, monto, saldo) VALUES (${cuentabancaria.value}, '${fecha.value}', 'DEPOSITO EFECTIVO N ${nrecibox.value} ${namesucursal.value}', 'DEPOSITO EFECTIVO  N ${nrecibox.value} ${namesucursal.value}', ${cantidadxx.value}, 
                (select cm.saldo + ${cantidadxx.value} from cuenta_mov cm where cm.id_cuenta = ${cuentabancaria.value} order by cm.id_cuenta_mov desc limit 1))`

                dataxx.detalle.push(querydepbancario);
            } else if (motivo.value == "cajatumbes") {
                const query = `
                    insert into despose 
                        (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo, motivo, estado, fromdespose) 
                    values
                        ('${nrecibox.value}', ${cantidadxx.value}, '${fecha.value}', '${byfrom.value}', ${personal.value}, 11, 'ingresocaja', 'CAJA TUMBES - ENVIO DE ${namesucursal.value}', 'EN ESPERA', ###ID###)`
                dataxx.detalle.push(query);
            }
            else if(motivo.value == "Sueldo"){
                const query = `
                    update personalsueldo 
                        set estadosueldo = NOW()
                    where personal = ${idpersonal} and estadosueldo is null`
                dataxx.detalle.push(query);
            }
            const query = `
                insert into despose 
                    (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo, motivo, estado) 
                values
                ('${nrecibox.value}', ${cantidadxx.value}, '${fecha.value}', '${byfrom.value}', ${personal.value}, ${msucursal.value}, 'despose', '${motivo.value}', 'ENVIADO')`

            dataxx.header = query;

            let res = await ll_dynamic(dataxx);
            alert("DATOS GUARDADOS CORRECTAMENTE");
            clickguardardespse = false;

            await initTable()
            await getdetail(msucursal.value, namesucursal.value)
            $("#mdespose").modal("hide")
        } else {
            alert("debe seleccionar personal")
        }
    }
    const setStatusIngresos = async (id, status, fromdepose) => {
        const dataxx = {
            header: "",
            detalle: []
        }
        dataxx.detalle.push(`update despose set estado = '${status}' where id = ${id}`);
        if (fromdepose)
            dataxx.detalle.push(`update despose set estado = '${status}' where id = ${fromdepose}`);
        let res = await ll_dynamic(dataxx);
        if (res && res.success) {
            alert("DATOS GUARDADOS CORRECTAMENTE");
            await initTable()
            await getdetail(msucursal.value, namesucursal.value)
        } else {
            alert("hubo un error")
        }
    }
    const getdetail = async (id, name) => {
        msucursal.value = id
        namesucursal.value = name
        $("#moperation").modal();
        moperationtitle.textContent = "EFECTIVO - CAJA " + name
        let despose = [];
        if (id != 11) {
            const query1 = `
                SELECT 
                    id, fecha, nrorecibo, cantidad as despose, '' as total, tipo, CONCAT(motivo, ' - ',estado) as motivo, por, fromdespose
                FROM despose
                WHERE 
                    sucursal = ${id} and (tipo = 'despose' or tipo = 'ingreso')`;

            despose = await get_data_dynamic(query1);
        } else {
            const query1 = `
                SELECT 
                    id, fecha, nrorecibo, tipo, cantidad as total, motivo, estado, fromdespose
                FROM despose
                WHERE 
                    sucursal = ${id} and (tipo = 'despose' or tipo = 'ingresocaja')`;

            despose = await get_data_dynamic(query1);
        }
        setConsolidado(id, despose)

    }
    const onloadPersonal = async () => {
        const res = await get_data_dynamic("SELECT codigopersonal, concat(paterno, ' ', materno, ' ', nombre) as fullname FROM personal WHERE estado = 0");
        res.unshift({
            codigopersonal: "",
            fullname: "Seleccionar"
        })
        cargarselect2("#personal", res, "codigopersonal", "fullname")
        cargarselect2("#personalingreso", res, "codigopersonal", "fullname")
    }
    

    const onloadCliente = async () => {
        const res = await get_data_dynamic("SELECT 'natural' as tipo, codigoclienten as codigo, CONCAT(paterno, ' ', materno, ' ', nombre, ' ',cedula) as name FROM cnatural WHERE estado = 0 UNION SELECT 'juridico' as tipo, codigoclientej as codigo, CONCAT(razonsocial,' ',ruc) as name FROM cjuridico WHERE estado = 0");
        res.unshift({
            codigo: "",
            name: "Seleccionar"
        })

        cargarselect2("#clienteingreso", res, "codigo", "name", ["tipo"]);
    }

    const onloadCuentas = async () => {

        const query = 'SELECT c.id_cuenta, concat(b.nombre_banco, " - ", c.tipo, " - CTA ", c.numero_cuenta, " - ", c.moneda) as description FROM `cuenta` c inner JOIN banco b on c.idcodigobanco=b.codigobanco';
        const arraycuentaabonado = await get_data_dynamic(query);
        arraycuentaabonado.forEach(x => {
            cuentabancaria.innerHTML += `
                <option value="${x.id_cuenta}">${x.description}</option>
            `;
        });
    }
    const setConsolidado = async (id, des) => {
        let qwer = []
        if ((id == 1 && suc == 1) ) {
            btndispose.style.display = ""
            btndisposeingreso.style.display = ""
        } else {
            if (id != 1 && suc != 1) {
                btndispose.style.display = ""
                btndisposeingreso.style.display = ""
            } else {
                btndispose.style.display = "none"
                btndisposeingreso.style.display = "none"
            }
        }
        if (id != 11) {

            const rr = await proccessIngresosEfectivo(id)
            const datatotble = rr.datatotble;

            des = des.map(x => {
                const motivxxo = x.motivo || ""
                x.motivo = motivxxo.includes("cajatumbes") ? motivxxo.replace("cajatumbes", "Remesa en Efectivo a Caja Central") : motivxxo

                return {
                    ...x,
                    iddespose: x.id,
                    total: x.tipo == "ingreso" ? x.despose : 0,
                    despose: x.tipo == "ingreso" ? 0 : x.despose,
                    motivo: x.motivo == "" || x.motivo == " - " ?   x.por.substring(0, 30) : x.motivo,
                    nrorecibo: x.tipo == "ingreso" ? `RI - ${x.nrorecibo}` : `RE - ${x.nrorecibo}`
                }
            })

            qwer = [...datatotble, ...des];

            qwer.sort((a, b) => new Date(a.fecha) - new Date(b.fecha))

            let saldo = 0;

            qwer = qwer.map(x => {
                const despose = x.despose ? parseFloat(x.despose) : 0
                const total = x.total ? parseFloat(x.total) : 0
                saldo = saldo + total - despose
                x.saldo = saldo.toFixed(2)
                x.nrorecibo = x.nrorecibo || "";
                x.iddespose = x.iddespose || 0;
                return x
            })
            qwer = qwer.reverse();
        } else {
            let saldo = 0;
            des.forEach(x => {

                if (x.tipo == "ingresocaja") {
                    saldo += parseFloat(x.total)
                    qwer.push({
                        ...x,
                        iddespose: x.iddespose,
                        motivo: `${x.motivo} ${x.estado}`,
                        despose: 0,
                        saldo: saldo.toFixed(2),
                        nrorecibo: "RI - " + x.nrorecibo
                    })
                } else {
                    saldo -= parseFloat(x.total)
                    qwer.push({
                        ...x,
                        iddespose: x.iddespose,
                        total: 0,
                        despose: x.total,
                        nrorecibo: "RE - " + x.nrorecibo,
                        saldo: saldo.toFixed(2)
                    })
                }
            })
            qwer = qwer.reverse();
        }
        

        $('#ventastable').DataTable({
            data: qwer,
            destroy: true,
            ordering: false,
            buttons: [{
                    extend: 'print',
                    className: 'btn dark btn-outline'
                },
                {
                    extend: 'copy',
                    className: 'btn red btn-outline'
                },
                {
                    extend: 'pdf',
                    className: 'btn green btn-outline'
                },
                {
                    extend: 'excel',
                    className: 'btn yellow btn-outline '
                },
                {
                    extend: 'csv',
                    className: 'btn purple btn-outline '
                },
                {
                    extend: 'colvis',
                    className: 'btn dark btn-outline',
                    text: 'Columns'
                }
            ],
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            columns: [{
                    title: 'fecha',
                    data: 'fecha'
                },
                {
                    title: 'NRO',
                    data: 'nrorecibo'
                },
                { //pruebalo
                    title: 'motivo',
                    data: 'motivo'
                },
                {
                    title: 'Ingreso',
                    data: 'total',
                    className: 'dt-body-right'
                },
                {
                    title: 'Egreso',
                    data: 'despose',
                    className: 'dt-body-right'
                },
                {
                    title: 'saldo',
                    data: 'saldo',
                    className: 'dt-body-right'
                },
                {
                    title: 'acciones',

                    render: function(data, type, row) {
                        let aux11 = "";
                        if ((row.motivo.includes('EN ESPERA') || (row.motivo.includes('ENVIADO')) && msucursal.value == 11)) {
                            aux11 = `
                                <div class="">
                                    <button class="btn btn-success" onclick="setStatusIngresos(${row.id},'ACEPTADO', ${row.fromdespose})">Aceptar</button>
                                    <button class="btn btn-danger" onclick="setStatusIngresos(${row.id},'RECHAZADO', ${row.fromdespose})">Rechazar</button>
                                </div>
                                `
                        } else {
                            aux11 = ""
                        }
                        
                        if (row.iddespose) {
                            aux11 += `<a href="#" data-iddespose="${row.iddespose}" onclick="verdespose(this)">Ver</a>`;
                        }

                        return aux11;
                    }
                }
            ]
        });
    }

    const limpiardespise = () => {
        nreciboxingreso.value = "";
        fechaingreso.value = "";
        cantidadxxingreso.disabled = false;
        cantidadxxingreso.value = "";
        byfromingreso.disabled = false;
        byfromingreso.value = "";
        
        clienteingreso.disabled = false;
        personalingreso.disabled = false;
    }
    
    const limpiardespisexx = () => {
        cantidadxx.disabled = false;
        cantidadxx.value = "";
        byfrom.disabled = false;
        byfrom.value = "";
        motivo.disabled = false;
        motivo.value = "";
        personal.disabled = false;
        personal.value = "";
    }

    const imprimirdesposeii = () => {
        const printnreciboxingreso = nreciboxingreso.value;
        const printfechaingreso = fechaingreso.value;
        const printcantidadxxingreso = cantidadxxingreso.value;
        const printbyfromingreso = byfromingreso.value;

        var copyprrr = document.querySelector('#formdisposeingreso .modal-content');
        
        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        mywindow.document.write('<html><head><title>Imprimir</title>');

        mywindow.document.write('</head><body >');

        mywindow.document.write(copyprrr.innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.document.querySelector("#nreciboxingreso").value = printnreciboxingreso;
        mywindow.document.querySelector("#fechaingreso").value = printfechaingreso;
        mywindow.document.querySelector("#cantidadxxingreso").value = printcantidadxxingreso;
        mywindow.document.querySelector("#byfromingreso").value = printbyfromingreso;
        
        mywindow.document.querySelector("#clienteingreso").remove()
        mywindow.document.querySelector("#personalingreso").remove()
        mywindow.print();
    }
    const imprimirdesposeX = () => {
        
        const printnrecibox = nrecibox.value;
        const printfecha = fecha.value;
        const printcantidadxx = cantidadxx.value;
        const printmotivo = motivo.value;
        const printbyfrom = byfrom.value;

        var copyprrr = document.querySelector('#formdispose .modal-content');
        
        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        mywindow.document.write('<html><head><title>Imprimir</title>');

        mywindow.document.write('</head><body >');

        mywindow.document.write(copyprrr.innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/


        mywindow.document.querySelector("#nrecibox").value = printnrecibox;
        mywindow.document.querySelector("#fecha").value = printfecha;
        mywindow.document.querySelector("#cantidadxx").value = printcantidadxx;
        mywindow.document.querySelector("#motivo").value = printmotivo;
        mywindow.document.querySelector("#byfrom").value = printbyfrom;
        
        mywindow.print();
    }

    const verdespose = async (e) => {
        const id = e.dataset.iddespose;

        const query = `
            select ds.* from despose ds
            where ds.id = ${id}
            `;
        let data = await get_data_dynamic(query);
        data = data[0];

        if (data.tipo === "ingreso" || data.tipo === "ingresocaja") {

            guardardesposeingreso.style.display = "none";
            imprimirdesposeingreso.style.display = "";

            nreciboxingreso.disabled = true;
            fechaingreso.disabled = true;
            cantidadxxingreso.disabled = true;
            byfromingreso.disabled = true;
            
            clienteingreso.disabled = true;
            personalingreso.disabled = true;

            nreciboxingreso.value = data.nrorecibo;
            fechaingreso.value = data.fecha;
            cantidadxxingreso.value = data.cantidad;
            byfromingreso.value = data.por;            
            $('#clienteingreso').val(data.codigocliente).trigger('change');
            $('#personalingreso').val(data.personal).trigger('change');

            $("#mdesposeingreso").modal();
        } else if (data.tipo === "despose") { 

            guardardespose.style.display = "none";
            imprimirdespose.style.display = "";

            inputfechasueldo.closest(".divparent").style.display = "none"
            cantidadxx.disabled = true;
            byfrom.disabled = true;
            motivo.disabled = true;
            personal.disabled = true;

            nrecibox.value = data.nrorecibo;
            fecha.value = data.fecha;
            cantidadxx.value = data.cantidad;
            byfrom.value = data.por; 
            $('#personal').val(data.personal).trigger('change');
            motivo.value = data.motivo;
            
            cuentabancaria.closest(".divparent").style.display = "none";

            $("#mdespose").modal()
        }
    }

    const proccessIngresosEfectivo = async id => {
        const query = `
            SELECT 
                v.jsonpagos, v.fecha_emision
            FROM ventas v
            WHERE 
                v.sucursal = ${id}`;

        const da = await get_data_dynamic(query);
        const res = {
            datatotble: [],
            total: 0
        }
        const data = {}

        da.forEach(iii => {
            const arraypagos = JSON.parse(iii.jsonpagos);
            arraypagos.forEach(ixx => {
                if (ixx.tipopago == "efectivo") {
                    if (!data[iii.fecha_emision])
                        data[iii.fecha_emision] = 0
                    data[iii.fecha_emision] += ixx.montoextra ? parseFloat(ixx.montoextra) : 0
                    res.total += parseFloat(ixx.montoextra)
                }
            })
        })
        for (const [key, value] of Object.entries(data))
            res.datatotble.push({
                fecha: key,
                total: value.toFixed(2),
                despose: '',
                motivo: "Ventas del Dia"
            })
        return res
    }
    const initTable = async () => { //ESTA ES UNA FUNCION QUE TE CARGA LA TABLA,
        //modifica la query, ve como lo ordenasya dejalo asi eso es tdo mañana lo veo con ochoa grascias

        let query = "";
        if (suc == 1)
            query = `
            select 
                s.cod_sucursal, s.nombre_sucursal,
                sum(Case When d.tipo = 'ingresocaja'  or d.tipo = 'ingreso' Then d.cantidad Else 0 End) ingreso,
                sum(Case When d.tipo = 'despose' Then d.cantidad Else 0 End) egreso
            from sucursal s 
            left join despose d on d.sucursal = s.cod_sucursal and d.estado <> 'EN ESPERA'
            where (s.estado = 1 ) 
            group by s.cod_sucursal
        `;
        else
            query = `
            select 
                s.cod_sucursal, s.nombre_sucursal,
                sum(Case When d.tipo = 'ingresocaja' or d.tipo = 'ingreso'  Then d.cantidad Else 0 End) ingreso,
                sum(Case When d.tipo = 'despose' Then d.cantidad Else 0 End) egreso
            from sucursal s 
            left join despose d on d.sucursal = s.cod_sucursal 
            where s.estado = 1 and s.cod_sucursal= ${suc} 
            group by s.cod_sucursal
        `;

        let data = await get_data_dynamic(query);

        for (let i = 0; i < data.length; i++) {
            const x = data[i];
            if (x.cod_sucursal != 11) {
                const rr = await proccessIngresosEfectivo(x.cod_sucursal).then(r => r);
                const ingreso = rr.total  + parseFloat(x.ingreso || 0);
                data[i] = {
                    ...x,
                    ingreso: ingreso.toFixed(2),
                    saldo: (ingreso - x.egreso).toFixed(2)
                }
            } else {
                data[i] = {
                    ...x,
                    ingreso: parseFloat(x.ingreso).toFixed(2),
                    saldo: (x.ingreso - x.egreso).toFixed(2)
                }
            }
        }
        $('#maintable').DataTable({
            data: data,
            ordering: false,

            destroy: true,
            columns: [
                {
                    title: 'Sucursal',
                    render: function(data, type, row) {
                        const nn = row.nombre_sucursal.replace('"', '').replace("'", "");
                        return `<a href="#" onclick='getdetail(${parseInt(row.cod_sucursal)}, "${nn}")'>${row.nombre_sucursal}</a>`
                    }
                },
                {
                    title: 'INGRESOS',
                    data: 'ingreso',
                    className: 'dt-body-right'
                },
                {
                    title: 'EGRESOS',
                    data: 'egreso',
                    className: 'dt-body-right'
                },
                {
                    title: 'SALDO',
                    data: 'saldo',
                    className: 'dt-body-right'
                }
            ]
        });
        document.querySelector("#maintable tbody tr:last-child").style.fontWeight = "bold"

    }
</script>