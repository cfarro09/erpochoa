<script type="text/javascript">
function Confirmar_Guardado() {
  return confirm('Está seguro que desea guardar este registro?');
}


function Cerrar_Popup(){ 
opener.location.reload(); 
window.close(); 
}  
</script>
<table width="70%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><button type="submit" class="btn blue-steel" onclick="return Confirmar_Guardado()">Guardar <i class="fa fa-save"></i> </button></td>
    <td align="center"><button type="reset" class="btn yellow-gold">Limpiar <i class="fa fa-warning"></i> </button></td>
    <td align="center"><button type="submit" class="btn red-thunderbird" data-dismiss="modal" onclick="return Cerrar_Popup()">Salir <i class="fa fa-close"></i> </button></td>
  </tr>
</table>
<br>
