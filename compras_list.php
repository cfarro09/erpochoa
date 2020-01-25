<?php require_once('Connections/Ventas.php'); ?>
<?php

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "
SELECT *, p.ruc, t.tipocomprobante as tipocomprobantet, nc.tipocomprobante as tipocomprobantenc, nd.tipocomprobante as tipocomprobantend, e.tipocomprobante as tipocomprobantee, nc.numerocomprobante as numerocomprobantenc, nd.numerocomprobante as numerocomprobantend, e.numerocomprobante as numerocomprobantee, t.numerocomprobante as numerocomprobantet, r.numerocomprobante as numerocomprobantec, count(t.codigocompras) as counttransporte, count(e.codigocompras) as countestibador, count(nd.codigocompras) as countnotadebito, count(nc.codigocompras) as countnotacredito, p.ruc, s.nombre_sucursal from registro_compras r 
left join transporte_compra t on t.codigocompras = r.codigorc 
left join estibador_compra e on e.codigocompras = r.codigorc 
left join notadebito_compra nd on nd.codigocompras = r.codigorc 
left join notacredito_compra nc on nc.codigocompras = r.codigorc 
left join sucursal s on s.cod_sucursal=r.codigosuc 
LEFT JOIN proveedor p on p.ruc=r.rucproveedor";



$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas

//Titulo e icono de la pagina
$Icono="fa fa-magic";
$Color="font-blue";
$Titulo="Registro Compras";
$NombreBotonAgregar="";
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
$i = 1;

?>

<div class="row">
  <h1 style="font-weight: bold">REGITRO COMPRAS</h2>
</div>
<div class="row" style="margin-top: 20px">
    <!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty ?>
  <div class="alert alert-danger" style="margin-top: 20px">
    <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
  </div>
<?php } // Show if recordset empty ?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty ?>
  <table class="table table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
          <th  width="10%" > TIPO - NUM </th>
          <th  width="15%"> FECHA REG  </th>
          
          <th  width="10%"> TOTAL </th>
          <th  width="20%"> SUCURSAL </th>
          <th  width="20%"> RUC - PROVEEDOR  </th>
          <th  width="15%"> DETALLE  </th>
          <th  width="5%"> VER </th>
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
            <?php if($row_Listado['codigorc']!=NULL) { ?>
         
               <tr>
             
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['tipo_comprobante'].' - '.$row_Listado['numerocomprobantec']; ?>                                                           </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['total']; ?> </td>
                  <td><?php echo $row_Listado['nombre_sucursal']; ?>  </td>
                  
                  <td> <?php echo $row_Listado['razonsocial'].' '.$row_Listado['ruc']; ?> </td>
                  <td> COMPRA </td>

                  <td align="center">  
                      VER
                  </td>
          
         
            </tr>
            <?php } ?>
         <?php if($row_Listado['id_transporte']!=NULL) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['tipocomprobantet'].' - '.$row_Listado['numerocomprobantet']; ?>                                                           </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo round($row_Listado['preciotransp_soles'],2); ?> </td>
                  <td> <?php echo $row_Listado['nombre_sucursal']; ?>  </td>
                  
                  <td> <?php echo $row_Listado['razonsocial'].' '.$row_Listado['ruc']; ?> </td>
                  <td> TRANSPORTE - <?PHP echo $row_Listado['tipo_transporte']; ?> </td>

                  <td align="center">  
                      VER
                  </td>
            </tr>
         <?php } ?>

          
         <?php if($row_Listado['id_notadebito']!=NULL) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['tipocomprobantend'].' - '.$row_Listado['numerocomprobantend']; ?>                                                           </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo round($row_Listado['preciond_soles'],2); ?> </td>
                  <td> <?php echo $row_Listado['nombre_sucursal']; ?>  </td>
                  
                  <td> <?php echo $row_Listado['razonsocial'].' '.$row_Listado['ruc']; ?> </td>
                  <td> NOTA DEBITO </td>

                  <td align="center">  
                      VER
                  </td>
            </tr>
         <?php } ?>



         <?php if($row_Listado['id_notacredito']!=NULL) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['tipocomprobantenc'].' - '.$row_Listado['numerocomprobantenc']; ?>                                                           </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo round($row_Listado['precionc_soles']/$IGV1, 2);?> </td>
                  <td> <?php echo $row_Listado['nombre_sucursal']; ?> </td>
                  <td align="center"> <?php echo $row_Listado['razonsocial'].' '.$row_Listado['ruc']; ?> </td>
                  <td> NOTA CREDITO </td>

                  <td align="center">  
                      VER
                  </td>
            </tr>
         <?php } ?>
      <?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>

    </tbody>
  </table>

<?php } // Show if recordset not empty ?>

</div>
<?php 

//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);
?>
<script type="text/javascript">
getSelector(".caption").style.display = "none"

</script>