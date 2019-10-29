<?php require_once('Connections/Ventas.php'); ?>
<?php

mysql_select_db($database_Ventas, $Ventas);
$query_Listado = "SELECT pv.*, p.cedula, p.nombre, p.paterno, p.materno FROM personal_vacaciones pv inner join personal p on p.codigopersonal=pv.codigopersonal";

$Listado = mysql_query($query_Listado, $Ventas) or die(mysql_error());
$row_Listado = mysql_fetch_assoc($Listado);
$totalRows_Listado = mysql_num_rows($Listado);
 //Enumerar filas de data tablas
$i = 1;


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
<?php if ($totalRows_Listado > 0) { // Show if recordset not empty?>
<table class="table table-striped table-bordered table-hover" id="sample_1">
  <thead>
    <tr>
      <th> N&deg; </th>
      <th> DNI</th>
      <th> NOMBRES</th>
      <th> TIPO </th>
      <th> FECHA INICIO</th>
      <th> FECHA FIN </th>
      <th>Observación </th>
      <th class="text-center"> Accion </th>
    </tr>
  </thead>
  <tbody>
    <?php do { //echo '<pre>'.var_dump($row_Listado).'</pre>'; die;?>

    <?php
        $color = "#FFF";
        $estado = "";
        if ($row_Listado['estado'] == 0) {
          $color = "#fdf701";
          $estado = "PENDIENTE";
        } elseif ($row_Listado['estado'] == 1) {
          $color = "#01fd0b";
          $estado = "APROBADO";
        } elseif ($row_Listado['estado'] == -1) {
          $color = "#d05656";
          $estado = "RECHAZADO";
        }  
      ?>

    <tr style="background-color: <?= $color; ?>">
      <td> <?php echo $i; ?> </td>
      <td><?= $row_Listado['cedula']; ?></td>
      <td><?= $row_Listado['nombre'].' '.$row_Listado['paterno'].' '.$row_Listado['materno']; ?> </td>
      <td><?= $row_Listado['tipo']; ?></td>
      <td><?= $row_Listado['fecha_inicio']; ?></td>
      <td><?= $row_Listado['fecha_fin']; ?></td>
      <td><?= $row_Listado['observacion'] ?></td>
      <td class="text-center">
        <button onclick="checkvacaciones(this)" data-codigo="<?= $row_Listado['codigo_personal_vacaciones'] ?>"
          data-estado="1" class="btn btn-success">ACEPTAR</button>
        <button onclick="checkvacaciones(this)" data-codigo="<?= $row_Listado['codigo_personal_vacaciones'] ?>"
          data-estado="-1" class="btn btn-danger">DENEGAR</button>
      </td>

    </tr>
    <?php $i++;} while ($row_Listado = mysql_fetch_assoc($Listado)); ?>


  </tbody>
</table>
<?php } // Show if recordset not empty?>


<?php


//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
mysql_free_result($Listado);
?>
<script>
  function checkvacaciones(e) {
    const queries = []
    const estado = e.dataset.estado;
    const codigo = e.dataset.codigo;

    const query = `update personal_vacaciones set estado = ${estado} where codigo_personal_vacaciones = ${codigo}`
    queries.push(query)

    var formData = new FormData();
    formData.append("exearray", JSON.stringify(queries))

    fetch(`setPrecioVenta.php`, { method: 'POST', body: formData })
      .then(res => res.json())
      .catch(error => console.error("error: ", error))
      .then(res => {
        if (res.success) {
          alert("registro actualizado!")
          location.reload()
        }
      });
  }
</script>