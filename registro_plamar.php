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

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT * from plamar";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
$i = 1;

 //Enumerar filas de data tablas



//Titulo e icono de la pagina
$Icono="fa fa-building-o";
$Color="font-blue";
$Titulo="Historial de ordenes de compras";
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


//________________________________________________________________________________________________________________
?>

<!--  ----------------------------------------------------------------------------------------------------------------------------------->
<?php if ($totalRows_Listado == 0) { // Show if recordset empty?>
<div class="alert alert-danger">
  <strong>AUN NO SE HA INGRESADO NINGUN REGISTRO...!</strong>
</div>
<?php } // Show if recordset empty?>
<h2>PLAMAR</h2>
<a href="#" id="show_modal" class="btn btn-success" style="margin-bottom: 20px">Agregar Plamar</a>
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty?>
<table class="table table-striped table-bordered table-hover" id="sample_1">
  <thead>
    <tr>
      <th> N&deg; </th>
      <th> RUC</th>
      <th> NOMBRE </th>
      <th> N° RECIBO </th>
      <th> MONTO </th>
      <th> FECHA INICIO </th>
      <th> FECHA INICIO </th>
    </tr>
  </thead>
  <tbody>
    <?php do { //echo '<pre>'.var_dump($row_Listado).'</pre>'; die;?>
    <tr style="background-color: <?= $color; ?>">
      <td><?php echo $i; ?> </td>
      <td><?= $row_Listado['ruc'] ?> </td>
      <td><?= $row_Listado['nombre'] ?> </td>
      <td> <?php echo $row_Listado['nro_recibo']; ?></td>
      <td> <?php echo $row_Listado['monto']; ?></td>
      <td> <?php echo $row_Listado['fecha_inicio']; ?></td>
      <td> <?php echo $row_Listado['fecha_fin']; ?></td>
      

    </tr>
    <?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>


  </tbody>
</table>
<?php } // Show if recordset not empty?>
<form id="form-plamar">
  <div class="modal fade" id="mPlamar" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content m-auto">
        <div class="modal-header">
          <h2 class="modal-title" id="moperation-title">Registro Plamar</h2>
        </div>
        <div class="modal-body">

          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="" class="control-label">RUC</label>
                  <input type="number"  maxlength="10" autocomplete="off" name="ruc_plamar" id="ruc_plamar" class="form-control"
                    required />
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="" class="control-label">N° RECIBO</label>
                  <input type="text" autocomplete="off" name="nro_recibo_plamar" id="nro_recibo_plamar"
                    class="form-control" required />
                </div>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label for="" class="control-label">Nombre</label>
                  <input type="text" autocomplete="off" name="nombre_plamar" id="nombre_plamar" class="form-control"
                    required />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="field-1" class="control-label">Fecha Inicio</label>
                  <input type="text" onchange="changeinputdate(this)" readonly autocomplete="off" name="fecha_inicio"
                    id="fecha_inicio" class="form-control date-picker" data-date-format="yyyy-mm-dd" required />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="field-1" class="control-label">Fecha Fin</label>
                  <input type="text" onchange="changeinputdate(this)" readonly autocomplete="off" name="fecha_fin"
                    id="fecha_fin" class="form-control date-picker" data-date-format="yyyy-mm-dd" required />
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="" class="control-label">Cant Dias</label>
                  <input type="text" autocomplete="off" readonly name="periodo" id="periodo" class="form-control"
                    required />
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="" class="control-label">Monto en Soles</label>
                  <input type="number" autocomplete="off" name="monto_plamar" id="monto_plamar" class="form-control"
                    required />
                </div>
              </div>
              <div class="col-sm-12" style="margin-bottom: 10px">
                  <label for="">Descripcion Servicio</label>
                  <textarea name="descripcion_plamar" class="form-control" id="descripcion_plamar" required cols="30" rows="3"></textarea>
                </div>
            </div>
          </div>
          <div class="modal-footer" id="manageButtons">
            <button type="submit" name="aceptar" class="btn btn-primary">Aceptar</button>
            <button type="button" data-dismiss="modal" aria-label="Close"
              class="modal_close btn btn-danger">Cerrar</button>

          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<script>

  getSelector("#show_modal").addEventListener("click", e => {
    $("#mPlamar").modal()
  })

  function changeinputdate(e) {
    if (e.id == "fecha_inicio") {
      const fecha_fin = $("#fecha_fin").val()
      const fecha_inicio = e.value

      if (fecha_fin) {
        const date_inicio = new Date(fecha_inicio)
        const date_fin = new Date(fecha_fin)
        if (date_inicio >= date_fin) {
          e.value = ""
        } else {
          var timeDiff = date_fin.getTime() - date_inicio.getTime();
          var DaysDiff = timeDiff / (1000 * 3600 * 24);

          $("#periodo").val(DaysDiff)
        }
      }
    } else {
      const fecha_inicio = $("#fecha_inicio").val()
      const fecha_fin = e.value
      if (fecha_inicio) {
        const date_inicio = new Date(fecha_inicio)
        const date_fin = new Date(fecha_fin)
        if (date_inicio >= date_fin) {
          e.value = ""
        } else {
          var timeDiff = date_fin.getTime() - date_inicio.getTime();
          var DaysDiff = timeDiff / (1000 * 3600 * 24);

          $("#periodo").val(DaysDiff)
        }
      }
    }
  }

  getSelector("#form-plamar").addEventListener("submit", e => {
    e.preventDefault();
    const data = {
      ruc_plamar: $("#ruc_plamar").val(),
      nro_recibo_plamar: $("#nro_recibo_plamar").val(),
      nombre_plamar: $("#nombre_plamar").val(),
      fecha_inicio: $("#fecha_inicio").val(),
      fecha_fin: $("#fecha_fin").val(),
      periodo: $("#periodo").val(),
      monto_plamar: $("#monto_plamar").val(),
      descripcion_plamar: $("#descripcion_plamar").val(),
      codigoacceso: "<?= $_SESSION['kt_login_id']; ?>",
    }
    var formData = new FormData();
    formData.append("json", JSON.stringify(data))

    fetch(`setManagePlamar.php`, { method: 'POST', body: formData })
      .then(res => res.json())
      .catch(error => console.error("error: ", error))
      .then(res => {
        if (res.success) {
          $("#ruc_plamar").val("")
          $("#nro_recibo_plamar").val("")
          $("#nombre_plamar").val("")
          $("#fecha_inicio").val("")
          $("#fecha_fin").val("")
          $("#periodo").val("")
          $("#monto_plamar").val("")
          $("#descripcion_plamar").val("")
          $("#mPlamar").modal("hide")
        }
      });
  })


</script>
<?php


//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");

mysql_free_result($Listado);
?>