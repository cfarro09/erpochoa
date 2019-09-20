<?php
$Icono="glyphicon glyphicon-warning-sign ";
$Color="font-red-thunderbird";
$Titulo="ERROR";
include("Fragmentos/cabecera.php");
include("Fragmentos/bloquea_caja.php");
?>
<script type="text/javascript">
function Atras(){ 
 window.history.back();
}  



function Cerrar_Popup(){ 
opener.location.reload(); 
window.close(); 
}  
</script>

<table width="100%" height="100%" align="center" cellpadding="5" cellspacing="5" class="font-red-thunderbird">
                                            
                                            <tbody>
                                                <tr>
                                                    <td width="20%" rowspan="2" align="center"><img src="../img/warning.png" width="120" height="120" /></td>
                                                    <td valign="top"><h1>REGISTRO DUPLICADO</h1></td>
                                                </tr>
                                                <tr>
                                                    <td valign="top"><p>Ha ocurrido un error al momento de guardar su registro. El sistema ha encontrado un registro guardado anteriormente con los mismos datos.</p>
                                                    <p>Por favor sirvase a verificar su información e intentarlo nuevamente.</p></td>
                                                </tr>
                                            </tbody>
                                         </table>
                                         <br><br>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><button type="submit" class="btn blue-steel" onclick="return Atras()">Volver <i class="glyphicon glyphicon-refresh"></i> </button></td>
    
    <td align="center"><button type="reset" class="btn red-thunderbird" data-dismiss="modal" onclick="return Cerrar_Popup()">Cancelar <i class="fa fa-close"></i> </button></td>
  </tr>
</table>
  <br>           

                  
<?php 
include("Fragmentos/pie.php"); 
?>

