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
$query_Listado = sprintf("select *, p.ruc, t.tipocomprobante as tipocomprobantet, nc.tipocomprobante as tipocomprobantenc, nd.tipocomprobante as tipocomprobantend, e.tipocomprobante as tipocomprobantee, nc.numerocomprobante as numerocomprobantenc, nd.numerocomprobante as numerocomprobantend, e.numerocomprobante as numerocomprobantee, t.numerocomprobante as numerocomprobantet, r.numerocomprobante as numerocomprobantec, count(t.codigocompras) as counttransporte, count(e.codigocompras) as countestibador, count(nd.codigocompras) as countnotadebito, count(nc.codigocompras) as countnotacredito, p.ruc, s.nombre_sucursal from registro_compras r left join transporte_compra t on t.codigocompras = r.codigorc left join estibador_compra e on e.codigocompras = r.codigorc left join notadebito_compra nd on nd.codigocompras = r.codigorc left join notacredito_compra nc on nc.codigocompras = r.codigorc left join sucursal s on s.cod_sucursal=r.codigosuc LEFT JOIN proveedor p on p.ruc=r.rucproveedor where p.ruc= '%s' or t.ructransporte='%s'", GetSQLValueString($colname_Listado, "char"),GetSQLValueString($colname_Listado, "char"));
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);

$colname_Proveedor = "-1";
if (isset($_GET['codigoproveedor'])) {
  $colname_Proveedor = $_GET['codigoproveedor'];
}
mysql_select_db($database_Ventas, $Ventas);
$query_Proveedor = sprintf("SELECT * FROM proveedor WHERE ruc = %s", GetSQLValueString($colname_Listado, "char"));
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
          <th  width="15%" > TIPO - NUMERO </th>
          <th  width="20%"> FECHA REG  </th>
          <th  width="20%"> SUCURSAL </th>
          <th  width="10%" > SUB TOTAL </th>
          <th  width="5%"> IGV</th>
          <th  width="5%"> TOTAL </th>
          <th  width="5%" > SALDO  </th>
          <th  width="10%"> DETALLE  </th>
          <th  width="5%"> VER </th>
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
            <?php if($row_Listado['codigorc']!=NULL && $row_Listado['rucproveedor']==$colname_Listado ) { ?>
         
               <tr>
             
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['tipo_comprobante'].' - '.$row_Listado['numerocomprobantec']; ?>                                                           </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td><?php echo $row_Listado['nombre_sucursal']; ?>  </td>
                  <td> <?php echo $row_Listado['subtotal']; ?> </td>
                  <td> <?php echo $row_Listado['igv']; ?> </td>
                  <td> <?php echo $row_Listado['total']; ?> </td>
                  <td> 0 </td>
                  <td> COMPRA </td>

                  <td align="center">  
                      VER
                  </td>
          
         
            </tr>
            <?php } ?>
         <?php if($row_Listado['id_transporte']!=NULL && $row_Listado['ructransporte']==$colname_Listado ) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['tipocomprobantet'].' - '.$row_Listado['numerocomprobantet']; ?>                                                           </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['nombre_sucursal']; ?>  </td>
                  <td> <?php echo round($row_Listado['preciotransp_soles']/$IGV1, 2);?> </td>
                  <td> <?php echo $row_Listado['preciotransp_soles']-round($row_Listado['preciotransp_soles']/$IGV1,2); ?> </td>
                  <td> <?php echo round($row_Listado['preciotransp_soles'],2); ?> </td>
                  <td> 0 </td>
                  <td> TRANSPORTE - <?PHP echo $row_Listado['tipo_transporte']; ?> </td>

                  <td align="center">  
                      VER
                  </td>
            </tr>
         <?php } ?>

          <?php if($row_Listado['id_estibador']!=NULL && $row_Listado['rucestibador']==$colname_Listado ) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['tipocomprobantee'].' - '.$row_Listado['numerocomprobantee']; ?>                                                           </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['nombre_sucursal']; ?>  </td>
                  <td> <?php echo round($row_Listado['precioestibador_soles']/$IGV1, 2);?> </td>
                  <td> <?php echo $row_Listado['precioestibador_soles']-round($row_Listado['precioestibador_soles']/$IGV1,2); ?> </td>
                  <td> <?php echo round($row_Listado['precioestibador_soles'],2); ?> </td>
                  <td> 0 </td>
                  <td> Estibador </td>

                  <td align="center">  
                      VER
                  </td>
            </tr>
         <?php } ?>
         <?php if($row_Listado['id_notadebito']!=NULL && $row_Listado['rucnd']==$colname_Listado) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['tipocomprobantend'].' - '.$row_Listado['numerocomprobantend']; ?>                                                           </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['nombre_sucursal']; ?>  </td>
                  <td> <?php echo round($row_Listado['preciond_soles']/$IGV1, 2);?> </td>
                  <td> <?php echo $row_Listado['preciond_soles']-round($row_Listado['preciond_soles']/$IGV1,2); ?> </td>
                  <td> <?php echo round($row_Listado['preciond_soles'],2); ?> </td>
                  <td> 0 </td>
                  <td> NOTA DEBITO </td>

                  <td align="center">  
                      VER
                  </td>
            </tr>
         <?php } ?>



         <?php if($row_Listado['id_notacredito']!=NULL  && $row_Listado['runotacredito']==$colname_Listado) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['tipocomprobantenc'].' - '.$row_Listado['numerocomprobantenc']; ?>                                                           </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['nombre_sucursal']; ?> </td>
                  <td> <?php echo round($row_Listado['precioNC_soles']/$IGV1, 2);?> </td>
                  <td> <?php echo $row_Listado['precionc_soles']-round($row_Listado['precionc_soles']/$IGV1,2); ?> </td>
                  <td> <?php echo round($row_Listado['precionc_soles'],2); ?> </td>
                  <td> 0 </td>
                  <td> NOTA CREDITO </td>

                  <td align="center">  
                      VER
                  </td>
            </tr>
         <?php } ?>

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