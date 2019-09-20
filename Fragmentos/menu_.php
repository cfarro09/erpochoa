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
                                <i class="glyphicon glyphicon-shopping-cart  font-blue-steel"></i>
                                <span class="title">VENTAS</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                            <li class="nav-item">
                                    <a href="ventas_add.php?codigo=<?php echo $_GET['codigo']; ?>" class="nav-link font-blue-ebonyclay">
                                        <i class="glyphicon glyphicon-shopping-cart  font-blue-ebonyclay"></i>
                                        <span class="title">Generar</span>
                                        <span class="selected"></span>
                                        
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="ventas_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-building-o  font-blue-ebonyclay"></i>
                                        <span class="title">Listado</span>
                                        <span class="selected"></span>
                                        
                                        
                                    </a>
                                </li>
                                
                                
                                
                            </ul>
                        </li>
                        
                        
                           <li class="nav-item"> 
                           <a href="product_list.php" class="nav-link font-blue-steel">
                                <i class="fa fa-cubes font-blue-steel"></i>
                                <span class="title">PRODUCTOS</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                            <li class="nav-item">
                                    <a href="product_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-building-o font-blue-ebonyclay"></i>
                                        <span class="title">Productos</span>
                                        <span class="selected"></span>
                                        
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="compras_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="glyphicon glyphicon-shopping-cart  font-blue-ebonyclay"></i>
                                        <span class="title">Generar Comprobante</span>
                                        <span class="selected"></span>
                                        
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#.php" class="nav-link font-blue-ebonyclay">
                                        <i class="glyphicon glyphicon-retweet  font-blue-ebonyclay"></i>
                                        <span class="title">Numero Serie</span>
                                        <span class="selected"></span>
                                        
                                        
                                    </a>
                                </li>
                                
                                
                                
                            </ul>
                        </li>
                                                  
                        <li class="nav-item"> 
                            <a href="javascript:;" class="nav-link font-blue-steel">
                                <i class="fa fa-building font-blue-steel"></i>
                                <span class="title">INVENTARIO</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                            <li class="nav-item">
                                    <a href="bienes_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-building-o font-blue-ebonyclay"></i>
                                        <span class="title">Bien</span>
                                        <span class="selected"></span>
                                        
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="tipo_movimiento_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="glyphicon glyphicon-retweet  font-blue-ebonyclay"></i>
                                        <span class="title">Tipo de Movimiento</span>
                                        <span class="selected"></span>
                                        
                                        
                                    </a>
                                </li>
                                
                                
                                
                            </ul>
                        </li>
                        
                        
						<?php if  ($nombre_archivo = "personal_list.php") {
						    	   $ClaseMenu='<li class="nav-item start active open">'; 
							  	   $Selecicion='<span class="selected"></span>';
								   }
							  else {
								  	$ClaseMenu='<li class="nav-item">';
							  	  	$Selecicion="";
								   }
						 ?>
                        <li class="nav-item"> 
                            <a href="javascript:;" class="nav-link font-blue-steel">
                                <i class="fa fa-users font-blue-steel"></i>
                                <span class="title">CLIENTES</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                            <li class="nav-item">
                                    <a href="cliente_juridico_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-black-tie font-blue-ebonyclay"></i>
                                        <span class="title">Jur&iacute;dicos</span>
                                        <span class="selected"></span>
                                        
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="cliente_natural_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-leaf font-blue-ebonyclay"></i>
                                        <span class="title">Naturales</span>
                                        <span class="selected"></span>
                                        
                                        
                                    </a>
                                </li>
                                
                                
                                
                                
                            </ul>
                        </li>
                        
                        
                        
                        
                        
                        
                        <li class="nav-item"> 
                            <a href="proveedor_list.php" class="nav-link font-blue-steel">
                                <i class="fa fa-magic font-blue-steel"></i>
                                <span class="title">PROVEEDORES</span>
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
                                    <a href="personal_money.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-money font-blue-ebonyclay"></i>
                                        <span class="title">Sueldos</span>
                                        <span class="selected"></span>
                                        
                                    </a>
                                </li>
                                
                            </ul>
                        </li>
<li class="nav-item"> 
                            <a href="javascript:;" class="nav-link font-blue-steel">
                                <i class="fa fa-balance-scale font-blue-steel"></i>
                                <span class="title">SERVICIOS</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="service_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="glyphicon glyphicon-list-alt font-blue-ebonyclay"></i>
                                        <span class="title">Listado Servicios</span>
                                        <span class="selected"></span>
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="service_pago.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-dollar font-blue-ebonyclay"></i>
                                        <span class="title">Pago de Servicios</span>
                                        <span class="selected"></span>
                                        
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="service.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-gears font-blue-ebonyclay"></i>
                                        <span class="title">Servicios</span>
                                        <span class="selected"></span>
                                        
                                    </a>
                                </li>
                                
                                
                                
                            </ul>
                        </li>
<li class="nav-item"> 
                            <a href="javascript:;" class="nav-link font-blue-steel">
                                <i class="fa fa-sitemap font-blue-steel"></i>
                                <span class="title">CLASIFICACION</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item">
                                    <a href="category_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-th-large font-blue-ebonyclay"></i>
                                        <span class="title">Categorias</span>
                                        <span class="selected"></span>
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="sub_category_list.php" class="nav-link font-blue-ebonyclay">
                                        <i class="fa fa-th-list font-blue-ebonyclay"></i>
                                        <span class="title">Sub Categorias</span>
                                        <span class="selected"></span>
                                        
                                    </a>
                                </li>
                                
                                
                                
                            </ul>
                        </li>
<li class="nav-item"> 
                            <a href="javascript:;" class="nav-link font-blue-steel">
                                <i class="fa fa-pie-chart font-blue-steel"></i>
                                <span class="title">REPORTES</span>
                                <span class="selected"></span>
                                <span class="arrow open"></span>
                            </a>
                            <ul class="sub-menu">
                            
                            
                                
                                
                                
                                
                                
                                
                                
                                
                                
                            </ul>
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
                                
                                
                                
                                
                                
                            </ul>
                        </li>
                    </ul>

                    <!-- END SIDEBAR MENU -->
                </div>
                <!-- END SIDEBAR -->
            </div>
            <!-- END SIDEBAR -->

            <!-- BEGIN CONTENT -->
            <?php if  ($nombre_archivo = "proveedor_cuentas.php") {
						    	   
							  	   $VarUrl2 = $VarUrl;
								   $TituloGeneral2 = $TituloGeneral;
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
                                        <span class="caption-subject <?php echo $Color; ?> bold uppercase"><?php echo $Titulo; ?></span>
                                        <div class="btn-group">
<a class="btn sbold blue <?php echo $EstadoBotonAgregar?>" onClick="abre_ventana('Emergentes/<?php echo $agregar.$VarUrl2?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $NombreBotonAgregar?> <i class="fa fa-plus"></i></a>
</div>
<div class="btn-group">
<a class="btn sbold green" href="javascript:location.reload()"> Actualizar <i class="fa fa-refresh"></i></a>
</div> 
                                    </div>
                                    <div class="actions">
                                        <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"> </a>
                                        
                                    </div>
                                </div>
                                <div class="portlet-body">   