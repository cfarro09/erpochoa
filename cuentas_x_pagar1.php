<?php require_once('Connections/Ventas.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

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
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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
 // $updateSQL = sprintf("UPDATE proveedor SET estado=%s WHERE codigoproveedor=%s",
 //                      GetSQLValueString($_POST['estado'], "text"),
 //                      GetSQLValueString($_POST['codigoproveedor'], "int"));

$codproveedor=$_POST['codigoproveedor'];
$total = mysql_num_rows(mysql_query("SELECT codigoproveedor FROM proveedor_cuentas WHERE codigoproveedor='$codproveedor'"));
$total1 = mysql_num_rows(mysql_query("SELECT codigoproveedor FROM historial_producto WHERE codigoproveedor='$codproveedor'"));
if ($total>=1 || $total1>=1)
{ echo "<script language='javascript'>"; 
echo "alert('Error!! NO SE PUEDE ELIMINAR EL REGISTRO PORQUE PUEDE TENER NUMERO DE CUENTA ACTIVO O TENER ARTICULOS REGISTRADOS ')"; 
echo "</script>";  

}
else
{
$updateSQL = sprintf("DELETE FROM proveedor WHERE codigoproveedor=%s",
  						GetSQLValueString($_POST['codigoproveedor'], "int"));


  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "proveedor_list.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}}
//------------Fin Actualizar(Eliminar) Registro----------------
//------------Inicio Juego de Registro "Listado"----------------
mysql_select_db($database_Ventas, $Ventas);

$query_Listado = "SELECT p.codigoproveedor, p.ruc, p.razonsocial,
  sum(rc.total) as totalrc, sum(e.precioestibador_soles) as totale, sum(preciotransp_soles) as totalt, sum(preciond_soles) as totalnd, sum(precionc_soles) as totalnc ,
  
  (select sum(dess.cantidad) from desposeproveedor dess where dess.motivo = 'cuentasxpagar' and dess.proveedor = p.codigoproveedor) as abonodespose, sum(rc.montoochoa) as abonorc, sum(e.montoochoa) as abonoe, sum(t.montoochoa) as abonot, sum(nd.montoochoa) as abonond, sum(nc.precionc_soles) as abononc 
  FROM proveedor p
  LEFT JOIN registro_compras rc on rc.rucproveedor = p.ruc 
  LEFT JOIN transporte_compra t on t.ructransporte = p.ruc
  LEFT JOIN estibador_compra e on e.rucestibador = p.ruc
  LEFT JOIN notadebito_compra nd on nd.rucnd = p.ruc
  LEFT JOIN notacredito_compra nc on nc.rucnotacredito = p.ruc

  


  WHERE p.estado = '0' 
  GROUP BY p.ruc ";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$rr = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
//------------Fin Juego de Registro "Listado"----------------
 //Enumerar filas de data tablas
 $i = 1;

//Titulo e icono de la pagina
$Icono="fa fa-magic";
$Color="font-blue";
$Titulo="Listado de Proveedores";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 545;

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
        <th width="5%"> N&deg; </th>
          <th  width="10%"> RUC </th>
          <th  width="30%"> RAZ�N SOCIAL </th>
          
          <th  width="20%"> CARGOS </th>
          <th  width="20%"> ABONOS </th>
          <th  width="10%"> SALDO </th>
          <th  width="5%">  Est. Cuenta</th>

        </tr>
      </thead>
    <tbody>
      <?php do { 
        $totalx = $rr["totalrc"] + $rr["totale"] + $rr["totalt"] + $rr["totalnd"] + $rr["totalnc"];
        $abono = $rr["abonorc"] + $rr["abonoe"] + $rr["abonot"] + $rr["abonond"] + $rr["abononc"] + $rr["abonodespose"];
        $saldo = $totalx - $abono;
        $cargo = number_format($totalx, 2, '.', '');
        $saldo = number_format($saldo, 2, '.', '');
        $abono = number_format($abono, 2, '.', '');
        ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoproveedor=<?php echo $rr['codigoproveedor']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $rr['ruc']; ?> </a>                                                          </td>
          <td> <?php echo $rr['razonsocial']; ?></td>
          <td class="text-right"><?= $abono ?></td>
          <td class="text-right"><?= $cargo ?></td>
          
          
          <td class="text-right"><?= $saldo ?></td>
          <td> <a href="proveedor_comprobante.php?codigoproveedor=<?php echo $rr['ruc']; ?>" class="btn yellow-casablanca tooltips" data-placement="top" data-original-title="Registro Comprobantes"><i class="glyphicon glyphicon-credit-card" ></i></a>
           </td>
           
        </tr>
        <?php $i++; } while ($rr = mysql_fetch_assoc($Listado)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>
<?php 
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);
?>