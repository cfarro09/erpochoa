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
<style>
    #containerplan {
        overflow-y: auto;
        height: 50vh
    }
</style>

<button class="btn btn-success" data-toggle="modal" data-target="#moperation" onclick="openmodal()" style="margin-bottom: 10px">Agregar Cuenta</button>

<div id="containerplan"></div>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <form id="formoperacion" action="">
                <div class="modal-header">
                    <h2 class="modal-title">Registrar Cuenta</h2>
                </div>
                <input type="hidden" id="codigocontable">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Codigo</label>
                                    <input type="text" required class="form-control" id="cuenta">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Descripción</label>
                                    <input type="text" required class="form-control" id="descripcion">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Padre</label>
                                    <select id="padre"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Subcuenta 1</label>
                                    <select id="subcuenta1"></select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Subcuenta 2</label>
                                    <select id="subcuenta2"></select>
                                </div>
                            </div>
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
        openmodalplan()
    });
    const openmodalplan = async () => {
        const res = await get_data_dynamic("select id, codigo, descripcion, padre, level from plancontable order by codigo asc");
        let parentsresult = res;
        const parents = res;

        containerplan.innerHTML = "";

        parents.forEach(ix => {
            containerplan.innerHTML += gethtml(ix, ix.padre == null ? true : false)
        })

        parents.reverse().filter(ix => ix.padre != null).forEach(ix => {
            const tomove = getSelector(`#plan_${ix.id}`);
            getSelector(`#plan_${ix.id}`).remove()
            
            const listpadres =  getSelectorAll(`#plan_${ix.padre} .hijos .padre`);
            
            if(listpadres.length == 0) {
                getSelector(`#plan_${ix.padre} .hijos`).append(tomove)
            }else{
                const listhijos = [];
                listpadres.forEach(x => {
                    listhijos.push({
                        id: x.id,
                        codigo: x.dataset.codigo
                    })
                })
                listhijos.push({
                    id: tomove.id,
                    codigo: tomove.dataset.codigo
                })
                listhijos.sort(function(a, b) {
                    var textA = a.codigo;
                    var textB = b.codigo;
                    return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
                });
                
                const indexpreview = listhijos.map(ii => ii.id).indexOf(tomove.id)
                getSelector(`#${listhijos[indexpreview + 1].id}`).before(tomove)
            }
        });
    }
    const edit = async (id, level) => {
        codigocontable.value = id;

        const rescombo = await get_data_dynamic(`select id, level, CONCAT(codigo, ' ', descripcion) as descripcion from plancontable where id <> ${id}`);

        cargarselect2("#padre", rescombo.filter(x => x.level <= level), 'id', 'descripcion', ["level"]);
        cargarselect2("#subcuenta1", rescombo, 'id', 'descripcion');
        cargarselect2("#subcuenta2", rescombo, 'id', 'descripcion');

        const res = await get_data_dynamic(`select * from plancontable where id = ${id}`);
        $("#moperation").modal()
        if(res){
            const data = res[0];
            cuenta.value = data.codigo;
            descripcion.value = data.descripcion;
            if(data.padre){
                $("#padre").val(data.padre)
                $('#padre').trigger('change')
            }
            if(data.subcuenta1){
                $("#subcuenta1").val(data.subcuenta1)
                $('#subcuenta1').trigger('change')
            }
            if(data.subcuenta2){
                $("#subcuenta2").val(data.subcuenta2)
                $('#subcuenta2').trigger('change')
            }
        }
    }
    const gethtml = (ix, parent = false) => {
        const ss = parent ? 'font-weight: bold;' : '';
        return `
            <div class="padre" data-codigo="${ix.codigo.toUpperCase()}" id="plan_${ix.id}">
                    <div onclick="edit(${ix.id}, ${ix.level})" style="${ss} margin-bottom: 5px; cursor: pointer">${ix.codigo.toUpperCase()} - ${ix.descripcion.toUpperCase()}</div>
                    <div style="margin-left: 20px" class="hijos"></div>
            </div>`;
    }
    const openmodal = async () => {
        codigocontable.value = 0;
        cuenta.value = "";
        descripcion.value = "";
        const res = await get_data_dynamic("select id, level, CONCAT(codigo, ' ', descripcion) as descripcion from plancontable");
        cargarselect2("#padre", res, 'id', 'descripcion', ["level"]);
        cargarselect2("#subcuenta1", res, 'id', 'descripcion');
        cargarselect2("#subcuenta2", res, 'id', 'descripcion');
    }
    const guardar = e => {
        e.preventDefault();
        const data = {
            header: "",
            detalle: []
        }
        const padrex = padre.value == "Seleccione" ? "null" : padre.value;

        const levelcurrent = padre.value == "Seleccione" ? 0 : (parseInt(padre.options[padre.selectedIndex].dataset.level) + 1);

        const subcuentax1 = subcuenta1.value == "Seleccione" ? "null" : subcuenta1.value;
        const subcuentax2 = subcuenta2.value == "Seleccione" ? "null" : subcuenta2.value;
        if(parseInt(codigocontable.value) == 0){
            data.header = `insert into plancontable (codigo, descripcion, padre, subcuenta1, subcuenta2, level) values ('${cuenta.value.toUpperCase()}', '${descripcion.value.toUpperCase()}', ${padrex}, ${subcuentax1}, ${subcuentax2}, ${levelcurrent})`;
        }else{
            data.header = `
                        UPDATE plancontable set 
                            codigo = '${cuenta.value.toUpperCase()}',
                            descripcion = '${descripcion.value.toUpperCase()}',
                            padre = ${padrex},
                            subcuenta1 = ${subcuentax1},
                            subcuenta2 = ${subcuentax2}
                        WHERE id = ${codigocontable.value}`
        }

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
    formoperacion.addEventListener("submit", guardar)
</script>