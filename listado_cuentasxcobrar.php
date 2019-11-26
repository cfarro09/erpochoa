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

//------------Inicio Juego de Registro "Listado"----------------
$colname_Listado = "-1";
if (isset($_GET['codigocliente'])) {
  $colname_Listado = $_GET['codigocliente'];
}
mysql_select_db($database_Ventas, $Ventas);

$query_Listado = "SELECT * FROM cnatural n INNER JOIN ventas v on n.codigoclienten = v.codigoclienten WHERE v.jsonpagos like '%porcobrar%' and n.codigoclienten =$colname_Listado";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
/*
$query_Listado = sprintf("SELECT * FROM cnatural n INNER JOIN ventas v on n.codigoclienten = v.codigoclienten WHERE v.jsonpagos like '%porcobrar%' and n.codigoclienten = %s", GetSQLValueString($colname_Listado, "int"));
$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
*/
 //Enumerar filas de data tablas
 $i = 1;

//Titulo e icono de la pagina
$Icono="glyphicon glyphicon-credit-card";
$Color="font-blue";

$VarUrl= "?codigoclienten=".$row_Listado['cedula'];
$TituloGeneral='<div class="page-title"><h1 class="font-red-thunderbird">CLIENTE: '.$row_Listado['nombre'].' '.$row_Listado['paterno'].' '.$row_Listado['materno'].' - '.$row_Listado['cedula'].'</h1></div>';
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
          <th  width="5%" > FECHA REG </th>
          <th  width="5%" > TIPO - NUMERO </th>
          <th  width="30%"> DETALLE</th>
          <th  width="25%"> CARGO </th>
          <th  width="5%"> ABONOS </th>
          <th  width="5%"> SALDO  </th>
         <th width="5%"> VER </th>
          
        </tr>
      </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td> <?php echo $i; ?> </td>
          <td> <?php echo $row_Listado['fecha_emision']; ?>                                                           </td>
          <td> <?php echo $row_Listado['tipocomprobante'].' - '.$row_Listado['codigocomprobante']; ?></td>
          <td> VENTAS </a> </td>
          <td> <?php echo $row_Listado['total']; ?> </td>
          <td> 0 </td>
          <td> 0 </td>
          <td align="center">  
          <a href="#" data-dt="<?= $row_Listado['codigoventas']; ?>"
          data-codigodetalle="<?= $row_Listado['codigoclienten']; ?>" onclick="mostrarModalDET(this)">Ver</a>
           </td>
        </tr>
        <?php $i++; } while ($row_Listado = mysql_fetch_assoc($Listado)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>







<!-- MODAL DE CLIENTES Y VENTAS  -->

<div role="dialog" tabindex="-1" class="modal fade" id="ver_dt"
  style="max-width:600px;margin-right:auto;margin-left:auto;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <!-- CABECERA -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">x</span></button>
        <h4 class="text-center modal-title">Registro de Compra <span id="ver_dt_cedula"></span></h4>
      </div>
      <div class="modal-body">
        <!-- CUERPO DEL MENSAJE -->
        <br align="center">COMPROBANTE: <span id="ver_dt_tipocomp"> </span>- <span
          id="ver_rc_numerocomprobante"></span>
        <br align="right">FECHA:<span id="ver_dt_fecha"></span>
        <br align="right">TOTAL:<span id="ver_dt_total"></span>
        <br align="left">RUC: <span id="ver_dt_ruc"></span>
        <br align="left">PROVEEDOR: <span id="ver_dt_proveedor"></span>
        <br align="left">SUCURSAL: <span id="ver_dt_sucursal"></span>
        <br align="left">GENERADA POR: <span id="ver_dt_usuario"></span>
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
            <tbody id="ver_dt_body_tabla">
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <!-- PIE -->
        <button class="btn btn-default btn btn-primary btn-lg" type="button" data-dismiss="modal">Cerrar
        </button>
      </div>
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
    function mostrarModalDET(etiqueta) {

      $("#ver_dt").modal();
      ver_dt_ruc.textContent = etiqueta.dataset.codigoproveedor;
      ver_dt_codigorc.textContent = etiqueta.dataset.rc;
      ver_dt_body_tabla.innerHTML = ""
      fetch("traerregistroclienteventas.php?codigodt=" + etiqueta.dataset.rc)
        .then(res => res.json())
        .catch(error => console.error("error: ", error))

        .then(res => {
          ver_dt_numerocomprobante.textContent = res.header.numerocomprobante
          ver_dt_fecha.textContent = res.header.fecha
          ver_dt_total.textContent = res.header.total
          ver_dt_proveedor.textContent = res.header.razonsocial
          ver_dt_sucursal.textContent = res.header.nombre_sucursal
          ver_dt_usuario.textContent = res.header.usuario
          ver_dt_tipocomp.textContent = res.header.tipo_comprobante
          

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
        });
    }
    function removecontainerpay(e) {
      e.closest(".containerx").remove()
    }
    function changetypepago(e) {
      guardar_button.style.display = ""
      e.closest(".containerx").querySelectorAll(".inputxxx").forEach(ix => ix.style.display = "none");
      e.closest(".containerx").querySelectorAll("." + e.value).forEach(ix => ix.style.display = "");
    }
    function addPayExtra() {

      const newxx = document.createElement("div");
      newxx.className = "col-md-12 containerx";
      newxx.style = "border: 1px solid #cdcdcd; padding: 5px; margin-bottom: 5px";

      newxx.innerHTML += `
        <div class="text-right">
          <button type="button" class="btn btn-danger" onclick="removecontainerpay(this)">Cerrar</button>
        </div>

        <div class="col-md-3">
          <div class="form-group">
          <label class="control-label">Tipo Pago</label>
          <select onchange="changetypepago(this)" class="form-control tipopago">
            <option value="">[Seleccione]</option>
            <option value="depositobancario">Deposito Bancario</option>
            <option value="tarjetadebito">Tarjeta Debito</option>
            <option value="tarjetacredito">Tarjeta Credito</option>
            <option value="cheque">Cheque</option>
            <option value="efectivo">Efectivo</option>
          </select>
          </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx depositobancario cheque tarjetacredito tarjetadebito">
          <div class="form-group">
          <label class="control-label">Banco</label>
          <select class="form-control bancoextra">
            <option value="BANCO AZTECA">BANCO AZTECA</option>
            <option value="BANCO BCP">BANCO BCP</option>
            <option value="BANCO CENCOSUD">BANCO CENCOSUD</option>
            <option value="BANCO DE LA NACION">BANCO DE LA NACION</option>
            <option value="BANCO FALABELLA">BANCO FALABELLA</option>
            <option value="BANCO GNB PERÚ">BANCO GNB PERÚ</option>
            <option value="BANCO MI BANCO">BANCO MI BANCO</option>
            <option value="BANCO PICHINCHA">BANCO PICHINCHA</option>
            <option value="BANCO RIPLEY">BANCO RIPLEY</option>
            <option value="BANCO SANTANDER PERU">BANCO SANTANDER PERU</option>
            <option value="BANCO SCOTIABANK">BANCO SCOTIABANK</option>
            <option value="CMAC AREQUIPA">CMAC AREQUIPA</option>
            <option value="CMAC CUSCO S A">CMAC CUSCO S A</option>
            <option value="CMAC DEL SANTA">CMAC DEL SANTA</option>
            <option value="CMAC HUANCAYO">CMAC HUANCAYO</option>
            <option value="CMAC ICA">CMAC ICA</option>
            <option value="CMAC LIMA">CMAC LIMA</option>
            <option value="CMAC MAYNA">CMAC MAYNA</option>
            <option value="CMAC PAITA">CMAC PAITA</option>
            <option value="CMAC SULLANA">CMAC SULLANA</option>
            <option value="CMAC TRUJILLO">CMAC TRUJILLO</option>
          </select>
          </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx depositobancario cheque tarjetacredito tarjetadebito efectivo porcobrar">
          <div class="form-group">
          <label class="control-label">Monto</label>
          <input type="number" step="any" class="form-control montoextra">
          </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx cheque tarjetacredito tarjetadebito">
          <div class="form-group">
          <label class="control-label">Numero</label>
          <input type="number" class="form-control numero">
          </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx depositobancario cheque">
          <div class="form-group">
          <label class="control-label">Cuenta Corriente</label>
          <input type="text" class="form-control cuentacorriente">
          </div>
        </div>


        <div style="display: none" class="col-md-3 inputxxx depositobancario">
          <div class="form-group">
          <label class="control-label">Numero Operacion</label>
          <input type="text"  class="form-control numerooperacion">
          </div>
        </div>
        
        <div style="display: none" class="col-md-3 inputxxx depositobancario">
          <div class="form-group">
          <label class="control-label">Fecha</label>
          <input type="text" class="form-control form-control-inline input-medium date-picker fechaextra" data-date-format="yyyy-mm-dd" readonly autocomplete="off">
          </div>
        </div>

        <div style="display: none" class="col-md-3 inputxxx depositobancario">
          <div class="form-group">
          <label class="control-label">Cta Abonado</label>
          <input type="text" class="form-control cuentaabonado">
          </div>
        </div>`;
      containerpayextra.appendChild(newxx);

      $('.date-picker').datepicker({
        rtl: App.isRTL(),
        autoclose: true
      });
    }