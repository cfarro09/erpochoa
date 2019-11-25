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
$query_Listado = sprintf("select *, p.ruc, t.id_transporte, t.tipocomprobante as tipocomprobantet, nc.tipocomprobante as tipocomprobantenc, nd.tipocomprobante as tipocomprobantend, e.tipocomprobante as tipocomprobantee, nc.numerocomprobante as numerocomprobantenc, nd.numerocomprobante as numerocomprobantend, e.numerocomprobante as numerocomprobantee, t.numerocomprobante as numerocomprobantet, r.numerocomprobante as numerocomprobantec, p.ruc, s.nombre_sucursal from registro_compras r left join transporte_compra t on t.codigocompras = r.codigorc left join estibador_compra e on e.codigocompras = r.codigorc left join notadebito_compra nd on nd.codigocompras = r.codigorc left join notacredito_compra nc on nc.codigocompras = r.codigorc left join sucursal s on s.cod_sucursal=r.codigosuc LEFT JOIN proveedor p on p.ruc=r.rucproveedor where p.ruc= '%s' or t.ructransporte='%s'", GetSQLValueString($colname_Listado, "char"),GetSQLValueString($colname_Listado, "char"));
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
          
          <th  width="20%"> FECHA REG  </th>
          <th  width="15%" > TIPO - NUMERO </th>
          <th  width="10%"> DETALLE  </th>
          <th  width="5%"> CARGO </th>
          <th  width="20%"> ABONOS </th>
          <th  width="5%" > SALDO  </th>
          
          <th  width="5%"> VER </th>
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
            <?php if($row_Listado['codigorc']!=NULL && $row_Listado['rucproveedor']==$colname_Listado ) { ?>
         
               <tr>
                       <?php $rc=$row_Listado['codigorc']; ?>  
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['tipo_comprobante'].' - '.$row_Listado['numerocomprobantec']; ?>                                                           </td>
                  <td> COMPRA </td>
                  
                 
                  <td> <?php echo $row_Listado['total']; ?> </td>
                  <td>0 </td>
                  <td> 0 </td>
                  

                  <td align="center"> 
                    <a href="#" data-rc="<?= $row_Listado['codigorc']; ?>" data-codigoproveedor="<?= $row_Listado['rucproveedor']; ?>" onclick="mostrarModalRC(this)">Ver</a>
                  </td>
            </tr>
            <?php } ?>
         <?php if($row_Listado['id_transporte']!=NULL && $row_Listado['ructransporte']==$colname_Listado ) { ?>
               <tr>
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['tipocomprobantet'].' - '.$row_Listado['numerocomprobantet']; ?>    </td>
                  <td> TRANSPORTE - <?PHP echo $row_Listado['tipo_transporte']; ?> </td>
                  
                  <td> <?php echo round($row_Listado['preciotransp_soles'],2); ?> </td>
                  <td> 0  </td>
                  <td> 0 </td>
                  

                  <td align="center">  
                      <a href="#" data-trans="<?= $row_Listado['id_transporte']; ?>" data-codigotrans="<?= $row_Listado['ructransporte']; ?>" onclick="mostrarModalTRANS(this)">Ver</a>
                  </td>
            </tr>
         <?php } ?>

          <?php if($row_Listado['id_estibador']!=NULL && $row_Listado['rucestibador']==$colname_Listado ) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['tipocomprobantee'].' - '.$row_Listado['numerocomprobantee']; ?>                                                           </td>
                  <td> Estibador </td>
                  
                  <td> <?php echo round($row_Listado['precioestibador_soles'],2); ?> </td>
                  <td> 0 </td>
                  <td> 0 </td>
                  

                  <td align="center">  
                      <a href="#" data-toggle="modal" data-target="#ver_e">Ver </a>
                  </td>
            </tr>
         <?php } ?>
         <?php if($row_Listado['id_notadebito']!=NULL && $row_Listado['rucnd']==$colname_Listado) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['tipocomprobantend'].' - '.$row_Listado['numerocomprobantend']; ?>                                                           </td>
                  <td> NOTA DEBITO </td>
                  
                  <td> <?php echo round($row_Listado['preciond_soles'],2); ?> </td>
                  <td> 0  </td>
                  <td> 0 </td>
                  

                  <td align="center">  
                    <a href="#" data-notad="<?= $row_Listado['id_notadebito']; ?>" data-codigonotad="<?= $row_Listado['rucnd']; ?>" onclick="mostrarModalNOTAD(this)">Ver</a>
                  </td>
            </tr>
         <?php } ?>



         <?php if($row_Listado['id_notacredito']!=NULL  && $row_Listado['rucnotacredito']==$colname_Listado) { ?>
               <tr>
          
                  <td> <?php echo $i; ?> </td>
                  <td> <?php echo $row_Listado['fecha_registro']; ?></td>
                  <td> <?php echo $row_Listado['tipocomprobantenc'].' - '.$row_Listado['numerocomprobantenc']; ?>                                                           </td>
                  <td> NOTA CREDITO </td>
                  <td> <?php echo round($row_Listado['precionc_soles'],2); ?> </td>
                  <td> 0</td>
                  
                  <td> 0 </td>
                  

                  <td align="center">  
                      <a href="#" data-notac="<?= $row_Listado['id_notacredito']; ?>" data-codigonotac="<?= $row_Listado['rucnotacredito']; ?>" onclick="mostrarModalNOTAC(this)">Ver</a>
                  </td>
            </tr>
         <?php } ?>

        <?php $i++; } while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>





<!-- MODAL DE REGISTRO DE COMPRA  -->

<div role="dialog" tabindex="-1" class="modal fade" id="ver_rc"
style="max-width:600px;margin-right:auto;margin-left:auto;">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header"> <!-- CABECERA -->
       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
       <h4 class="text-center modal-title">Registro de Compra <span id="ver_rc_codigorc"></span></h4>
       </div>
       <div class="modal-body"> <!-- CUERPO DEL MENSAJE -->
       <br align="center">COMPROBANTE: <span id="ver_rc_tipocomp"> </span>- <span id="ver_rc_numerocomprobante"></span>
       <br align="right">FECHA:<span id="ver_rc_fecha"></span>
       <br align="right">TOTAL:<span id="ver_rc_total"></span>
       <br align="left">RUC: <span id="ver_rc_ruc"></span>
       <br align="left">PROVEEDOR: <span id="ver_rc_proveedor"></span>
       <br align="left">SUCURSAL: <span id="ver_rc_sucursal"></span>
       <br align="left">GENERADA POR: <span id="ver_rc_usuario"></span>
       <div class="table-responsive-sm">
          <table class="table">
            <thead>
              <tr>
                <td>#</td>
                <td>Cant</td>
                <td>Detalle</td>
                <td>Desc x Item</td>
                <td>Precio UND</td>
             </tr>
            </thead>
            <tbody id="ver_rc_body_tabla">             
            </tbody>           
          </table>
      </div>       </div>         <div class="modal-footer"> <!-- PIE -->
       <button class="btn btn-default btn btn-primary btn-lg" type="button" data-dismiss="modal">Cerrar </button>
       </div>      </div>         </div>
</div>

<!-- MODAL DE TRANSPORTE  -->
<div role="dialog" tabindex="-1" class="modal fade" id="ver_trans"
style="max-width:600px;margin-right:auto;margin-left:auto;">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header"> <!-- CABECERA -->
       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
       <h4 class="text-center modal-title">TRANSPORTE <span id="ver_trans_id_transporte"></span></h4>
       </div>
       <div class="modal-body"> <!-- CUERPO DEL MENSAJE -->
    <!--   <br align="center">COMPROBANTE: <span id="ver_trans_tipocomp"> </span>- <span id="ver_trans_numerocomprobante"></span>
      -->
       <br align="right">TIPO:<span id="ver_trans_tipotransp"></span>
       <br align="left">RUC: <span id="ver_trans_ruc"></span>
       <br align="left">PROVEEDOR: <span id="ver_trans_razonsocial"></span>
       <br align="left">FECHA: <span id="ver_trans_fecha"></span>
       <br align="center">COMPROBANTE: <span id="ver_trans_tipocomp"> </span>- <span id="ver_trans_numerocomprobante"></span>
       <br align="center">MONEDA: <span id="ver_trans_moneda"></span>
       <br align="center">TOTAL SOLES: <span id="ver_trans_preciotransp_soles"></span>     
     <!--  <br align="right">MONEDA:<span id="ver_trans_moneda"></span>
       <br align="right">TOTAL:<span id="ver_trans_ruc"></span>
       <br align="left">SUCURSAL: <span id="ver_trans_ruc"></span>-->
       </div>
       <div class="modal-footer"> <!-- PIE -->
       <button class="btn btn-default btn btn-primary btn-lg" type="button" data-dismiss="modal">Cerrar </button>
       </div>
     </div>
   </div>
</div>






<!-- MODAL DE NOTA DEBITO  -->

<div role="dialog" tabindex="-1" class="modal fade" id="ver_notad"
style="max-width:600px;margin-right:auto;margin-left:auto;">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header"> <!-- CABECERA -->
       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
       <h4 class="text-center modal-title">NOTA DE DEBITO <span id="ver_notad_id_notadebito"></span></h4>
       </div>
       <div class="modal-body"> <!-- CUERPO DEL MENSAJE -->
    <!--   <br align="center">COMPROBANTE: <span id="ver_trans_tipocomp"> </span>- <span id="ver_trans_numerocomprobante"></span>
    -->
       <br align="left">RUC: <span id="ver_notad_ruc"></span>
       <br align="left">PROVEEDOR: <span id="ver_notad_razonsocial"></span>
       <br align="left">FECHA: <span id="ver_notad_fecha"></span>
       <br align="center">COMPROBANTE: <span id="ver_notad_tipocomp"> </span>- <span id="ver_notad_numerocomprobante"></span>
       <br align="center">MONEDA: <span id="ver_notad_moneda"></span>
       <br align="center">TOTAL SOLES: <span id="ver_notad_preciond_soles"></span>     
    
       </div>
       <div class="modal-footer"> <!-- PIE -->
       <button class="btn btn-default btn btn-primary btn-lg" type="button" data-dismiss="modal">Cerrar </button>
       </div>
     </div>
   </div>
   </div>


<!-- MODAL DE NOTA CREDITO  -->

<div role="dialog" tabindex="-1" class="modal fade" id="ver_notac"
style="max-width:600px;margin-right:auto;margin-left:auto;">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header"> <!-- CABECERA -->
       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
       <h4 class="text-center modal-title">NOTA DE CREDITO <span id="ver_notac_id_notacrebito"></span></h4>
       </div>
       <div class="modal-body"> <!-- CUERPO DEL MENSAJE -->
    <!--   <br align="center">COMPROBANTE: <span id="ver_trans_tipocomp"> </span>- <span id="ver_trans_numerocomprobante"></span>
    -->
       <br align="left">RUC: <span id="ver_notac_ruc"></span>
       <br align="left">PROVEEDOR: <span id="ver_notac_razonsocial"></span>
       <br align="left">FECHA: <span id="ver_notac_fecha"></span>
       <br align="center">COMPROBANTE: <span id="ver_notac_tipocomp"> </span>- <span id="ver_notac_numerocomprobante"></span>
       <br align="center">MONEDA: <span id="ver_notac_moneda"></span>
       <br align="center">TOTAL SOLES: <span id="ver_notac_precionc_soles"></span>     
    
       </div>
       <div class="modal-footer"> <!-- PIE -->
       <button class="btn btn-default btn btn-primary btn-lg" type="button" data-dismiss="modal">Cerrar </button>
       </div>
     </div>
   </div>


<?php 
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);

mysql_free_result($Proveedor);
?>
<script type="text/javascript">
  function mostrarModalRC(etiqueta){

    $("#ver_rc").modal();
    ver_rc_ruc.textContent = etiqueta.dataset.codigoproveedor;
    ver_rc_codigorc.textContent = etiqueta.dataset.rc;
    ver_rc_body_tabla.innerHTML = ""
    fetch("http://localhost:8080/erpochoa/traerregistrocompra.php?codigorc="+etiqueta.dataset.rc)
        .then(res => res.json())
        .catch(error => console.error("error: ", error))

        .then(res => {
          ver_rc_numerocomprobante.textContent = res.header.numerocomprobante
          ver_rc_fecha.textContent = res.header.fecha
          ver_rc_total.textContent = res.header.total
          ver_rc_proveedor.textContent = res.header.razonsocial
          ver_rc_sucursal.textContent = res.header.nombre_sucursal
          ver_rc_usuario.textContent = res.header.usuario
          ver_rc_tipocomp.textContent = res.header.tipo_comprobante
          console.log(ver_rc_numerocomprobante.textContent)
          res.detalle.forEach(row => {
            ver_rc_body_tabla.innerHTML += `
              <tr>
                <td></td>
                <td>${row.cantidad}</td>
                <td>${row.nombre_producto}</td>
                <td>${row.descxitem}</td>
                <td>${row.vcu}</td>
              </tr>
            `;

          });
          //console.log(res.numerocomprobante)
        });
   // console.log(etiqueta.dataset.rc)
    //console.log(etiqueta.dataset.codigoproveedor)

  }
  
  function mostrarModalTRANS(etiqueta){

    $("#ver_trans").modal();
    ver_trans_ruc.textContent = etiqueta.dataset.ructransporte;
    ver_trans_tipotransp.textContent = etiqueta.dataset.tipo_transporte;
    ver_trans_razonsocial.textContent = etiqueta.dataset.razonsocial;
    ver_trans_id_transporte.textContent = etiqueta.dataset.trans;
    ver_trans_tipocomp.textContent = etiqueta.dataset.tipocomprobante;
    ver_trans_numerocomprobante.textContent = etiqueta.dataset.numerocomprobante;
    ver_trans_moneda.textContent = etiqueta.dataset.moneda;
    ver_trans_preciotransp_soles.textContent=etiqueta.dataset.preciotransp_soles
    
    //ver_trans_body_tabla.innerHTML = ""
    fetch("http://localhost:8080/erpochoa/traerregistrocompra.php?codigotrans="+etiqueta.dataset.trans)
    .then(res => res.json())
    .catch(error => console.error("error: ", error))

    .then(res => {
      ver_trans_ruc.textContent = res.header.ructransporte
      ver_trans_tipotransp.textContent = res.header.tipo_transporte
      ver_trans_tipocomp.textContent = res.header.tipocomprobante
      ver_trans_numerocomprobante.textContent = res.header.numerocomprobante
      ver_trans_razonsocial.textContent = res.header.razonsocial
      ver_trans_moneda.textContent = res.header.moneda
      ver_trans_preciotransp_soles.textContent = res.header.preciotransp_soles
      //ver_trans_usuario.textContent = res.header.usuario
      //ver_trans_tipocomp.textContent = res.header.tipo_comprobante
      console.log(res)
                //console.log(res.numerocomprobante)
    });
    //console.log(etiqueta.dataset.codigoproveedor)

  }





function mostrarModalNOTAD(etiqueta){

    $("#ver_notad").modal();
    //ver_notad_ruc.textContent = etiqueta.dataset.rucnd;
    ver_notad_tipocomp.textContent = etiqueta.dataset.tipocomprobante;
    ver_notad_numerocomprobante.textContent = etiqueta.dataset.numerocomprobante;
    ver_notad_razonsocial.textContent = etiqueta.dataset.razonsocial;
    ver_notad_id_notadebito.textContent = etiqueta.dataset.notad;
    ver_notad_moneda.textContent = etiqueta.dataset.moneda;
    ver_notad_preciond_soles.textContent=etiqueta.dataset.preciond_soles
    
    //ver_trans_body_tabla.innerHTML = ""
    fetch("http://localhost:8080/erpochoa/traerregistrocompra.php?codigonotad="+etiqueta.dataset.notad)
    .then(res => res.json())
    .catch(error => console.error("error: ", error))

    .then(res => {
      ver_notad_ruc.textContent = res.header.rucnd
      ver_notad_tipocomp.textContent = res.header.tipocomprobante
      ver_notad_numerocomprobante.textContent = res.header.numerocomprobante
      ver_notad_id_notadebito.textContent=res.header.id_notadebito
      ver_notad_razonsocial.textContent = res.header.razonsocial
      ver_notad_moneda.textContent = res.header.moneda
      ver_notad_preciond_soles.textContent = res.header.preciond_soles
      
      console.log(res)
      
    });
    //console.log(etiqueta.dataset.codigoproveedor)

  }



function mostrarModalNOTAC(etiqueta){

    $("#ver_notac").modal();
   // ver_notac_ruc.textContent = etiqueta.dataset.rucnotacredito;
    ver_notac_tipocomp.textContent = etiqueta.dataset.tipocomprobante;
    ver_notac_numerocomprobante.textContent = etiqueta.dataset.numerocomprobante;
    ver_notac_razonsocial.textContent = etiqueta.dataset.razonsocial;
    ver_notac_id_notacrebito.textContent = etiqueta.dataset.notac;
    ver_notac_moneda.textContent = etiqueta.dataset.moneda;
    ver_notac_precionc_soles.textContent=etiqueta.dataset.precionc_soles
    
    //ver_trans_body_tabla.innerHTML = ""
    fetch("http://localhost:8080/erpochoa/traerregistrocompra.php?codigonotac="+etiqueta.dataset.notac)
    .then(res => res.json())
    .catch(error => console.error("error: ", error))

    .then(res => {
      ver_notac_ruc.textContent = res.header.rucnotacredito
      ver_notac_tipocomp.textContent = res.header.tipocomprobante
      ver_notac_numerocomprobante.textContent = res.header.numerocomprobante
      ver_notac_id_notacrebito.textContent=res.header.id_notacredito
      ver_notac_razonsocial.textContent = res.header.razonsocial
      ver_notac_moneda.textContent = res.header.moneda
      ver_notac_precionc_soles.textContent = res.header.precionc_soles
      //ver_trans_usuario.textContent = res.header.usuario
      //ver_trans_tipocomp.textContent = res.header.tipo_comprobante
      console.log(res)
                //console.log(res.numerocomprobante)
    });
    //console.log(etiqueta.dataset.codigoproveedor)

  }


</script>