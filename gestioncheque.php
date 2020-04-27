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

$codpersonal = $_SESSION['kt_codigopersonal'];
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
<table id="maintable" class="display" width="100%"></table>

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
    let codventas = 0;
    let chequeselected = {};
    let indexselected = -1;
    let tipo = "";
    $(function() {
        initTable();
    });
    const cobrarcheque = (ttipo, id, monto, indexcheque, json) => {
        
        codventas = id;
        tipo = ttipo;
        montoextra.value = monto;
        chequeselected = JSON.parse(json);
        indexselected = indexcheque;
        $("#moperation").modal();
    }

    function addpaylocal() {
        if (formpago.value == "unico")
            divparentpayextra.style.display = "none";
        addPayExtraG(containerpayextraG, "efectivo", "depositobancario");
    }

    function changetypepagoG(e) {
        getSelectorAll(".montoextra").forEach(x => x.disabled = formpago.value == "unico" ? true : false)
        e.closest(".containerx").querySelectorAll(".inputxxx").forEach(ix => ix.style.display = "none");
        e.closest(".containerx").querySelectorAll("." + e.value).forEach(ix => ix.style.display = "");
        if (formpago.value == "unico") {
            getSelector(".montoextra").value = montoextra.value;
        }
    }
    const guardar = () => {

        const data = {
            header: "",
            detalle: []
        }

        let totalpagando = 0;
        let totalx = parseFloat(montoextra.value);
        let errorxxx = "";
        const pagosextras = [];
        

        getSelectorAll(".containerx").forEach(ix => {
            const pay = {
                bancoextra: ix.querySelector(".bancoextra").value,
                montoextra: ix.querySelector(".montoextra").value ? parseFloat(ix.querySelector(".montoextra").value) : 0,
                numero: ix.querySelector(".numero").value,
                cuentacorriente: ix.querySelector(".cuentacorriente").value,
                numerooperacion: ix.querySelector(".numerooperacion").value,
                fechaextra: ix.querySelector(".fechaextra").value,
                cuentaabonado: ix.querySelector(".cuentaabonado").value,
                tipopago: ix.querySelector(".tipopago").value,
                fechaxxx: new Date(new Date().setHours(10)).toISOString().substring(0, 10)
            }

            if (pay.tipopago == "depositobancario" && (!pay.montoextra || !pay.numerooperacion || !pay.fechaextra || !pay.cuentaabonado)) {
                errorxxx = "Llena todos los datos de deposito bancario";
                return;
            } else if (pay.tipopago == "cheque" && (!pay.bancoextra || !pay.montoextra || !pay.numero || !pay.cuentacorriente)) {
                errorxxx = "Llena todos los datos de cheque";
                return;
            } else if ((pay.tipopago == "tarjetacredito" || pay.tipopago == "tarjetadebito") && (!pay.bancoextra || !pay.montoextra || !pay.numero)) {
                errorxxx = "Llena todos los datos de " + pay.tipopago;
                return;
            }

            totalpagando += pay.montoextra;
            pagosextras.push(pay)
        })
        if (errorxxx) {
            alert(errorxxx);
            return;
        }
        debugger
        if (totalx != totalpagando) {
            alert("Los montos no coinciden");
            return;
        }
        pagosextras.filter(y => y.tipopago == "depositobancario").forEach(xx => {
            data.detalle.push(
                `
                insert into cuenta_mov (id_cuenta, fecha_trans, tipo_mov, detalle, monto, saldo) VALUES (${xx.cuentaabonado}, '${xx.fechaextra}', 'CHEQUE N° ${xx.numero}', 'DEPOSITO EN CHEQUE', '${xx.montoextra}', (select cm.saldo from cuenta_mov cm where cm.id_cuenta = ${xx.cuentaabonado} order by cm.id_cuenta_mov desc limit 1) + ${xx.montoextra})
                `
            );
        })
        pagosextras.filter(x => x.tipopago == "efectivo").forEach(x => {
                const query1x = `
                insert into despose 
                    (nrorecibo, cantidad, fecha, por, personal, sucursal, tipo)
                values
                    ((select \`value\` from propiedades where \`key\` = 'ningresos') + 1, ${x.montoextra}, NOW(), 'cobro cheque', <?= $codpersonal ?>, 11, 'ingreso')
                `;
                data.detalle.push(query1x);
        })
        
        chequeselected = chequeselected.map(x => {
            let ix = 0;
            if (x.tipopago == "cheque" && ix == indexselected) {
                x.estado  = "COBRADO";
                x.cobrocheque = pagosextras;
                ix++;
            };
            return x;
        });
        
        data.header = `
            update ventas set ${tipo} = '${JSON.stringify(chequeselected)}' where codigoventas = ${codventas}
            `;
        const formData = new FormData();
        formData.append("json", JSON.stringify(data))

        fetch(`setVenta.php`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res.success) {
                    alert("registro completo!")
                    location.reload()
                }
            });
    }

    function removecontainerpay(e) {
        e.closest(".containerx").remove()
        calcularmontopagado();
    }

    function changemodopago(e) {
        if (e.value == "unico") {
            getSelector(".montoextra").value = montoextra.value;
            divparentpayextra.style.display = "none";
            let ii = 0;
            getSelectorAll(".containerx").forEach(ix => {
                if (ii != 0) {
                    ix.remove();
                }
                ii++;
            });
            getSelectorAll(".montoextra").forEach(x => x.disabled = true)
        } else {
            divparentpayextra.style.display = "";
            getSelectorAll(".montoextra").forEach(x => x.disabled = false)
        }
    }
    const calcularmontopagado = e => {
        const importe = parseFloat(montoextra.value);
        let total = 0;
        getSelectorAll(".montoextra").forEach(x => {
            total += parseFloat(x.value);
        })
        if (total > importe) {
            if (e)
                e.value = 0;
        }

    }
    const initTable = async () => {
        const query = `
        SELECT v.codigoventas, v.tipocomprobante,v.jsonpagos, v.abonoproveedor, v.total, v.codigocomprobante, c.cedula, c.nombre, c.paterno, j.ruc, j.razonsocial 
        FROM ventas v 
        left join cnatural c on c.codigoclienten=v.codigoclienten 
        left join cjuridico j on j.codigoclientej=v.codigoclientej 
        where v.jsonpagos like "%cheque%" or v.abonoproveedor like "%cheque%"
        group by v.codigoventas
        `
        //     WHERE r.fecha_registro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
        // `;
        let data = await get_data_dynamic(query);
        const arrayxx = [];
        data = data.forEach(x => {
            

            let indexcheque = 0;

            if(x.abonoproveedor){
                const list = JSON.parse(x.abonoproveedor);
                list.filter(o => o.tipopago == "cheque").forEach(y => {
                    const dateemited = new Date(y.fechaextra);
                    const current = new Date();
                    const days = parseInt((current.getTime() - dateemited.getTime()) / (1000 * 3600 * 24));
                    debugger;
                    arrayxx.push({
                        ...x,
                        ["estado"]: y.estado || "",
                        ["daysto"]: days,
                        ["tipopago"]: y.tipopago,
                        ["indexcheque"]: indexcheque,
                        ["fecha_emision"]: y.fechaextra,
                        ["tipo"]: "abonoproveedor",
                        ["jsonformated"]: x.abonoproveedor,
                        ["montoextra"]: y.montoextra,
                        ["documento"]: x.cedula ? x.cedula : x.ruc,
                        ["identificacion"]: x.cedula ? `${x.paterno} ${x.nombre}` : x.razonsocial
                    });
                    indexcheque++;
                })    
            }
            
            if(x.jsonpagos){
                const list = JSON.parse(x.jsonpagos);
                list.filter(o => o.tipopago == "cheque").forEach(y => {
                    const dateemited = new Date(y.fechaextra);
                    const current = new Date();
                    const days = parseInt((current.getTime() - dateemited.getTime()) / (1000 * 3600 * 24));
                    arrayxx.push({
                        ...x,
                        ["estado"]: y.estado || "",
                        ["daysto"]: days,
                        ["tipopago"]: y.tipopago,
                        ["indexcheque"]: indexcheque,
                        ["fecha_emision"]: y.fechaextra,
                        ["tipo"]: "jsonpagos",
                        ["jsonformated"]: x.jsonpagos,
                        ["montoextra"]: y.montoextra,
                        ["documento"]: x.cedula ? x.cedula : x.ruc,
                        ["identificacion"]: x.cedula ? `${x.paterno} ${x.nombre}` : x.razonsocial
                    });
                    indexcheque++;
                })
            }
        })
        $('#maintable').DataTable({
            data: arrayxx,
            destroy: true,
            columns: [
                {
                    title: 'FECHAEMISION',
                    data: 'fecha_emision'
                },
                {
                    title: 'codigoventas',
                    data: 'codigoventas'
                },
                {
                    title: 'DAYSTO',
                    data: 'daysto'
                },
                {
                    title: 'tipopago',
                    data: 'tipopago'
                },
                {
                    title: 'DOCUMENTO',
                    data: 'documento'
                },
                {
                    title: 'IDENTIFICACION',
                    data: 'identificacion'
                },
                {
                    title: 'TIPOCOMPROBANTE',
                    data: 'tipocomprobante'
                },
                {
                    title: 'CODIGOCOMPROBANTE',
                    data: 'codigocomprobante'
                },
                {
                    title: 'MONTOEXTRA',
                    data: 'montoextra'
                },
                {
                    title: 'ESTADO',
                    data: 'estado'
                },
                {
                    title: 'ACCIONES',
                    render: function(data, type, row, meta) {
                        console.log(row);
                        
                        if(row.estado == "CARTERA")
                            return `<button class="btn btn-primary" onclick='cobrarcheque("${row.tipo}", ${parseInt(row.codigoventas)}, ${row.montoextra}, ${row.indexcheque}, ` + '`' + row.jsonformated + "`)'>Cobrar Cheque</button>";
                        else
                            return '';
                    }
                },
            ]
        });
    }
</script>