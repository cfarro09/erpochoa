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
$query_Listado = "SELECT codcomprobante, codigoventa, count(codcomprobante) as cant_item, sum(cantidad) as cant_articulo, total, igv, subtotal, nombre, paterno, materno FROM detalle_ventas dv inner join ventas v on dv.codcomprobante=v.codigoventa inner join cnatural cn on v.codigoclienten=cn.codigoclienten where codcomprobante!='' group by codcomprobante";

//SELECT a.codigoprod, a.nombre_producto, b.nombre AS Marca, c.nombre AS Categoria, d.nombre AS SubCategoria, e.nombre_color AS Color, f.nombre_presentacion AS Presentacion, max(h.precio_compra), max(h.precio_venta), sum(h.cantidad), h.codigoproveedor FROM producto a INNER JOIN marca b ON a.codigomarca = b.codigomarca INNER JOIN categoria c ON a.codigocat = c.codigocat INNER JOIN subcategoria d ON a.codigosubcat = d.codigosubcat INNER JOIN color e ON a.codigocolor = e.codigocolor INNER JOIN presentacion f ON a.codigopresent = f.codigopresent INNER JOIN historial_producto h ON h.codigoprod=a.codigoprod WHERE a.estado = 0 group by a.codigoprod order by codigoprod

//SELECT a.codigoprod, a.nombre_producto, b.nombre AS Marca, c.nombre AS Categoria, d.nombre AS SubCategoria, e.nombre_color AS Color, f.nombre_presentacion AS Presentacion, h.precio_compra, h.precio_venta, h.cantidad, h.codigoproveedor FROM producto a INNER JOIN marca b ON a.codigomarca = b.codigomarca INNER JOIN categoria c ON a.codigocat = c.codigocat INNER JOIN subcategoria d ON a.codigosubcat = d.codigosubcat INNER JOIN color e ON a.codigocolor = e.codigocolor INNER JOIN presentacion f ON a.codigopresent = f.codigopresent INNER JOIN historial_producto h ON h.codigoprod=a.codigoprod WHERE a.estado = 0
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
 $i = 1;


//para asignar precio y cantidad
$query_Listado1 = "SELECT a.codigoprod, a.nombre_producto, b.codigoprod, max(b.precio_compra) as precio_compra, max(b.precio_venta), sum(b.cantidad) from producto a INNER JOIN historial_producto b ON a.codigoprod = b.codigoprod group by a.codigoprod";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);
 //Enumerar filas de data tablas



//Titulo e icono de la pagina
$Icono="fa fa-building-o";
$Color="font-blue";
$Titulo="Listado de Ventas";
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
          <th  > N&deg; COMPROBANTE</th>
          <th  > MONTO TOTAL</th>
          <th  class="none"> COMPRA </th>
          <th  class="none">SUBTOTAL</th>
          <th  class="none"> IVA </th>
          <th  >CLIENTE</th>
          <th  class="none"> FECHA </th>
          <th  > CANT ITEM </th>
          <th  > CANT ARTICULOS </th>
          
          <th  >  </th>
        
      </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['codigoventa']; ?> </a>                                                          </td>
          <td> <?php echo $row_Listado['total']; ?></td>
          <td><?php  echo "&#36; ".$row_Listado['total'].".00"; ?> </td>
          <td> <?php echo "&#36; ".round($row_Listado['subtotal'],2); ?></td>
          <td> <?php echo "&#36; ".round(($row_Listado['igv']),2); ?></td>
          <td> <?php echo $row_Listado['nombre']." ".$row_Listado['paterno']." ".$row_Listado['materno']; ?></td>
          <td> <?php echo $row_Listado['fecha']; ?></td>
          <td> <?php echo $row_Listado['cant_item']; ?></td>
          <td> <?php echo $row_Listado['cant_articulo']; ?></td>
          

          <td> 
          <a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" onClick="abre_ventana('Emergentes/product_list_cantidad.php?codigoprod=#,<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="glyphicon glyphicon-credit-card" ></i></a>          </td>
          
          
          
                    
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