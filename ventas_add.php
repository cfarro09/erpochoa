<?php $total = 0;
$validarc = 1; ?>
<?php require_once('Connections/Ventas.php'); ?>
<?php


mysql_select_db($database_Ventas, $Ventas);

$query_Clientes = "SELECT codigoclienten, CONCAT(paterno,  ' ', materno, ' ', nombre, ' ',cedula) as ClienteNatural  FROM cnatural  WHERE estado = 0";
$Clientes = mysql_query($query_Clientes, $Ventas) or die(mysql_error());
$row_Clientes = mysql_fetch_assoc($Clientes);
$totalRows_Clientes = mysql_num_rows($Clientes);



//Titulo e icono de la pagina
$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Generar Guia sin orden de compra - Principal - Tumbes";
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

$codsucursal = $_SESSION['cod_sucursal'];

$query_Productos = "select k.id_kardex_contable, `a`.`codigoprod` AS `codigoprod`,`a`.`nombre_producto` AS `nombre_producto`,`b`.`codigomarca` AS `codigomarca`,`b`.`nombre` AS `Marca`,`c`.`nombre_color` AS `nombre_color`, pv.precioventa1 as p1, pv.precioventa2 as p2, pv.precioventa3 as p3, max(k.saldo) as saldo, k.sucursal, pv.totalunidad from `producto` `a` inner join `marca` `b` on(`a`.`codigomarca` = `b`.`codigomarca`) inner join `color` `c` on(`a`.`codigocolor` = `c`.`codigocolor`) inner join precio_venta pv on pv.codigoprod=a.codigoprod inner JOIN kardex_contable k on k.codigoprod=a.codigoprod where `a`.`estado` = 0 and k.saldo>0 and k.sucursal = $codsucursal and pv.precioventa1>0 group by a.codigoprod order by `k`.`id_kardex_contable` asc";

$Productos = mysql_query($query_Productos, $Ventas) or die(mysql_error());
$row_Productos = mysql_fetch_assoc($Productos);
$totalRows_Productos = mysql_num_rows($Productos);

//________________________________________________________________________________________________________________
$querySucursales = "select * from sucursal where estado = 1 or estado = 999";
$sucursales = mysql_query($querySucursales, $Ventas) or die(mysql_error());
$row_sucursales = mysql_fetch_assoc($sucursales);
$totalRows_sucursales = mysql_num_rows($sucursales);

?>

<form id="form-generate-venta">
  <div class="row">
    <div class="col-sm-12 text-center">
      <button class="btn btn-success" type="submit" style="margin-top:10px;margin-bottom: 10px; font-size: 20px">VENTA<br>
        <H5><STRONG>
            (CONFIRMAR)
          </STRONG></H5>
      </button>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12">
      <div class="row" style="margin-top: 10px">
        <div class="col-md-6">
          <div class="form-group">
            <label for="field-1" class="control-label">Cliente</label>
            <select name="cliente" required id="cliente" required class="form-control select2 tooltips" id="single" data-placement="top" data-original-title="Seleccionar cliente">
              <option value=""></option>
              <?php do {  ?>
                <option value="<?= $row_Clientes['codigoclienten'] ?>">
                  <?php echo $row_Clientes['ClienteNatural'] ?>
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
            <select name="sucursal" required id="sucursal-oc-new" disabled class="form-control ">
              <?php do {  ?>
                <option <?= $row_sucursales['cod_sucursal'] == $_SESSION['cod_sucursal'] ? 'selected' : '' ?> value="<?php echo $row_sucursales['cod_sucursal'] ?>">
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
            <label for="field-1" class="control-label">Tipo Comprobante</label>
            <select required class="form-control" id="tipocomprobante">
              <option value="factura">Factura</option>
              <option value="boleta">Boleta</option>
              <option value="recibo">Recibo</option>
              <option value="otros">Otros</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="field-1" class="control-label">Forma de Pago</label>
            <select required class="form-control" onchange="changeTipoPago(this)" id="tipopago">
              <option value="contado">Contado</option>
              <option value="depositobancario">Deposito Bancario</option>
              <option value="tarjetadebito">Tarjeta Debito</option>
              <option value="tarjetacredito">Tarjeta de Credito</option>
              <option value="cheque">Cheque</option>
              <option value="otros">Otros</option>
            </select>
          </div>
        </div>
        
        <div class="col-md-3 tarjetaso" style="display: none">
          <div class="form-group">
            <label class="control-label">Banco</label>
            <input type="text" class="form-control" id="bancoextra">
          </div>
        </div>
        <div class="col-md-3 tarjetaso" style="display: none">
          <div class="form-group">
            <label class="control-label">Codigo Transaccion</label>
            <input type="number" value="0" class="form-control" id="codigotransaccionextra">
          </div>
        </div>
        <div class="col-md-3 tarjetaso" style="display: none">
          <div class="form-group">
            <label class="control-label">Fecha</label>
            <input type="text" class="form-control" id="fechaextra">
          </div>
        </div>
        <div class="col-md-3 tarjetaso" style="display: none">
          <div class="form-group">
            <label class="control-label">Monto</label>
            <input type="number" class="form-control" id="montoextra">
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="row" style="display: none">
    <div class="col-sm-12 text-center">
      <button class="btn btn-success" type="submit" id="generateCompra" style="margin-top:10px;margin-bottom: 10px; font-size: 20px">VENTA</button>
    </div>
  </div>
  <div class="row">
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
          <option value="<?php echo $row_Productos['codigoprod'] ?>" data-preciocompra="<?= $row_Productos['totalunidad'] ?>" data-precioventa="<?= $row_Productos['p2'] ?>" data-stock="<?= $row_Productos['saldo'] ?>" data-nombre="<?php echo $row_Productos['nombre_producto'] ?>" data-marca="<?= $row_Productos['Marca']; ?>" <?php if (!(strcmp($row_Productos['codigoprod'], "compras_add.php"))) {echo "selected=\"selected\""; } ?>>
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
  <div class="row" style="margin-top:20px">
    <div class="col-sm-12">
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
  </div>
  <div class="row" style="background-color:antiquewhite; font-weight: bold; height: 50px; padding-top:15px" id="header-guia">
    <div class="col-sm-4">
      Total: <span id="total-header"></span>
    </div>
    <div class="col-sm-4">
      SubTotal: <span id="subtotal-header"></span>
    </div>
    <div class="col-sm-4">
      IGV: <span id="igv-header"></span>
    </div>
  </div>
</form>
<?php
//___________________________________________________________________________________________________________________
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");


mysql_free_result($Clientes);
mysql_free_result($Detalle_Compras);
?>

<script type="text/javascript">
  
  function changeTipoPago(e){
    if(e.value == "contado")
      getSelectorAll(".tarjetaso").forEach(i => i.style.display = "none")
    else
      getSelectorAll(".tarjetaso").forEach(i => i.style.display = "")
  }

  $("#sucursal-oc-new").on("change", function() {

    if ($("#sucursal-oc-new").val() == 10) {
      $("#direccion").val("");
      $("#div_direccion").show("fast/300/slow");
      $("#div_aux").show("fast/300/slow");
    } else {
      $("#div_direccion").hide("fast/300/slow");
      $("#div_aux").hide("fast/300/slow");
    }
  })
  $('#codigoprod').on('change', function() {
    if (getSelector(`.codigo_${this.value}`)) {

    } else {
      const option = this.options[this.selectedIndex]
      const cantrows = document.querySelectorAll("#detalleFormProducto tr").length + 1
      $("#detalleFormProducto").append(`
					<tr class="producto">
  					<td data-codigo="${this.value}" class="codigopro codigo_${this.value}" style="display: none">${this.value}</td>
  					<td class="indexproducto">${cantrows}</td>
  					<td><input type="number" data-type="cantidad" data-stock="${option.dataset.stock}" oninput="changevalue(this)" required class="cantidad tooltips form-control" value="1" style="width: 80px" data-placement="top" data-original-title="Stock: ${option.dataset.stock}"></td>
  					<td>
  					<select class="form-control unidad_medida" name="unidad_medida" required>
  					<option value="unidad">unidad</option>
  					<option value="kilo">kilo</option>
  					<option value="tonelada">tonelada</option>
  					</select>
  					</td>
  					<td class="nombre">${option.dataset.nombre}</td>
  					<td class="marca">${option.dataset.marca}</td>
  					<td style="width: 100px"><input type="number" oninput="changevalue(this)" required value="${option.dataset.precioventa}" class="precio tooltips form-control" data-placement="top" data-original-title="P. Compra: ${option.dataset.preciocompra}"></td>
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

  function changevalue(e) {
    if (e.value < 0 || "" == e.value) {
      e.value = 0
    } else {
      if(e.dataset.type == "cantidad"){
        if(parseInt(e.dataset.stock) < parseInt(e.value)){
          e.value = 0
        }
      }
      const precio = parseFloat(e.closest(".producto").querySelector(".precio").value);
      const cantidad = parseInt(e.closest(".producto").querySelector(".cantidad").value);

      const mu = precio * cantidad
      const res = mu.toFixed(2)

      e.closest(".producto").querySelector(".importe").textContent = res
      let total = 0;
      getSelectorAll(".producto").forEach(p => {
        total += parseFloat(p.querySelector(".importe").textContent);
      })
      if (total != 0) {
        total = parseFloat(total)
        getSelector("#subtotal-header").textContent = total.toFixed(3);
        getSelector("#total-header").textContent = (total * 1.18).toFixed(3);
        getSelector("#igv-header").textContent = (total * 0.18).toFixed(3);
      } else {
        getSelector("#subtotal-header").textContent = 0;
        getSelector("#total-header").textContent = 0;
        getSelector("#igv-header").textContent = 0;
      }
    }

  }

  function eliminarproducto(e) {
    e.closest(".producto").remove()

    var i = 1;
    getSelectorAll(".producto").forEach(p => {
      p.querySelector(".indexproducto").textContent = i;
      i++;
    })
  }

  function makeid(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
  }
  

  getSelector("#form-generate-venta").addEventListener("submit", e => {
    e.preventDefault();
    if (getSelectorAll(".producto").length < 1) {
      alert("Debes agregar almenos un producto")
    } else {
      const data = {
        header: {},
        detalle: []
      }
      data.header = {
        codigo: makeid(20),
        codigoventa: makeid(20),
        tipocomprobante: tipocomprobante.value,
        codigobanco: bancoextra.value,
        tipo_pago: tipopago.value,
        codigocomprobante: codigotransaccionextra.value,
        codigoclienten: cliente.value,
        codigoclientej: cliente.value,

        subtotal: getSelector("#subtotal-header").textContent ? getSelector("#subtotal-header").textContent : 0,
        igv: getSelector("#igv-header").textContent ? getSelector("#igv-header").textContent : 0,
        total: getSelector("#total-header").textContent ? getSelector("#total-header").textContent : 0,
        montofact: getSelector("#total-header").textContent ? getSelector("#total-header").textContent : 0,
        fecha_emision: '<?php echo date("Y-m-d"); ?>',
        hora_emision: '<?php echo date("h:i:s"); ?>',
        codigoacceso: "<?= $_SESSION['kt_login_id']; ?>",
        codigopersonal: "<?php echo $_SESSION['kt_codigopersonal']; ?>",
        estadofact: 1,

        
      }
      getSelectorAll(".producto").forEach(item => {
        data.detalle.push({
          codigoprod: item.querySelector(".codigopro").dataset.codigo,
          cantidad: item.querySelector(".cantidad").value,
          unidad_medida: item.querySelector(".unidad_medida").value,
          concatenacion: "<?= $_GET['codigo'] ?>" + item.querySelector(".codigopro").dataset.codigo,
          pcompra: item.querySelector(".precio").value,
          igv: parseFloat(item.querySelector(".precio").value) * 0.18,
          totalcompras: parseFloat(item.querySelector(".precio").value) * 1.18
        })
      })
      var formData = new FormData();
      formData.append("json", JSON.stringify(data))

      fetch(`setOrdenCompraNew.php`, {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .catch(error => console.error("error: ", error))
        .then(res => {
          if (res.success) {
            alert("registro completo!")
            getSelector("#form-generate-venta").reset();
            getSelector("#detalleFormProducto").innerHTML = ""
            location.reload()
          }
        });

    }
  })
</script>