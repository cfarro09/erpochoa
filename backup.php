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
 
//Titulo e icono de la pagina
$Icono="glyphicon glyphicon-home";
$Color="font-blue";
$Titulo="Copias de Seguridad	";
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
//include("generar_json_productos.php");
?>       

<div class="portlet-body">
                                    <div class="tabbable-custom nav-justified">
                                        <ul class="nav nav-tabs nav-justified">
                                            <li class="active">
                                                <a href="#tab_1_1_1" data-toggle="tab"> Generar Copia </a>
                                            </li>
                                            <li>
                                                <a href="#tab_1_1_2" data-toggle="tab"> Restablecer Copia </a>
                                            </li>
                                            
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_1_1_1">
<a href="bd/backup.php">Clic Generar Backup</a>
                                            </div>
                                            <div class="tab-pane" id="tab_1_1_2">
dd
                                            </div>
                                            
                                        </div>
                                    </div>



      
                               
<?php 
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>
<?php
mysql_free_result($Contador_Clientes);

mysql_free_result($ContadorProveedor);
?>
