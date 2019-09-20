<!-- BEGIN HEADER & CONTENT DIVIDER -->
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
            <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                <li class="nav-item ">

                    <li class="nav-item">
                        <a href="principal.php" class="nav-link font-blue-steel">
                            <i class="glyphicon glyphicon-home font-blue-steel"></i>
                            <span class="title">PRINCIPAL</span>
                        </a>

                    </li>

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
                                <a href="javascript:;" class="nav-link font-blue-steel">
                                    <i class="glyphicon glyphicon-user font-blue-steel"></i>
                                    <span class="title">PERSONAL</span>
                                    <span class="selected"></span>
                                    <span class="arrow open"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item">
                                        <a href="personal_list.php" class="nav-link font-blue-ebonyclay">
                                            <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                            <span class="title">Listado</span>
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
                                        <a href="personal_cargo.php" class="nav-link font-blue-ebonyclay">
                                            <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                            <span class="title">Cargos</span>
                                            <span class="selected"></span>

                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="" id="manageUsuarios" class="nav-link font-blue-ebonyclay">
                                            <i class="glyphicon glyphicon-signal font-blue-ebonyclay"></i>
                                            <span class="title">Permisos</span>
                                            <span class="selected"></span>

                                        </a>
                                    </li>

                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="reportes.php" class="nav-link font-blue-steel">
                            <i class="fa fa-file-pdf-o font-blue-steel"></i>
                            <span class="title">REPORTES</span>
                        </a>

                    </li>
                </ul>
            </li>
        </ul>

        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->
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
                                            <input type="hidden" name="cod_acceso_seguridad" value="0" id="cod_acceso_seguridad">
                                            <label for="field-1" class="control-label">Personal</label>
                                            <select name="personal" onchange="selectpersonalx()" id="personalx" required class="form-control select2 tooltips" data-placement="top" data-original-title="Seleccionar personal">
                                                <?php $i = 1; ?>
                                                <option selected disabled value="0">Seleccione</option>
                                                <?php do { ?>

                                                    <option value="<?= $row_personalx['codigopersonal'] ?>"><?= $row_personalx['paterno']." ".$row_personalx['materno']." ".$row_personalx['nombre'] ?></option>

                                                    <?php $i++; } while ($row_personalx = mysql_fetch_assoc($listado_personalx)); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="field-1" class="control-label">Asignar Surcusal</label>
                                                <select name="sucursal" id="sucursal" required class="sucursal form-control select2 tooltips"
                                                data-placement="top" data-original-title="Seleccionar sucursal">
                                                <option selected disabled value="0">Seleccione</option>
                                                <?php do {  ?>
                                                    <option value="<?php echo $row_sucursales['cod_sucursal']?>">
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
                                            <input type="checkbox" class="form-check-input" id="check_ventas">
                                            <label class="form-check-label" for="check_ventas">Ventas</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="margin-bottom: 10px">
                                       <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="check_compras">
                                        <label class="form-check-label" for="check_compras">Compras</label>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-bottom: 10px">
                                   <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check_almacen">
                                    <label class="form-check-label" for="check_almacen">Almacen</label>
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
    <button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
</form>
</div>
</div>
</div>
</div>

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
                        class="caption-subject <?php echo $Color; ?> bold uppercase"><?php echo $Titulo; ?></span>
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
                                getSelector("#msavesucursal").addEventListener("submit" , e => {
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
                                function selectpersonalx(){
                                    var formData = new FormData();
                                    formData.append("personal", $("#personalx").val())

                                    fetch(`getAccesosSeguridad.php`, { method: 'POST', body: formData })
                                    .then(res => res.json())
                                    .catch(error => console.error("error: ", error))
                                    .then(res => {
                                        if(res){
                                            document.querySelector("#cod_acceso_seguridad").value = res.cod_acceso_seguridad
                                            $("#sucursal").val(res.cod_sucursal).trigger('change');
                                            const compras =  document.querySelector("#check_compras").checked
                                            const ac = JSON.parse(res.acceso)

                                            document.querySelector("#check_gerencia").checked = ac.gerencia
                                            document.querySelector("#check_ventas").checked = ac.ventas
                                            document.querySelector("#check_almacen").checked = ac.almacen
                                            document.querySelector("#check_compras").checked = ac.compras

                                            if(ac.gerencia)
                                                document.querySelector("#check_gerencia").parentElement.classList.add("checked")
                                            else
                                                document.querySelector("#check_gerencia").parentElement.classList.remove("checked")
                                            if(ac.ventas)
                                                document.querySelector("#check_ventas").parentElement.classList.add("checked")
                                            else
                                                document.querySelector("#check_ventas").parentElement.classList.remove("checked")
                                            if(ac.almacen)
                                                document.querySelector("#check_almacen").parentElement.classList.add("checked")
                                            else
                                                document.querySelector("#check_almacen").parentElement.classList.remove("checked")
                                            if(ac.compras)
                                                document.querySelector("#check_compras").parentElement.classList.add("checked")
                                            else
                                                document.querySelector("#check_compras").parentElement.classList.remove("checked")
                                        }else{
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
                                    const compras =  document.querySelector("#check_compras").checked
                                    const gerencia =  document.querySelector("#check_gerencia").checked
                                    const ventas =  document.querySelector("#check_ventas").checked
                                    const almacen =  document.querySelector("#check_almacen").checked

                                    const sucursal =  document.querySelector("#sucursal").value
                                    const personal =  document.querySelector("#personalx").value

                                    const cod_acceso_seguridad = document.querySelector("#cod_acceso_seguridad").value

                                    if((compras || gerencia || ventas|| almacen) && personal && sucursal) {

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
                                    }else{
                                        alert("debes asignar almenos una funcion")
                                    }


                                })
                            </script>