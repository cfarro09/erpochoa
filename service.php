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



 
mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT a.codigosao, a.codigosv, a.codigoclientej, a.codigoclienten, a.usuario_recepcion, a.personal_recepcion, a.fecha_recepcion, a.hora_recepcion, a.observacion_recepcion, a.estado_servicio, a.estado, b.nombre, c.razonsocial, CONCAT(d.paterno,  ' ', d.materno, ' ', d.nombre) as ClienteN FROM serviciosaofrecer a  INNER JOIN servicios b ON a.codigosv = b.codigosv LEFT JOIN cjuridico c ON a.codigoclientej = c.codigoclientej LEFT JOIN cnatural d ON a.codigoclienten = d.codigoclienten WHERE a.estado = 0";
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);

mysql_select_db($database_Ventas, $Ventas);
$query_EnProceso = "SELECT a.codigoservenproceso, a.codigosao, a.fecha_enproceso, a.hora_enproceso, a.observacion_enproceso, b.nombre, f.estado_servicio, c.razonsocial, CONCAT(d.paterno, ' ', d.materno, ' ', d.nombre) as ClienteN FROM hist_serv_enproceso a INNER JOIN servicios b ON a.codigosv = b.codigosv INNER JOIN serviciosaofrecer f ON a.codigosao = f.codigosao LEFT JOIN cjuridico c ON f.codigoclientej = c.codigoclientej LEFT JOIN cnatural d ON f.codigoclienten = d.codigoclienten WHERE f.estado_servicio = 'P'";
$EnProceso = mysql_query($query_EnProceso, $Ventas) or die(mysql_error());
$row_EnProceso = mysql_fetch_assoc($EnProceso);
$totalRows_EnProceso = mysql_num_rows($EnProceso);

mysql_select_db($database_Ventas, $Ventas);
$query_Atendidos = "SELECT a.codigoservatendidos, a.fecha_atendidos, a.hora_atendidos, a.observacion_atendidos, b.nombre, f.estado_servicio,f.estado_servicio, c.razonsocial, CONCAT(d.paterno, ' ', d.materno, ' ', d.nombre) as ClienteN FROM hist_serv_atendidos a INNER JOIN servicios b ON a.codigosv = b.codigosv INNER JOIN serviciosaofrecer f ON a.codigosao = f.codigosao LEFT JOIN cjuridico c ON f.codigoclientej = c.codigoclientej LEFT JOIN cnatural d ON f.codigoclienten = d.codigoclienten WHERE f.estado_servicio = 'A'";
$Atendidos = mysql_query($query_Atendidos, $Ventas) or die(mysql_error());
$row_Atendidos = mysql_fetch_assoc($Atendidos);
$totalRows_Atendidos = mysql_num_rows($Atendidos);

//Enumerar filas de data tablas
 $i = 1;
 $e = 1;
 $a = 1;

//Titulo e icono de la pagina
$Icono="fa fa-gears";
$Color="font-blue";
$Titulo="Servicios a Ofrecer";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 455;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//________________________________________________________________________________________________________________
?>        

<!--  ----------------------------------------------------------------------------------------------------------------------------------->
  <div class="portlet-body">
                                    
                                    
                                        <ul class="nav nav-tabs ">
                                            <li class="active">
                                                <a href="#tab_15_1" data-toggle="tab"> SERVICIOS </a>
                                            </li>
                                            <li>
                                                <a href="#tab_15_2" data-toggle="tab"> POR ANTENDER </a>
                                            </li>
                                            <li>
                                                <a href="#tab_15_3" data-toggle="tab"> ATENDIDOS </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tab_15_1">
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
          <th  width="20%">SERVICIO </th>
          <th  width="20%"> A NOMBRE </th>
          <th  width="10%"> F. INGRESO </th>
          <th  width="10%"> H. INGRESO </th>
          <th  width="10%"> ESTADO </th>
          <th  width="5%">  </th>
          <th  width="5%">  </th>
        </tr>
      </thead>
    <tbody>
   
      <?php do { ?>
        <tr>
          <td > <?php echo $i; ?> </td>
          <td><?php echo $row_Listado['nombre']; ?></td>
          <td>
         <?php echo $row_Listado['ClienteN']; ?>
         <?php echo $row_Listado['razonsocial']; ?>
          </td>
          <td><?php echo $row_Listado['fecha_recepcion']; ?></td>
          <td><?php echo $row_Listado['hora_recepcion']; ?></td>
          <td valign="middle">
          
<?php 
if ($row_Listado['estado_servicio']== 'R') {echo '<span class="label label-danger">Recepcionado</span>';$EstadoBotonRecepcion=""; $ColorColumnas ="class='danger'";}
if ($row_Listado['estado_servicio']== 'P') {echo '<span class="label label-warning">En Proceso</span>';$EstadoBotonRecepcion="disabled"; $ColorColumnas ="class='warning'";}
if ($row_Listado['estado_servicio']== 'A') {echo '<span class="label label-primary">Atendido</span>'; $EstadoBotonRecepcion="disabled"; $ColorColumnas ="class='success'";}
?>

</td>
          <td>
          <a  class="btn red-thunderbird tooltips <?php echo $EstadoBotonRecepcion ?>" data-placement="top" data-original-title="Atender Recepción"  onClick="abre_ventana('Emergentes/service_reception.php?codigosao=<?php echo $row_Listado['codigosao']; ?>',700,350)"><i class="glyphicon glyphicon-log-in" ></i></a>  
          
</td>
<!--
<td>
          <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Imprimir Movimientos" href="Imprimir/ejemplo.php?codigosao=<?php //echo $row_Listado['codigosao']; ?>" target="new"><i class="glyphicon glyphicon-print" ></i></a>  
          </td>
-->
          <td>
           <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Imprimir Comprobamte" href="Imprimir/ejemplo.php?codigosao=<?php echo $row_Listado['codigosao']; ?>" target="new"><i class="glyphicon glyphicon-print" ></i></a>  
          </td>
        </tr>
        <?php $i++; } while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>
                                            </div>
                                            <div class="tab-pane" id="tab_15_2">
                                                <table class="table table-striped table-bordered table-hover" id="sample_2">
    <thead>
      <tr>
          <th  width=5%> N&deg; </th>
          <th  width=50%>SERVICIO </th>
          <th  width=10%> CLIENTE </th>
          <th  width=10%> F. H. INTERNAMIENTO </th>
          <th  width=20%> DETELLE U OBSERVACIÓN </th>
          <th  width=5%>  </th>
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $e; ?> </td>
          <td><?php echo $row_EnProceso['nombre']; ?></td>
          <td><?php echo $row_EnProceso['ClienteN']; ?>
         <?php echo $row_EnProceso['razonsocial']; ?></td>
          <td><?php echo $row_EnProceso['fecha_enproceso']." - ".$row_EnProceso['hora_enproceso']; ?></td>
          <td><?php echo $row_EnProceso['observacion_enproceso']; ?></td>
          <td> 
              <a  class="btn blue tooltips" data-placement="top" data-original-title="Atender Recepción"  onClick="abre_ventana('Emergentes/service_atended.php?codigosao=<?php echo $row_EnProceso['codigosao']; ?>',700,350)"><i class="fa fa-thumbs-up" ></i></a> </td>

        </tr>
        <?php $e++; } while ($row_EnProceso = mysql_fetch_assoc($EnProceso)); ?>
    </tbody>
  </table>
                                            </div>
                                            <div class="tab-pane" id="tab_15_3">
                                                <table class="table table-striped table-bordered table-hover" id="sample_3">
    <thead>
      <tr>
          <th  width=5%> N&deg; </th>
          <th  width=50%>SERVICIO </th>
          <th  width=10%> CLIENTE</th>
          <th  width=10%> F. H. INTERNAMIENTO </th>
          <th  width=20%> DETELLE U OBSERVACIÓN </th>
          
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $a; ?> </td>
          <td><?php echo $row_Atendidos['nombre']; ?></td>
          <td>         <?php echo $row_Atendidos['ClienteN']; ?>
         <?php echo $row_Atendidos['razonsocial']; ?></td>
          <td><?php echo $row_Atendidos['fecha_atendidos']." - ".$row_Atendidos['hora_atendidos']; ?></td>
          <td><?php echo $row_Atendidos['observacion_atendidos']; ?></td>
          

        </tr>
        <?php $a++; } while ($row_Atendidos = mysql_fetch_assoc($Atendidos)); ?>
    </tbody>
  </table>
                                            </div>
                                        </div>
                                    </div>
  
  
  
<?php 
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);

mysql_free_result($EnProceso);

mysql_free_result($Atendidos);
?>