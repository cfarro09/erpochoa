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
    (select max(id_kardex_contable) from kardex_contable where sucursal = $sucursal_actual group by codigoprod)";

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
                <select name="sucursal_origen" id="sucursal_origen" class="form-control" disabled>
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
                <select name="sucursal_destino" id="sucursal_destino" class="form-control" required>
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
        <div class="col-md-3">
            <div class="form-group">
                <label for="sucursal_destino">Numero de Guia:</label>
                <input type="text" name="numero_guia" disabled id="numero_guia" class="form-control" required>
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
                    <th>Precio Venta</th>
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
        $(function() {
            var query = "select value from propiedades where `key` = 'despacho_guia_"+sucursal_origen.value+"'";
            get_data_dynamic(query).then(resguia => {
                numero_guia.value = resguia[0].value
            });
        });
        
        // Inicializando elementos
        $('#sucursal_destino option[value='+<?=$sucursal_actual?>+']').hide();
        
        function eliminarproducto(e) {
            e.closest(".producto").remove()
            var i = 1;
            getSelectorAll(".producto").forEach(p => {
                p.querySelector(".indexproducto").textContent = i;
                i++;
            })
        }

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
            }
        }

        function validaciones(){
            var validar = new Array();
            validar['success'] = true;

            if (getSelectorAll(".producto").length < 1) {
                validar['success'] = false;
                validar['msj'] = 'Debes agregar al menos un producto'; 
            } else if (!$('#sucursal_destino').val()) {
                validar['success'] = false;
                validar['msj'] = 'Debes seleccionar una sucursal de destino'; 
            }
            getSelectorAll(".producto").forEach(item => {
                if (item.querySelector(".cantidad").value == 0) {
                    validar['success'] = false;
                    validar['msj'] = 'La cantidad no puede ser 0'; 
                }
            });
            return validar;
        }

        $('#sucursal_origen').on('change', async function(e){
            var actual = $(this).val();
            cambiar_sucursal(actual);
            limpiar_productos();

            var query = "select value from propiedades where `key` = 'despacho_guia_"+actual+"'";
            const resguia = await get_data_dynamic(query).then(r => r);
            numero_guia.value = resguia[0].value

            var formData = new FormData();
            var query = "select k.codigoprod, k.saldo, p.nombre_producto, m.nombre as Marca, c.nombre_color,  pv.precioventa1 as p1, pv.precioventa2 as p2, pv.precioventa3 as p3, pv.totalunidad "+
                        "from kardex_contable k "+
                        "inner join producto p on p.codigoprod = k.codigoprod "+
                        "inner join marca m on m.codigomarca = p.codigomarca "+
                        "inner join `color` `c` on(p.codigocolor = c.codigocolor) "+
                        "inner join precio_venta pv on pv.codigo_pv = (select max(pv2.codigo_pv) from precio_venta pv2 where pv2.codigoprod = k.codigoprod) "+
                        "where k.sucursal = "+actual+" and saldo > 0 "+
                        "and k.id_kardex_contable in "+
                        "(select max(id_kardex_contable) from kardex_contable where sucursal = "+actual+" group by codigoprod)";
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
                        <td style="width: 100px"><input type="text" oninput="changevalue(this)" required value="${option.dataset.precioventa}" class="precio tooltips form-control" data-placement="top" data-original-title="P. Compra: ${option.dataset.preciocompra}"></td>
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

        
        getSelector("#form-cambio-mercancia").addEventListener("submit", e => {
            e.preventDefault();
            /* Declaracion de variables */
            const data = {};
			data.detalle = [];

            /* Valiaciones */
            var res = validaciones();
            if (!res['success']) {
                alert(res['msj']);
            } else {
                const h = {
                    fecha: '<?php echo date("Y-m-d"); ?>',
                    sucursal_origen: $('#sucursal_origen').val(),
                    personal_origen: "<?= $_SESSION['kt_login_id']; ?>",
                    sucursal_destino: $('#sucursal_destino').val(),
                    nro_guia: $('#numero_guia').val(),
                    productos: ""
                }
                
                
                const productos = [];
                getSelectorAll(".producto").forEach(item => {
                    const d = {
                        codigoprod: item.querySelector(".codigopro").dataset.codigo,
                        cantidad: item.querySelector(".cantidad").value,
                        unidad_medida: item.querySelector(".unidad_medida").value,
                        concatenacion: "<?= $_GET['codigo'] ?>" + item.querySelector(".codigopro").dataset.codigo,
                        nombreproducto: item.querySelector(".nombre").textContent,
                        pventa: item.querySelector(".precio").value,
                    }
                    data.detalle.push(`
                    INSERT INTO kardex_contable (codigoprod, fecha, codigocompras, numero, tipo_comprobante, detalle, cantidad, precio, saldo, sucursal, preciodolar, preciototal, tipocomprobante,codigoproveedor)
                    VALUES
                    (
                        ${d.codigoprod},
                        '${h.fecha}',
                        ###ID###,
                        '${h.nro_guia}',
                        '',
                        'Cambio mercancia - Sale',
                        ${d.cantidad}, 
                        0,
                        (select saldo from kardex_contable kc where kc.codigoprod = ${d.codigoprod} and kc.sucursal = ${h.sucursal_origen} order by kc.id_kardex_contable desc limit 1) - ${d.cantidad},
                        ${h.sucursal_origen},
                        0, 
                        0, 
                        'guia',
                        0
                    )
                `)
                // Se registra la salida en kardex_almacen
                data.detalle.push(`
                    INSERT INTO kardex_alm (codigoprod, codigoguia, numero, detalle, cantidad, saldo, fecha, codsucursal, tipo, tipodocumento)
                    VALUES 
                    (
                        ${d.codigoprod},
                        ###ID###,
                        '${h.nro_guia}',
                        'Cambio mercancia - Sale',
                        ${d.cantidad},
                        (select saldo from kardex_alm kc where kc.codigoprod = ${d.codigoprod} and kc.codsucursal = ${h.sucursal_origen} order by kc.id_kardex_alm desc limit 1) - ${d.cantidad},
                        '${h.fecha}',
                        ${h.sucursal_origen},
                        '',
                        'guia'
                    )
                `)
                    productos.push(d);
                })
                h.productos = JSON.stringify(productos);
                data.header = `
                insert into guiasucursal (sucursalorigen, personalorigen, sucursaldestino, nroguia, estado, productos) `+
                `values ('${h.sucursal_origen}', ${h.personal_origen}, '${h.sucursal_destino}','${h.nro_guia}', 'PENDIENTE','${h.productos}')`;

                data.detalle.push("UPDATE propiedades SET value = (" + h.nro_guia + "+1) where `key` = 'despacho_guia_"+h.sucursal_origen+"'");


                // Se registra la salida kardex_contable
         
                const jjson = JSON.stringify(data).replace("%select%", "lieuiwuygyq").replace("%SELECT%", "lieuiwuygyq")
                var formData = new FormData();
                formData.append("json", jjson)

                fetch(`setVenta.php`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .catch(error => console.error("error: ", error))
                .then(res => {
                    if (res.success) {
                        alert("registro completo!")
                        // getSelector("#form-cambio-mercancia").reset();
                        // getSelector("#detalleFormProducto").innerHTML = ""
                        location.reload()
                    }
                });
                console.log(data);
            }
        });
                
    </script>

