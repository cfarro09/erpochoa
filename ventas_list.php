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



if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Anular_Factura")) {
  $updateSQL = sprintf("UPDATE ventas set estadofact=0 WHERE codigoventas=%s",
                       GetSQLValueString($_POST['codigoventas'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());


$updateSQL = sprintf("UPDATE producto_stock INNER JOIN detalle_ventas ON producto_stock.codigoprod = detalle_ventas.codigoprod SET producto_stock.stock = producto_stock.stock+detalle_ventas.cantidad where detalle_ventas.codcomprobante=%s",
                          GetSQLValueString($_POST['codigoventa'], "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());





  $updateGoTo = "ventas_list.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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
$query_Listado = "SELECT v.codigoventas, v.codigo, v.tipocomprobante, v.fecha_emision, dv.codcomprobante, v.codigoventa, count(dv.codcomprobante) as cant_item, sum(dv.cantidad) as cant_articulo, v.total, v.igv, v.subtotal, cn.cedula, CONCAT (cn.paterno,' ', cn.nombre) AS ClienteN, cj.razonsocial AS ClienteJ, CONCAT (p.paterno,' ', p.materno, ' ', p.nombre) AS Vendedor, v.codigoventa, v.estadofact FROM detalle_ventas dv inner join ventas v on dv.codcomprobante=v.codigoventa left join cnatural cn on v.codigoclienten=cn.codigoclienten left join cjuridico cj on v.codigoclientej=cj.codigoclientej inner join personal p on p.codigopersonal=v.codigopersonal where dv.codcomprobante!='' and (v.tipocomprobante='fac' or v.tipocomprobante='bol') group by dv.codcomprobante Order by v.codigoventas DESC";

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
          <th  > FECHA </th>
<th  > TOTAL</th>
          <th  class="none"> CEDULA </th>
          <th  class="none"> COMPRA </th>
          <th  class="none">SUBTOTAL</th>
          <th  class="none"> IVA </th>
          <th  >CLIENTE / RAZON SOCIAL</th>
          
          <th  class="none"> Vendedor </th>
          <th  class="none"> CANT. ITEM'S </th>
          <th  class="none"> CANT. ARTICULOS </th>
          
          <th  >  </th>
        <th  >  </th>
      </tr>
      </thead>
    <tbody>
      <?php do { ?>

   

        <tr>
        <?php 
		if ($row_Listado['tipocomprobante']=='fac')
		{
			$UrlImprimir= "factura_ventas.php";
			
			}
		else if ($row_Listado['tipocomprobante']=='bol')
		{
			$UrlImprimir= "boleta_ventas.php";
			
			}
?>
        
 
          <td>  <?php echo $i; ?> </td>
          <td align="center"><?php echo $row_Listado['tipocomprobante'].substr($row_Listado['codigoventas'], -4); ?>               </td>
<td> <?php echo $row_Listado['fecha_emision']; ?></td>          
<td> <?php echo "&#36; ".$row_Listado['total']; ?></td>
          <td><?php  echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".$row_Listado['cedula']; ?> </td>
          <td><?php  echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; "." &#36; ".$row_Listado['total']; ?> </td>
          <td> <?php echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; "." &#36; ".round($row_Listado['subtotal'],2); ?></td>
          <td> <?php echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; "." &#36; ".round(($row_Listado['igv']),2); ?></td>
          <td> <?php echo $row_Listado['ClienteN'];?><?php echo $row_Listado['ClienteJ']; ?></td>
          <?php if ($row_Listado['ClienteN']==NULL)
		  			$tipocliente='j';
					else
					$tipocliente='n';?>
          
          <td> <?php echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".$row_Listado['Vendedor']; ?></td>
          <td> <?php echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".$row_Listado['cant_item']; ?></td>
          <td> <?php echo $row_Listado['cant_articulo']; ?></td>
          

          <td> 
            <?php 
         if ($row_Listado['estadofact'] == 1) { ?>
          <a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" href="Imprimir/<?php echo $UrlImprimir?>?codigoventas=<?php echo $row_Listado['codigoventas']; ?>&codigo=<?php echo $row_Listado['codigo']; ?>&tipocliente=<?php echo $tipocliente; ?>" target="new"><i class="glyphicon glyphicon-credit-card" ></i></a>          </td>

           <?php } 
      ?>
        
          <?php 
         if ($row_Listado['estadofact'] == 2) { ?>
          <a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" href="Imprimir/factura_abono.php?codigo=<?php echo $row_Listado['codigoventa']; ?>" target="new"><i class="glyphicon glyphicon-credit-card" ></i></a>          </td>
                  
           <?php } 
      ?>

                    
            </td>
            
            
             <td> 
               <?php 
         if ($row_Listado['estadofact'] == 1) { ?>
<form method="POST" action="<?php echo $editFormAction; ?>" name="Anular_Factura" id="Anular_Factura" onSubmit="return confirm('¿ESTA SEGURO QUE DESEA ANULAR ESTA FACTURA N°: <?php echo $row_Listado['tipocomprobante'].substr($row_Listado['codigoventas'], -4).' del cliente '.$row_Listado['ClienteN']; ?>?');">
              <input name="codigoventas" id="codigoventas" type="hidden" value="<?php echo $row_Listado['codigoventas']; ?>">
              <input name="codigoventa" id="codigoventa" type="hidden" value="<?php echo $row_Listado['codigoventa']; ?>">
              <button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Anular_Factura"><i class="glyphicon glyphicon-trash"></i></button>
                              
              <input type="hidden" name="MM_update" value="Anular_Factura" />
          </form>
            <?php } 
      ?>
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