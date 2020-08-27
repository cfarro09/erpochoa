<?php
require_once('Connections/Ventas.php');

mysql_select_db($database_Ventas, $Ventas); //ALTER TABLE ventasochoa.ventas ADD despachado INT DEFAULT 0 NULL;
//INSERT INTO propiedades (propiedades_id, `key`, value, datecreated, datechanged) VALUES(3, 'despacho_guia', '1', '2020-01-02 16:54:32', '2020-01-02 17:38:22');
//ALTER TABLE ventasochoa.ventas ADD nroguia int(11) NULL;

$Icono = "glyphicon glyphicon-shopping-cart";
$Color = "font-blue";
$Titulo = "Lista Entregas";
$NombreBotonAgregar = "Agregar";
$EstadoBotonAgregar = "disabled";
$popupAncho = 700;
$popupAlto = 525;

include("Fragmentos/archivo.php");
include("Fragmentos/head.php");
include("Fragmentos/top_menu.php");
include("Fragmentos/menu.php");
include("Fragmentos/abrirpopupcentro.php");

// Logica de la pagina 
$codsucursal = $_SESSION['cod_sucursal'];

?>

<table class="table table-bordered table-hover" id="maintable"></table>

<div class="modal fade" id="mdetalle" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content m-auto">
            <div class="modal-header">
                <h5 class="modal-title" id="moperation-title">Detalle Traslado</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sucdestino">
                <input type="hidden" id="sucorigen">
                <input type="hidden" id="listproductos">
                <input type="hidden" id="inputnroguia">
                <input type="hidden" id="guiasucursalid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Estado</label>
                            <input type="text" readonly class="form-control" name="estadotraslado" id="estadotraslado">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Suc Origen</label>
                            <input type="text" readonly class="form-control" name="sucursalorigen" id="sucursalorigen">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Fecha Salida</label>
                            <input type="text" readonly class="form-control" name="fechasalida" id="fechasalida">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Personal Origen</label>
                            <input type="text" readonly class="form-control" name="personalorigen" id="personalorigen">
                        </div>
                    </div>

                </div>
                <div class="row" style="margin-bottom: 62px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Suc Destino</label>
                            <input type="text" readonly class="form-control" name="sucursaldestino" id="sucursaldestino">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Fecha Llegada</label>
                            <input type="text" readonly class="form-control" name="fechallegada" id="fechallegada">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Personal Destino</label>
                            <input type="text" readonly class="form-control" name="personaldestino" id="personaldestino">
                        </div>
                    </div>
                </div>


                <table id="detalletabla" class="display" width="100%"></table>
                <div class="text-right">
                    <button type="button" class="btn btn-primary" id="buttonanular" onclick="anular()">Anular</button>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-danger">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="moperation" role="dialog" data-backdrop="static" style="display: none;" data-keyboard="false">
    <div class="modal-dialog" role="document" style="width: 900px" style="display: none;">
        <div class="modal-content m-auto">
            <form id="formoperacion" action="">
                <div class="modal-header">
                    <h2 class="modal-title">Detalle Venta</h2>
                </div>
                <input type="hidden" id="codigoventa">
                <input type="hidden" id="despachado">
                <input type="hidden" id="modentrega">
                <input type="hidden" id="jsonpagos">
                <input type="hidden" id="codsucursal">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputfecha" class="control-label">Fecha</label>
                                    <input type="text" readonly autocomplete="off" id="inputfecha" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputtipocomprobante" class="control-label">Tipo Comp</label>
                                    <input type="text" readonly autocomplete="off" id="inputtipocomprobante" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputnumerocomprobante" class="control-label">N° Comprobante</label>
                                    <input type="text" readonly autocomplete="off" id="inputnumerocomprobante" class="form-control" />
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputcliente" class="control-label">Cliente</label>
                                    <input type="text" readonly autocomplete="off" id="inputcliente" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputrestante" class="control-label">Falta Pagar</label>
                                    <input type="number" readonly autocomplete="off" id="inputrestante" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="inputtotal" class="control-label">Total Venta</label>
                                    <input type="number" readonly autocomplete="off" id="inputtotal" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <td class="text-center" colspan="7"><b>DEALLE PRODUCTOS</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>PRODUCTO</b></td>
                                            <td><b>MARCA</b></td>
                                            <td><b>CANT TOTAL</b></td>
                                            <td><b>CANT ENTREGADA</b></td>
                                            <td><b>CANT A ENTREGAR</b></td>
                                            <td><b>P VENTA</b></td>
                                            <td><b>UNIDAD MEDIDA</b></td>
                                        </tr>
                                    </thead>
                                    <tbody id="detallebody">
                                    </tbody>
                                </table>
                            </div>
                            <div class="row" id="dataguiainput">
                                <div class="col-sm-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">N° Guia</label>
                                            <input type="number" class="obligatory form-control" id="nroguia">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Punto de Llegada</label>
                                            <input type="text" class="obligatory form-control" id="puntollegada">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Quien Recibe</label>
                                            <input type="text" class="obligatory form-control" id="quienrecibe">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Quien recoge</label>
                                            <input type="text" class="obligatory form-control" id="quienrecoge">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="quienrecoge" class="control-label">Nombre Transportista</label>
                                            <input required type="text" class="obligatory form-control" id="nombretransportista">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="quienrecoge" class="control-label">RUC transportista</label>
                                            <input required type="text" class="obligatory form-control" id="ructransportista">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="quienrecoge" class="control-label">Marca U. Transporte</label>
                                            <input required type="text" class="obligatory form-control" id="marcatransporte">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="quienrecoge" class="control-label">N° Placa</label>
                                            <input required type="text" class="obligatory form-control" id="nroplaca">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="quienrecoge" class="control-label">N° Licencia conducir</label>
                                            <input required type="text" class="obligatory form-control" id="nlicencia">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="quienrecoge" class="control-label">Certificado Inscripcion</label>
                                            <input required type="text" class="obligatory form-control" id="certinscripcion">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <td class="text-center" colspan="6"><b>HISTORIAL GUIA REMISION</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>FECHA</b></td>
                                            <td><b>N° GUIA</b></td>
                                            <td><b>PUNTO DEST</b></td>
                                            <td><b>QUIEN RECOJIÓ</b></td>
                                            <td><b>QUIEN RECIBIÓ</b></td>
                                            <td><b>DETALLE</b></td>
                                        </tr>
                                    </thead>
                                    <tbody id="historialbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" aria-label="Close" id="btnimprimir" onClick="imprimir()">Imprimir Guia</button>
                    <button type="button" class="modal_close btn btn-danger" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
// ========== Cargar Footer ==========
include("Fragmentos/footer.php");
include("Fragmentos/pie.php");
?>

<script>
    const suc = <?= $codsucursal ?>;
    const codsucursal = <?= $codsucursal ?>;

    window.onload = () => {
        initdata();
    }
    async function initdata() {

        const queryguiasuc = `
            select 'guia' tipocomprobante,  'sucursales' tipo, id, gs.estado, fechainicio fecha_emision, fechallegada, productos, s1.nombre_sucursal sucursalorigen, s2.nombre_sucursal cliente, nroguia codigocomprobante
            from guiasucursal gs 
            inner join sucursal s1 on s1.cod_sucursal = gs.sucursalorigen 
            inner join sucursal s2 on s2.cod_sucursal = gs.sucursaldestino
            where gs.sucursalorigen = ${suc} or gs.sucursaldestino = ${suc}
        `;

        let datasucursales = await get_data_dynamic(queryguiasuc);

        const queryventas = `
            SELECT 'ventas' tipo,  s.nombre_sucursal sucursalorigen,  v.fecha_emision, v.fecha_emision fechallegada, v.tipocomprobante, v.codigocomprobante, v.total, v.pagoefectivo, v.jsonpagos, v.codigoventas, v.modalidadentrega, v.despachado, IFNULL(v.dataguia, '') dataguia, v.pagoacomulado, (v.total - v.pagoacomulado) restante, CONCAT(c.paterno, ' ', c.materno, ' ', c.nombre) as cnatural, c.cedula, cj.razonsocial, cj.ruc 
            from ventas v 
            left join cnatural c on  c.codigoclienten = v.codigoclienten 
            left join  cjuridico cj on cj.codigoclientej = v.codigoclientej 
            left join sucursal s on s.cod_sucursal = IFNULL(v.sucursaldestino, sucursal)
            where  ((v.sucursaldestino is null and v.sucursal = ${suc}) or sucursaldestino = ${suc}) and (modalidadentrega = 'Entrega almacen C/G' or modalidadentrega = 'Entrega inmediata C/G')
        `;

        let data = await get_data_dynamic(queryventas);

        data.map(x => {
            x.cliente = x.cnatural ? x.cnatural : x.razonsocial;
            if (x.modalidadentrega == "Entrega inmediata C/G") {
                x.estado = "ENTREGADO INMEDIATAMENTE";
            } else {
                if (x.despachado == "0") {
                    x.estado = "SIN ENTREGAR";
                } else if (x.despachado == "1") {
                    x.estado = "ENTREGADO TOTALMENTE";
                } else if (x.despachado == "2") {
                    x.estado = "ENTREGADO PARCIALMENTE";
                }
            }
            return x;
        });

        data = data.concat(datasucursales);

        $('#maintable').DataTable({
            data: data,
            destroy: true,
            buttons: [{
                    extend: 'print',
                    className: 'btn dark btn-outline'
                },
                {
                    extend: 'copy',
                    className: 'btn red btn-outline'
                },
                {
                    extend: 'pdf',
                    className: 'btn green btn-outline'
                },
                {
                    extend: 'excel',
                    className: 'btn yellow btn-outline '
                },
                {
                    extend: 'csv',
                    className: 'btn purple btn-outline '
                },
                {
                    extend: 'colvis',
                    className: 'btn dark btn-outline',
                    text: 'Columns'
                }
            ],
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            columns: [
                // {
                //     title: 'Tipo Comp',
                //     data: 'tipocomprobante'
                // },
                {
                    title: 'N° Comp',
                    data: 'codigocomprobante'
                },
                {
                    title: 'Fecha Inicio',
                    data: 'fecha_emision'
                },
                {
                    title: 'Sucursal Origen',
                    data: 'sucursalorigen'
                },
                {
                    title: 'Fecha Llegada',
                    data: 'fechallegada'
                },
                
                {
                    title: 'SucDestino/Cliente',
                    data: 'cliente'
                },
                {
                    title: 'Estado',
                    data: 'estado'
                },
                {
                    title: 'Detalle',
                    render: function(data, type, row) {
                        if (row.tipo === "ventas") {
                            return `
                                <a href="#" 
                                    data-codigoproveedor="${row.ruc ? row.ruc : row.cedula}" 
                                    data-fecha="${row.fecha_emision }" 
                                    data-cliente="${row.cliente}" 
                                    data-codigocomprobante="${row.codigocomprobante}"
                                    data-tipocomprobante="${row.tipocomprobante}"
                                    data-total="${row.total}" 
                                    data-restante="${row.restante}"
                                    data-pagoefectivo="${row.pagoefectivo}"
                                    data-json='${row.jsonpagos}'
                                    data-id="${row.codigoventas}"
                                    data-modentrega='${row.modalidadentrega}'
                                    data-despachado='${row.despachado}'
                                    data-sucursal='${suc}'
                                    data-dataguia='${row.dataguia}'

                                    onclick="pagar(this)">Detalle</a>
                                `;
                        } else {
                            return `
                                <a href="#" data-productos='${row.productos}' data-id='${row.id}'  onclick="verdetalle(this)">Ver Detalle</a href="#"
                            `;
                        }
                    }
                }
            ]
        });
    }

    function changecantidad(e) {
        const limit = parseFloat(e.dataset.limit);
        const value = parseFloat(e.value);
        if (value < 0) {
            e.value = 0;
            return;
        }
        if (value > limit) {
            e.value = 0
        }
    }
    let dataguia = [];

    async function pagar(e) {   
        $("#moperation").modal();
        codigoventa.value = e.dataset.id;
        detallebody.innerHTML = "";
        codsucursal.value = e.dataset.sucursal;
        dataguia = e.dataset.dataguia ? JSON.parse(e.dataset.dataguia.replace(/(\r\n|\n|\r)/gm, "")) : [];
        await fetch(`getDetalleVenta.php?id=${parseInt(e.dataset.id)}`)
            .then(res => res.json())
            .catch(error => console.error("error: ", error))
            .then(res => {
                if (res) {
                    res.forEach(ix => {
                        ix.codigoprod = "" + parseInt(ix.codigoprod)
                        detallebody.innerHTML += `
                        <tr class="producto">
                            <input type="hidden" class="codigoprod" value="${ix.codigoprod}">
                            <td class="descripcion">${ix.nombre_producto}</td>
                            <td>${ix.marca}</td>
                            <td>${ix.cantidad}</td>
                            <td id="prodto_${ix.codigoprod}" >0</td>
                            <td><input type="number" class="form-control cantidad" style="width: 100px" id="prod_${ix.codigoprod}" oninput="changecantidad(this)" data-umedida="${ix.nombre_presentacion}" data-limit="${ix.cantidad}" value="${ix.cantidad}"></td>
                            <td class="pventa">${ix.pventa}</td>
                            <td>${ix.unidad_medida}</td>
                        </tr>
                        `;
                    })
                }
            })

        const pagoefectivo = parseFloat(e.dataset.pagoefectivo);
        // historialbody.innerHTML = "";

        inputfecha.value = e.dataset.fecha;
        inputnumerocomprobante.value = e.dataset.codigocomprobante;
        inputcliente.value = e.dataset.cliente + " - " + e.dataset.codigoproveedor;
        inputtipocomprobante.value = e.dataset.tipocomprobante;
        modentrega.value = e.dataset.modentrega;

        jsonpagos.value = e.dataset.json;
        despachado.value = e.dataset.despachado;
        inputrestante.value = e.dataset.restante;
        inputtotal.value = e.dataset.total;

        if (e.dataset.despachado == 1) {
            dataguiainput.style.display = "none";
            btnimprimir.style.display = "none";
            getSelectorAll(".producto .cantidad").forEach(x1 => x1.disabled = true)
        } else {
            btnimprimir.style.display = "";
            dataguiainput.style.display = "";
            getSelectorAll(".producto .cantidad").forEach(x1 => x1.disabled = false)
        }


        var query = `select despachado from ventas where codigoventas = ${codigoventa.value}`;
        const res = await get_data_dynamic(query).then(r => r);
        despachado.value = res[0].despachado;
        historialbody.innerHTML = "";
        const cantproductos = {};
        dataguia.forEach(x => {
            x.productos.forEach(yy => {
                yy.codigoprod = "" + parseInt(yy.codigoprod)
                if (!cantproductos[yy.codigoprod])
                    cantproductos[yy.codigoprod] = 0;
                cantproductos[yy.codigoprod] += parseFloat(yy.cantidad);
            })
            historialbody.innerHTML += `
                 <tr>
                 <td>${x.fecha}</td>
                 <td>${x.nguia}</td>
                 <td>${x.puntollegada}</td>
                 <td>${x.quienrecoge}</td>
                 <td>${x.quienrecibe}</td>
                 <td><a href="Imprimir/guia_imprimir.php?idventas=${codigoventa.value}&idguia=${x.id}">Ver Guia</a></td>
                 </tr>
                 `;
        })
        for (const [key, value] of Object.entries(cantproductos)) {
            const pp = getSelector(`#prod_${key}`);
            const ppto = getSelector(`#prodto_${key}`);
            pp.dataset.limit = parseFloat(pp.dataset.limit) - value;
            pp.value = pp.dataset.limit
            ppto.textContent = value
        }
    }

    async function imprimir() {
        
        const data = {};
        data.header = '';
        data.detalle = [];

        let error = "";
        getSelectorAll(".obligatory").forEach(x => {
            if (!x.value) {
                error = `Debe llenar ${x.closest(".form-group").querySelector(".control-label").textContent}`;
            }
        });

        if (error) {
            alert(error);
            return;
        }
        btnimprimir.disabled = true;
        const id = uuidv4();
        if ((despachado.value == 2 || despachado.value == 0) && modentrega != "Entrega inmediata C/G") {
            const r = confirm("Se emitirá la guia y se descontará del kardex Almancen. ¿Está seguro que desea continuar");
            if (r) {
                // Conseguimos el numero de guia para el despacho
                // var query = "select value from propiedades where `key` = 'despacho_guia_" + codsucursal + "'";
                // const res = await get_data_dynamic(query).then(r => r);

                // Descontar producto del almacen de la sucursal
                const h = {
                    id,
                    fecha: '<?php echo date("Y-m-d"); ?>',
                    sucursal: codsucursal.value,
                    codventa: codigoventa.value,
                    nguia: nroguia.value,
                    puntollegada: puntollegada.value,
                    quienrecibe: quienrecibe.value,
                    quienrecoge: quienrecoge.value,

                    nombretransportista: nombretransportista.value,
                    ructransportista: ructransportista.value,
                    marcatransporte: marcatransporte.value,
                    nroplaca: nroplaca.value,
                    nlicencia: nlicencia.value,
                    certinscripcion: certinscripcion.value,

                    productos: []
                }
                let isdespachado = 1;
                getSelectorAll(".producto").forEach(item => {
                    const d = {
                        codigoprod: item.querySelector(".codigoprod").value,
                        cantidad: item.querySelector(".cantidad").value,
                        canttotal: item.querySelector(".cantidad").dataset.limit,
                        unidad_medida: item.querySelector(".cantidad").dataset.umedida,
                        nombre_producto: item.querySelector(".descripcion").textContent.replace(/"|'/gi, ''),
                        pventa: item.querySelector(".pventa").textContent,
                    }
                    h.productos.push(d)
                    if (d.canttotal != d.cantidad)
                        isdespachado = 2;

                    if (d.cantidad != 0) {
                        data.detalle.push(`
                        INSERT INTO kardex_alm (codigoprod, codigoguia, numero, detalle, cantidad, saldo, fecha, codsucursal, tipo, tipodocumento, detalleaux)
                        VALUES 
                        (
                            ${d.codigoprod},
                            ${h.codventa},
                            ${h.nguia},
                            'Despacho de Mercancia',
                            ${d.cantidad},
                            (select saldo from kardex_alm kc where kc.codigoprod = ${d.codigoprod} and kc.codsucursal = ${h.sucursal} order by kc.id_kardex_alm desc limit 1) - ${d.cantidad},
                            NOW(),
                            ${h.sucursal},
                            '',
                            'guia',
                            '${inputcliente.value}')
                        `);
                        debugger
                    }
                })
                dataguia.push(h);
                data.header = `
                UPDATE ventas SET 
                    despachado = ${isdespachado}, 
                    nroguia = ${h.nguia},
                    dataguia = '${JSON.stringify(dataguia)}'
                WHERE codigoventas=${h.codventa}`;

                data.detalle.push("UPDATE propiedades SET value = (" + h.nguia + "+1) where `key` = 'despacho_guia_" + codsucursal.value + "'")

                const jjson = JSON.stringify(data).replace(/select/g, "lieuiwuygyq")
                var formData = new FormData();
                formData.append("json", jjson);

                await fetch(`setVenta.php`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .catch(error => console.error("error: ", error))
                    .then(res => {
                        if (res.success) {
                            alert("registro completo!");

                        }
                    });
            } else {
                alert('Debes aceptar el descuento para poder imprimir la guia');
                btnimprimir.removeAttribute("disabled");
                return false;
            }
        }
        id
        var url = `Imprimir/guia_imprimir.php?idventas=${parseInt(codigoventa.value, 10)}&idguia=${id}`;
        window.location = url;

        puntollegada.value = "";
        quienrecibe.value = "";
        quienrecoge.value = "";
        nombretransportista.value = "";
        ructransportista.value = "";
        marcatransporte.value = "";
        nroplaca.value = "";
        nlicencia.value = "";
        certinscripcion.value = "";


        $("#moperation").modal('hide');
        btnimprimir.removeAttribute("disabled");

        setTimeout(() => {
            location.reload()
        }, 1500);
    }


    async function verdetalle(e) {
        $("#mdetalle").modal();

        const query = `
            select id, ac1.usuario personalorigen, ac2.usuario personaldestino, gs.estado, fechainicio, fechallegada, productos,  gs.sucursalorigen sucorigen, gs.sucursaldestino sucdestino, s1.nombre_sucursal sucursalorigen, s2.nombre_sucursal sucursaldestino, nroguia 
            from guiasucursal gs 
            left join acceso ac1 on ac1.codigopersonal = gs.personalorigen
            left join acceso ac2 on ac2.codigopersonal = gs.personaldestino
            inner join sucursal s1 on s1.cod_sucursal = gs.sucursalorigen 
            inner join sucursal s2 on s2.cod_sucursal = gs.sucursaldestino 
            where gs.id = ${e.dataset.id}
        `;

        let trasladosucursal = await get_data_dynamic(query);
        const row = trasladosucursal[0];

        sucdestino.value = row.sucdestino;
        sucorigen.value = row.sucorigen;
        debugger
        if (row.estado === "PENDIENTE" && codsucursal == row.sucorigen) {
            buttonanular.style.display = "";
        } else {
            buttonanular.style.display = "none";
        }
        guiasucursalid.value = e.dataset.id;
        estadotraslado.value = row.estado;
        inputnroguia.value = row.nroguia;
        sucursalorigen.value = row.sucursalorigen;
        sucursaldestino.value = row.sucursaldestino;
        fechasalida.value = row.fechainicio;
        personalorigen.value = row.personalorigen;
        if (row.estado === "COMPLETO") {
            fechallegada.value = row.fechallegada;
            personaldestino.value = row.personaldestino;
        } else {
            fechallegada.value = "";
            personaldestino.value = "";
        }
        listproductos.value = e.dataset.productos;

        $('#detalletabla').DataTable({
            data: JSON.parse(e.dataset.productos),
            destroy: true,
            buttons: [{
                    extend: 'print',
                    className: 'btn dark btn-outline'
                },
                {
                    extend: 'copy',
                    className: 'btn red btn-outline'
                },
                {
                    extend: 'pdf',
                    className: 'btn green btn-outline'
                },
                {
                    extend: 'excel',
                    className: 'btn yellow btn-outline '
                },
                {
                    extend: 'csv',
                    className: 'btn purple btn-outline '
                },
                {
                    extend: 'colvis',
                    className: 'btn dark btn-outline',
                    text: 'Columns'
                }
            ],
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            columns: [{
                    title: 'Producto',
                    data: 'nombreproducto'
                },
                {
                    title: 'Cantidad',
                    data: 'cantidad'
                }
            ]
        });
    }
    const anular = async () => {
        const confirr = confirm("¿Desea anular la guia?");
        if (confirr) {
            const sucodcorigen = sucorigen.value;
            const id = guiasucursalid.value
            const arrayproduct = JSON.parse(listproductos.value);

            const datatotrigger = {
                header: `delete from guiasucursal where id = ${id}`,
                detalle: []
            }

            arrayproduct.forEach(p => {
                const querykalm = `
                    insert into kardex_alm (codigoprod, codigoguia, numero, detalle, cantidad, saldo, codsucursal, tipo, tipodocumento, detalleaux) values 
                    (${p.codigoprod}, ${inputnroguia.value}, ${inputnroguia.value}, 'anulacion', ${p.cantidad}, (select saldo from kardex_alm kc where kc.codigoprod = ${p.codigoprod} and kc.codsucursal = ${sucodcorigen} order by kc.id_kardex_alm desc limit 1) + ${p.cantidad}, ${sucodcorigen}, 'anulacion', 'guia', '${sucursaldestino.value}')`;
                datatotrigger.detalle.push(querykalm);

                const querykcont = `
                        insert into kardex_contable (codigoprod, codigocompras, numero, detalle, cantidad, precio, saldo, sucursal, preciototal, tipocomprobante, codigoproveedor) values 
                        (${p.codigoprod}, 0, ${inputnroguia.value}, 'anulacion', ${p.cantidad}, 0, (select saldo from kardex_contable kc where kc.codigoprod = ${p.codigoprod} and kc.sucursal = ${sucodcorigen} order by kc.id_kardex_contable desc limit 1) + ${p.cantidad}, ${sucodcorigen}, 0, 'guia', 0)`
                datatotrigger.detalle.push(querykcont);
            });


            let res = await ll_dynamic(datatotrigger);

            if (res.success) {
                alert("La guia se anuló correctamente.");
                location.reload()
            } else {
                alert("Hubo un problema");
            }
        }
    }
</script>