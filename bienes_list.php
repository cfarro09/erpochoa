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
  $updateSQL = sprintf("UPDATE inventario_bienes SET estado=%s WHERE codigoinventario=%s",
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['codigoinventario'], "int"));

  mysql_select_db($database_Ventas, $Ventas);
  $Result1 = mysql_query($updateSQL, $Ventas) or die(mysql_error());

  $updateGoTo = "bienes_list.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT a.codigoinventario, a.codigo, a.nombre_bien, a.serie, a.descripcion_bien, a.fecha_adquisicion, a.numero_factura, a.fecha_incorporacion, a.precio_compra, b.nombre AS Categoria, c.nombre AS SubCategoria, d.nombre AS Marca, e.nombre_presentacion AS Presentacion, f.nombre_color FROM inventario_bienes a INNER JOIN categoria b ON a.codigocat=b.codigocat  INNER JOIN subcategoria c ON a.codigosubcat =c.codigosubcat INNER JOIN marca d ON a.codigomarca = d.codigomarca INNER JOIN presentacion e ON a.codigopresent = e.codigopresent INNER JOIN color f ON a.codigocolor=f.codigocolor WHERE a.estado = 0 ";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
 $i = 1;

//Titulo e icono de la pagina
$Icono="fa fa-building-o";
$Color="font-blue";
$Titulo="Listado de Bienes";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 800;
$popupAlto= 650;

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
          <th  > CODIGO </th>
          <th  > NOMBRE DE BIEN </th>
          <th  > SERIE </th>
          <th  > FACTURA </th>
          <th  > COMPRA </th>
          <th  > ADQUI. </th>
          <th  > INCORP. </th>
          <th  class="none"> DETALLE </th>
          <th  class="none"> CATEGORIA </th>
          <th  class="none"> SUB CATEGORIA </th>
          <th  class="none"> MARCA </th>
          <th  class="none"> PRESENTACION </th>
          <th  class="none"> COLOR </th>
          
          <th  >  </th>
          <th  >  </th>
      </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigo=<?php echo $row_Listado['codigo']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['codigoinventario']; ?> </a>                                                          </td>
          <td> <?php echo $row_Listado['nombre_bien']; ?></td>
          <td> <?php echo $row_Listado['serie']; ?></td>
          <td> <?php echo $row_Listado['numero_factura']; ?></td>
          <td><?php echo "&#36; ".$row_Listado['precio_compra']; ?> </td>
          <td> <?php echo $row_Listado['fecha_adquisicion']; ?></td>
          <td> <?php echo $row_Listado['fecha_incorporacion']; ?></td>
          <td> <?php echo $row_Listado['descripcion_bien']; ?></td>          
          <td><?php echo $row_Listado['Categoria']; ?> </td>
          <td> <?php echo $row_Listado['SubCategoria']; ?></td>
          <td><?php echo $row_Listado['Marca']; ?> </td>
          <td><?php echo $row_Listado['Presentacion']; ?> </td>
          <td> <?php echo $row_Listado['nombre_color']; ?></td>

          <td> 
            <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Actualizar Registro"  onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigo=<?php echo $row_Listado['codigo']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="fa fa-refresh" ></i></a>          
            </td>
            <td>
            <form method="POST" action="<?php echo $editFormAction; ?>" name="Eliminar_Registro" id="Eliminar_Registro" onSubmit="return confirm('¿ESTA SEGURO QUE DESEA ELIMINAR ESTE REGISTRO: <?php echo $row_Listado['nombre_bien']; ?>?');">
              <input name="codigoinventario" id="codigoinventario" type="hidden" value="<?php echo $row_Listado['codigoinventario']; ?>">
              <input name="estado" id="estado" type="hidden" value="1">
              <button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Eliminar Registro"><i class="glyphicon glyphicon-trash"></i></button>
                              
              <input type="hidden" name="MM_update" value="Eliminar_Registro" />
          </form>
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