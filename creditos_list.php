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






if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Anular_Abono")) {
  $updateSQL = sprintf("UPDATE abonos set estadoabono=0 WHERE codigoventa=%s",
                       GetSQLValueString($_POST['codigoventa'], "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());


$updateSQL = sprintf("UPDATE producto_stock INNER JOIN detalle_ventas ON producto_stock.codigoprod = detalle_ventas.codigoprod SET producto_stock.stock = producto_stock.stock+detalle_ventas.cantidad where detalle_ventas.codcomprobante=%s",
                          GetSQLValueString($_POST['codigoventa'], "text"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());





  $updateGoTo = "abonos_list.php";
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
$query_Listado = "select cr.codigocredito, cr.codigoventa, cr.codigocomprobante, cr.cantcuota as totalcuotas, cr.fecha_emision, cr.total, sum(cr.monto_rec) as monto_rec, CONCAT (c.paterno,' ', c.materno, ' ', c.nombre) as cliente,c.cedula as cedulac, CONCAT (p.paterno,' ', p.materno, ' ', p.nombre) as personal, c.codigoclienten, cr.metodopago, cr.tea, cr.monto_rec, cr.totalcredito, cr.cuotames, a.fecha_venc, max(pg.ncuotapag) as ncuotapag, min(pg.saldo) as saldo, max(pg.totalpagocr) as totalpagocr from credito cr left join cnatural c on c.codigoclienten=cr.codigoclienten left join personal p on p.codigopersonal=cr.codigopersonal left join cronogramacredito a on a.codigocredito=cr.codigocredito left join pagocredito pg on pg.codigocredito=cr.codigocredito GROUP BY cr.codigoventa order by cr.codigocredito";


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
$Titulo="Listado de Creditos";
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
          <th  >CODIGO</th>
          <th  > FECHA </th>
<th  >CLIENTE / RAZON SOCIAL</th>
<th  >T. CREDITO</th>
<th class="none" > VENTA</th>
<th class="none" > TEA</th>
<th class="none" > C. CUOTAS</th>

<th class="none" > INICIAL</th>
<th class="none" > CUOTA</th>
<th class="none" > C. PAGADAS</th>

<th  > ABONO</th>
<th  > SALDO</th>
       
         
          
          <th class="none"> Vendedor </th>
          
          <th  >  </th>
          <th  >  </th>
        <th  >  </th>
        <th  >  </th>
      </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
        <?php 
    		$UrlImprimir= "abono_ventas.php";
		?>
        
        
          <td> <?php echo $i; ?> </td>
          <td align="center"><?php echo round($row_Listado['codigocredito'],4); ?>               </td>
<td> <?php echo $row_Listado['fecha_emision']; ?></td>          
<td> <?php echo $row_Listado['cliente'];?></td>
          
<td> <?php echo "&#36; ".$row_Listado['totalcredito']; ?></td>          
<td> <?php echo "&#36; ".$row_Listado['total']; ?></td>

<td> <?php echo $row_Listado['tea']."%"; ?></td>
<td> <?php echo $row_Listado['totalcuotas']; ?></td>
          <td> <?php echo "&#36; ".round($row_Listado['monto_rec'],2); ?></td>
          <td> <?php echo $row_Listado['cuotames']; ?></td>
          <td> <?php if ($row_Listado['ncuotapag']==NULL) echo 0; else echo $row_Listado['ncuotapag']; ?></td>
          <td> <?php if($row_Listado['totalpagocr']==NULL) echo "&#36; 0.00"; else echo "&#36; ".$row_Listado['totalpagocr']; ?></td>
          <td> <?php if ($row_Listado['ncuotapag']==NULL) echo $row_Listado['totalcredito']; else echo ($row_Listado['saldo']); ?></td>
      
          <td> <?php echo $row_Listado['personal']; ?></td>
             
<td> 
          
          <a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Cronograma Pagos - Pagos" href="Imprimir/cronogramacreditos.php?codigocredito=<?php echo $row_Listado['codigocredito']; ?>&codigo=<?php echo $row_Listado['codigoventa']; ?>" target="new"><i class="glyphicon glyphicon-credit-card" ></i></a></td>
        <td>
          <?php
            if($row_Listado['ncuotapag']!=NULL) { ?>
           <a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Recibo de Pagos Credito" href="Imprimir/pagocredito.php?codigocredito=<?php echo $row_Listado['codigocredito']; ?>&codigo=<?php echo $row_Listado['codigoventa']; ?>" target="new"><i class="glyphicon glyphicon-credit-card" ></i></a>
           <?php } ?>

            </td>
         <td> 
            <?php 
            if ($row_Listado['saldo']>0  || $row_Listado['ncuotapag']==NULL){ ?>
            <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Registrar Credito"  onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigocredito=<?php echo $row_Listado['codigocredito']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="fa fa-refresh" ></i></a>          </td>
          <?php } ?>
          
            
             
<!--
             <td> 
                <?php 
         if ($saldo>0) { ?>
<form method="POST" action="<?php echo $editFormAction; ?>" name="Anular_Abono" id="Anular_Abono" onSubmit="return confirm('¿ESTA SEGURO QUE DESEA ANULAR ESTE ABONONO N°: <?php echo substr($row_Listado['codigoabono'], -4).' del cliente '.$row_Listado['cliente']; ?>?');">
              
              <input name="codigoventa" id="codigoventa" type="hidden" value="<?php echo $row_Listado['codigoventa']; ?>">
              <button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Anular_Abono"><i class="glyphicon glyphicon-trash"></i></button>
                              
              <input type="hidden" name="MM_update" value="Anular_Abono" />
          </form>
            <?php } 
      ?>
          </td>
-->

  <td> 
    <?php 
            if ($row_Listado['saldo']<=0 and $row_Listado['saldo']<>NULL){ ?>

          <a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" href="Imprimir/factura_credito.php?codigo=<?php echo $row_Listado['codigoventa']; ?>" target="new"><i class="glyphicon glyphicon-credit-card" ></i></a></td>
         <?php } ?>
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