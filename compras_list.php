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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Eliminar_Registro")) {
  $updateSQL = sprintf("UPDATE producto SET estado=%s WHERE codigoprod=%s",
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['codigoprod'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "product_list.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT c.codigocompras, c.codigocompra, numerofactura, montofact as precio_compra, razonsocial, p.codigoproveedor as codigoproveedor, fecha_emision FROM compras c inner join proveedor p on c.codigoproveedor=p.codigoproveedor group by codigocompras";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
 $i = 1;


//para asignar precio y cantidad
$query_Listado1 = "SELECT a.codigoprod, a.nombre_producto from producto a INNER JOIN detalle_compras b ON a.codigoprod = b.codigoprod group by a.codigoprod";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);
 //Enumerar filas de data tablas



//Titulo e icono de la pagina
$Icono="fa fa-building-o";
$Color="font-blue";
$Titulo="Historial de Compras";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="disabled";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 525;

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
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
          <th  > N&deg; </th>
          <th  > N° COMPROBANTE</th>
          <th  > M. TOTAL</th>
          <th  class="none"> COMPRA </th>
          <th  class="none">SUBTOTAL</th>
          <th  class="none"> IVA </th>
          <th  > PROVEEDOR </th>
          <th  > FECHA </th>
          
          <th  > IMPRIMIR </th>
      </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['numerofactura']; ?> </a>                                                          </td>
          <td> <?php 
		  $preciocompra=$row_Listado['precio_compra'];
		  echo number_format($row_Listado['precio_compra'],2); ?></td>
          <td><?php  echo "&#36; ".number_format($row_Listado['precio_compra'],2); ?> </td>
          <td> <?php echo "&#36; ".number_format($row_Listado['precio_compra']/1.14,2); ?></td>
          <td> <?php echo "&#36; ".number_format(($row_Listado['precio_compra']-number_format($row_Listado['precio_compra']/1.14,2)),2); ?></td>
          <td> <?php echo $row_Listado['razonsocial']; ?></td>
          <td> <?php echo $row_Listado['fecha_emision']; ?></td>
        
          

          <td> 
                  

<a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" href="Imprimir/factura_compras.php?codigocompras=<?php echo $row_Listado['codigocompras']; ?>&codigo=<?php echo $row_Listado['codigocompra']; ?>" target="new"><i class="glyphicon glyphicon-credit-card" ></i></a>
          </td>
          
          
          
                    
            </td>
            
   
           
        </tr>
        <?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
        
        
        
        
        
        
        
        
        
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>
<?php 
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);
?>