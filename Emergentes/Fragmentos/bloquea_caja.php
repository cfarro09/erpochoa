<script language="JavaScript">
function makeReadOnly(ID) {
        var textarea = document.getElementById(ID);
        if( textarea )
            textarea.onkeypress = textarea.onkeydown = cancelWrite;
    }
     
    // HTML event
    function cancelWrite(evt) {
        if ( !evt )
            evt = window.event;
         
        code = null;
         
        if (evt.keyCode)
            code = evt.keyCode;
        else if (evt.which )
            code = evt.which;
 
        if( code == 9 ) // La tecla TAB no es cancelada
            return;
             
        evt.cancelBubble = true;
        if ( evt.preventDefault )
            evt.preventDefault( );
        if ( evt.stopPropagation )
            evt.stopPropagation( );
        return false;
    }
</script>