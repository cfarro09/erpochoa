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
<table id="maintable" class="display" width="100%"></table>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <form id="formoperacion" action="">
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
                    <button type="submit" class="modal_close btn btn-success">Guardar</button>
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
    $(function() {
        initTable()
    });
    const cobrarcheque = (id, monto) => {
        montoextra.value = monto;
        $("#moperation").modal()
    }
    function addpaylocal(){
        if(formpago.value == "unico")
            divparentpayextra.style.display = "none";
        addPayExtraG(containerpayextraG)
    }
    function changetypepagoG(e) {

        getSelectorAll(".montoextra").forEach(x => x.disabled = formpago.value == "unico" ? true : false)
        

        e.closest(".containerx").querySelectorAll(".inputxxx").forEach(ix => ix.style.display = "none");
        e.closest(".containerx").querySelectorAll("." + e.value).forEach(ix => ix.style.display = "");
        getSelector(".montoextra").value = formpago.value == "unico" ? montoextra.value : 0;
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
    const initTable = async () => {
        const query = `
        SELECT v.codigoventas, v.tipocomprobante,v.jsonpagos, v.total, v.codigocomprobante, c.cedula, c.nombre, c.paterno, j.ruc, j.razonsocial FROM ventas v left join cnatural c on c.codigoclienten=v.codigoclienten left join cjuridico j on j.codigoclientej=v.codigoclientej where v.jsonpagos like "%cheque%" or v.abonoproveedor like "%cheque%"`
        //     WHERE r.fecha_registro BETWEEN '${fecha_inicio.value}' and '${fecha_fin.value}'
        // `;
        let data = await get_data_dynamic(query);
        const arrayxx = [];
        data = data.forEach(x => {
            const list = JSON.parse(x.jsonpagos);
            list.filter(o => o.tipopago == "cheque").forEach(y => {
                const dateemited = new Date(y.fechaextra);
                const current = new Date();
                const days = (current.getTime() - current.getTime()) / (1000 * 3600 * 24);
                arrayxx.push({
                    ...x,
                    ["daysto"]: days,
                    ["tipopago"]: y.tipopago,
                    ["fecha_emision"]: y.fechaextra,
                    ["montoextra"]: y.montoextra,
                    ["documento"]: x.cedula ? x.cedula : x.ruc,
                    ["identificacion"]: x.cedula ? `${x.paterno} ${x.nombre}` : x.razonsocial
                })
            })
        })
        $('#maintable').DataTable({
            data: arrayxx,
            destroy: true,
            columns: [{
                    title: 'FECHAEMISION',
                    data: 'fecha_emision'
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
                    defaultContent: "CARTERA"
                },
                {
                    title: 'ACCIONES',
                    sortable: false,
                    render: function(data, type, row, meta) {
                        return `<button class="btn btn-primary" onclick="cobrarcheque(${row.codigoventas},${row.montoextra})">Cobrar Cheque</button>`;
                    }
                },
            ]
        });
    }
</script>