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
$query_Listado = sprintf("SELECT a.codprovcue, a.codigobanco, a.titular, a.numero_cuenta, a.tipo_cuenta, a.estado_cuenta, b.ruc, b.razonsocial, c.nombre_banco FROM proveedor_cuentas a  INNER JOIN proveedor b on a.codigoproveedor = b. codigoproveedor INNER JOIN banco c ON a.codigobanco = c.codigobanco WHERE a.codigoproveedor = %s", GetSQLValueString($colname_Listado, "int"));
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);

$colname_Proveedor = "-1";
if (isset($_GET['codigoproveedor'])) {
  $colname_Proveedor = $_GET['codigoproveedor'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Proveedor = sprintf("SELECT * FROM proveedor WHERE codigoproveedor = %s", GetSQLValueString($colname_Proveedor, "int"));
$Proveedor = mysql_query($query_Proveedor, $Ventas) or die(mysql_error());
$row_Proveedor = mysql_fetch_assoc($Proveedor);
$totalRows_Proveedor = mysql_num_rows($Proveedor);
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
          <th  width="5%" class="none"> RUC </th>
          <th  width="5%" class="none"> RAZÓN SOCIAL </th>
          <th  width="30%"> TITULAR CUENTA</th>
          <th  width="25%"> NOMBRE DE BANCO </th>
          <th  width="5%"> N° CUENTA </th>
          <th  width="5%"> TIPO  </th>
          <th  width="5%"> ESTADO </th>
          <th  width="5%" align="center">  </th>
          <th  width="5%" align="center">  </th>
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td> <?php echo $row_Listado['ruc']; ?>                                                           </td>
          <td> <?php echo $row_Listado['razonsocial']; ?></td>
          <td> <a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codprovcue=<?php echo $row_Listado['codprovcue']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['titular']; ?> </a> </td>
          <td> <?php echo $row_Listado['nombre_banco']; ?> </td>
          <td> <?php echo $row_Listado['numero_cuenta']; ?> </td>
          <td> 
      <?php 
      if ($row_Listado['tipo_cuenta'] == "ah") {echo "Ahorro";}
      if ($row_Listado['tipo_cuenta'] == "co") {echo "Corriente";}
      if ($row_Listado['tipo_cuenta'] == "cd") {echo "Certificado de Déposito";}
      if ($row_Listado['tipo_cuenta'] == "ch") {echo "Cheque";}
      ?>
              
           </td>
          <td align="center">  
          <?php 
      if ($row_Listado['estado_cuenta'] == 0) {echo '<dt class="font-blue">Activa</dt>';}
      if ($row_Listado['estado_cuenta'] == 1) {echo '<dt class="font-red">Inactiva</dt>';}
      ?>
           </td>
          
          <td> 
            <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Actualizar Registro"  onClick="abre_ventana('Emergentes/<?php echo $editar?>?codprovcue=<?php echo $row_Listado['codprovcue']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="fa fa-refresh" ></i></a>          </td>
          <td>
            <form method="POST" action="<?php echo $editFormAction; ?>" name="Eliminar_Registro" id="Eliminar_Registro" onSubmit="return confirm('¿ESTA SEGURO QUE DESEA ELIMINAR ESTA CUENTA: <?php echo $row_Listado['numero_cuenta']; ?> - EN EL: <?php echo $row_Listado['banco']; ?> ?');">
              <input name="codprovcue" id="codprovcue" type="hidden" value="<?php echo $row_Listado['codprovcue']; ?>">
              <input name="estado" id="estado" type="hidden" value="1">
              <button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Eliminar Registro"><i class="glyphicon glyphicon-trash"></i></button>
                              
              <input type="hidden" name="MM_update" value="Eliminar_Registro" />
          </form></td>
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

mysql_free_result($Proveedor);
?>