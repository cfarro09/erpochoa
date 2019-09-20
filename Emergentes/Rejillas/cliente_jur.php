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
$query_Listado = "SELECT * FROM cjuridico WHERE estado = estado";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 
$Icono="fa fa-black-tie";
$Color="font-blue";
$Titulo="Clientes Jurídicos";
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
          <th  width="20%"> RUC </th>
          <th  width="45%"> RAZON SOCIAL </th>
          <th  width="30%"> E-MAIL </th>


          
      </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a  class="tooltips" data-placement="bottom" data-original-title="Utilizar Registro" onclick="Cliente('<?php echo $row_Listado['razonsocial']; ?> '),Natural('0'),Juridico('<?php echo $row_Listado['codigoclientej']; ?>')"> <?php echo $row_Listado['ruc']; ?> </a>                                                          </td>
          <td> <?php echo $row_Listado['razonsocial']; ?></td>
          <td> <?php echo $row_Listado['email']; ?> </td>


        </tr>
        <?php $i++; } while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>




<?php include("../Fragmentos/pierejilla.php"); 
mysql_free_result($Listado);
?>
