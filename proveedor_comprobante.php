<head></head>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
//------------Inicio Actualizar(Eliminar) Registro----------------
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Eliminar_Registro")) {
  $updateSQL = sprintf("UPDATE proveedor_cuentas SET estado=%s WHERE codprovcue=%s",
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['codprovcue'], "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "proveedor_cuentas.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
//------------Fin Actualizar(Eliminar) Registro----------------
//------------Inicio Juego de Registro "Listado"----------------
$colname_Listado = "-1";
if (isset($_GET['codigoproveedor'])) {
  $colname_Listado = $_GET['codigoproveedor'];
}
mysql_select_db($database_Ventas, $Ventas);
//$query_Listado = sprintf("SELECT a.codprovcue, a.banco, a.titular, a.numero_cuenta, a.tipo_cuenta, a.estado_cuenta, b.ruc, b.razonsocial FROM proveedor_cuentas a INNER JOIN proveedor b on a.codigoproveedor = b. codigoproveedor WHERE a.codigoproveedor = %s", GetSQLValueString($colname_Listado, "int"));
$query_Listado = sprintf("SELECT a.codigoproveedor, a.razonsocial, b.comprobante, b.numero, sum(b.precio_compra) as suma, sum(b.cantidad) as cantidad1, count(b.cantidad) as cantidad, b.fecha, c.igv as igv FROM proveedor a INNER JOIN historial_producto b on a.codigoproveedor = b.codigoproveedor left join comprobante_cpmpra c on c.numero=b.numero where a.codigoproveedor= $colname_Listado group by b.numero, b.comprobante");
                          
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
/*
$colname_Proveedor = "-1";
if (isset($_GET['codigoproveedor'])) {
  $colname_Proveedor = $_GET['codigoproveedor'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Proveedor = sprintf("SELECT a.codigoproveedor, a.numero, b.igv as igv FROM historial_producto a left JOIN comprobante_cpmpra b on a.numero = b.numero where a.codigoproveedor= $colname_Listado");
$Proveedor = mysql_query($query_Proveedor, $Ventas) or die(mysql_error());
$row_Proveedor = mysql_fetch_assoc($Proveedor);
$totalRows_Proveedor = mysql_num_rows($Proveedor);*/

//------------Fin Juego de Registro "Listado"----------------
 //Enumerar filas de data tablas
 $i = 1;

//Titulo e icono de la pagina
$Icono="glyphicon glyphicon-credit-card";
$Color="font-blue";

$VarUrl= "?codigoproveedor=".$row_Proveedor['codigoproveedor'];
$TituloGeneral='<div class="page-title"><h1 class="font-red-thunderbird">PROVEEDOR: '.$row_Proveedor['razonsocial'].' - '.$row_Proveedor['ruc'].'</h1></div>';
$Titulo= "Cuentas Proveedor"; 
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 475;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//________________________________________________________________________________________________________________
?>        
<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty ?>
  <div class="alert alert-danger">
    <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
    
    
  </div>
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty ?>


  <table width="700" class="table table-striped table-bordered table-hover dt-responsive" id="sample_1">
   
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
          <th  width="5%" > COMPROBANTE </th>
          <th  width="5%" > FECHA </th>
          <th  width="30%"> NUMERO</th>
          <th  width="25%"> CANTIDAD ARTICULOS </th>
          <th  width="5%" class="none"> CANTIDAD ARTICULOS EN COMPROBANTE </th>
          <th  width="5%" > SUMA DE COMPROBANTE  </th>
          <th  width="5%"> ESTADO </th>
          <th  width="5%" align="center">  </th>
          <th  width="5%" align="center">  </th>
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td> <?php echo $row_Listado['comprobante']; ?>                                                           </td>
          <td> <?php echo $row_Listado['fecha']; ?></td>
          <td> <?php echo $row_Listado['numero']; ?></td>
          <td> <?php echo $row_Listado['cantidad']; ?> </td>
          <td> <?php echo $row_Listado['cantidad1']; ?> </td>
          <td> 
		  <?php echo $row_Listado['suma']; ?>
              
           </td>
          <td align="center">  
          <?php 
		  if ($row_Listado['igv'] > 0) 
		  		echo '<dt class="font-blue">Conforme</dt>';
		  else
		  		echo '<dt class="font-red">Falta Asignar Datos</dt>';
		  ?>
           </td>
          
          <td> 
           <?php 
		  if ($row_Listado['igv'] == NULL) { ?> 
            <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Registrar Comprobante"  onClick="abre_ventana('Emergentes/<?php echo $editar?>?numero=<?php echo $row_Listado['numero']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="fa fa-refresh" ></i></a>          </td>
           <?php } ?>
           
           
          <td>
           <a href="proveedor_reporte.php?codigonumero=<?php echo $row_Listado['numero']; ?>" class="btn yellow-casablanca tooltips" data-placement="top" data-original-title="Bancos y Cuentas"><i class="glyphicon glyphicon-credit-card" ></i></a>          </td>
           
            
         
        </tr>
        <?php $i++; } while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>
<?php 
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);

//mysql_free_result($Proveedor);
?>