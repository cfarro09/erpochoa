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
                <button class="btn btn-primary" id="btndispose" onclick="dispose()">Egreso</button>
                <button class="btn btn-primary" id="btndisposeingreso" onclick="disposeingreso()">Ingreso</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12 col-md-12" >
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

<div class="modal fade" id="mdespose" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">

        <form id="formdispose">
            <input type="hidden" id="msucursal">
            <input type="hidden" id="namesucursal">
            <div class="modal-content m-auto">
                <div class="modal-header">
                    <h2 class="modal-title" id="desposetitle">EMPOZE EGRESO</h2>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N° Recibo</label>
                                            <input type="text" id="nrecibox" required class="form-control" />
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
                                                <option value="cajatumbes">Caja Principal Tumbes</option>
                                                <option value="Deposito en cuenta">Deposito en cuenta</option>
                                                <option value="Pago Servicios">Pago Servicios</option>
                                                <option value="Sueldo">Sueldo</option>
                                                <option value="Viatico">Viatico</option>
                                                <option value="Vacaciones">Vacaciones</option>    
                                            </select>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Fecha</label>
                                            <input type="text" required name="fecha" autocomplete="off" id="fecha" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Por</label>
                                            <textarea class="form-control" id="byfrom"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Personal</label>
                                            <select id="personal"></select>
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



<div class="modal fade" id="mdesposeingreso" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 700px">

        <form id="formdisposeingreso">
            <input type="hidden" id="msucursal">
            <input type="hidden" id="namesucursal">
            <div class="modal-content m-auto">
                <div class="modal-header">
                    <h2 class="modal-title" id="desposetitleingreso">EMPOZE INGRESO</h2>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">N° Recibo</label>
                                            <input type="text" id="nreciboxingreso" required class="form-control" />
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Fecha</label>
                                            <input type="text" required name="fecha" autocomplete="off" id="fechaingreso" class="form-control form-control-inline input-medium date-picker tooltips" data-date-format="yyyy-mm-dd" data-placement="top" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Por</label>
                                            <textarea class="form-control" id="byfromingreso"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Personal</label>
                                            <select id="personalingreso"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" >Guardar</button>
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
    $(function() {
        initTable()
        onloadPersonal()
        onloadCuentas()
        if(suc == 1 ){
            btndispose.style.display = "none"
            btndisposeingreso.style.display = "none"


        }else{
            btndispose.style.display = ""
            btndisposeingreso.style.display = ""
        }
        formdispose.addEventListener("submit", guardardespse)
        formdisposeingreso.addEventListener("submit", guardardespseingreso)
    });
    const changeMotivo = e => {
        cuentabancaria.closest(".divparent").style.display = e.target.value == "Deposito en cuenta" ? "" : "none"

    }
    motivo.onchange = changeMotivo;

    const dispose = () => {
        formdispose.reset()
        personal.value = 0
        cuentabancaria.closest(".divparent").style.display = "none"
        $("#mdespose").modal()
        $('#personal').val('').trigger('change');


    }
    const disposeingreso = () => {
        formdisposeingreso.reset()
        personalingreso.value = 0
        $("#mdesposeingreso").modal()
        $('#personalingreso').val('').trigger('change');
    }
    
    const guardardespseingreso = async e => {
        debugger
        e.preventDefault();
        if(personalingreso.value){
            const query = `
            insert into despose 
                (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo) 
            values
                ('${nreciboxingreso.value}', ${cantidadxxingreso.value}, '${fechaingreso.value}', '${byfromingreso.value}', ${personalingreso.value}, ${msucursal.value}, 'ingreso')`
            let res = await ff_dynamic(query);
            alert("DATOS GUARDADOS CORRECTAMENTE");
            $("#mdesposeingreso").modal("hide")
            await initTable()
            await getdetail(msucursal.value, namesucursal.value)
        }else{
            alert("debe seleccionar personal")
        }
    }
    const guardardespse = async e => {
        e.preventDefault();
        // ll_dynamic
        const dataxx = {header: "", detalle: []}
        if (personal.value) {

            if (motivo.value == "Deposito en cuenta") {
                
                const querydepbancario = `insert into cuenta_mov (id_cuenta, fecha_trans, tipo_mov, detalle, monto, saldo) VALUES (${cuentabancaria.value}, '${fecha.value}', 'DEPOSITO EFECTIVO SUC ${namesucursal.value}', 'DEPOSITO EFECTIVO SUC ${namesucursal.value}', ${cantidadxx.value}, 
                (select cm.saldo + ${cantidadxx.value} from cuenta_mov cm where cm.id_cuenta = ${cuentabancaria.value} order by cm.id_cuenta_mov desc limit 1))`
                
                dataxx.detalle.push(querydepbancario);
            }else if(motivo.value == "cajatumbes"){
                const query = `
                    insert into despose 
                        (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo, motivo, estado, fromdespose) 
                    values
                        ('${nrecibox.value}', ${cantidadxx.value}, '${fecha.value}', '${byfrom.value}', ${personal.value}, 11, 'ingresocaja', 'CAJA TUMBES - ENVIO DE ${namesucursal.value}', 'EN ESPERA', ###ID###)`
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
            await initTable()
            await getdetail(msucursal.value, namesucursal.value)
            $("#mdespose").modal("hide")
        } else {
            alert("debe seleccionar personal")
        }
    }
    const setStatusIngresos = async (id, status, fromdepose) => {
        const dataxx = {header: "", detalle: []}
        dataxx.detalle.push(`update despose set estado = '${status}' where id = ${id}`);
        if(fromdepose)
            dataxx.detalle.push(`update despose set estado = '${status}' where id = ${fromdepose}`);
        let res = await ll_dynamic(dataxx);
        if(res && res.success){
            alert("DATOS GUARDADOS CORRECTAMENTE");
            await initTable()
            await getdetail(msucursal.value, namesucursal.value)
        }else{
            alert("hubo un error")
        }
    }
    const getdetail = async (id, name) => {
        msucursal.value = id
        namesucursal.value = name
        $("#moperation").modal();
        moperationtitle.textContent = "EFECTIVO CAJA - " + name
        let despose = [];
        if(id != 11){
            const query1 = `
                SELECT 
                    id, fecha, nrorecibo, cantidad as despose, '' as total, tipo, CONCAT(motivo, ' - ',estado) as motivo, por, fromdespose
                FROM despose
                WHERE 
                    sucursal = ${id} and (tipo = 'despose' or tipo = 'ingreso')`;

            despose = await get_data_dynamic(query1);
        }else{
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
         if((id == 1 && suc==1) || (id == 11 && suc==1)){
            btndispose.style.display = ""
            btndisposeingreso.style.display = ""
        }
        else
        {
            if(id != 1 && suc!=1){
                btndispose.style.display = ""
                btndisposeingreso.style.display = ""
            }
            else
            {
            btndispose.style.display = "none"
            btndisposeingreso.style.display = "none"
}

        }
        if(id != 11){//OK LO Q ESTA DENTOR DE ESA CONDICION ES DE LAS SCUURSALES Q NO ES LA CAJA TUMBES

            const rr = await proccessIngresosEfectivo(id)
            const datatotble = rr.datatotble; 
            
            des = des.map(x => {
                return {
                    ...x,
                    total: x.tipo == "ingreso" ? x.despose : 0,
                    despose: x.tipo == "ingreso" ? 0 : x.despose,
                    motivo: x.motivo || x.por.substring(0, 30)
                }
            })
            debugger
            qwer = [...datatotble, ...des];
            let saldo = 0;
            qwer.sort(function (a, b) {
                if (a.fecha < b.fecha) {
                    return -1;
                }
                if (b.fecha < a.fecha) {
                    return 1;
                }
                return 0;
            });
            
            qwer = qwer.map(x => { //PEROOO EN ESTA FUNCION TE PERMITE RECORRER TODOYa AHI PONLE
                const despose = x.despose ? parseFloat(x.despose) : 0
                const total = x.total ? parseFloat(x.total) : 0
                saldo = saldo + total - despose
                x.saldo = saldo.toFixed(2)
                x.nrorecibo = x.nrorecibo || ""; 
                return x
            })
        }else{
            let saldo = 0;
            des.forEach(x => {

                if(x.tipo == "ingresocaja"){
                    saldo += parseFloat(x.total)
                    qwer.push({
                        ...x,
                        motivo: `${x.motivo} ${x.estado}`,
                        despose: 0,
                        saldo: saldo.toFixed(2),
                        nrorecibo: x.nrorecibo
                    })
                }else{
                    saldo -= parseFloat(x.total)
                    qwer.push({
                        ...x,
                        total: 0,
                        despose: x.total,
                        nrorecibo: x.nrorecibo,
                        saldo: saldo.toFixed(2)
                    })
                }
            })
        }
        
        $('#ventastable').DataTable({
            data: qwer,
            destroy: true,
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
            columns: [
                {
                    title: 'fecha',
                    data: 'fecha'
                },
                 {
                    title: 'NRO',
                    data: 'nrorecibo'
                },
                {//pruebalo
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
                        if(row.motivo.includes('EN ESPERA') || (row.motivo.includes('ENVIADO') && msucursal.value == 1)){
                            return `
                                <div class="">
                                    <button class="btn btn-success" onclick="setStatusIngresos(${row.id},'ACEPTADO', ${row.fromdespose})">Aceptar</button>
                                    <button class="btn btn-danger" onclick="setStatusIngresos(${row.id},'RECHAZADO', ${row.fromdespose})">Rechazar</button>
                                </div>
                                `
                        }else{
                            return ""
                        }
                        
                    }
                }
            ]
        });


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
        if(suc == 1)
              query = `
            select 
                s.cod_sucursal, s.nombre_sucursal,
                sum(Case When d.tipo = 'ingresocaja' Then d.cantidad Else 0 End) ingreso,
                sum(Case When d.tipo = 'despose' Then d.cantidad Else 0 End) egreso
            from sucursal s 
            left join despose d on d.sucursal = s.cod_sucursal and d.estado <> 'EN ESPERA'
            where (s.estado = 1 or s.estado=6969) 
            group by s.cod_sucursal
        `;
        else
             query = `
            select 
                s.cod_sucursal, s.nombre_sucursal,
                sum(Case When d.tipo = 'ingresocaja' Then d.cantidad Else 0 End) ingreso,
                sum(Case When d.tipo = 'despose' Then d.cantidad Else 0 End) egreso
            from sucursal s 
            left join despose d on d.sucursal = s.cod_sucursal 
            where s.estado = 1 and s.cod_sucursal= ${suc} 
            group by s.cod_sucursal
        `;

        let data = await get_data_dynamic(query);

        for (let i = 0; i < data.length; i++) {
            const x = data[i];
            if(x.cod_sucursal != 11){
                const rr = await proccessIngresosEfectivo(x.cod_sucursal).then(r => r);
                const ingreso = rr.total;
                data[i] = {
                    ...x,
                    ingreso: ingreso.toFixed(2),
                    saldo: (ingreso - x.egreso).toFixed(2)
                }
            }else{
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
            columns: [{
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