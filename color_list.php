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
  $updateSQL = sprintf("DELETE from color WHERE codigocolor=%s",
                       GetSQLValueString($_POST['codigocolor'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "color_list.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT c.codigocolor as codigocolor, c.nombre_color, codigoprod  FROM color c left join producto p on c.codigocolor=p.codigocolor group by c.codigocolor order by c.codigocolor";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
 $i = 1;

//Titulo e icono de la pagina
$Icono="glyphicon glyphicon-tint ";
$Color="font-blue";
$Titulo="Listado de Colores";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 600;
$popupAlto= 210;

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
          <th  width="85%"> NOMBRE DE COLOR </th>
         
          <th  width="5%">  </th>
          <th  width="5%">  </th>
        </tr>
      </thead>
    <tbody>
      <?php $i = 1; do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigocolor=<?php echo $row_Listado['codigocolor']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['nombre_color']; ?> </a>                                                          </td>
          
          <td> 
            <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Actualizar Registro"  onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigocolor=<?php echo $row_Listado['codigocolor']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="fa fa-refresh" ></i></a>          </td>
          <td>
          <?php 
		  if ($row_Listado['codigoprod'] == NULL) { ?>
            <form method="POST" action="<?php echo $editFormAction; ?>" name="Eliminar_Registro" id="Eliminar_Registro" onSubmit="return confirm('¿ESTA SEGURO QUE DESEA ELIMINAR ESTE COLOR: <?php echo $row_Listado['nombre_color']; ?>?');">
              <input name="codigocolor" id="codigocolor" type="hidden" value="<?php echo $row_Listado['codigocolor']; ?>">
              <button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Eliminar Registro"><i class="glyphicon glyphicon-trash"></i></button>
                              
              <input type="hidden" name="MM_update" value="Eliminar_Registro" />
          </form>
          <?php } 
		  ?>
          </td>
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