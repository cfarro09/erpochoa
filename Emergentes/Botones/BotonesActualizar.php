<script type="text/javascript">
function Confirmar_Actualizado() {
  return confirm('Está seguro que desea actualizar este registro?');
}

function Imprimir() {
  window.print()
}

function Cerrar_Popup(){ 
opener.location.reload(); 
window.close(); 
}  
</script>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><button type="submit" class="btn blue-steel" onclick="return Confirmar_Actualizado()">Actualizar <i class="fa fa-save"></i> </button></td>
    <td align="center"><button type="reset" class="btn yellow-gold">Restablecer <i class="fa fa-warning"></i> </button></td>
    <td align="center"><button type="reset" class="btn green-jungle" data-dismiss="modal" onclick="return Imprimir()">Imprimir <i class="fa fa-print"></i> </button></td>
    <td align="center"><button type="reset" class="btn red-thunderbird" data-dismiss="modal" onclick="return Cerrar_Popup()">Salir <i class="fa fa-close"></i> </button></td>
  </tr>
</table>
<br>