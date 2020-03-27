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
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h2 class="modal-title" style="display: inline-block; margin-right: 10px" id="moperationtitle">Detalle Ingresos</h2>
                <button class="btn btn-primary" onclick="dispose()">Despose</button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12 col-md-12" style="margin-bottom: 200px">
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
    <div class="modal-dialog" role="document" style="width: 700px">

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

<?php
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    $(function() {
        initTable()
        onloadPersonal()
        // onloadSucursales()
        onloadCuentas()
        formdispose.addEventListener("submit", guardardespse)
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
    }
    const guardardespse = async e => {
        e.preventDefault();
        if (personal.value) {

            if (motivo.value == "Deposito en cuenta") { //MIRA GORDITO, ESTA ES  UNA CONDICION QME DIJSITE Q CUANDO SE DEPOSITO EN CUENTA, SE AGREGUE A SU CUENTA
                const querydepbancario = `insert into cuenta_mov (id_cuenta, fecha_trans, tipo_mov, detalle, monto, saldo) VALUES (${cuentabancaria.value}, '${fecha.value}', 'DEPOSITO EFECTIVO SUC ${namesucursal.value}', 'DEPOSITO EFECTIVO SUC ${namesucursal.value}', ${cantidadxx.value}, 
                (select cm.saldo + ${cantidadxx.value} from cuenta_mov cm where cm.id_cuenta = ${cuentabancaria.value} order by cm.id_cuenta_mov desc limit 1))`
                ff_dynamic(querydepbancario);
            }else if(motivo.value == "cajatumbes"){ //ESTA ES OTRA CONDICION SI ES CAJA TUMBES LE INGRESA A CAJA TUMBES, HASTA AHI ENTENDISTE?si
                const query = `
                insert into despose 
                    (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo, motivo) 
                values
                    ('${nrecibox.value}', ${cantidadxx.value}, '${fecha.value}', '${byfrom.value}', ${personal.value}, 11, 'ingresocaja', '${motivo.value}')`
                let res = await ff_dynamic(query);    
            }
            //ENTONCES TODO LO Q SELECCIONE SON CONDICIOENS QUE PUEDE PASAR SI EL MOTIVO ES DEPOSITO EN CUENTA O CAJA TUMBES

            const query = `
            insert into despose 
                (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo, motivo) 
            values
                ('${nrecibox.value}', ${cantidadxx.value}, '${fecha.value}', '${byfrom.value}', ${personal.value}, ${msucursal.value}, 'despose', '${motivo.value}')`
            let res = await ff_dynamic(query);
            alert("DATOS GUARDADOS CORRECTAMENTE");
            //SI SALIO LO VISTE O NO
            //YA MUY /BIEN, PERO NO SALE

            //AHORA HAY UNA FUNCION Q SE LLAMA INIT TABLE Q SE ENCARGA DE INICIAR LA TABLA
            //ahora si ya sabes q hay una funcion q carga la tabla, q harias?, en el boton cerrar llamo la funcion ini tabler
            //NO
            //PORQUE NO CARGAS CUANDO SE GUARDE, OSEA , pruebala no actualizo
            
            await initTable()

            await getdetail(msucursal.value, namesucursal.value)
            $("#mdespose").modal("hide")

            //LO ULTIMO Q SELECCIONE ES LO Q SIEMPRE SE VA A EJECUTAR SEA EL MOTIVO Q SEA PORQUE NO HAY NINGUNA CONDICION
            //ENTENDISTE?si//
            //AHORA DIME DONDE PONDRIAS EL ALERT P
        } else {
            alert("debe seleccionar personal")
        }
    }
    // const getdetail = async (id, name) => {
    //     msucursal.value = id
    //     namesucursal.value = name
    //     $("#moperation").modal();
    //     moperationtitle.textContent = "EGRESOS CAJA " + name

    //     const query1 = `
    //         SELECT 
    //             fecha, cantidad, tipo, motivo, nrorecibo
    //         FROM despose
    //         WHERE 
    //             sucursal = ${id}
    //         ORDER by fecha asc`;

    //     let despose = await get_data_dynamic(query1);

    //     setConsolidado(despose)
    // }
    const getdetail = async (id, name) => {
        msucursal.value = id
        namesucursal.value = name
        $("#moperation").modal();
        moperationtitle.textContent = "EGRESO CAJA - " + name
        let despose = [];
        //la columna se llama nrecibo q tienes q hacer
        if(id != 11){
            const query1 = `
                SELECT 
                    fecha, nrorecibo, cantidad as despose, '' as total, motivo
                FROM despose
                WHERE 
                    sucursal = ${id} and (tipo = 'despose')`;

        
            despose = await get_data_dynamic(query1);
        }else{
            const query1 = `
                SELECT 
                    fecha, nrorecibo, tipo, cantidad as total, motivo
                FROM despose
                WHERE 
                    sucursal = ${id} and (tipo = 'despose' or tipo = 'ingresocaja')`;

            despose = await get_data_dynamic(query1);
        }
        //ya bacan ya en la variable despose te viene con nrorecibo, pero ahora hay q procesarlo en la tabla, veamos q hace la siguiente funcion
        setConsolidado(id, despose)

    }
    const onloadPersonal = async () => {
        const res = await get_data_dynamic("SELECT codigopersonal, concat(paterno, ' ', materno, ' ', nombre) as fullname FROM personal WHERE estado = 0");
        res.unshift({
            codigopersonal: "",
            fullname: "Seleccionar"
        })
        cargarselect2("#personal", res, "codigopersonal", "fullname")
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
        if(id != 11){//OK LO Q ESTA DENTOR DE ESA CONDICION ES DE LAS SCUURSALES Q NO ES LA CAJA TUMBES

            const rr = await proccessIngresosEfectivo(id) //ESTA FUNCION TE TRAE LOS IGNRESOS, OK PERO LOS INGRESOS TIENEN NRORECIBO O NO?siiil 
            //METETE UN MANASO POR GIL, DE DONDE SACAS LOS INGRESOS, SI NO ES DEL EFECTIVO aya lso egresos son, AJAM ALMENOS Q SE QUEIRA SACAR EL NRO COMPROBANTE DE LA VENTA PORQUE EL EFECTIVO VIENE DE LA VENTA, ENTONCES
            //CHECA Q ESA FUNCION RECIBE EL ID Y EL DES , EL DES ES EL ARRAY DE Q TE DEVUELVE EL EQURY
            const datatotble = rr.datatotble; //EL RR.DATATOBLE ES UN ARRAY PS AHI ESTA EL ARRAY DE IGNRESOS, CAPTASTE?ALGO 
            
            qwer = [...datatotble, ...des]; //MIRA ESTE CODIGO JUNTA EL DATATOTBLE Q ES EL ARRAY DE LOS IGNRESOS(NO TIENEN NRORECIBO) Y EL DES SI, ENTONCES NO VA  FUNCAR
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
            //dime  q necesitas, el numer de recibo, en donde en la tabla principal o delen la segunda ok esa tabla se llena en getdetail
            
            qwer = qwer.map(x => { //PEROOO EN ESTA FUNCION TE PERMITE RECORRER TODOYa AHI PONLE
                const despose = x.despose ? parseFloat(x.despose) : 0
                const total = x.total ? parseFloat(x.total) : 0
                saldo = saldo + total - despose
                x.saldo = saldo.toFixed(2)
                x.nrorecibo = x.nrorecibo ?? ""; //LO Q HACE ESOS ?? ES Q SI X.NROECIBO ES NULL LE PONGA "", EN Q CASO EL NRORECIBO SERA NULL?RESPONDEcuando es ingreso
                return x
            })
            //ESTO PASA CUANDO ES UNA SUCURSAL DIFERENTE A LA CAJA TUMBES, AHORA TU HAS CUANDO ES CAJA TUMBES
        }else{
            let saldo = 0;
            //Q HARIAS AQUI4 nada pq no tengo el nro d recibo ponlo
            des.forEach(x => {

                //CUANDO ES IGNRESO DE UN EGRESO SE GUARDA EN LA TABLA DESPOSE PERO COMO TIPO INGRESOCAJA
                //ENTONCES TENGO 2 TIPOS, INGRESO CAJA Q ES INGRESO, Y DESPOSE Q ES EGRESOok
                //SI ENTENDISTE? ALGUNA OTRA PREGUNTA ALUMNO BOBIS has esto producto list
                if(x.tipo == "ingresocaja"){
                    saldo += parseFloat(x.total)
                    qwer.push({
                        ...x,
                        despose: 0,
                        saldo: saldo.toFixed(2), //NO SIRVESPARAOGRAMAR
                        nrorecibo: x.nrorecibo //cuando es ingreso nunca va nada entonces lo seteo vacio, tienes q inicializar las propiedades si nose rompe como tu culo

                    })
                }else{
                    saldo -= parseFloat(x.total)
                    qwer.push({
                        ...x,
                        total: 0,
                        despose: x.total,
                        nrorecibo: x.nrorecibo, //porqie pones lo mismo en los dos, //porque ambos tienen nrorecibo
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
                motivo: ""
            })
        return res
    }
    const initTable = async () => { //ESTA ES UNA FUNCION QUE TE CARGA LA TABLA,
        //modifica la query, ve como lo ordenasya dejalo asi eso es tdo mañana lo veo con ochoa grascias
        const query = `
            select 
                s.cod_sucursal, s.nombre_sucursal,
                sum(Case When d.tipo = 'ingresocaja' Then d.cantidad Else 0 End) ingreso,
                sum(Case When d.tipo = 'despose' Then d.cantidad Else 0 End) egreso
            from sucursal s 
            left join despose d on d.sucursal = s.cod_sucursal 
            where s.estado = 1 OR s.estado = 6969
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
    // const setConsolidado = (res) => {
    //     let saldo = 0;
    //     res = res.map(x => {

    //         if (x.tipo == "despose") {
    //             saldo += parseFloat(x.cantidad)
    //             return {
    //                 fecha: x.fecha,
    //                 ingreso: x.cantidad,
    //                 nrorecibo: x.nrorecibo,
    //                 despose: '',
    //                 saldo: saldo.toFixed(2),
    //                 motivo: x.motivo
    //             }
    //         } else {
    //             saldo = saldo - parseFloat(x.cantidad)
    //             return {
    //                 fecha: x.fecha,
    //                 ingreso: '',
    //                 despose: x.cantidad,
    //                 saldo: saldo.toFixed(2),
    //                 motivo: x.motivo,
    //                 nrorecibo: x.nrorecibo
    //             }
    //         }
    //     })

    //     $('#ventastable').DataTable({
    //         data: res,
    //         destroy: true,
    //         buttons: [{
    //                 extend: 'print',
    //                 className: 'btn dark btn-outline'
    //             },
    //             {
    //                 extend: 'copy',
    //                 className: 'btn red btn-outline'
    //             },
    //             {
    //                 extend: 'pdf',
    //                 className: 'btn green btn-outline'
    //             },
    //             {
    //                 extend: 'excel',
    //                 className: 'btn yellow btn-outline '
    //             },
    //             {
    //                 extend: 'csv',
    //                 className: 'btn purple btn-outline '
    //             },
    //             {
    //                 extend: 'colvis',
    //                 className: 'btn dark btn-outline',
    //                 text: 'Columns'
    //             }
    //         ],
    //         dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
    //         columns: [{
    //                 title: 'fecha',
    //                 data: 'fecha'
    //             },
    //             {
    //                 title: 'nrorecibo',
    //                 data: 'nrorecibo',
    //                 className: 'dt-body-left'
    //             },
    //             {
    //                 title: 'motivo',
    //                 data: 'motivo',
    //                 className: 'dt-body-right'
    //             },
    //             {
    //                 title: 'ingreso',
    //                 data: 'ingreso',
    //                 className: 'dt-body-right'
    //             },
    //             {
    //                 title: 'despose',
    //                 data: 'despose',
    //                 className: 'dt-body-right'
    //             },
    //             {
    //                 title: 'saldo',
    //                 data: 'saldo',
    //                 className: 'dt-body-right'
    //             },

    //         ]
    //     });


    // }
</script>