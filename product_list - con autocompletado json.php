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
	
	
	
  $updateSQL = sprintf("UPDATE producto p, producto_stock ps SET p.estado=%s WHERE p.codigoprod=%s and ps.stock=0 and ps.codigoprod=%s",
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['codigoprod'], "int"),
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
$query_Listado = "SELECT a.codigoprod, a.nombre_producto, b.nombre AS Marca, c.nombre AS Categoria, e.nombre_color AS Color, f.nombre_presentacion AS Presentacion, max(h.precio_compra) as precio_compra,  h.codigoproveedor FROM producto a INNER JOIN marca b ON a.codigomarca = b.codigomarca INNER JOIN categoria c ON a.codigocat = c.codigocat INNER JOIN color e ON a.codigocolor = e.codigocolor INNER JOIN presentacion f ON a.codigopresent = f.codigopresent INNER JOIN WHERE a.estado = 0 group by a.codigoprod order by codigoprod";


$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
 $i = 1;


//para asignar precio y cantidad
/*$query_Listado1 = "SELECT * from producto_stock a INNER JOIN historial_producto b ON a.codigoprod = b.codigoprod group by a.codigoprod";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);*/
 //Enumerar filas de data tablas



//Titulo e icono de la pagina
$Icono="fa fa-cubes";
$Color="font-blue";
$Titulo="Listado de Productos";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 320;

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
          <th  > PRODUCTO </th>
          <th  class="none"> COMPRA </th>
          <th  class="none"> VENTA </th>
          <th  class="none"> STOCK </th>
          <th  > MARCA </th>
          <th  class="none"> CATEGORIA </th>
          <th  > SUB CATEGORIA </th>
          <th  > PRESENTACION </th>
          
          <th  class="none"> COLOR </th>
          <th  >  </th>
          <th  >  </th>
          <th  >  </th>
          <th  >  </th>
      </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['codigoprod']; ?> </a>                                                          </td>
          <td> <?php echo $row_Listado['nombre_producto']; ?></td>
          <td><?php  echo "&#36; ".$row_Listado['precio_compra'].".00"; ?> </td>
          <td> <?php echo "&#36; ".$row_Listado['precio_venta'].".00"; ?></td>
          <td> <?php echo $row_Listado['Stock']; ?></td>
          <td> <?php echo $row_Listado['Marca']; ?></td>
          <td> <?php echo $row_Listado['Categoria']; ?></td>
          <td> <?php echo $row_Listado['SubCategoria']; ?></td>
          <td> <?php echo $row_Listado['Presentacion']; ?></td>
          <td> <?php echo $row_Listado['Color']; ?></td>

          <td> 
          <a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Stock" onClick="abre_ventana('Emergentes/product_list_cantidad.php?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,580)"><i class="glyphicon glyphicon-credit-card" ></i></a>          </td>
          
          
          
                    
            </td>
            <td> 
            <a  class="btn blue-dark tooltips" data-placement="top" data-original-title="Actualizar Ultima Compra"  onClick="abre_ventana('Emergentes/product_list_editar_cantidad.php?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,580)"><i class="fa fa-refresh" ></i></a>  
            </td>
            <td> 
            <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Actualizar Registro"  onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigoprod=<?php echo $row_Listado['codigoprod']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="fa fa-refresh" ></i></a>  
            </td>
            <td>
            <form method="POST" action="<?php echo $editFormAction; ?>" name="Eliminar_Registro" id="Eliminar_Registro" onSubmit="return confirm('¿ESTA SEGURO QUE DESEA ELIMINAR ESTE REGISTRO: <?php echo $row_Listado['nombre_producto']; ?>?');">
            
              <input name="codigoprod" id="codigoprod" type="hidden" value="<?php echo $row_Listado['codigoprod']; ?>">
              <input name="estado" id="estado" type="hidden" value="1">
              <button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Eliminar Registro"><i class="glyphicon glyphicon-trash"></i></button>
                              
              <input type="hidden" name="MM_update" value="Eliminar_Registro" />
          </form>
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