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
  $updateSQL = sprintf("UPDATE acceso SET estado=%s WHERE codacceso=%s",
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['codacceso'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "personal_access.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "Eliminar_Registro")) {
  $updateSQL = sprintf("UPDATE personal SET asignacion_acceso=%s WHERE codigopersonal=%s",
                       GetSQLValueString($_POST['asignacion_acceso'], "int"),
                       GetSQLValueString($_POST['codigopersonal'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "personal_access.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT a.codacceso,a.codigopersonal, a.usuario, a.nivel, CONCAT(b.paterno,  ' ', b.materno, ' ', b.nombre) as Personal FROM acceso a INNER JOIN personal b ON a.codigopersonal = b.codigopersonal WHERE a.estado = '0' and b.estado=0";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
 $i = 1;

//Titulo e icono de la pagina
$Icono="glyphicon glyphicon-log-in";
$Color="font-blue";
$Titulo="Acceso Personal";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 600;
$popupAlto= 345;

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
          <th  width="15%"> USUARIO </th>
          <th  width="15%"> NIVEL DE ACCESO</th>
          <th  width="30%"> APELLIDOS Y NOMBRES </th>
          
          <th  width="5%">  </th>
          <th  width="5%">  </th>
          
        </tr>
      </thead>
    <tbody>
      <?php  $i = 1;
      do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codacceso=<?php echo $row_Listado['codacceso']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['usuario']; ?> </a>                                                          </td>
          <td> <?php echo $row_Listado['nivel']; ?></td>
          
          <td><?php echo $row_Listado['Personal']; ?> </td>
          <td> 
            <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Actualizar Registro"  onClick="abre_ventana('Emergentes/<?php echo $editar?>?codacceso=<?php echo $row_Listado['codacceso']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="fa fa-refresh" ></i></a>          </td>
            
            <td> 
            <form method="POST" action="<?php echo $editFormAction; ?>" name="Eliminar_Registro" id="Eliminar_Registro" onSubmit="return confirm('�ESTA SEGURO QUE DESEA ELIMINAR ESTE REGISTRO: <?php echo $row_Listado['usuario']; ?>?');">
<input name="codacceso" id="codacceso" type="hidden" value="<?php echo $row_Listado['codacceso']; ?>">
              <input name="estado" id="estado" type="hidden" value="1">
              <input name="asignacion_acceso" id="asignacion_acceso" type="hidden" value="0">
              <input name="codigopersonal" id="codigopersonal" type="hidden" value="<?php echo $row_Listado['codigopersonal']; ?>">
              <button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Eliminar Registro"><i class="glyphicon glyphicon-trash"></i></button>
                              
              <input type="hidden" name="MM_update" value="Eliminar_Registro" />
          </form> </td>
          
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
?>