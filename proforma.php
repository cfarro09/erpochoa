<?php
    //  Conexion a la BD
    require_once('Connections/Ventas.php');
    mysql_select_db($database_Ventas, $Ventas);

    //Titulo e icono de la pagina
    $Icono = "glyphicon glyphicon-shopping-cart";
    $Color = "font-blue";
    $Titulo = "Proforma";
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
   
    # Cargar Clientes
    $query_Clientes = "SELECT 'natural' as tipo, codigoclienten as codigo, CONCAT(paterno,  ' ', materno, ' ', nombre, ' ',cedula) as ClienteNatural  FROM cnatural  WHERE estado = 0 UNION SELECT 'juridico' as tipo,  codigoclientej as codigo, razonsocial as cliente  FROM cjuridico  WHERE estado = 0";
    $Clientes = mysql_query($query_Clientes, $Ventas) or die(mysql_error());
    $row_Clientes = mysql_fetch_assoc($Clientes);
    $totalRows_Clientes = mysql_num_rows($Clientes);

    # Cargar Productos
    $query_Productos = "
    select pv.codigoprod, pro.nombre_producto, m.nombre as Marca, c.nombre_color, pv.precioventa1 as p1, pv.precioventa2 as p2, pv.precioventa3 as p3, pv.totalunidad from producto pro
    inner join marca m on m.codigomarca = pro.codigomarca
    inner join color c on pro.codigocolor = c.codigocolor
    inner join precio_venta pv on pv.codigo_pv = (select max(pv2.codigo_pv) from precio_venta pv2 where pv2.codigoprod = pro.codigoprod);";

    $Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
    $row_Productos = mysql_fetch_assoc($Productos);
    $totalRows_Productos = mysql_num_rows($Productos);

    //________________________________________________________________________________________________________________
    # Cargar sucursales
    $res = mysql_query("select * from sucursal where estado = 1 or estado = 999", $Ventas) or die(mysql_error());
    $sucursales = array();
    while($fila = mysql_fetch_array($res, MYSQL_NUM)) {
        array_push($sucursales,$fila);
    }
?>

<form id="form-generate-proforma">
    <div class="row">
        <div class="col-sm-12 text-center">
            <button class="btn btn-success" type="submit"
                style="margin-top:10px;margin-bottom: 10px; font-size: 20px">Confirmar<br>
            </button>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="field-1" class="control-label">Cliente</label>
                <select name="cliente" required id="cliente" required class="form-control select2 tooltips"
                id="single" data-placement="top" data-original-title="Seleccionar cliente">
                <option value=""></option>
                <?php do {  ?>
                    <option data-tipo="<?= $row_Clientes['tipo'] ?>" value="<?= $row_Clientes['codigo'] ?>">
                        <?= $row_Clientes["ClienteNatural"] != null ? $row_Clientes["ClienteNatural"] : $row_Clientes["razonsocial"]  ?>
                    </option>
                    <?php
                } while ($row_Clientes = mysql_fetch_assoc($Clientes));
                $rows = mysql_num_rows($Clientes);
                if ($rows > 0) {
                    mysql_data_seek($Clientes, 0);
                    $row_Clientes = mysql_fetch_assoc($Clientes);
                }
                ?>
				</select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="field-1" class="control-label">Sucursal</label>
                <select name="sucursal" required id="sucursal-oc-new" class="form-control ">
                    <?php do {  ?>
                        <option
                        <?= $row_sucursales['cod_sucursal'] == $_SESSION['cod_sucursal'] ? 'selected' : '' ?>
                        value="<?php echo $row_sucursales['cod_sucursal'] ?>">
                        <?php echo $row_sucursales['nombre_sucursal'] ?>
                    </option>
                    <?php
                } while ($row_sucursales = mysql_fetch_assoc($sucursales));
                $rows = mysql_num_rows($sucursales);
                if ($rows > 0) {
                    mysql_data_seek($sucursales, 0);
                    $row_sucursales = mysql_fetch_assoc($sucursales);
                }
                ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="" class="control-label">Asunto</label>
                <input type="text" class="form-control" name="asunto" id="asunto" maxlength="80">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="" class="control-label">Referencia</label>
                <input type="text" class="form-control" name="referencia" id="referencia" maxlength="220">
            </div>
        </div>
        <div class="col-sm-12">
            <label class="" style="font-weight: bold">Seleccione un producto</label>
            <select id="codigoprod" class="form-control select2-allow-clear" name="codigoprod">
                <option value="" <?php if (!(strcmp("", "compras_add.php"))) {
                    echo "selected=\"selected\"";
                } ?>>
            </option>
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
                    <?php echo "$/." . $row_Productos['p2']; ?></option>
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
        <div class="col-sm-12" style="margin-top: 20px">
            <table class="table">
                <thead>
                    <th>Nº</th>
                    <th>Cantidad</th>
                    <th>U. Medida</th>
                    <th>Producto</th>
                    <th>Marca</th>
                    <th>Precio Venta</th>
                    <th>Importe</th>
                    <th>Accion</th>
                </thead>
                <tbody id="detalleFormProducto">
                </tbody>
            </table>
        </div>
        <div class="col-sm-12" style="background-color:antiquewhite;font-weight:bold;height:50px;padding-top:15px" id="header-guia">
            <input type="hidden" id="totalpreciocompra">
            <div class="col-sm-4">
                SUBTOTAL: <span id="subtotal-header"></span>
            </div>
            <div class="col-sm-4">
                IGV: <span id="igv-header"></span>
            </div>
            <div class="col-sm-4">
                TOTAL: <span id="total-header"></span>
            </div>
        </div>
    </div>
</form>

<?php
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    // Funciones - eliminar producto
    function eliminarproducto(e) {
        e.closest(".producto").remove()
		var i = 1;
		getSelectorAll(".producto").forEach(p => {
			p.querySelector(".indexproducto").textContent = i;
			i++;
		})

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
			getSelector("#subtotal-header").textContent = (total / 1.18).toFixed(3);
			getSelector("#total-header").textContent = (total).toFixed(3);
			getSelector("#igv-header").textContent = (total - total / 1.18).toFixed(3);

		} else {
			totalpreciocompra.value = 0;

			getSelector("#subtotal-header").textContent = 0;
			getSelector("#total-header").textContent = 0;
			getSelector("#igv-header").textContent = 0;
		}

	}
    
    // Funciones - crear id
    function makeid(length) {
		var result = '';
		var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for (var i = 0; i < length; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
    }

    // Funciones - Validaciones
    function validaciones(){
        var validar = new Array();
        validar['success'] = true;

        if (getSelectorAll(".producto").length < 1) {
            validar['success'] = false;
            validar['msj'] = 'Debes agregar al menos un producto';
        }
        getSelectorAll(".producto").forEach(item => {
            if (item.querySelector(".cantidad").value == 0) {
                validar['success'] = false;
                validar['msj'] = 'La cantidad no puede ser 0'; 
            }
        });
        return validar;
    }
    
    // Cargar productos
    $('#codigoprod').on('change', function () {
		if (getSelector(`.codigo_${this.value}`)) {

		} else {
			const option = this.options[this.selectedIndex]
			const cantrows = document.querySelectorAll("#detalleFormProducto tr").length + 1
			$("#detalleFormProducto").append(`
				<tr class="producto">
				<input type="hidden" class="pcompra" value="${option.dataset.preciocompra}">
				<td data-codigo="${this.value}" class="codigopro codigo_${this.value}" style="display: none">${this.value}</td>
				<td class="indexproducto">${cantrows}</td>
				<td><input type="number" data-type="cantidad" oninput="changevalue(this)" required class="cantidad form-control" value="0" style="width: 80px" data-placement="top"></td>
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
				<td class="importe">0</td>
				<td>
				<button type="button" onclick="eliminarproducto(this)" class="btn red-thunderbird btn-sm tooltips" data-placement="top"  data-original-title="Eliminar Producto"><i class="glyphicon glyphicon-trash"></i></button>
				</td>
				</tr>
				`)
			$('[data-toggle="tooltip"]').tooltip()
			$('.tooltips').tooltip();
		}
    });
    
    // Funciones - cambio de cantidad/precio
    function changevalue(e) {
		if (e.value < 0 || "" == e.value) {
			e.value = 0
		} else {
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
			} else {
				totalpreciocompra.value = 0;

				getSelector("#subtotal-header").textContent = 0;
				getSelector("#total-header").textContent = 0;
				getSelector("#igv-header").textContent = 0;
			}
		}
    }
    
    getSelector("#form-generate-proforma").addEventListener("submit", e => {
		e.preventDefault();
        const tipocliente = cliente.options[cliente.selectedIndex].dataset.tipo;
		var res = validaciones();
        if (!res['success']) {
            alert(res['msj']);
        } else {
			let totalpagando = 0;
			let pagoacomulado = 0;
			const codigo = makeid(20);
			const data = {};
			let porpagar = 0;
			const pagosextras = [];
			data.detalle = [];
			conpayextra = [];

			const h = {
				// tipocomprobante: tipocomprobante.value,
				// codigocomprobante: codigocomprobante.value,
				codigoclienten: tipocliente == "natural" ? cliente.value : "null",
				codigoclientej: tipocliente == "juridico" ? cliente.value : "null",
				subtotal: getSelector("#subtotal-header").textContent ? getSelector("#subtotal-header").textContent : 0,
				igv: getSelector("#igv-header").textContent ? getSelector("#igv-header").textContent : 0,
				total: getSelector("#total-header").textContent ? getSelector("#total-header").textContent : 0,
				// ntofact: getSelector("#total-header").textContent ? getSelector("#total-header").textContent : 0,
				fecha_emision: '<?php echo date("Y-m-d"); ?>',
				hora_emision: '<?php echo date("h:i:s"); ?>',
				codigoacceso: "<?= $_SESSION['kt_login_id']; ?>",
				codigopersonal: "<?php echo $_SESSION['kt_codigopersonal']; ?>",
				// estadofact: 1,
				codsucursal: <?= $_SESSION['cod_sucursal'] ?>,
                asunto: $('#asunto').val(),
                referencia: $('#referencia').val(),
				// totalc: totalpreciocompra.value,
				// pagoefectivo: montoefectivo.value ? montoefectivo.value : 0
			}
			let errorxxx = "";
			if (!h.total) {
				alert("debe ingresar una cantidad al producto");
				return;
			}

			// getSelectorAll(".containerx").forEach(ix => {
			// 	const pay = {
			// 		bancoextra: ix.querySelector(".bancoextra").value,
			// 		montoextra: ix.querySelector(".montoextra").value ? parseFloat(ix.querySelector(".montoextra").value) : 0,
			// 		numero: ix.querySelector(".numero").value,
			// 		cuentacorriente: ix.querySelector(".cuentacorriente").value,
			// 		numerooperacion: ix.querySelector(".numerooperacion").value,
			// 		fechaextra: ix.querySelector(".fechaextra").value,
			// 		cuentaabonado: ix.querySelector(".cuentaabonado").value,
			// 		tipopago: ix.querySelector(".tipopago").value,
			// 	}

			// 	if (pay.tipopago == "depositobancario" && (!pay.bancoextra || !pay.montoextra || !pay.cuentacorriente || !pay.numerooperacion || !pay.fechaextra || !pay.cuentaabonado)) {
			// 		errorxxx = "Llena todos los datos de deposito bancario";
			// 		return;
			// 	} else if (pay.tipopago == "cheque" && (!pay.bancoextra || !pay.montoextra || !pay.numero || !pay.cuentacorriente)) {
			// 		errorxxx = "Llena todos los datos de cheque";
			// 		return;
			// 	} else if ((pay.tipopago == "tarjetacredito" || pay.tipopago == "tarjetadebito") && (!pay.bancoextra || !pay.montoextra || !pay.numero)) {
			// 		errorxxx = "Llena todos los datos de " + pay.tipopago;
			// 		return;
			// 	}
			// 	if (ix.querySelector(".tipopago").value == "porcobrar")
			// 		porpagar = 1;
			// 	else
			// 		pagoacomulado += pay.montoextra

			// 	totalpagando += pay.montoextra;
			// 	pagosextras.push(pay)
			// })

            data.header = `
                INSERT INTO proforma (codigo,codigoclienten,codigoclientej,subtotal,igv,total,fecha_emision,hora_emision,codacceso,codigopersonal,sucursal,asunto,referencia)
                VALUES
                   ('',${h.codigoclienten}, ${h.codigoclientej}, ${h.subtotal}, ${h.igv},${h.total}, '${h.fecha_emision}', '${h.hora_emision}', ${h.codigoacceso}, ${h.codigopersonal}, ${h.codsucursal}, '${h.asunto}', '${h.referencia}')`

			getSelectorAll(".producto").forEach(item => {
				const d = {
					codigoprod: item.querySelector(".codigopro").dataset.codigo,
					cantidad: item.querySelector(".cantidad").value,
					unidad_medida: item.querySelector(".unidad_medida").value,
					// concatenacion: "<?= $_GET['codigo'] ?>" + item.querySelector(".codigopro").dataset.codigo,
					pventa: item.querySelector(".precio").value,
					igv: parseFloat(item.querySelector(".precio").value) * 0.18,
					totalventa: (parseInt(item.querySelector(".cantidad").value) * parseFloat(item.querySelector(".precio").value)).toFixed(4)
				}
                data.detalle.push(`
                    INSERT INTO detalle_proforma
                        (codigoprod, cantidad, pventa, codigoproforma, unidad_medida, igv, totalventa)
                    VALUES
                        (${d.codigoprod},${d.cantidad},${d.pventa},###ID###,'${d.unidad_medida}',${d.igv},${d.totalventa})`
                )
            })
            
			var formData = new FormData();
			formData.append("json", JSON.stringify(data))

			fetch(`setVenta.php`, {
				method: 'POST',
				body: formData
			})
			.then(res => res.json())
			.catch(error => console.error("error: ", error))
			.then(res => {
				if (res.success) {
					alert("registro completo!")
					getSelector("#form-generate-proforma").reset();
					getSelector("#detalleFormProducto").innerHTML = ""
					location.reload()
				}
			});

		}
	})
</script>