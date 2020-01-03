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

<button class="btn btn-success" data-toggle="modal" data-target="#moperation" onclick="openmodal()" style="margin-bottom: 10px">Agregar Cuenta</button>

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
                <th>Descripcion</th>
            </tr>
        </thead>
        <tbody>
            <?php do {  ?>
                <tr>
                    <?php if(!$row["padrexx"]): ?> 
                        <td style="font-weight: bold"><?= $i ?></td>
                        <td style="font-weight: bold"><?= $row["codigo"] ?></td>
                        <td style="font-weight: bold"><?= $row["descripcion"] ?></td>
                    <?php else: ?>
                        <td><?= $i ?></td>
                        <td><?= $row["codigo"] ?></td>
                        <td><?= $row["descripcion"] ?></td>
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
                                    <select  id="padre"></select>
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
    const openmodal = async () => {
        const res = await get_data_dynamic("select id, CONCAT(codigo, ' ', descripcion) as descripcion from plancontable where padre is null or padre = 0")
        cargarselect2("#padre", res, 'id', 'descripcion')
    }
    const guardar = e => {
        e.preventDefault();
        const data = {
            header: "",
            detalle: []
        }
        const padrex = padre.value == "Seleccione" ? "null" : padre.value;
        data.header = `insert into plancontable (codigo, descripcion, padre) values ('${cuenta.value}', '${descripcion.value}', ${padrex})`;

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
                }else if(res.msg){
                    alert(res.msg.includes("uplicate") ? "El codigo y la descripción que ingresó está duplicado." : "hubo un error, vuelva a intentarlo");
                }
            });
    }
    formoperacion.addEventListener("submit", guardar)
</script>