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
                       GetSQLValueString($_POST['codprovcue'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "proveedor_reporte.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
//------------Fin Actualizar(Eliminar) Registro----------------
//------------Inicio Juego de Registro "Listado"----------------
$colname_Listado = "-1";
if (isset($_GET['numero'])) {
  $colname_Listado = $_GET['numero'];
}
mysql_select_db($database_Ventas, $Ventas);
//$query_Listado = sprintf("SELECT a.codprovcue, a.banco, a.titular, a.numero_cuenta, a.tipo_cuenta, a.estado_cuenta, b.ruc, b.razonsocial FROM proveedor_cuentas a INNER JOIN proveedor b on a.codigoproveedor = b. codigoproveedor WHERE a.codigoproveedor = %s", GetSQLValueString($colname_Listado, "int"));
$query_Listado = sprintf("SELECT a.codigoproveedor, a.razonsocial, a.ruc, a.direccion, a.contacto, a.fax, a.paginaweb, a.ciudad, b.comprobante, b.numero, b.precio_compra, b.precio_compra AS total, b.cantidad, b.fecha, b.detalle_producto, c.nombre_producto, b.codigoprod, b.pcomprasiniva, b.pcomprasiniva AS sub_total, b.ivaart_ind, b.ivaart_ind AS igv, b.preciototalc FROM proveedor a INNER JOIN historial_producto b ON a.codigoproveedor = b.codigoproveedor
INNER JOIN producto c ON c.codigoprod = b.codigoprod
WHERE b.numero= '$colname_Listado'");
                          
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

$Titulo= "Reporte de Comprobante"; 
$NombreBotonAgregar="EXPORTAR PDF"; 

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

<?php 
if ($totalRows_Listado == 0) { // Show if recordset empty ?>
  <div class="alert alert-danger">
    <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
    
    
  </div>
  <?php } // Show if recordset empty 
 
  ?>
  
  
  
  <p>
  
<table width="100%" border="1"  id="sample_1">
    <tr>
      <td colspan="8">DATOS DEL PROVEEDOR</td>
    </tr>
    <tr>
      <td width="5%">RUC:</td>
      <td width="15%"><?php echo $row_Listado['ruc']; ?></td>
      <td width="5%">Razon Social:</td>
      <td width="10%"><?php echo $row_Listado['razonsocial']; ?></td>
      <td width="5%">Direccion:</td>
      <td width="15%"><?php echo $row_Listado['direccion']; ?></td>
      <td width="5%">FAX</td>
      <td width="10%"><?php echo $row_Listado['fax']; ?></td>
    </tr>

    
     <tr>
      <td>Pagina Web</td>
      <td><?php echo $row_Listado['paginaweb']; ?></td>
      <td>Ciudad</td>
      <td><?php echo $row_Listado['ciudad']; ?></td>
      <td>Vendedor</td>
      <td><?php echo $row_Listado['contacto']; ?></td>
      <td>Recibe Producto</td>
      <td><?php echo ("."); ?></td>
    </tr>
    
    <tr>
      <td colspan="8">DATOS DEL COMPRADOR</td>
    </tr>
   
    <tr>
      <td>CLIENTE</td>
      <td>PC PLUS</td>
      <td>RUC</td>
      <td>0703245415001</td>
      <td>FECHA EMISION:</td>
      <td><?php echo $row_Listado['direccion']; ?></td>
      <td>FECHA DE REGISTRO</td>
      <td><?php echo $row_Listado['fecha']; ?></td>
    </tr>
     <tr>
      <td>FACTURA</td>
      <td><?php echo $row_Listado['numero']; ?></td>
      <td>SUB TOTAL</td>
      <td><?php echo number_format($row_Listado['sub_total'],2); ?></td>
      <td>IGV</td>
      <td><?php echo number_format($row_Listado['igv'],2); ?></td>
      <td>TOTAL</td>
      <td><?php echo number_format($row_Listado['total'],2); ?></td>
    </tr>

  </table>
  
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty ?>

     
  <table width="100%" border="1">
   
    <thead>
      <tr>
        <th width="5%" > N&deg; </th>
          <th width="5%" > CANTIDAD </th>
          <th  width="60%" > <center>DESCRIPCION</center> </th>
          <th  width="10%" align="center"> VALOR UND</th>
          <th  width="5%" align="center"> IVA</th>
          <th  width="15%" align="center"> VALOR UND C/IVA</th>
          <th  width="15%" align="center"> TOTAL </th>
          
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td> <?php echo $row_Listado['cantidad']; ?>                                                           </td>
          <td> <?php echo $row_Listado['nombre_producto']; ?></td>
          <td> <?php echo number_format($row_Listado['pcomprasiniva'],2); ?></td>
          <td> <?php echo number_format($row_Listado['ivaart_ind'],2); ?></td>
          <td> <?php echo number_format($row_Listado['precio_compra'],2); ?></td>
          <td> <?php echo number_format($row_Listado['preciototalc'],2); ?> </td>
          
          
         
          
         
        
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