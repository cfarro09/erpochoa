<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Lista Proformas";
$NombreBotonAgregar = "Agregar";
$EstadoBotonAgregar = "disabled";
$popupAncho = 700;
$popupAlto = 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");

// Logica de la pagina 
$codsucursal = $_SESSION['cod_sucursal'];

# Cargar lista proforma
$query = "select pro.*,CONCAT(c.paterno, ' ', c.materno, ' ', c.nombre) as ClienteNatural,c.cedula from proforma pro left join cnatural c on c.codigoclienten = pro.codigoclienten";
$listado = mysql_query($query, $Ventas) or die(mysql_error());
$row = mysql_fetch_assoc($listado);
$totalRows_Listado = mysql_num_rows($listado);
$i = 1;

if ($totalRows_Listado == 0) : ?>
    <div class="alert alert-danger">
        <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
    </div>
<?php else: ?>
    <table class="table table-bordered table-hover" id="sample_1">
        <thead>
            <tr>
                <th>N°</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php do { ?>
                        
            <tr>
                <td><?= $i ?></td>
                <td><?= $row["fecha_emision"] ?></td>
                <td><?= $row["ClienteNatural"] ?></td>
                <td><?= $row["total"] ?></td>
                <td><a href="#" 
                        data-id="<?= $row["codigoproformas"] ?>"
                        data-fecha="<?= $row["fecha_emision"] ?>"
                        data-cliente="<?= $row["ClienteNatural"] ?>"
                        data-codigocomprobante="<?= $row["codigoproformas"] ?>"
                        data-total="<?= $row["total"] ?>"
                        onclick="detalle(this)">Detalle</a></td>
            </tr>
            <?php
                        $i++;
                    } while ($row = mysql_fetch_assoc($listado)); ?>
        </tbody>
    </table>
<?php endif ?>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content m-auto">
            <form id="formoperacion" action="">
                <div class="modal-header">
                    <h2 class="modal-title">Detalle Proforma</h2>
                </div>
                <input type="hidden" id="codigoproforma">
                <input type="hidden" id="jsonpagos">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputfecha" class="control-label">Fecha</label>
                                    <input type="text" readonly autocomplete="off" id="inputfecha"
                                        class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputnumerocomprobante" class="control-label">N° Proforma</label>
                                    <input type="text" readonly autocomplete="off" id="inputnumeroproforma"
                                        class="form-control" />
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputcliente" class="control-label">Cliente</label>
                                    <input type="text" readonly autocomplete="off" id="inputcliente"
                                        class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputtotal" class="control-label">Total</label>
                                    <input type="number" readonly autocomplete="off" id="inputtotal"
                                        class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <td class="text-center" colspan="5"><b>DEALLE PRODUCTOS</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>PRODUCTO</b></td>
                                            <td><b>MARCA</b></td>
                                            <td><b>CANTIDAD</b></td>
                                            <td><b>P VENTA</b></td>
                                            <td><b>UNIDAD MEDIDA</b></td>
                                        </tr>
                                    </thead>
                                    <tbody id="detallebody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" aria-label="Close"
                        onClick="devolver()">Imprimir</button>
                    <button type="button" class="modal_close btn btn-danger" data-dismiss="modal"
                        aria-label="Close">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// ========== Cargar Footer ==========
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    const detalle = e => {
        $("#moperation").modal();
        codigoproforma.value = e.dataset.id;
        detallebody.innerHTML = "";

        fetch(`getDetalleProforma.php?id=${e.dataset.id}`)
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res) {
                    res.forEach(ix => {
                        detallebody.innerHTML += `
                        <tr>
                        <td>${ix.nombre_producto}</td>
                        <td>${ix.marca}</td>
                        <td>${ix.cantidad}</td>
                        <td>${ix.pventa}</td>
                        <td>${ix.unidad_medida}</td>
                        </tr>
                        `;
                    })
                }
            })
        inputfecha.value = e.dataset.fecha;
        inputnumeroproforma.value = e.dataset.codigocomprobante;
        inputcliente.value = e.dataset.cliente;
        inputtotal.value = e.dataset.total;
    }
</script>