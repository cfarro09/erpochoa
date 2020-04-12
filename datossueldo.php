<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Gestion AFP/ONP";
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
<button class="btn btn-secondary" style="display: none" onclick="agregaroperation()">Agregar</button>
<table id="maintable" class="display table table-bordered" width="100%"></table>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <form id="formoperation">
            <div class="modal-content m-auto">
                <div class="modal-header">
                    <h2 class="modal-title" id="titleoperation">Cobro cheque</h2>
                </div>
                <input type="hidden" id="idoperation">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Nombre AFP</label>
                                    <select disabled id="nombreafp" required class="form-control">
                                        <option value="">Selecciona</option>
                                        <option value="PRIMA">PRIMA</option>
                                        <option value="INTEGRA">INTEGRA</option>
                                        <option value="PROFUTURO">PROFUTURO</option>
                                        <option value="HABITAT">HABITAT</option>
                                        <option value="ONP">ONP</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Aporte</label>
                                    <input type="number" step="any" required class="form-control" id="aporte">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Comision</label>
                                    <input type="number" step="any" required class="form-control" id="comision">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Prima</label>
                                    <input type="number" step="any" required class="form-control" id="prima">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Essalud</label>
                                    <input type="number" step="any" required class="form-control" id="essalud">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="modal_close btn btn-success">Guardar</button>
                    <button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
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
        initTable();
        formoperation.addEventListener("submit", guardaroperation)
    });

    const agregaroperation = () => {
        formoperation.reset();
        idoperation.value = "0";
        $("#moperation").modal();
        nombreafp.value = ""
        titleoperation.textContent = "Registrar datos de sueldo."
    }
    const editar = async (id) => {
        $("#moperation").modal();
        titleoperation.textContent = "Editar datos de sueldo."
        idoperation.value = id;

        let res = await get_data_dynamic(`select * from datos_sueldo where id = ${id}`);

        
        nombreafp.value = res[0].nombre;
        aporte.value = res[0].aporte;
        comision.value = res[0].comision;
        prima.value = res[0].prima;
        essalud.value = res[0].essalud;
    }
    const guardaroperation = async e => {
        e.preventDefault();
        let query = "";
        if(idoperation.value != "0"){
            query = `
                update datos_sueldo set
                    aporte = ${aporte.value},
                    comision = ${comision.value},
                    prima = ${prima.value},
                    essalud = ${essalud.value}
                where id = ${idoperation.value}
                `;
        }
        
        const res = await ff_dynamic(query);
        if(res.succes){
            alert("Registro Completo")
            $("#moperation").modal("hide");
            initTable();
        }else{
            alert(res.msg)
        }
    }
    const initTable = async () => {
        const query = `
            select id, nombre, aporte, comision, prima, essalud from datos_sueldo where nombre <> 'essalud'
        `;
        let data = await get_data_dynamic(query);

        $('#maintable').DataTable({
            data,
            destroy: true,
            columns: [
                {
                    title: 'nombre',
                    data: 'nombre',
                },
                {
                    title: 'aporte',
                    data: 'aporte',
                },
                {
                    title: 'comision',
                    data: 'comision',
                },
                {
                    title: 'prima',
                    data: 'prima',
                },
                {
                    title: 'essalud',
                    data: 'essalud',
                },
                {
                    title: 'Acciones',
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-primary" onclick='editar(${row.id})'><i class="glyphicon glyphicon-edit"></i></button>
                        `;
                    }
                },

            ]
        });
    }
</script>