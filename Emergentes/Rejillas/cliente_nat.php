<?php require_once('../../Connections/Ventas.php'); ?>
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

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT * FROM cnatural WHERE estado = '0'";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 
$Icono="fa fa-leaf";
$Color="font-blue";
$Titulo="Clientes Naturales";
include("../Fragmentos/cabecerarejilla.php");
 $i = 1;
?>
<script>
function Cliente(pref){
    opener.document.Ingresar.persona.value = pref
    window.close()
}
</script> 
<script>
function Juridico(pref2){
    opener.document.Ingresar.codigoclientej.value = pref2
    window.close()
}
</script> 
<script>
function Natural(pref3){
    opener.document.Ingresar.codigoclienten.value = pref3
    window.close()
}
</script> 

<?php if ($totalRows_Listado == 0) { // Show if recordset empty ?>
  <div class="alert alert-danger">
    <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
    
    
  </div>
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty ?>
  <table class="table table-striped table-bordered table-hover order-column" id="sample_1">
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
          <th  width="15%"> CEDULA </th>
          <th  width="25%"> PATERNO </th>
          <th  width="25%"> MATERNO </th>
          <th  width="30%"> NOMBRES </th>

          
      </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a  class="tooltips" data-placement="bottom" data-original-title="Utilizar Registro" onclick="Cliente('<?php echo $row_Listado['paterno']; ?> <?php echo $row_Listado['materno']; ?> <?php echo $row_Listado['nombre']; ?>'),Natural('<?php echo $row_Listado['codigoclienten']; ?>'),Juridico('0')"> <?php echo $row_Listado['cedula']; ?> </a>                                                          </td>
          <td> <?php echo $row_Listado['paterno']; ?></td>
          <td> <?php echo $row_Listado['materno']; ?> </td>
          <td> <?php echo $row_Listado['nombre']; ?> </td>

        </tr>
        <?php $i++; } while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>




<?php include("../Fragmentos/pierejilla.php"); 
mysql_free_result($Listado);
?>
