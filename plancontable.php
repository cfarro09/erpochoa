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

$query_Listado = "select p1.*, concat(p2.codigo, ' ', p2.descripcion) as padrexx from plancontable p1 left join plancontable p2 on p2.id = p1.padre";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
$i = 1;
?>
<style>
    #containerplan{
        overflow-y: auto;
        height: 50vh
    }
</style>
<div class="modal fade" id="mcontainerplan" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h2 class="modal-title">PLAN CONTABLE</h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">

                            <div id="containerplan">

                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<button class="btn btn-success" data-toggle="modal" data-target="#moperation" onclick="openmodal()" style="margin-bottom: 10px">Agregar Cuenta</button>
<button class="btn btn-success" data-toggle="modal" data-target="#mcontainerplan" onclick="openmodalplan()" style="margin-bottom: 10px">Ver Plan Contable</button>

<?php if ($totalRows_Listado == 0) : ?>
    <div class="alert alert-danger">
        <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
    </div>
<?php else : ?>
    <table class="table table-bordered table-hover" id="sample_1">
        <thead>
            <tr>
                <th>N°</th>
                <th>Codigo</th>
                <th>Padre</th>
            </tr>
        </thead>
        <tbody>
            <?php do {  ?>
                <tr>
                    <?php if (!$row["padrexx"]) : ?>
                        <td style="font-weight: bold"><?= $i ?></td>
                        <td style="font-weight: bold"><?= $row["codigo"] . " - " . $row["descripcion"] ?></td>
                        <td style="font-weight: bold"><?= $row["padrexx"] ?></td>
                    <?php else : ?>
                        <td><?= $i ?></td>
                        <td><?= $row["codigo"] . " - " . $row["descripcion"] ?></td>
                        <td><?= $row["padrexx"] ?></td>
                    <?php endif ?>
                </tr>
            <?php
                $i++;
            } while ($row = mysql_fetch_assoc($Listado)); ?>
        </tbody>
    </table>
<?php endif ?>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <form id="formoperacion" action="">
                <div class="modal-header">
                    <h2 class="modal-title">Registrar Cuenta</h2>
                </div>
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
    const gethtml = (ix, parent = false) =>{
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
    formoperacion.addEventListener("submit", guardar)
</script>