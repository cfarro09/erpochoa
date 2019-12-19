<?php require_once('Connections/Ventas.php'); ?>
<?php 

              

date_default_timezone_set('America/Lima');
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

 
//Titulo e icono de la pagina
$Icono="glyphicon glyphicon-home";
$Color="font-blue";
$Titulo="Pagina Principal1 - Sucursal Tumbes";
$NombreBotonAgregar="Agregar";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
$EstadoBotonAgregar="disabled";
//$EstadoBotonAgregar="";
//--------------------CAMBIO DE ESTADO DEL BOTON----------------------
include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/cod_gen.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");

$queryfrases = "select * from frases where selected = 1" ;
$frases = mysql_query($queryfrases, $Ventas) or die(mysql_error());
$row_frase = mysql_fetch_assoc($frases);
?>

<div class="row">
  <div class="col-sm-12 text-center" >


    <div id="myCarousel" class="carousel slide" style="height: 400px" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
      </ol>

      <!-- Wrapper for slides -->
      <div class="carousel-inner" style="background-color: white">
        <div class="item active tex-center">
          <img width="300px" hieght="300px" src="./assets/images/logoochoa.jpeg" alt="" style="margin:50px auto 0 auto">
        </div>

        <div class="item" style="margin-top:150px">
          <h2 style="margin-bottom: 30px"><?= $row_frase['titulo'] ?></h2>
          <h4><?= $row_frase['frase'] ?></h4>
        </div>
      </div>
      <!-- Left and right controls -->
      <a class="left carousel-control" href="#myCarousel" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#myCarousel" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>

  </div>



</div>





<?php 
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>
<?php
mysql_free_result($Contador_Clientes);

mysql_free_result($ContadorProveedor);

mysql_free_result($ContadorVentas);
?>