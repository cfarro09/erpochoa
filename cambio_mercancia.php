<?php
    //  Conexion a la BD
    require_once('Connections/Ventas.php');

    //Titulo e icono de la pagina
    $Icono = "glyphicon glyphicon-shopping-cart";
    $Color = "font-blue";
    $Titulo = "Cambio Mercancia";
    $NombreBotonAgregar = "Agregar";
    //--------------------CAMBIO DE ESTADO DEL BOTON----------------------
    //$EstadoBotonAgregar="disabled";
    $EstadoBotonAgregar = "disabled";
    //--------------------CAMBIO DE ESTADO DEL BOTON----------------------
    $popupAncho = 700;
    $popupAlto = 525;

    include("Fragmentos/archivo.php");
    include("Fragmentos/head.php");
    include("Fragmentos/top_menu.php");
    include("Fragmentos/menu.php");

    include("Fragmentos/abrirpopupcentro.php");

    // Logica de la pagina 
    $sucursal_actual = (int) $_SESSION['cod_sucursal'];

    # Cargar Productos
    $query_Productos = "
    select k.codigoprod, k.saldo, p.nombre_producto, m.nombre as Marca, c.nombre_color,  pv.precioventa1 as p1, pv.precioventa2 as p2, pv.precioventa3 as p3, pv.totalunidad
    from kardex_contable k
    inner join producto p on p.codigoprod = k.codigoprod
    inner join marca m on m.codigomarca = p.codigomarca
    inner join `color` `c` on(p.codigocolor = c.codigocolor)
    inner join precio_venta pv on pv.codigo_pv = (select max(pv2.codigo_pv) from precio_venta pv2 where pv2.codigoprod = k.codigoprod)
    where k.sucursal = $sucursal_actual and saldo > 0
    and k.id_kardex_contable in
    (select max(id_kardex_contable) from kardex_contable group by codigoprod)";

    $Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
    $row_Productos = mysql_fetch_assoc($Productos);
    $totalRows_Productos = mysql_num_rows($Productos);
    
    # Cargar sucursales
    $res = mysql_query("select * from sucursal where estado = 1 or estado = 999", $Ventas) or die(mysql_error());
    $sucursales = array();
    while($fila = mysql_fetch_array($res, MYSQL_NUM)) {
        array_push($sucursales,$fila);
    }
?>

<form id="form-cambio-mercancia">
    <div class="row">
        <div class="col-sm-12 text-center" style="margin-bottom: 15px">
                <button class="btn btn-success" type="submit"
                    style="margin-top:10px;margin-bottom: 10px; font-size: 20px">Confirmar<br>
                </button>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="sucursal_origen">Sucursal Origen:</label>
                <select name="sucursal_origen" id="sucursal_origen" class="form-control">
                    <?php
                        foreach ($sucursales as $sucursal) { ?>
                            <option 
                                <?=$sucursal[0] == $_SESSION['cod_sucursal'] ? 'selected' : '' ?>
                                value="<?= $sucursal[0] ?>">
                                <?= $sucursal[1] ?>
                            </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="sucursal_destino">Sucursal Destino:</label>
                <select name="sucursal_destino" id="sucursal_destino" class="form-control">
                            <option value="" disabled selected> SELECCIONAR </option>
                    <?php
                        foreach ($sucursales as $sucursal) { ?>
                            <option 
                                value="<?= $sucursal[0] ?>">
                                <?= $sucursal[1] ?>
                            </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="sucursal_destino">Numero de Guia:</label>
                <input type="text" name="numero_guia" class="form-control">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="producto">Seleccione Producto:</label>
                <select name="producto" id="producto" class="form-control select2 select2-allow-clear">
                    <option value="">Seleccionar</option>
                    <?php
                    do {
                        ?>
                        <option value="<?php echo $row_Productos['codigoprod'] ?>"
                            data-preciocompra="<?= $row_Productos['totalunidad'] ?>"
                            data-precioventa="<?= $row_Productos['p2'] ?>" data-stock="<?= $row_Productos['saldo'] ?>"
                            data-nombre="<?php echo $row_Productos['nombre_producto'] ?>"
                            data-marca="<?= $row_Productos['Marca']; ?>">
                            <?php echo $row_Productos['nombre_producto'] ?> -
                            <?php echo $row_Productos['Marca']; ?> -
                            <?php echo $row_Productos['nombre_color']; ?> -
                            <?php echo "$/." . $row_Productos['p2']; ?> -
                            (<?= "Stock " . $row_Productos['saldo']; ?>)</option>
                            <?php
                        } while ($row_Productos = mysql_fetch_assoc($Productos));
                        $rows = mysql_num_rows($Productos);
                        if ($rows > 0) {
                            mysql_data_seek($Productos, 0);
                            $row_Productos = mysql_fetch_assoc($Productos);
                        }
                        ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12" style="margin-top: 20px">
            <table class="table">
                <thead>
                    <th>Nº</th>
                    <th>Cantidad</th>
                    <th>U. Medida</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Accion</th>
                </thead>
                <tbody id="detalleFormProducto">
                </tbody>
            </table>
        </div>
    </div>
</form>
    
<?php
    // ========== Cargar Footer ==========
    include("Fragmentos/footer.php");
    include("Fragmentos/pie.php");
?>
    <script>
        // Inicializando elementos
        $('#sucursal_destino option[value='+<?=$sucursal_actual?>+']').hide();

        function cambiar_sucursal(actual) {
            $('#sucursal_destino').val($('#sucursal_destino option:first').val());
            $('#sucursal_destino option').show();
            $('#sucursal_destino option[value='+actual+']').hide();
        }

        function limpiar_productos(){
            $('#producto').val(null).trigger('change');
            getSelector("#producto").innerHTML = "";
            getSelector("#detalleFormProducto").innerHTML = "";
            $('#producto').append('<option value="">Seleccionar</option>');
        }


        $('#sucursal_origen').on('change',function(e){
            var actual = $(this).val();
            cambiar_sucursal(actual);
            limpiar_productos();

            var formData = new FormData();
            var query = "select k.codigoprod, k.saldo, p.nombre_producto, m.nombre as Marca, c.nombre_color,  pv.precioventa1 as p1, pv.precioventa2 as p2, pv.precioventa3 as p3, pv.totalunidad "+
                        "from kardex_contable k "+
                        "inner join producto p on p.codigoprod = k.codigoprod "+
                        "inner join marca m on m.codigomarca = p.codigomarca "+
                        "inner join `color` `c` on(p.codigocolor = c.codigocolor) "+
                        "inner join precio_venta pv on pv.codigo_pv = (select max(pv2.codigo_pv) from precio_venta pv2 where pv2.codigoprod = k.codigoprod) "+
                        "where k.sucursal = "+actual+" and saldo > 0 "+
                        "and k.id_kardex_contable in "+
                        "(select max(id_kardex_contable) from kardex_contable group by codigoprod)";
            formData.append('query',query);

            fetch(`get_data_dynamic.php`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                res.forEach(row => {
                    var option = "<option value='"+row[0]+"' "+
                                    "data-preciocompra='"+row[8]+"' "+
                                    "data-precioventa='"+row[6]+"' "+
                                    "data-nombre='"+row[2]+"' "+
                                    "data-stock='"+row[1]+"' "+
                                    "data-marca='"+row[3]+"' > "+
                                    row[2]+" - "+row[3]+" - "+row[4]+" - $/."+row[6]+" - (Stock "+row[1]+")"+
                                 "</option>"
                    $('#producto').append(option);
                });
            });
        });

        $('#producto').on('change',function (e){
            var valor = $(this).val();
            if (valor) {
                if (!getSelector(`.codigo_${this.value}`)) {
                    const option = this.options[this.selectedIndex];
                    const cantrows = document.querySelectorAll("#detalleFormProducto tr").length + 1
                    $("#detalleFormProducto").append(`
                        <tr class="producto">
                        <input type="hidden" class="pcompra" value="${option.dataset.preciocompra}">
                        <td data-codigo="${this.value}" class="codigopro codigo_${this.value}" style="display: none">${this.value}</td>
                        <td class="indexproducto">${cantrows}</td>
                        <td><input type="number" data-type="cantidad" data-stock="${option.dataset.stock}" oninput="changevalue(this)" required class="cantidad tooltips form-control" value="0" style="width: 80px" data-placement="top" data-original-title="Stock: ${option.dataset.stock}"></td>
                        <td>
                        <select class="form-control unidad_medida" name="unidad_medida" required>
                        <option value="unidad">unidad</option>
                        <option value="kilo">kilo</option>
                        <option value="tonelada">tonelada</option>
                        </select>
                        </td>
                        <td class="nombre">${option.dataset.nombre}</td>
                        <td class="marca">${option.dataset.marca}</td>
                        <td>
                        <button type="button" onclick="eliminarproducto(this)" class="btn red-thunderbird btn-sm tooltips" data-placement="top"  data-original-title="Eliminar Producto"><i class="glyphicon glyphicon-trash"></i></button>
                        </td>
                        </tr>
                        `)
                    $('[data-toggle="tooltip"]').tooltip()
                    $('.tooltips').tooltip();
                    // alert(option.dataset.preciocompra);
                }
            }
          });

        function changevalue(e) {
            if (e.value < 0 || "" == e.value) {
                e.value = 0
            } else {
                if (e.dataset.type == "cantidad") {
                    if (parseInt(e.dataset.stock) < parseInt(e.value)) {
                        e.value = 0
                    }
                }
                const precio = parseFloat(e.closest(".producto").querySelector(".precio").value);
                const cantidad = parseInt(e.closest(".producto").querySelector(".cantidad").value);

                const mu = precio * cantidad
                const res = mu.toFixed(2)

                e.closest(".producto").querySelector(".importe").textContent = res
                let total = 0;
                let totalpc = 0;
                getSelectorAll(".producto").forEach(p => {
                    total += parseFloat(p.querySelector(".importe").textContent);
                    totalpc += (parseFloat(p.querySelector(".pcompra").value) * parseInt(p.querySelector(".cantidad").value));
                })
                if (total != 0) {
                    totalpreciocompra.value = (totalpc * 1.18).toFixed(3);
                    total = parseFloat(total)
                    getSelector("#subtotal-header").textContent = (total/1.18).toFixed(3);
                    getSelector("#total-header").textContent = (total).toFixed(3);
                    getSelector("#igv-header").textContent = (total - total/1.18).toFixed(3);

                    if(formpago.value == "unico" ){
                        getSelector(".montoextra").value = (total).toFixed(3);
                    }else{
                        getSelector(".montoextra").value = 0
                    }
                } else {
                    totalpreciocompra.value = 0;

                    getSelector("#subtotal-header").textContent = 0;
                    getSelector("#total-header").textContent = 0;
                    getSelector("#igv-header").textContent = 0;
                }
            }

        }
    </script>

