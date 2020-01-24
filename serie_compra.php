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

//------------Inicio Juego de Registro "Listado"----------------
$colname_Listado = "-1";
if (isset($_GET['numero'])) {
  $colname_Listado = $_GET['numero'];
  $colname_Proveedor = $_GET['codigoproveedor'];
  $colname_Cantidad = $_GET['cantarticulo'];
  $colname_Item = $_GET['cantitem'];
  $colname_preciocompra = number_format($_GET['preciocompra'],2);
  
}
mysql_select_db($database_Ventas, $Ventas);

$query_Listado = sprintf("SELECT * FROM historial_producto hp inner join producto p on p.codigoprod=hp.codigoprod WHERE numero= '$colname_Listado'");
                          
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);









mysql_select_db($database_Ventas, $Ventas);

$query_Listado1 = sprintf("SELECT * FROM proveedor WHERE codigoproveedor= '$colname_Proveedor'");
                          
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);










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
      <td width="15%"><?php echo $row_Listado1['ruc']; ?></td>
      <td width="5%">Razon Social:</td>
      <td width="10%"><?php echo $row_Listado1['razonsocial']; ?></td>
      <td width="5%">Direccion:</td>
      <td width="15%"><?php echo $row_Listado1['direccion']; ?></td>
      <td width="5%">FAX</td>
      <td width="10%"><?php echo $row_Listado1['fax']; ?></td>
    </tr>

    
   

     <tr>
      <td>FACTURA</td>
      <td><?php echo $row_Listado['numero']; ?></td>
      <td>SUB TOTAL</td>
      <td><?php echo number_format($colname_preciocompra/$IGV1,2); ?></td>
      <td>IGV</td>
      <td><?php echo number_format($colname_preciocompra-$colname_preciocompra/$IGV1,2); ?></td>
      <td>TOTAL</td>
      <td><?php echo $colname_preciocompra; ?></td>
    </tr>

  </table>
  <form>
  <table border="1" width="100%">
  <tr>
  		<td width="3%">N°</td>
        <td width="7%">Codigo</td>
    	<td width="50%">Producto</td>
    	<td width="5%">Precio Compra</td>
    	<td width="15%">Numero Serie</td>
    	<td width="15%">CUN</td>
    	<td width="5%">Meses Garantia</td>
  </tr>
  <tr>
  
<?php 
$cont=0;
if ($totalRows_Listado > 0) { // Show if recordset not empty 
	$cantidad= $row_Listado['cantidad'];
	for($i=0;$i<$cantidad;$i++){
		$cont++;
		$codigoprod= $row_Listado['codigoprod'];
		$producto= $row_Listado['nombre_producto'];
		$pcompra= $row_Listado['precio_compra'];
		echo ("<td>".$cont."</td>");
		echo ("<td>".$codigoprod."</td>");
		echo ("<td>".$producto."</td>");
		echo ("<td>".$pcompra."</td>");?>
        <td><input type="text" name="nserie" id="nserie" placeholder="Numero Serie" /></td>
        <td><input type="text" name="cun" id="cun" placeholder="Numero CUN" /></td>
        <td><input type="text" name="mesg" id="mesg" placeholder="Meses Garantia" width="5" /></td>
        </tr>
		<?php 
	}
	
	
	while($fila = mysql_fetch_array($Listado))
	{
		$cantidad= $fila['cantidad'];
		for($i=0;$i<$cantidad;$i++){
			$cont++;
			$codigoprod= $fila['codigoprod'];
			$producto= $fila['nombre_producto'];
			$pcompra= $fila['precio_compra'];
			echo ("<tr><td>".$cont."</td>");
			echo ("<td>".$codigoprod."</td>");
			echo ("<td>".$producto."</td>");
			echo ("<td>".$pcompra."</td>");?>
        	<td><input type="text" name="nserie" id="nserie" placeholder="Numero Serie" /></td>
        	<td><input type="text" name="cun" id="cun" placeholder="Numero CUN" /></td>
        	<td><input type="text" name="mesg" id="mesg" placeholder="Meses Garantia" width="5" /></td>
        	</tr>
        <?php 
		}
	}
   } // Show if recordset not empty ?>
   </table>
   </form>
<?php 
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);

//mysql_free_result($Proveedor);
?>