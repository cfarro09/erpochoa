<?php require_once('Connections/Ventas.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_Ventas, $Ventas);
$query_Contador_Clientes = "SELECT count(codigoclienten) AS ContadorCliente FROM cnatural";
$Contador_Clientes = mysql_query($query_Contador_Clientes, $Ventas) or die(mysql_error());
$row_Contador_Clientes = mysql_fetch_assoc($Contador_Clientes);
$totalRows_Contador_Clientes = mysql_num_rows($Contador_Clientes);

mysql_select_db($database_Ventas, $Ventas);
$query_ContadorProveedor = "SELECT count(codigoproveedor) AS ContadorProveedor FROM proveedor";
$ContadorProveedor = mysql_query($query_ContadorProveedor, $Ventas) or die(mysql_error());
$row_ContadorProveedor = mysql_fetch_assoc($ContadorProveedor);
$totalRows_ContadorProveedor = mysql_num_rows($ContadorProveedor);

mysql_select_db($database_Ventas, $Ventas);
$query_ContadorVentas = "SELECT count(codigoventas) AS ContadorVentas FROM ventas";
$ContadorVentas = mysql_query($query_ContadorVentas, $Ventas) or die(mysql_error());
$row_ContadorVentas = mysql_fetch_assoc($ContadorVentas);
$totalRows_ContadorVentas = mysql_num_rows($ContadorVentas);
 
//Titulo e icono de la pagina
$Icono="glyphicon glyphicon-home";
$Color="font-blue";
$Titulo="Pagina Principal";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$EstadoBotonAgregar="disabled";
//$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/cod_gen.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");





?>       

<!-- BEGIN DASHBOARD STATS 1-->
<div class="row">
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat blue">
<div class="visual">
<i class="fa fa-users"></i>
</div>
<div class="details">
<div class="number">
<span data-counter="counterup" data-value="<?php echo $row_Contador_Clientes['ContadorCliente']; ?>">0</span>
</div>
<div class="desc"> CLIENTES </div>
</div>
<a class="more" href="javascript:;"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat red">
<div class="visual">
<i class="fa fa-magic"></i>
</div>
<div class="details">
<div class="number">
<span data-counter="counterup" data-value="<?php echo $row_ContadorProveedor['ContadorProveedor']; ?>"></span></div>
<div class="desc"> PROVEEDORES </div>
</div>
<a class="more" href="javascript:;"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat green">
<div class="visual">
<i class="glyphicon glyphicon-shopping-cart "></i>
</div>
<div class="details">
<div class="number">
<span data-counter="counterup" data-value="<?php echo $row_ContadorVentas['ContadorVentas']; ?>"></span>
</div>
<div class="desc"> VENTAS </div>
</div>
<a class="more" href="ventas_add.php?codigo=<?php echo $_GET['codigo']; ?>"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>

</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="dashboard-stat purple">
<div class="visual">
<i class="fa fa-balance-scale"></i>
</div>
<div class="details">
<div class="number">
<span data-counter="counterup" data-value="89"></span></div>
<div class="desc"> SERVICIOS </div>
</div>
<a class="more" href="javascript:;"> VER MAS
<i class="fa fa-arrow-right"></i>
</a>
</div>
</div>
</div>

<br>
<div class="tiles text-center">
<a href="product_list.php">
<div class="tile bg-green">
<div class="tile-body">
<i class="fa fa-cubes"></i>
</div>
<div class="tile-object">
<div class="name "> Productos </div>
<div class="number"> </div>
</div>
</div>
</a>


<a href="bienes_list.php">
<div class="tile bg-blue-steel">
<div class="tile-body">
<i class="fa fa-building-o"></i>
</div>
<div class="tile-object">
<div class="name"> Bienes </div>
<div class="number">  </div>
</div>
</div>
</a>



<a href="personal_list.php">
<div class="tile bg-green-jungle">
<div class="tile-body">
<i class="glyphicon glyphicon-user"></i>
</div>
<div class="tile-object">
<div class="name"> Personal </div>
<div class="number"> </div>
</div>
</div>
</a>


<a href="cliente_natural_list.php">
<div class="tile bg-yellow-gold">
<div class="tile-body">
<i class="fa fa-pie-chart"></i>
</div>
<div class="tile-object">
<div class="name"> Reportes </div>
<div class="number"> </div>
</div>
</div>
</a>

<a href="category_list.php">
<div class="tile bg-purple-studio">
<div class="tile-body">
<i class="fa fa-sitemap"></i>
</div>
<div class="tile-object">
<div class="name"> Clasificaci&oacute;n </div>
<div class="number">  </div>
</div>
</div>
</a> 
<a href="category_list.php">
<div class="tile bg-yellow-soft">
<div class="tile-body">
<i class="fa fa-cogs"></i>
</div>
<div class="tile-object">
<div class="name"> Manteniminetos </div>
<div class="number">  </div>
</div>
</div>
</a>  



<a href="proforma_add.php?codigo=<?php echo $_GET['codigo']; ?>">
<div class=tile bg-red-thunderbird">
<div class="tile-body">
<i class="fa fa-balance-scale"></i>
</div>
<div class="tile-object">
<div class="name"> Proforma  </div>
<div class="number">  </div>
</div>
</div>
</a> 


<a href="">
<div class="tile bg-red-thunderbird">
<div class="tile-body">
<i class="fa fa-balance-scale"></i>
</div>
<div class="tile-object">
<div class="name"> Configuraciones </div>
<div class="number">  </div>
</div>
</div>
</a> 
</div>



      
                               
<?php 
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>
<?php
mysql_free_result($Contador_Clientes);

mysql_free_result($ContadorProveedor);

mysql_free_result($ContadorVentas);
?>
