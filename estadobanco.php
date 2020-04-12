<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas);

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Cuentas";
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

$query_Listado = "select c.*, cm.saldo, b.nombre_banco from cuenta c left join banco b on b.codigobanco = c.idcodigobanco 
inner join cuenta_mov cm on cm.id_cuenta = c.id_cuenta and cm.id_cuenta_mov = (select max(cm1.id_cuenta_mov) from cuenta_mov cm1 where cm1.id_cuenta = c.id_cuenta )
";

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
                <th>Banco</th>
                <th>Moneda</th>
                <th>N° Cuenta</th>
                <th>Firma Autorizada</th>
                <th>Sectorista</th>
                <th>Celular</th>
                <th>Saldo Inicial</th>
                <th>d</th>
            </tr>
        </thead>
        <tbody>
            <?php do {  
                $fullname = $row["nombre_banco"] . " : " . $row["numero_cuenta"] . " : " .  $row["moneda"];
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $row["nombre_banco"] ?></td>
                    <td><?= $row["moneda"] ?></td>
                    <td><?= $row["numero_cuenta"] ?></td>
                    <td><?= $row["titular"] ?></td>
                    <td><?= $row["nombre_sectorista"] ?></td>
                    <td><?= $row["cel_sectorista"] ?></td>
                    <td><?= $row['saldo'] ?></td>
                    <td>
                        <a <?= "href='detallecaja.php?id=" . $row['id_cuenta'] . "&fullname=$fullname'" ?> class="btn btn-primary">DET</a>
                    </td>
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
                    <h2 class="modal-title" id="titlemodal">Registrar Cuenta</h2>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Banco</label>
                                    <select class="form-control" id="bancocuenta">
                                        <option value="1">BANCO AZTECA</option>
                                        <option value="2">BANCO BCP</option>
                                        <option value="22">BANCO BBVA CONTINENTAL </option>
                                        <option value="3">BANCO CENCOSUD</option>
                                        <option value="4">BANCO DE LA NACION</option>
                                        <option value="5">BANCO FALABELLA</option>
                                        <option value="6">BANCO GNB PERÚ</option>
                                        <option value="23">BANCO INTERBANK </option>
                                        <option value="7">BANCO MI BANCO</option>
                                        <option value="8">BANCO PICHINCHA</option>
                                        <option value="9">BANCO RIPLEY</option>
                                        <option value="10">BANCO SANTANDER PERU</option>
                                        <option value="11">BANCO SCOTIABANK</option>
                                        <option value="12">CMAC AREQUIPA</option>
                                        <option value="13">CMAC CUSCO S A</option>
                                        <option value="14">CMAC DEL SANTA</option>
                                        <option value="15">CMAC HUANCAYO</option>
                                        <option value="16">CMAC ICA</option>
                                        <option value="17">CMAC LIMA</option>
                                        <option value="18">CMAC MAYNA</option>
                                        <option value="19">CMAC PAITA</option>
                                        <option value="20">CMAC SULLANA</option>
                                        <option value="21">CMAC TRUJILLO</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Tipo</label>
                                    <select class="form-control" id="tipocuenta">
                                        <option value="CORRIENTE" selected>CORRIENTE</option>
                                        <option value="AHORROS">AHORROS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">CCI</label>
                                    <input type="text" required class="form-control" id="cci">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Moneda</label>
                                    <select class="form-control" id="monedacuenta">
                                        <option value="SOLES" selected>SOLES</option>
                                        <option value="DOLARES">DOLARES</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Numero cuenta</label>
                                    <input type="text" required class="form-control" id="numero_cuenta">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Firma Autorizada</label>
                                    <input type="text" required class="form-control" id="titular">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Sectorista</label>
                                    <input type="text" required class="form-control" id="sectorista">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Cel Sectorista</label>
                                    <input type="text" required class="form-control" id="celsectorista">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label">Saldo Inicial</label>
                                    <input type="text" required class="form-control" id="saldoinicial">
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
    let currentid = 0;
    const editarcuenta = async id => {
        currentid = id
        const res = await get_data_dynamic(`select * from cuenta where id_cuenta = ${id}
            `).then(r => r);
        titlemodal.textContent = "Editar cuenta"
        $("#moperation").modal()
        const cc = res[0]
        console.log(cc);
        
        bancocuenta.value = cc.idcodigobanco
        tipocuenta.value = cc.tipo
        cci.value = cc.cci
        monedacuenta.value = cc.moneda
        numero_cuenta.value = cc.numero_cuenta

        numero_cuenta.disabled = true
        cci.disabled = true

        titular.value = cc.titular
        sectorista.value = cc.nombre_sectorista
        celsectorista.value = cc.cel_sectorista
        saldoinicial.value = cc.saldoinicial
    }
    const btnregister = () => {
        currentid = 0
        numero_cuenta.disabled = false
        cci.disabled = false
        titlemodal.textContent = "Registrar Cuenta"
        $("#moperation").modal()
    }
    const eliminarcuenta = id => {
        const cc = confirm("¿Desea eliminar la cuenta seleccionada?")
        if (cc) {
            const data = {
                header: "",
                detalle: []
            }
            data.header = `delete from cuenta where id_cuenta=${id}`
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
                        alert("se eleminó con exito!")
                        location.reload()
                    }
                });
        }
    }
    const guardar = e => {
        e.preventDefault();
        const data = {
            header: "",
            detalle: []
        }
        if(currentid == 0){
            data.header = `insert into cuenta (idcodigobanco, tipo, cci, moneda, numero_cuenta, titular, nombre_sectorista, cel_sectorista, saldoinicial) values (${bancocuenta.value}, '${tipocuenta.value}', '${cci.value}', '${monedacuenta.value}', '${numero_cuenta.value}', '${titular.value}', '${sectorista.value}', '${celsectorista.value}','${saldoinicial.value}')`

            const dd = new Date().toISOString().substring(0, 10);
            const query = `insert into cuenta_mov (id_cuenta, fecha_trans, tipo_mov, detalle, monto, saldo) VALUES (###ID###, '${dd}', 'saldo inicial', 'saldo inicial', '${saldoinicial.value}', '${saldoinicial.value}')`
            data.detalle.push(query);
        }else{
            data.header = `
                update cuenta set 
                    idcodigobanco = ${bancocuenta.value}, 
                    tipo = '${tipocuenta.value}', 
                    moneda = '${monedacuenta.value}',
                    titular = '${titular.value}', 
                    nombre_sectorista = '${sectorista.value}', 
                    cel_sectorista = '${celsectorista.value}', 
                    saldoinicial = '${saldoinicial.value}'
                where id_cuenta = ${currentid}
                    `
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
                }
            });
    }
    formoperacion.addEventListener("submit", guardar)
</script>