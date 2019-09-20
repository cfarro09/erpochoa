<?php
//Titulo e icono de la pagina
$Icono="fa fa-gears";
$Color="font-blue";
$Titulo="Servicios a Ofrecer";
$NombreBotonAgregar="Agregar";

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");
//________________________________________________________________________________________________________________
?>        

<!--  ----------------------------------------------------------------------------------------------------------------------------------->
  <div class="portlet-body">
                                    
                                    <div class="tabbable-line">
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
          <th  width="45%">SERVICIO </th>
          <th  width="20%"> A NOMBRE </th>
          <th  width="20%"> F. INGRESO </th>
          <th  width="20%"> H. INGRESO </th>
          <th  width="20%"> ESTADO </th>
          <th  width="5%">  </th>
          <th  width="5%">  </th>
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><?php echo $row_Listado['nombre']; ?></td>
          
          <td></td>
          <td><?php echo $row_Listado['fecha_recepcion']; ?></td>
          <td><?php echo $row_Listado['hora_recepcion']; ?></td>
          <td valign="middle">
          
<?php 
		  if ($row_Listado['estado_servicio']== 'R') {echo '<span class="font-red">Recepcionado</span>';$EstadoBotonRecepcion="";}
		  if ($row_Listado['estado_servicio']== 'P') {echo '<span class="font-green">En Proceso</span>';$EstadoBotonRecepcion="disabled";}
		  if ($row_Listado['estado_servicio']== 'A') {echo '<span class="font-blue">Atendido</span>'; $EstadoBotonRecepcion="disabled";}
		  ?>

</td>
          <td>
          <a  class="btn red-thunderbird tooltips <?php echo $EstadoBotonRecepcion ?>" data-placement="top" data-original-title="Atender Recepción"  onClick="abre_ventana('Emergentes/service_reception.php?codigosao=<?php echo $row_Listado['codigosao']; ?>',700,350)"><i class="glyphicon glyphicon-log-in" ></i></a>  
          
</td>
          <td>
           <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Imprimir Ticket" href="emergentes/service_ticket.php" target="new"><i class="glyphicon glyphicon-print" ></i></a>  
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
        <th width="5%"> N&deg; </th>
          <th  width="45%">SERVICIO </th>
          <th  width="20%"> F. EMISIÓN </th>
          <th  width="20%"> F. CANCELACIÓN </th>
          <th  width="20%"> N° RECIBO </th>
          <th  width="20%"> MONTO </th>
          <th  width="5%">  </th>
          <th  width="5%">  </th>
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigosap=<?php echo $row_Listado['codigosap']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['nombre']; ?> </a>                                                          </td>
          
          <td><?php echo $row_Listado['femision']; ?></td>
          <td><?php echo $row_Listado['fpago']; ?></td>
          <td><?php echo $row_Listado['nrecibo']; ?></td>
          <td><?php echo "&#36; ".number_format($row_Listado['monto'],2); ?></td>
          <td> 
            <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Actualizar Registro"  onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigosap=<?php echo $row_Listado['codigosap']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="fa fa-refresh" ></i></a>          </td>
          <td>
            <form method="POST" action="<?php echo $editFormAction; ?>" name="Eliminar_Registro" id="Eliminar_Registro" onSubmit="return confirm('¿ESTA SEGURO QUE DESEA ELIMINAR ESTE REGISTRO: <?php echo $row_Listado['nombre']; ?>?');">
              <input name="codigosap" id="codigosap" type="hidden" value="<?php echo $row_Listado['codigosap']; ?>">
              <input name="estado" id="estado" type="hidden" value="1">
              <button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Eliminar Registro"><i class="glyphicon glyphicon-trash"></i></button>
                              
              <input type="hidden" name="MM_update" value="Eliminar_Registro" />
          </form></td>
        </tr>
        <?php $i++; } while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
    </tbody>
  </table>
                                            </div>
                                            <div class="tab-pane" id="tab_15_3">
                                                <table class="table table-striped table-bordered table-hover" id="sample_3">
    <thead>
      <tr>
        <th width="5%"> N&deg; </th>
          <th  width="45%">SERVICIO </th>
          <th  width="20%"> F. EMISIÓN </th>
          <th  width="20%"> F. CANCELACIÓN </th>
          <th  width="20%"> N° RECIBO </th>
          <th  width="20%"> MONTO </th>
          <th  width="5%">  </th>
          <th  width="5%">  </th>
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td><a onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigosap=<?php echo $row_Listado['codigosap']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)" data-toggle="modal"> <?php echo $row_Listado['nombre']; ?> </a>                                                          </td>
          
          <td><?php echo $row_Listado['femision']; ?></td>
          <td><?php echo $row_Listado['fpago']; ?></td>
          <td><?php echo $row_Listado['nrecibo']; ?></td>
          <td><?php echo "&#36; ".number_format($row_Listado['monto'],2); ?></td>
          <td> 
            <a  class="btn blue-ebonyclay tooltips" data-placement="top" data-original-title="Actualizar Registro"  onClick="abre_ventana('Emergentes/<?php echo $editar?>?codigosap=<?php echo $row_Listado['codigosap']; ?>',<?php echo $popupAncho?>,<?php echo $popupAlto?>)"><i class="fa fa-refresh" ></i></a>          </td>
          <td>
            <form method="POST" action="<?php echo $editFormAction; ?>" name="Eliminar_Registro" id="Eliminar_Registro" onSubmit="return confirm('¿ESTA SEGURO QUE DESEA ELIMINAR ESTE REGISTRO: <?php echo $row_Listado['nombre']; ?>?');">
              <input name="codigosap" id="codigosap" type="hidden" value="<?php echo $row_Listado['codigosap']; ?>">
              <input name="estado" id="estado" type="hidden" value="1">
              <button type="submit" class="btn red-thunderbird tooltips" data-placement="top" data-original-title="Eliminar Registro"><i class="glyphicon glyphicon-trash"></i></button>
                              
              <input type="hidden" name="MM_update" value="Eliminar_Registro" />
          </form></td>
        </tr>
        <?php $i++; } while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
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
?>