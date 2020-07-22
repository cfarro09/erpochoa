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
  $updateSQL = sprintf(
    "UPDATE producto SET estado=%s WHERE codigoprod=%s",
    GetSQLValueString($_POST['estado'], "text"),
    GetSQLValueString($_POST['codigoprod'], "int")
  );

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
//para asignar precio y cantidad
$query_Listado1 = "SELECT a.codigoprod, a.nombre_producto from producto a INNER JOIN detalle_compras b ON a.codigoprod = b.codigoprod group by a.codigoprod";
$Listado1 = mysql_query($query_Listado1, $Ventas) or die(mysql_error());
$row_Listado1 = mysql_fetch_assoc($Listado1);
$totalRows_Listado1 = mysql_num_rows($Listado1);
 //Enumerar filas de data tablas



//Titulo e icono de la pagina
$Icono="fa fa-building-o";
$Color="font-blue";
$Titulo="Orden Compra Gerencia";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar="disabled";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho= 700;
$popupAlto= 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/cod_gen.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");

$codsucursal = $_SESSION['cod_sucursal'];

$querysucursal = $codsucursal == 1 ? "" : " and c.sucursal = $codsucursal";

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT c.codigoordcomp, c.estado ,c.codigo, codigoref1, montofact as valor_compra, razonsocial, p.codigoproveedor as codigoproveedor, fecha_emision FROM ordencompra c inner join proveedor p on c.codigoproveedor=p.codigoproveedor where c.estado=1 or c.estado=2 $querysucursal group by codigo order by fecha_emision desc";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
$i = 1;

//________________________________________________________________________________________________________________
?>

<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty?>
<div class="alert alert-danger">
  <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>


</div>
<?php } // Show if recordset empty?>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty?>
<table class="table table-striped table-bordered table-hover" id="sample_1">
  <thead>
    <tr>
      <th class="text-center"> N&deg; </th>
      <th class="text-center"> CODIGO REF1</th>
      
      <th class="text-center"> PROVEEDOR</th>
      <th class="text-center"> FECHA </th>
      <th class="text-center"> TOTAL</th>
      <th class="none">SUBTOTAL</th>
      <th class="none"> IGV </th>
      <th class="text-center"> IMPRIMIR </th>
      <th class="text-center"> VER </th>
      <th class="text-center"> ESTADO </th>
    </tr>
  </thead>
  <tbody>
    <?php $i = 1; do {  //echo '<pre>'.var_dump($row_Listado).'</pre>'; die;?>
    <?php
      $color = "#FFF";
      $estado = "";
      if ($row_Listado['estado'] == '1') {
        $color = "#fdf701";
        $estado = "PENDIENTE";
      } elseif ($row_Listado['estado'] == '2') {
        $color = "#01fd0b";
        $estado = "APROBADO";
      } elseif ($row_Listado['estado'] == '3') {
        $color = "#d05656";
        $estado = "RECHAZADO";
      } elseif ($row_Listado['estado'] == '4') {
        $color = "#ce5151";
        $estado = "ANULADO";
      }
      ?>
    <tr style="background-color: <?= $color; ?>">
      <td> <?php echo $i; ?> </td>
      <td> <?php echo $row_Listado['codigoref1']; ?>  </td>
      
     
      <td> <?php echo $row_Listado['razonsocial']; ?></td>
     
      <td class="text-center"> <?php 
$newDate = date("d/m/Y", strtotime($row_Listado['fecha_emision']));

      echo $newDate; ?></td>
       <td class="text-right"> <?php
        $preciocompra=$row_Listado['valor_compra'];
        echo number_format($row_Listado['valor_compra'], 2); ?></td>

      <td> <?php echo "&#36; ".number_format($row_Listado['valor_compra']/$IGV1, 2); ?></td>
      <td>
        <?php echo "&#36; ".number_format(($row_Listado['valor_compra']-number_format($row_Listado['valor_compra']/$IGV1, 2)), 2); ?>
      </td>



      <td align="center">
        <a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante"
          href="Imprimir/orden_compra.php?codigocompras=<?php echo $row_Listado['codigo']; ?>&codigo=<?php echo $row_Listado['codigoref1']; ?>&codigop=<?php echo $row_Listado['codigoproveedor']; ?>"
          target="new"><i class="glyphicon glyphicon-print"></i></a>
      </td>
      <td class="text-center"><a href="#" data-estado="<?= $row_Listado['estado'] ?>" data-codigo="<?= $row_Listado['codigo'] ?>"
          class="verOrden">Ver</a></td>
      <td><?= $estado ?></td>


    </tr>
    <?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>


  </tbody>
</table>
<?php } // Show if recordset not empty?>

<div class="modal fade" id="mOrdenCompra" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document" style="width: 900px">
    <div class="modal-content m-auto">
      <div class="modal-header">
        <h5 class="modal-title" id="moperation-title"></h5>
      </div>
      <div class="modal-body">
        <input type="hidden" id="codigoOrdenCompra">
        <div class="container-fluid"><p align="right">
          GENERADA POR: <span id="mgeneradapor"></span><br>
          FECHA DE EMISION: <span id="mfechaemision"></span><br>
          SUCURSAL : <span id="msucursal"></span></p>
          RUC : <span id="mruc"></span><br>
          PROVEEDOR: <span id="mproveedor"></span> <BR>
          VALOR TOTAL: <span id="mvalortotal"></span><BR>
          DOCUMENTO REF 1 : <span id="mcodref1"></span> <br>
          DOCUMENTO REF2: <span id="mcodref2"></span> <br>

          <div class="row">
            <div class="col-xs-12 col-md-12">

              <table class="table" id="tableOrdengordis">
                <!-- <thead>
                  <th>Nº</th>
                  <th>Cantidad Solicitada</th>
                  <th>U. Medida</th>
                  <th>Producto</th>
                  <th>Valor de Compra</th>

                </thead>
                <tbody id="detalleTableOrden1">
                </tbody> -->
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="manageButtons">
          <button type="button" name="aceptar" id="aceptarModalOrdenCompra" class="btn btn-primary">Aceptar</button>
          <button type="button" class="btn btn-primary" id="rechazarModalOrdenCompra">Rechazar</button>

        </div>
        <button type="button" data-dismiss="modal" aria-label="Close"  class="modal_close btn btn-danger">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  //editarEstadoOrdenCompra.php
  document.querySelector("#aceptarModalOrdenCompra").addEventListener("click", () => {
    fetch(`editarEstadoOrdenCompra.php?codigo=${document.querySelector("#codigoOrdenCompra").value}&estado=2`)
      .then(res => res.json())
      .catch(error => console.error("error: ", error))
      .then(res => {
        alert("Se aceptó la orden de compra!")
        $("#mOrdenCompra").modal("hide");
        location.reload()
      });
  });
  document.querySelector("#rechazarModalOrdenCompra").addEventListener("click", () => {
    fetch(`editarEstadoOrdenCompra.php?codigo=${document.querySelector("#codigoOrdenCompra").value}&estado=3`)
      .then(res => res.json())
      .catch(error => console.error("error: ", error))
      .then(res => {
        alert("hecho!")
        $("#mOrdenCompra").modal("hide");
        location.reload()
      });
  });
  
  
  var i = 0;
  document.querySelectorAll(".verOrden").forEach(item => {
    item.addEventListener("click", (e) => {
      i = 0;
      document.querySelector("#codigoOrdenCompra").value = e.target.dataset.codigo
      fetch(`getDetalleOrdenCompra.php?codigo=${e.target.dataset.codigo}`)
        .then(res => res.json())
        .catch(error => console.error("error: ", error))
        .then(res => {
          $("#mproveedor").text(res.header.razonsocial)
          $("#mfechaemision").text(res.header.fecha_emision)
          $("#mvalortotal").text(res.header.montofact)
          $("#mcodref1").text(res.header.codigoref1)
          $("#mcodref2").text(res.header.codigoref2)
          $("#mgeneradapor").text(res.header.usuario)
          $("#mruc").text(res.header.ruc)
          $("#msucursal").text(res.header.nombre_sucursal + " " + (res.header.direccionOrden ? " :"+ res.header.direccionOrden : ""))

          if (e.target.dataset.estado == "2") {
            document.querySelector("#manageButtons").style.display = "none"
          } else {
            document.querySelector("#manageButtons").style.display = ""
          }
          // document.querySelector("#detalleTableOrden1").innerHTML = ""

          $('#tableOrdengordis').DataTable({
            ordering: false,
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            destroy: true,
            data: res.detalle,
            columns: [{
                title: 'C. Solicitada',
                data: 'cantidad',
                className: 'dt-body-right'
              },
              {
                title: 'U. Medida',
                data: 'unidad_medida'
              },
              {
                title: 'Codigo Fab',
                data: 'minicodigo',
                visible: res.detalle.some(x => x.minicodigo !== "")
              },
              {
                title: 'Producto',
                data: 'Producto'
              },
              {
                title: 'Marca',
                data: 'Marca'
              },
              {
                title: 'Color',
                data: 'Color'
              },
              {
                title: 'Valor Compra',
                data: 'pcompra',
              }
            ],
            buttons: [{
                extend: 'print',
                className: 'btn dark btn-outline'
              },
              {
                extend: 'copy',
                className: 'btn red btn-outline'
              },
              {
                extend: 'pdf',
                className: 'btn green btn-outline'
              },
              {
                extend: 'excel',
                className: 'btn yellow btn-outline '
              },
              {
                extend: 'csv',
                className: 'btn purple btn-outline '
              },
              {
                extend: 'colvis',
                className: 'btn dark btn-outline',
                text: 'Columns'
              }
            ],
          });
        });
      $("#mOrdenCompra").modal();

    })
  });
</script>
<?php


//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);
?>