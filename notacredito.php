<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Servicios por pagar";
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

$query_Listado = "select * from ventas where porpagar = 1";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
$i = 1;
?>

<?php if ($totalRows_Listado == 0) : ?>
    <div class="alert alert-danger">
        <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
    </div>
<?php else : ?>
    <table class="table table-bordered table-hover" id="sample_1">
        <thead>
            <tr>
                <th>N°</th>
                <th>Fecha</th>
                <th>Total Venta</th>
                <th>Total Pago</th>
                <th>Tipo Comp.</th>
                <th>Cod. Comp</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php do {  ?>
                <td><?= $i ?></td>
                <td><?= $row["fecha_emision"] ?></td>
                <td><?= $row["total"] ?></td>
                <td><?= $row["pagoacomulado"] ?></td>
                <td><?= $row["tipocomprobante"] ?></td>
                <td><?= $row["codigocomprobante"] ?></td>
                <td><a href="#" data-json='<?= $row["jsonpagos"] ?>' data-id="<?= $row["codigoventas"] ?>" onclick="pagar(this)">Pagar</a></td>
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
                    <h2 class="modal-title">Registrar servicio por pagar</h2>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="fechafacturacion" class="control-label">Fecha Facturacion</label>
                                    <input type="text" required autocomplete="off" id="fechafacturacion" class="form-control date-picker" data-date-format="yyyy-mm-dd" required />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="concepto" class="control-label">Concepto</label>
                                    <select class="form-control" name="concepto" id="concepto">
                                        <option value="agua">agua</option>
                                        <option value="luz">luz</option>
                                        <option value="cable">cable</option>
                                        <option value="internet">internet</option>
                                        <option value="alquilarlocal">Alquiler Local</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="numerorecibo" class="control-label">N° Recibo</label>
                                    <input type="text" required autocomplete="off" id="numerorecibo" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="mespago" class="control-label">Mes Pago</label>
                                    <select class="form-control" name="mespago" id="mespago">
                                        <option value="enero">enero</option>
                                        <option value="febrero">febrero</option>
                                        <option value="marzo">marzo</option>
                                        <option value="abril">abril</option>
                                        <option value="mayo">mayo</option>
                                        <option value="junio">junio</option>
                                        <option value="julio">julio</option>
                                        <option value="agosto">agosto</option>
                                        <option value="setiembre">setiembre</option>
                                        <option value="octubre">octubre</option>
                                        <option value="noviembre">noviembre</option>
                                        <option value="diciembre">diciembre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="aniopago" class="control-label">Año Pago</label>
                                    <select class="form-control" name="aniopago" id="aniopago">
                                        <option value="2019">2019</option>
                                        <option value="220">2020</option>
                                        <option value="2021">2021</option>
                                        <option value="2022">2022</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="preciopago" class="control-label">Precio</label>
                                    <input type="number" required autocomplete="off" id="preciopago" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="sucursal" class="control-label">Sucursal</label>
                                    <select class="form-control" name="codsucursal" id="codsucursal">
                                        <?php do { ?>
                                            <option value="<?= $row_sucursales['cod_sucursal'] ?>"><?= $row_sucursales['nombre_sucursal'] ?></option>
                                        <?php } while ($row_sucursales = mysql_fetch_assoc($sucursales)); ?>
                                    </select>
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
    const pagar = e => {
        console.log(JSON.parse(e.dataset.json))
    }
    const guardar = e => {
        e.preventDefault();
        const query = `insert into serviciosporpagar (fechafacturacion, concepto, numerorecibo, mespago, aniopago, precio, codsucursal) values ('${fechafacturacion.value}', '${concepto.value}', '${numerorecibo.value}', '${mespago.value}', '${aniopago.value}', ${preciopago.value}, ${codsucursal.value})`
        const detalle = [];
        detalle.push(query);
        const formData = new FormData();
        formData.append("exearray", JSON.stringify(detalle))

        fetch(`setPrecioVenta.php`, {
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
                }
            });
    }
    formoperacion.addEventListener("submit", guardar)

</script>