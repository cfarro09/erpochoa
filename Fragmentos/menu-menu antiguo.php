<?php 
if(!isset($_GET['codigo'])){
    include("Fragmentos/cod_gen.php");
}
$pp = (int) $_SESSION['kt_codigopersonal'];
mysql_select_db($database_Ventas, $Ventas);
$query_personal = "select acceso_seguridad.acceso, sucursal.cod_sucursal, sucursal.nombre_sucursal from acceso_seguridad left join sucursal on acceso_seguridad.cod_sucursal = sucursal.cod_sucursal  where personal = $pp";
$listado_personal = mysql_query($query_personal, $Ventas) or die(mysql_error());
$row_personal = mysql_fetch_assoc($listado_personal);

$totalRows_personal = mysql_num_rows($listado_personal);
?>
<!-- BEGIN HEADER & CONTENT DIVIDER -->

<!-- END HEADER & CONTENT DIVIDER -->
<!-- BEGIN CONTAINER -->





<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <!-- BEGIN SIDEBAR -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">
            <!-- BEGIN SIDEBAR MENU -->
            <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
            <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
            <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
            <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <?php if($row_personal): ?>
                <?php 
                $_SESSION['nombre_sucursal'] = $row_personal['nombre_sucursal'];
                $_SESSION['cod_sucursal'] = $row_personal['cod_sucursal'];
                ?>

                <?php $accesos = json_decode($row_personal['acceso']) ?>

                <?php if ($accesos->gerencia): ?>
                    <?php 

                    $querySucursales = "select * from sucursal where estado < 3" ;
                    $sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
                    $row_sucursales = mysql_fetch_assoc($sucursales);

                    mysql_select_db($database_Ventas, $Ventas);
                    $query_personalx = "SELECT * FROM personal WHERE estado = 0";
                    $listado_personalx = mysql_query($query_personalx, $Ventas) or die(mysql_error());
                    $row_personalx = mysql_fetch_assoc($listado_personalx);
                    $totalRows_personal = mysql_num_rows($listado_personalx);
                    ?>

                    <div class="modal fade" id="mSucursal" role="dialog" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content m-auto">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="moperation-title">Listar Sucursales</h5>
                                </div>
                                <div class="modal-body">
                                    <form id="msavesucursal">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label for="field-1" class="control-label">Agregar
                                                                sucursal</label>
                                                                <input type="text" id="name_sucursal" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button type="submit" class="btn btn-success">Guardar</button>
                                                        </div>
                                                    </div>
                                                    <table class="table">
                                                        <thead>
                                                            <th>Nº</th>
                                                            <th>Sucursal</th>
                                                            <th>Estado</th>
                                                            <th>Accion</th>
                                                            <th>IR</th>
                                                            <th></th>
                                                        </thead>
                                                        <tbody id="detalleTableOrden">
                                                            <?php
                                                            $i = 1;
                                                            do {  
                                                                $estado = "SUSPENDIDO";
                                                                if($row_sucursales['estado'] == "1"){
                                                                    $estado = "ACTIVO";
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td><?= $i ?></td>
                                                                    <td><?= $row_sucursales['nombre_sucursal']?></td>
                                                                    <td><?= $estado ?></td>
                                                                    <td>
                                                                        <?php if($estado == "SUSPENDIDO"): ?>
                                                                            <a href="#" data-estado="3"
                                                                            data-cod="<?= $row_sucursales['cod_sucursal'] ?>"
                                                                            class="changeEstado">Eliminar</a>
                                                                        <?php endif ?>

                                                                        <a href="#"
                                                                        data-estado="<?= $estado == "ACTIVO" ? "2" : "1"?>"
                                                                        data-cod="<?= $row_sucursales['cod_sucursal'] ?>"
                                                                        class="changeEstado"><?= $estado == "ACTIVO" ? "Suspender" : "Activar"?></a>
                                                                    </td>
                                                                    <td>
                                                                        <?= $row_sucursales['cod_sucursal'] == 1 ? '<a href="principal01.php">Ir</>' : '' ?>
                                                                    </td>
                                                                </tr>
                                                                <?php $i++;
                                                            } while ($row_sucursales = mysql_fetch_assoc($sucursales));
                                                            $rows = mysql_num_rows($sucursales);
                                                            if($rows > 0) {
                                                                mysql_data_seek($sucursales, 0);
                                                                $row_sucursales = mysql_fetch_assoc($sucursales);
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="modal_close btn btn-danger"
                                        data-dismiss="modal">Cerrar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php 
                    $querySucursales = "select * from sucursal where estado = 1" ;
                    $sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
                    $row_sucursales = mysql_fetch_assoc($sucursales);
                    $totalRows_sucursales = mysql_num_rows($sucursales);
                    ?>
                    <div class="modal fade" id="mManageUsuario" role="dialog" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content m-auto">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="moperation-title"></h5>
                                </div>
                                <div class="modal-body">
                                    <form id="saveManageUsuarios">
                                        <div class="container-fluid">
                                            <div class="row" style="">
                                                <div class="col-xs-12 col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <input type="hidden" name="cod_acceso_seguridad" value="0"
                                                                id="cod_acceso_seguridad">
                                                                <label for="field-1" class="control-label">Personal</label>
                                                                <select name="personal" onchange="selectpersonalx()"
                                                                id="personalx" required
                                                                class="form-control select2 tooltips" data-placement="top"
                                                                data-original-title="Seleccionar personal">
                                                                <?php $i = 1; ?>
                                                                <option selected disabled value="0">Seleccione</option>
                                                                <?php do { ?>

                                                                    <option value="<?= $row_personalx['codigopersonal'] ?>">
                                                                        <?= $row_personalx['paterno']." ".$row_personalx['materno']." ".$row_personalx['nombre'] ?>
                                                                    </option>

                                                                    <?php $i++; } while ($row_personalx = mysql_fetch_assoc($listado_personalx)); ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="field-1" class="control-label">Asignar
                                                                Surcusal</label>
                                                                <select name="sucursal" id="sucursal" required
                                                                class="sucursal form-control select2 tooltips"
                                                                data-placement="top"
                                                                data-original-title="Seleccionar sucursal">
                                                                <option selected disabled value="0">Seleccione</option>
                                                                <?php do {  ?>
                                                                    <option
                                                                    value="<?php echo $row_sucursales['cod_sucursal']?>">
                                                                    <?php echo $row_sucursales['nombre_sucursal']?>
                                                                </option>
                                                                <?php
                                                            } while ($row_sucursales = mysql_fetch_assoc($sucursales));
                                                            $rows = mysql_num_rows($sucursales);
                                                            if($rows > 0) {
                                                                mysql_data_seek($sucursales, 0);
                                                                $row_sucursales = mysql_fetch_assoc($sucursales);
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-bottom: 10px">
                                                    <div class="col-sm-12">
                                                        <div class="col-md-3" style="margin-bottom: 10px">
                                                            <div class="">
                                                                <input type="checkbox" class="" id="check_gerencia">
                                                                <label class="" for="check_gerencia">Gerencia</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" style="margin-bottom: 10px">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                id="check_ventas">
                                                                <label class="form-check-label"
                                                                for="check_ventas">Ventas</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" style="margin-bottom: 10px">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                id="check_compras">
                                                                <label class="form-check-label"
                                                                for="check_compras">Compras</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" style="margin-bottom: 10px">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                id="check_almacen">
                                                                <label class="form-check-label"
                                                                for="check_almacen">Almacen</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="btn-finalice" style="display: none"
                                class="btn btn-primary">Finalizar</button>
                                <button type="submit" id="btn-guardarGuia" class="btn btn-success">Guardar</button>
                                <button type="button" class="modal_close btn btn-danger" data-dismiss="modal"
                                aria-label="Close">Cerrar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




            <?php 
            $queryfrases = "select * from frases order by selected desc" ;
            $frases = mysql_query($queryfrases, $Ventas) or die(mysql_error());
            $row_frases = mysql_fetch_assoc($frases);
            $totalRows_frases = mysql_num_rows($frases);
            ?>
            <div class="modal fade" id="mFrases" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content m-auto">
                        <div class="modal-header">
                            <h2 class="modal-title">Frases</h2>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row" style="">
                                    <div class="col-xs-12 col-md-12" style="margin-bottom: 10px">
                                        <div class="row">
                                            <form id="saveFrase">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input type="hidden" name="cod_acceso_seguridad" value="0"
                                                        id="cod_acceso_seguridad">
                                                        <textarea class="form-control" required id="newfrase"></textarea>
                                                    </div>
                                                    <button class="btn btn-success">GUARDAR</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-12">
                                        <table class="table table-striped table-bordered table-hover" id="">
                                            <thead>
                                                <tr>
                                                    <td>Frase</td>
                                                    <td>Estado</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($totalRows_frases > 0): ?>
                                                    <?php do { ?>
                                                        <?php $id = $row_frases['id'] ?>
                                                        <tr>
                                                            <td> <?= $row_frases['frase']; ?></td>
                                                            <td> <?= $row_frases['selected'] == 0 ? "<a class='select-frase' data-idfrase='$id'>Seleccionar</a>" : "Seleccionada" ; ?></td>
                                                        </td>
                                                    </tr>
                                                    <?php $i++;} while ($row_frases = mysql_fetch_assoc($frases)); ?>
                                                <?php endif ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="modal_close btn btn-danger" data-dismiss="modal"
                            aria-label="Close">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>



        <?php endif ?>
        <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <li class="nav-item">
                <a href="javascript:;" class="nav-link font-blue-steel">
                    <i class="fa fa-users font-blue-steel"></i>
                    <span class="title">Gerencia General</span>
                    <span class="selected"></span>
                    <span class="arrow open"></span>
                </a>
                <ul class="sub-menu">

                    <li class="nav-item">
                        <a href="javascript:;" class="nav-link font-blue-steel">
                            <i class="glyphicon glyphicon-user font-blue-steel"></i>
                            <span class="title">Autorización</span>
                            <span class="selected"></span>
                            <span class="arrow open"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="ordencompragerencia.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                    <span class="title">Orden de Compra</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="auto_guia_sin_oc.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                    <span class="title">Guia sin OC</span>
                                    <span class="selected"></span>

                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="auto_devoluciones.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                    <span class="title">Devoluciones</span>
                                    <span class="selected"></span>

                                </a>
                            </li>

                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" id="showSucursales" class="nav-link font-blue-ebonyclay">
                            <i class="fa fa-black-tie font-blue-ebonyclay"></i>
                            <span class="title">Sucursales</span>
                            <span class="selected"></span>

                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" id="showFrases" class="nav-link font-blue-ebonyclay">
                            <i class="fa fa-black-tie font-blue-ebonyclay"></i>
                            <span class="title">Frase del día</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="javascript:;" class="nav-link font-blue-steel">
                    <i class="fa fa-users font-blue-steel"></i>
                    <span class="title">Area Logistica</span>
                    <span class="selected"></span>
                    <span class="arrow open"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="javascript:;" class="nav-link font-blue-steel">
                            <i class="glyphicon glyphicon-user font-blue-steel"></i>
                            <span class="title">Almacen</span>
                            <span class="selected"></span>
                            <span class="arrow open"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="product_list.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                    <span class="title">Data Mercaderia</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="ordencompra_alm_list.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                    <span class="title">Ordenes de compra</span>
                                    <span class="selected"></span>

                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="auto_guia_sin_oc.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                    <span class="title">Entrada de Mercaderia</span>
                                    <span class="selected"></span>

                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:;" class="nav-link font-blue-steel">
                            <i class="fa fa-users font-blue-steel"></i>
                            <span class="title">Edificaciones</span>
                            <span class="selected"></span>
                            <span class="arrow open"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="bienes_list.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                    <span class="title">Data de Inmuebles</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="tipo_movimiento_list.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                    <span class="title">Tipo de Movimiento</span>
                                    <span class="selected"></span>

                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-item">
                        <a href="javascript:;" class="nav-link font-blue-steel">
                            <i class="glyphicon glyphicon-user font-blue-steel"></i>
                            <span class="title">Personal</span>
                            <span class="selected"></span>
                            <span class="arrow open"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="personal_list.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                    <span class="title">Data Trabajador</span>
                                    <span class="selected"></span>


                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="personal_access.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                    <span class="title">Estado de cuenta</span>
                                    <span class="selected"></span>

                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="personal_cargo.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                    <span class="title">Cargos</span>
                                    <span class="selected"></span>

                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="personal_access.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                    <span class="title">Acceso</span>
                                    <span class="selected"></span>

                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="" id="manageUsuarios" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                    <span class="title">Permisos del Sistemas</span>
                                    <span class="selected"></span>

                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="managevacaciones.php" id="" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                    <span class="title">Vacaciones y Permisos</span>
                                    <span class="selected"></span>

                                </a>
                            </li>

                        </ul>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="javascript:;" class="nav-link font-blue-steel">
                    <i class="fa fa-users font-blue-steel"></i>
                    <span class="title">Area Comercial</span>
                    <span class="selected"></span>
                    <span class="arrow open"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="javascript:;" class="nav-link font-blue-steel">
                            <i class="glyphicon glyphicon-user font-blue-steel"></i>
                            <span class="title">Compras</span>
                            <span class="selected"></span>
                            <span class="arrow open"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="proveedor_list.php" class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                    <span class="title">Data Proveedor</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="new_ordencompra_add.php?codigo=<?php echo $_GET['codigo']; ?>"
                                    class="nav-link font-blue-ebonyclay">
                                    <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                    <span class="title">Orden de compra</span>
                                    <span class="selected"></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="ordencompra_list.php"
                                class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                <span class="title">Listado Orden de Compra</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="facturacion_list.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                <span class="title">Costeo</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="kardex_valorado.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                <span class="title">Kardex Valorado</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="cuentas_x_pagar.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                <span class="title">Cuentas por pagar</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="compras_list.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                <span class="title">Registro compras</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="registro_plamar.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                <span class="title">Registro Plamar</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:;" class="nav-link font-blue-steel">
                                <i class="fa fa-cogs font-blue-steel"></i>
                                <span class="title">MANTENIMIENTOS</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="banco_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-bank font-blue-ebonyclay"></i>
                                        <span class="title">Banco</span>
                                        <span class="selected"></span>

                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="color_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="glyphicon glyphicon-tint font-blue-ebonyclay"></i>
                                        <span class="title">Color</span>
                                        <span class="selected"></span>

                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="marca_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-apple font-blue-ebonyclay"></i>
                                        <span class="title">Marca</span>
                                        <span class="selected"></span>

                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="oficina_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-bank font-blue-ebonyclay"></i>
                                        <span class="title">Oficina</span>
                                        <span class="selected"></span>

                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="presentacion_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-object-group  font-blue-ebonyclay"></i>
                                        <span class="title">Presentac&iacute;on</span>
                                        <span class="selected"></span>

                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="profesion_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-graduation-cap  font-blue-ebonyclay"></i>
                                        <span class="title">Profes&iacute;on</span>
                                        <span class="selected"></span>

                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="cargo_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-graduation-cap  font-blue-ebonyclay"></i>
                                        <span class="title">Cargo</span>
                                        <span class="selected"></span>

                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="category_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-th-large font-blue-ebonyclay"></i>
                                        <span class="title">Categorias</span>
                                        <span class="selected"></span>
                                    </a>
                                </li>




                            </ul>
                        </li>

                    </ul>
                </li>
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link font-blue-steel">
                        <i class="fa fa-users font-blue-steel"></i>
                        <span class="title">Cuentas por pagar</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item">
                            <a href="conciliacion.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                <span class="title">Estado de cuenta</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link font-blue-steel">
                        <i class="fa fa-users font-blue-steel"></i>
                        <span class="title">Ventas</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item">
                            <a href="conciliacion.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                <span class="title">Data Clientes</span>
                                <span class="selected"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="cliente_natural_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-leaf font-blue-ebonyclay"></i>
                                        <span class="title">Naturales</span>
                                        <span class="selected"></span>


                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="cliente_juridico_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-black-tie font-blue-ebonyclay"></i>
                                        <span class="title">Jur&iacute;dicos</span>
                                        <span class="selected"></span>

                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="proforma_list.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                <span class="title">Proforma</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="ventas.php.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                <span class="title">Ventas</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="javascript:;" class="nav-link font-blue-steel">
                        <i class="fa fa-users font-blue-steel"></i>
                        <span class="title">Cuentas por cobrar</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item">
                            <a href="emitir_nota_debito.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                <span class="title">Emitir nota debito</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="emitir_nota_credito.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                <span class="title">Emitir nota credito</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="javascript:;" class="nav-link font-blue-steel">
                        <i class="glyphicon glyphicon-user font-blue-steel"></i>
                        <span class="title">Caja principal</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item">
                            <a href="abonos_cctt.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                <span class="title">Registro Abonos en cctt</span>
                                <span class="selected"></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="cargos_cctt.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-log-in font-blue-ebonyclay"></i>
                                <span class="title">Registro carggos cctt</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="giros_cheques.php" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                <span class="title">Registro giro cheques</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="estado_cuenta_bancario.php" id="" class="nav-link font-blue-ebonyclay">
                                <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                <span class="title">Estado cuenta bancario</span>
                                <span class="selected"></span>

                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a href="javascript:;" class="nav-link font-blue-steel">
                <i class="fa fa-users font-blue-steel"></i>
                <span class="title">Area Contable</span>
                <span class="selected"></span>
                <span class="arrow open"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item">
                    <a href="registro_compras.php" class="nav-link font-blue-steel">
                        <i class="glyphicon glyphicon-user font-blue-steel"></i>
                        <span class="title">Registro Compras</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="registro_ventas.php" class="nav-link font-blue-steel">
                        <i class="glyphicon glyphicon-user font-blue-steel"></i>
                        <span class="title">Registro ventas</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="diario.php" class="nav-link font-blue-steel">
                        <i class="glyphicon glyphicon-user font-blue-steel"></i>
                        <span class="title">Diario</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="mayor.php" class="nav-link font-blue-steel">
                        <i class="glyphicon glyphicon-user font-blue-steel"></i>
                        <span class="title">Mayor</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="balanace.php" class="nav-link font-blue-steel">
                        <i class="glyphicon glyphicon-user font-blue-steel"></i>
                        <span class="title">Balance</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="eepp.php" class="nav-link font-blue-steel">
                        <i class="glyphicon glyphicon-user font-blue-steel"></i>
                        <span class="title">EEPP y GG</span>
                        <span class="selected"></span>
                        <span class="arrow open"></span>
                    </a>
                </li>
            </ul>
        </li>

        




    </ul>
    <?php else: ?>
    <?php endif ?>

    <!-- END SIDEBAR MENU -->
</div>
<!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->

<!-- BEGIN CONTENT -->
<?php if  ($nombre_archivo = "proveedor_cuentas.php") {

 $VarUrl2 = "";
 $TituloGeneral2 = "";
}
else {
   $VarUrl2 = "";
}
?>
<div class="page-content-wrapper">
    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <div class="page-head">
            <!-- BEGIN PAGE TITLE -->
            <?php echo $TituloGeneral2; ?>
        </div>

        <!-- BEGIN PAGE HEAD-->
        <!-- BEGIN PAGE BASE CONTENT -->


        <div class="row">

            <!-- BEGIN REGIONAL STATS PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">

                    <div class="caption">
                        <i class="<?php echo $Icono; ?> <?php echo $Color; ?>"></i>
                        <span
                        class="caption-subject <?php echo $Color; ?> bold uppercase"><?= "SUCURSAL - ". $_SESSION['nombre_sucursal'] ?></span>
                        <div class="btn-group">
                            <a class="btn sbold blue <?php echo $EstadoBotonAgregar?>"
                                onClick="abre_ventana('Emergentes/<?php echo $agregar.$VarUrl2?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"
                                data-toggle="modal"> <?php echo $NombreBotonAgregar?> <i class="fa fa-plus"></i></a>
                            </div>
                            <div class="btn-group">
                                <a class="btn sbold green" href="javascript:location.reload()"> Actualizar <i
                                    class="fa fa-refresh"></i></a>
                                </div>
                            </div>
                            <div class="actions">
                                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"> </a>

                            </div>
                        </div>
                        <div class="portlet-body">


                            <script>
                                const getSelector = (tag) => document.querySelector(tag)
                                const getSelectorAll = (tag) => document.querySelectorAll(tag)


                                getSelector("#showFrases").addEventListener("click", e => {
                                    $("#mFrases").modal()
                                })
                                getSelectorAll(".select-frase").forEach(i => {
                                    i.addEventListener("click", e => {
                                        e.preventDefault();
                                        
                                        var formData = new FormData();
                                        formData.append("id", e.target.dataset.idfrase)
                                        formData.append("type", "update")
                                        fetch(`setFrase.php`, { method: 'POST', body: formData })
                                        .then(res => res.json())
                                        .catch(error => console.error("error: ", error))
                                        .then(res => {
                                            if (res.success) {
                                                alert("registro completo!")
                                                location.reload()
                                            }
                                        });
                                    })
                                })
                                getSelector("#saveFrase").addEventListener("submit", e => {
                                    e.preventDefault();
                                    
                                    var formData = new FormData();
                                    formData.append("frase", $("#newfrase").val())
                                    formData.append("type", "add")
                                    fetch(`setFrase.php`, { method: 'POST', body: formData })
                                    .then(res => res.json())
                                    .catch(error => console.error("error: ", error))
                                    .then(res => {
                                        if (res.success) {
                                            alert("registro completo!")
                                            location.reload()
                                        }
                                    });
                                })
                                getSelector("#showSucursales").addEventListener("click", e => {
                                    e.preventDefault();
                                    $("#mSucursal").modal();
                                })
                                getSelectorAll(".changeEstado").forEach(item => {
                                    item.addEventListener("click", e => {
                                        e.preventDefault();
                                        const estado = e.target.dataset.estado;
                                        const cod = e.target.dataset.cod;
                                        fetch(`setEstadoSucursal.php?estado=${estado}&cod=${cod}`)
                                        .then(res => res.json())
                                        .catch(error => console.error("error: ", error))
                                        .then(res => {


                                            location.reload();
                                        });

                                    })
                                })
                                getSelector("#msavesucursal").addEventListener("submit", e => {
                                    e.preventDefault()
                                    var formData = new FormData();
                                    formData.append("name_sucursal", getSelector("#name_sucursal").value)

                                    fetch(`saveSucursal.php`, { method: 'POST', body: formData })
                                    .then(res => res.json())
                                    .catch(error => console.error("error: ", error))
                                    .then(res => {
                                        if (res.success) {
                                            alert("registro completo!")
                                            location.reload()
                                            // getSelector("#form-generate-compra").reset();
                                            // getSelector("#detalleFormProducto").innerHTML = ""
                                        }
                                    });
                                })

                            </script>
                            <script>
                                document.querySelector("#manageUsuarios").addEventListener("click", e => {
                                    e.preventDefault();
                                    $("#mManageUsuario").modal();
                                });
                                function selectpersonalx() {
                                    var formData = new FormData();
                                    formData.append("personal", $("#personalx").val())

                                    fetch(`getAccesosSeguridad.php`, { method: 'POST', body: formData })
                                    .then(res => res.json())
                                    .catch(error => console.error("error: ", error))
                                    .then(res => {
                                        if (res) {
                                            document.querySelector("#cod_acceso_seguridad").value = res.cod_acceso_seguridad
                                            $("#sucursal").val(res.cod_sucursal).trigger('change');
                                            const compras = document.querySelector("#check_compras").checked
                                            const ac = JSON.parse(res.acceso)

                                            document.querySelector("#check_gerencia").checked = ac.gerencia
                                            document.querySelector("#check_ventas").checked = ac.ventas
                                            document.querySelector("#check_almacen").checked = ac.almacen
                                            document.querySelector("#check_compras").checked = ac.compras

                                            if (ac.gerencia)
                                                document.querySelector("#check_gerencia").parentElement.classList.add("checked")
                                            else
                                                document.querySelector("#check_gerencia").parentElement.classList.remove("checked")
                                            if (ac.ventas)
                                                document.querySelector("#check_ventas").parentElement.classList.add("checked")
                                            else
                                                document.querySelector("#check_ventas").parentElement.classList.remove("checked")
                                            if (ac.almacen)
                                                document.querySelector("#check_almacen").parentElement.classList.add("checked")
                                            else
                                                document.querySelector("#check_almacen").parentElement.classList.remove("checked")
                                            if (ac.compras)
                                                document.querySelector("#check_compras").parentElement.classList.add("checked")
                                            else
                                                document.querySelector("#check_compras").parentElement.classList.remove("checked")
                                        } else {
                                            document.querySelector("#cod_acceso_seguridad").value = "0"
                                            $("#sucursal").val("0").trigger('change');
                                            $("#personal").val("0").trigger('change');
                                            document.querySelector("#check_compras").parentElement.classList.remove("checked")
                                            document.querySelector("#check_ventas").parentElement.classList.remove("checked")
                                            document.querySelector("#check_almacen").parentElement.classList.remove("checked")
                                            document.querySelector("#check_gerencia").parentElement.classList.remove("checked")
                                        }
                                    });
                                }
                                document.querySelector("#saveManageUsuarios").addEventListener("submit", e => {
                                    e.preventDefault();
                                    const compras = document.querySelector("#check_compras").checked
                                    const gerencia = document.querySelector("#check_gerencia").checked
                                    const ventas = document.querySelector("#check_ventas").checked
                                    const almacen = document.querySelector("#check_almacen").checked

                                    const sucursal = document.querySelector("#sucursal").value
                                    const personal = document.querySelector("#personalx").value

                                    const cod_acceso_seguridad = document.querySelector("#cod_acceso_seguridad").value

                                    if ((compras || gerencia || ventas || almacen) && personal && sucursal) {

                                        const accesos = {
                                            compras,
                                            gerencia,
                                            almacen,
                                            ventas
                                        }
                                        const data = {
                                            sucursal,
                                            personal,
                                            accesos,
                                            cod_acceso_seguridad
                                        }
                                        var formData = new FormData();
                                        formData.append("json", JSON.stringify(data))

                                        fetch(`saveAccesosSeguridad.php`, { method: 'POST', body: formData })
                                        .then(res => res.json())
                                        .catch(error => console.error("error: ", error))
                                        .then(res => {
                                            if (res.success) {
                                                alert("registro completo!")
                                                location.reload()
                                            }
                                        });
                                    } else {
                                        alert("debes asignar almenos una funcion")
                                    }


                                })
                            </script>