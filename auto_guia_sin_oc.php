auto_guia_sin_oc<?php require_once('Connections/Ventas.php'); ?>
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

//Enumerar filas de data tablas



//Titulo e icono de la pagina
$Icono = "fa fa-building-o";
$Color = "font-blue";
$Titulo = "Mercaderias NPR";
$NombreBotonAgregar = "Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
//$EstadoBotonAgregar="disabled";
$EstadoBotonAgregar = "disabled";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$popupAncho = 700;
$popupAlto = 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/cod_gen.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");


$suc = $_SESSION['cod_sucursal'];
mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT c.codigo_guia_sin_oc, s.nombre_sucursal,c.estado, c.numero_guia, p.razonsocial, p.codigoproveedor as codigoproveedor, c.fecha 
FROM guia_sin_oc c 
inner join proveedor p on c.codigoproveedor=p.codigoproveedor 
left join sucursal s on s.cod_sucursal = c.sucursal
where s.cod_sucursal = $suc ";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
//Enumerar filas de data tablas
$i = 1;

?>

<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty
?>
  <div class="alert alert-danger">
    <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>


  </div>
<?php } // Show if recordset empty
?>
<a href="guia_add_sin_ordencompra.php" class="btn btn-success" style="margin-bottom: 20px">Entrada Mercaderia S/OC</a>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty
?>
  <table class="table table-striped table-bordered table-hover" id="sample_1">
    <thead>
      <tr>
        <th> N&deg; </th>
        <th> Nro guia</th>
        <th> PROVEEDOR </th>
        <th> FECHA </th>
        <th> SUCURSAL </th>
        <th> IMPRIMIR </th>
        <th> VER </th>
        <th> ESTADO </th>
      </tr>
    </thead>
    <tbody>
      <?php do { //echo '<pre>'.var_dump($row_Listado).'</pre>'; die;
      ?>
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
          $color = "#d05656";
          $estado = "ANULADO";
        }
        ?>
        <tr style="background-color: <?= $color; ?>">
          <td><?php echo $i; ?> </td>
          <td><?= $row_Listado['numero_guia'] ?> </td>

          <td> <?php echo $row_Listado['razonsocial']; ?></td>
          <td> <?php echo $row_Listado['fecha']; ?></td>
          <td> <?php echo $row_Listado['nombre_sucursal']; ?></td>
          <td>
            <a class="btn yellow-crusta tooltips" data-placement="top" data-original-title="Imprimir Comprobante" href="Imprimir/orden_compra.php?codigocompras=<?php echo $row_Listado['codigo']; ?>&codigo=<?php echo $row_Listado['codigoref1']; ?>" target="new"><i class="glyphicon glyphicon-credit-card"></i></a>
          </td>
          <td><a href="#" data-estado="<?= $row_Listado['estado'] ?>" data-codigo="<?= $row_Listado['codigo_guia_sin_oc'] ?>" class="verOrden">Ver</a></td>
          <td><?= $estado ?></td>


        </tr>
      <?php $i++;
      } while ($row_Listado = mysql_fetch_assoc($Listado)); ?>


    </tbody>
  </table>
<?php } // Show if recordset not empty
?>

<div class="modal fade" id="mOrdenCompra" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document" style="width: 700px;">
    <div class="modal-content m-auto">
      <div class="modal-header">
        <h5 class="modal-title" id="moperation-title"></h5>
      </div>
      <div class="modal-body">
        <input type="hidden" id="codigoOrdenCompra">
        <div class="container-fluid">

          PROVEEDOR: <span id="mproveedor"></span> <BR>
          FECHA DE EMISION: : <span id="mfechaemision"></span> <br>
          NUMERO GUIA : <span id="numeroguia"></span> <br>
          CODIGO REF2: : <span id="mcodref2"></span> <br>
          GENERADA POR: : <span id="mgeneradapor"></span> <br>
          RUC : <span id="mruc"></span><br>
          SUCURSAL : <span id="msucursal"></span>

          <div class="row" style="margin-top: 7rem;">
            <div class="col-xs-12 col-md-12">

              <table class="table" id="tableOrdengordis">
                <!-- <thead>
                  <th>Nº</th>
                  <th>Cantidad Solicitada</th>
                  <th>Producto</th>
                  <th id="headerminicodigo">Minicodigo</th>
                  <th>Unidad Medida</th>
                </thead>
                <tbody id="detalleTableOrden1">
                </tbody> -->
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="manageButtons" style="display: none">
          <button type="button" name="aceptar" id="aceptarModalOrdenCompra" class="btn btn-primary">Aceptar</button>
          <button type="button" class="btn btn-primary" id="rechazarModalOrdenCompra">Rechazar</button>

        </div>
        <button type="button" data-dismiss="modal" aria-label="Close" class="modal_close btn btn-danger">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  //editarEstadoOrdenCompra.php
  document.querySelector("#aceptarModalOrdenCompra").addEventListener("click", () => {
    console.log("click en aceptar " + document.querySelector("#codigoOrdenCompra").value)
    fetch(`editarEstadoGuiaSinOc.php?codigo=${document.querySelector("#codigoOrdenCompra").value}&estado=2`)
      .then(res => res.json())
      .catch(error => console.error("error: ", error))
      .then(res => {
        alert("Se ace´tó la orden de compra!")
        $("#mOrdenCompra").modal("hide");
        location.reload()
      });
  });
  document.querySelector("#rechazarModalOrdenCompra").addEventListener("click", () => {
    console.log("click en aceptar " + document.querySelector("#codigoOrdenCompra").value)
    fetch(`editarEstadoGuiaSinOc.php?codigo=${document.querySelector("#codigoOrdenCompra").value}&estado=3`)
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
      // document.querySelector("#detalleTableOrden1").innerHTML = "";
      i = 0;
      document.querySelector("#codigoOrdenCompra").value = e.target.dataset.codigo
      fetch(`getDetalleGuiaSinOc.php?codigo=${e.target.dataset.codigo}`)
        .then(res => res.json())
        .catch(error => console.error("error: ", error))
        .then(res => {
          $("#mproveedor").text(res.header.razonsocial)
          $("#mfechaemision").text(res.header.fecha)
          $("#numeroguia").text(res.header.numero_guia)
          $("#mcodref2").text(res.header.codigoref2)
          $("#mgeneradapor").text(res.header.usuario)
          $("#mruc").text(res.header.ruc)
          $("#msucursal").text(res.header.nombre_sucursal + " " + (res.header.direccionOrden ? " :" + res.header.direccionOrden : ""))

          $('#tableOrdengordis').DataTable({
            ordering: false,
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            destroy: true,
            data: res.detalle,
            columns: [
                {
                    title: 'Cant Solicitada',
                    data: 'cantidad',
                    className: 'dt-body-right'
                },
                {
                    title: 'Producto',
                    data: 'nombre_producto'
                },
                {
                    title: 'Minidicodigo',
                    data: 'minicodigo',
                    visible: res.detalle.some(x => x.minicodigo !== "")

                },
                {
                    title: 'U. Medida',
                    data: 'unidad_medida',
                    
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