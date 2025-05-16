<?php
include_once '../app/config.php';

$cot_id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT nombre, ClienteID, FechaCotizacion, FechaValidez, Estado, Descuento FROM cotizaciones WHERE CotizacionID = :id");
$stmt->execute(['id' => $cot_id]);
$cot = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cot) {
    die('Cotización no encontrada');
}
$nombreCotizacion = htmlspecialchars($cot['nombre']);
$clienteID = $cot['ClienteID'];
$fechaCreacion = $cot['FechaCotizacion'];
$fechaValidez = $cot['FechaValidez'];
$estadoActual = htmlspecialchars($cot['Estado']);
$descuento = $cot['Descuento'];


// Obtener la lista de clientes
$stmt = $pdo->prepare("SELECT id_cliente, nombre, apellido_paterno, apellido_materno, dni_ruc FROM clientes WHERE id_cliente = (SELECT ClienteID FROM cotizaciones WHERE CotizacionID = :id) AND estado = 1");
$stmt->execute(['id' => $cot_id]);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de impuestos (por si se usa en el futuro)
$stmt = $pdo->prepare("SELECT ImpuestoID, Nombre FROM impuestos");
$stmt->execute();
$impuestos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Productos activos
$stmt = $pdo->prepare("SELECT ProductoID, Nombre, PrecioUnitario, Descripcion FROM productos WHERE estado = 1");
$stmt->execute();
$productosModal = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Servicios activos
$stmt = $pdo->prepare("SELECT ServicioID, Nombre, Precio, Descripcion FROM servicios WHERE estado = 1");
$stmt->execute();
$serviciosModal = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="main-wrapper">
    <?php include_once '../layout/parte1.php'; ?>
    <div class="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Editar Cotización</h4>
                        <h6 class="card-subtitle">Complete la información básica y agregue productos/servicios</h6>
                        <form id="crearCotizacionForm" class="form-horizontal m-t-30">

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" id="nombre_cotizacion" name="nombre_cotizacion" class="form-control" placeholder="Nombre de la cotización" value="<?= $nombreCotizacion ?>" required>
                            </div>

                            <!-- Selección de cliente -->
                            <div class="form-group">
                                <label for="cliente_id">Cliente</label>
                                <?php
                                $nombre = htmlspecialchars($clientes[0]['nombre'])
                                    . ($clientes[0]['apellido_paterno'] !== null ? ' ' . htmlspecialchars($clientes[0]['apellido_paterno']) : '')
                                    . ($clientes[0]['apellido_materno'] !== null ? ' ' . htmlspecialchars($clientes[0]['apellido_materno']) : '');
                                ?>
                                <input type="text" id="cliente_id" name="cliente_id" value="<?= $nombre ?> (<?= $clientes[0]['dni_ruc'] !== null ? htmlspecialchars($clientes[0]['dni_ruc']) : 'SIN NUMERO DE IDENTIDAD' ?>)" class="form-control" readonly>
                            </div>

                            <!-- Fechas, estado y notas -->
                            <div class="form-group">
                                <label for="fecha_cotizacion">Fecha de Cotización</label>
                                <input type="date" id="fecha_cotizacion" name="fecha_cotizacion" value="<?= $fechaCreacion ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_validez">Fecha de Validez (Opcional)</label>
                                <input type="date" id="fecha_validez" name="fecha_validez" value="<?= $fechaValidez ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado" class="form-control" required>
                                    <option value="Pendiente" <?= $estadoActual === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="Aprobado" <?= $estadoActual === 'Aprobado' ? 'selected' : ''; ?>>Aprobado</option>
                                    <option value="Rechazado" <?= $estadoActual === 'Rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="notas">Notas (Opcional)</label>
                                <textarea id="notas" name="notas" class="form-control" rows="3"></textarea>
                            </div>
                            

                            <!-- Botones para modales -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalProductos">Agregar Producto</button>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalServicios">Agregar Servicio</button>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalOtros">Agregar Otro</button>

                            <hr>
                            <h4>Detalle de la Cotización</h4>
                            <div id="detalleCotizacion"></div>

                            <!-- Botones de previsualizar y guardar -->
                            <button id="btnPreview" type="button" class="btn btn-warning">Previsualizar</button>



                        </form>



                        <!-- Contenedor de previsualización -->
                        <div
                            id="preview"
                            class="mt-4"
                            style="display: none; border: 1px solid #ccc; padding: 1em;">
                            <!-- Aquí se inyecta el HTML que devuelve preview_cotizacion.php -->

                            <!-- Botón “Volver a editar” -->

                        </div>

                        <button
                            id="volverEditar"
                            class="btn btn-secondary mt-3"
                            type="button"
                            style="display: none;">
                            ← Volver a editar
                        </button>
                        <!-- Botón para guardar/crear la cotización -->
                        <button
                            id="btnGuardar"
                            type="button"
                            class="btn btn-success mt-3"
                            style="display: none;">
                            Crear Cotización
                        </button>

                        <div id="mensajeExitoCotizacion" class="mt-2 text-success font-weight-bold" style="display:none;"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once '../layout/parte2.php';
    include_once 'modal.php'; ?>
</div>

<script>
$(document).ready(function() {
    let itemsCotizacion = [];
    let igvOpciones = { producto: 0.18, servicio: 0.18 };
    let descuentos = { producto: 0, servicio: 0 }; // Se almacenan como monto fijo en Soles

    // ── Función principal para renderizar el detalle ──
    // Esta función ahora solo se llama al añadir/eliminar ítems o al cargar la cotización.
    // Los cambios de descuento o IGV la evitan para no saltar.
    function actualizarDetalleCotizacion() {
        const $detalleDiv = $('#detalleCotizacion').empty();

        // Separar productos y servicios
        const productos = [], productosIdx = [];
        const servicios = [], serviciosIdx = [];
        itemsCotizacion.forEach((item, idx) => {
            if (item.tipo === 'producto') {
                productos.push(item);
                productosIdx.push(idx);
            } else if (item.tipo === 'servicio') {
                servicios.push(item);
                serviciosIdx.push(idx);
            }
        });

        let totalProdFinal = 0; // Variable para el total final de productos (neto - descuento + IGV)
        let totalServFinal = 0; // Variable para el total final de servicios (neto - descuento + IGV)


        // — SECCIÓN PRODUCTOS —
        if (productos.length) {
            $detalleDiv.append('<h4>Productos</h4>');
            $detalleDiv.append(`
                <div class="form-group mb-2">
                  <label>IGV Productos:</label>
                  <select class="selectIGV form-control w-auto d-inline-block ml-2" data-tipo="producto">
                    <option value="0">Sin IGV</option>
                    <option value="0.18">IGV 18%</option>
                  </select>
                </div>
                <div class="form-group mb-2">
                  <label>Descuento Productos (Soles):</label>
                  <input type="number"
                         class="form-control w-auto d-inline-block ml-2 descuento-productos-input" // Añadido "-input" para diferenciar
                         min="0" step="0.01"
                         value="${descuentos.producto.toFixed(2)}" // Muestra el descuento guardado
                         placeholder="0.00">
                </div>
            `);
            // Selecciona la opción de IGV correcta al renderizar
            $detalleDiv.find('select[data-tipo="producto"]').val(igvOpciones.producto.toString());

            // Totales netos y cálculos para productos
            let totalProdNet = 0;
            productos.forEach(item => totalProdNet += item.cantidad * item.precioUnitario);

            const montoDescuentoP = descuentos.producto; // Usa el monto fijo de descuento guardado
            // Calcula el subtotal después del descuento. Asegúrate de que no sea negativo.
            const subtotalProdDesc = Math.max(0, totalProdNet - montoDescuentoP);
            // Calcula el IGV sobre el subtotal después del descuento
            const montoIgvP = subtotalProdDesc * igvOpciones.producto;

            // Calcula el total final para productos
            totalProdFinal = subtotalProdDesc + montoIgvP;


            // Tabla productos
            const $tableP = $(`
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Nombre</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th><th>Acciones</th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr><th colspan="3" class="text-right"><strong>Total Neto:</strong></th><th colspan="2" class="total-neto-productos"></th></tr>
                  <tr><th colspan="3" class="text-right"><strong>Descuento:</strong></th><th colspan="2" class="descuento-productos-display"></th></tr> <tr><th colspan="3" class="text-right"><strong>Monto IGV:</strong></th><th colspan="2" class="monto-igv-productos"></th></tr>
                  <tr><th colspan="3" class="text-right"><strong>Total con IGV:</strong></th><th colspan="2" class="total-con-igv-productos"></th></tr>
                </tfoot>
              </table>
            `);
            const $bodyP = $tableP.find('tbody');
            productos.forEach((item, i) => {
                const idx = productosIdx[i];
                const sub = item.cantidad * item.precioUnitario;
                $bodyP.append(`
                  <tr>
                    <td>${item.nombre}</td>
                    <td>
                      <button class="btn btn-sm btn-outline-secondary disminuirCantidad" data-index="${idx}">-</button>
                      <span class="mx-2">${item.cantidad}</span>
                      <button class="btn btn-sm btn-outline-secondary aumentarCantidad" data-index="${idx}">+</button>
                    </td>
                    <td>S/ ${item.precioUnitario.toFixed(2)}</td>
                    <td>S/ ${sub.toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm eliminarItem" data-index="${idx}">Eliminar</button></td>
                  </tr>
                `);
            });
            // Actualiza los valores mostrados en la tabla
            $tableP.find('.total-neto-productos').text(`S/ ${totalProdNet.toFixed(2)}`);
            $tableP.find('.descuento-productos-display').text(`S/ ${montoDescuentoP.toFixed(2)}`); // Usa "-display"
            $tableP.find('.monto-igv-productos').text(`S/ ${montoIgvP.toFixed(2)}`);
            $tableP.find('.total-con-igv-productos').text(`S/ ${totalProdFinal.toFixed(2)}`); // Muestra el total final calculado
            $detalleDiv.append($tableP);

            $detalleDiv.append('<h4>Descripción para la sección de productos</h4>');
            $detalleDiv.append(`
                <div class="form-group">
                  <textarea id="descripcion_cotizacion_productos" class="form-control" rows="3"></textarea>
                </div>
            `);
        } else {
            $detalleDiv.append('<p>No se han agregado productos.</p>');
        }

        // — SECCIÓN SERVICIOS —
        if (servicios.length) {
            $detalleDiv.append('<h4 class="mt-4">Servicios</h4>');
            $detalleDiv.append(`
                <div class="form-group mb-2">
                  <label>IGV Servicios:</label>
                  <select class="selectIGV form-control w-auto d-inline-block ml-2" data-tipo="servicio">
                    <option value="0">Sin IGV</option>
                    <option value="0.18">IGV 18%</option>
                  </select>
                </div>
                <div class="form-group mb-2">
                  <label>Descuento Servicios (Soles):</label>
                  <input type="number"
                         class="form-control w-auto d-inline-block ml-2 descuento-servicios-input" // Añadido "-input" para diferenciar
                         min="0" step="0.01"
                         value="${descuentos.servicio.toFixed(2)}" // Muestra el descuento guardado
                         placeholder="0">
                </div>
            `);
            // Selecciona la opción de IGV correcta al renderizar
            $detalleDiv.find('select[data-tipo="servicio"]').val(igvOpciones.servicio.toString());

            // Totales netos y cálculos para servicios
            let totalServNet = 0;
            servicios.forEach(item => totalServNet += item.cantidad * item.precioUnitario);

            const montoDescuentoS = descuentos.servicio; // Usa el monto fijo de descuento guardado
             // Calcula el subtotal después del descuento. Asegúrate de que no sea negativo.
            const subtotalServDesc = Math.max(0, totalServNet - montoDescuentoS);
            // Calcula el IGV sobre el subtotal después del descuento
            const montoIgvS = subtotalServDesc * igvOpciones.servicio;

            // Calcula el total final para servicios
            totalServFinal = subtotalServDesc + montoIgvS;


            const $tableS = $(`
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Nombre</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th><th>Acciones</th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr><th colspan="3" class="text-right"><strong>Total Neto:</strong></th><th colspan="2" class="total-neto-servicios"></th></tr>
                  <tr><th colspan="3" class="text-right"><strong>Descuento:</strong></th><th colspan="2" class="descuento-servicios-display"></th></tr> <tr><th colspan="3" class="text-right"><strong>Monto IGV:</strong></th><th colspan="2" class="monto-igv-servicios"></th></tr>
                  <tr><th colspan="3" class="text-right"><strong>Total con IGV:</strong></th><th colspan="2" class="total-con-igv-servicios"></th></tr>
                </tfoot>
              </table>
            `);
            const $bodyS = $tableS.find('tbody');
            servicios.forEach((item, i) => {
                const idx = serviciosIdx[i];
                const sub = item.cantidad * item.precioUnitario;
                $bodyS.append(`
                  <tr>
                    <td>${item.nombre}</td>
                    <td>
                      <button class="btn btn-sm btn-outline-secondary disminuirCantidad" data-index="${idx}">-</button>
                      <span class="mx-2">${item.cantidad}</span>
                      <button class="btn btn-sm btn-outline-secondary aumentarCantidad" data-index="${idx}">+</button>
                    </td>
                    <td>S/ ${item.precioUnitario.toFixed(2)}</td>
                    <td>S/ ${sub.toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm eliminarItem" data-index="${idx}">Eliminar</button></td>
                  </tr>
                `);
            });
            // Actualiza los valores mostrados en la tabla
            $tableS.find('.total-neto-servicios').text(`S/ ${totalServNet.toFixed(2)}`);
            $tableS.find('.descuento-servicios-display').text(`S/ ${montoDescuentoS.toFixed(2)}`); // Usa "-display"
            $tableS.find('.monto-igv-servicios').text(`S/ ${montoIgvS.toFixed(2)}`);
            $tableS.find('.total-con-igv-servicios').text(`S/ ${totalServFinal.toFixed(2)}`); // Muestra el total final calculado
            $detalleDiv.append($tableS);

            $detalleDiv.append('<h4>Descripción para la sección de servicios</h4>');
            $detalleDiv.append(`
                <div class="form-group">
                  <textarea id="descripcion_cotizacion_servicios" class="form-control" rows="3"></textarea>
                </div>
            `);
        } else {
            $detalleDiv.append('<p>No se han agregado servicios.</p>');
        }

        // ── Total general ──
        // Suma los totales finales de cada sección (que ya incluyen su descuento e IGV)
        const totalGeneral = totalProdFinal + totalServFinal;

        // Añadir un ID específico al total general para fácil actualización
        $detalleDiv.append(`<div class="mt-4"><h5 id="totalGeneralDisplay">Total General: S/ ${totalGeneral.toFixed(2)}</h5></div>`);

        // Cargar las descripciones guardadas si existen (al cargar una cotización existente)
        // Asumo que estas descripciones se cargan en el objeto 'data' al obtener la cotización.
        // Si no, necesitarías cargarlas aparte o almacenarlas en `itemsCotizacion` de alguna manera.
        // La carga inicial ya se maneja al inicio del script, aquí solo aseguramos que el valor se mantiene.
        $('#descripcion_cotizacion_productos').val($('#descripcion_cotizacion_productos').val() || '');
        $('#descripcion_cotizacion_servicios').val($('#descripcion_cotizacion_servicios').val() || '');
    }

    // Helper function to calculate totals without full re-render
    function updateCalculationsAndDisplay() {
         // Separar productos y servicios para los cálculos
        const productos = itemsCotizacion.filter(item => item.tipo === 'producto');
        const servicios = itemsCotizacion.filter(item => item.tipo === 'servicio');

        // --- Cálculos para Productos ---
        let totalProdNet = 0;
        productos.forEach(item => totalProdNet += item.cantidad * item.precioUnitario);
        const montoDescuentoP = descuentos.producto;
        const subtotalProdDesc = Math.max(0, totalProdNet - montoDescuentoP);
        const montoIgvP = subtotalProdDesc * igvOpciones.producto;
        const totalProdFinal = subtotalProdDesc + montoIgvP;

        // --- Cálculos para Servicios ---
        let totalServNet = 0;
        servicios.forEach(item => totalServNet += item.cantidad * item.precioUnitario);
        const montoDescuentoS = descuentos.servicio;
        const subtotalServDesc = Math.max(0, totalServNet - montoDescuentoS);
        const montoIgvS = subtotalServDesc * igvOpciones.servicio;
        const totalServFinal = subtotalServDesc + montoIgvS;

        // --- Calcular Total General ---
        const totalGeneral = totalProdFinal + totalServFinal;

        // --- Actualizar Display (solo los valores cambiantes) ---
        const $detalleDiv = $('#detalleCotizacion');

        // Actualizar totales de Productos (si la sección existe)
        if (productos.length) {
             $detalleDiv.find('.descuento-productos-display').text(`S/ ${montoDescuentoP.toFixed(2)}`);
             $detalleDiv.find('.monto-igv-productos').text(`S/ ${montoIgvP.toFixed(2)}`);
             $detalleDiv.find('.total-con-igv-productos').text(`S/ ${totalProdFinal.toFixed(2)}`);
             // El total neto de productos no cambia con descuento/IGV, no necesita actualización aquí
        }


        // Actualizar totales de Servicios (si la sección existe)
        if (servicios.length) {
             $detalleDiv.find('.descuento-servicios-display').text(`S/ ${montoDescuentoS.toFixed(2)}`);
             $detalleDiv.find('.monto-igv-servicios').text(`S/ ${montoIgvS.toFixed(2)}`);
             $detalleDiv.find('.total-con-igv-servicios').text(`S/ ${totalServFinal.toFixed(2)}`);
             // El total neto de servicios no cambia con descuento/IGV, no necesita actualización aquí
        }

        // Actualizar Total General
        $('#totalGeneralDisplay').text(`Total General: S/ ${totalGeneral.toFixed(2)}`);
    }


    // ── Eventos de interacción ──
    $('#detalleCotizacion')
      // Estos eventos siguen redibujando todo porque cambian la estructura de la tabla
      .on('click', '.aumentarCantidad', function() {
          const idx = +$(this).data('index');
          itemsCotizacion[idx].cantidad++;
          actualizarDetalleCotizacion(); // Redibuja todo
      })
      .on('click', '.disminuirCantidad', function() {
          const idx = +$(this).data('index');
          if (itemsCotizacion[idx].cantidad > 1) {
              itemsCotizacion[idx].cantidad--;
              actualizarDetalleCotizacion(); // Redibuja todo
          }
      })
      .on('click', '.eliminarItem', function() {
          const idx = +$(this).data('index');
          itemsCotizacion.splice(idx, 1);
          actualizarDetalleCotizacion(); // Redibuja todo
      })
      // Evento para cambiar IGV - Ahora usa la función updateCalculationsAndDisplay
      .on('change', '.selectIGV', function() {
          const tipo = $(this).data('tipo');
          igvOpciones[tipo] = parseFloat($(this).val());
          updateCalculationsAndDisplay(); // Solo actualiza los cálculos y display
      })
      // Eventos para los inputs de descuento - Ahora usan la función updateCalculationsAndDisplay
      .on('input', '.descuento-productos-input', function() { // Usa el nuevo selector
          descuentos.producto = parseFloat($(this).val()) || 0;
          if (descuentos.producto < 0) descuentos.producto = 0;
          updateCalculationsAndDisplay(); // Solo actualiza los cálculos y display
      })
      .on('input', '.descuento-servicios-input', function() { // Usa el nuevo selector
          descuentos.servicio = parseFloat($(this).val()) || 0;
           if (descuentos.servicio < 0) descuentos.servicio = 0;
          updateCalculationsAndDisplay(); // Solo actualiza los cálculos y display
      });


    // ── Modales AGREGAR PRODUCTO/SERVICIO/OTRO ──
    // Estos eventos añaden items y SÍ deben redibujar todo para mostrar el nuevo item en la tabla
    $('#agregarProductoModal').on('click', function(e) {
      e.preventDefault();
      const id       = $('#producto_id_modal').val();
      const cant     = parseFloat($('#cantidad_producto_modal').val());
      const nombre   = $('#producto_id_modal option:selected').text();
      const precio   = parseFloat($('#producto_id_modal option:selected').data('precio'));
      if (id && cant>0) {
        const idx = itemsCotizacion.findIndex(i=>i.tipo==='producto'&&i.id==id);
        if (idx>-1) itemsCotizacion[idx].cantidad += cant;
        else itemsCotizacion.push({ tipo:'producto', id, nombre, cantidad:cant, precioUnitario:precio });
        $('#modalProductos').modal('hide');
        actualizarDetalleCotizacion(); // Redibuja todo
      } else alert('Seleccione producto y cantidad válidos.');
    });

    $('#agregarServicioModal').on('click', function(e) {
      e.preventDefault();
      const id       = $('#servicio_id_modal').val();
      const cant     = parseFloat($('#cantidad_servicio_modal').val());
      const nombre   = $('#servicio_id_modal option:selected').text();
      const precio   = parseFloat($('#servicio_id_modal option:selected').data('precio'));
      if (id && cant>0) {
        const idx = itemsCotizacion.findIndex(i=>i.tipo==='servicio'&&i.id==id);
         // Asegurarse de no crear duplicados exactos si ya existe por ID
        if (idx>-1) itemsCotizacion[idx].cantidad += cant;
        else itemsCotizacion.push({ tipo:'servicio', id, nombre, cantidad:cant, precioUnitario:precio });
        $('#modalServicios').modal('hide');
        actualizarDetalleCotizacion(); // Redibuja todo
      } else alert('Seleccione servicio y cantidad válidos.');
    });

    $('#agregarOtro').on('click', function(e) {
      e.preventDefault();
      const tipo        = $('#tipo_otro').val();
      const nombre      = $('#nombre_otro').val().trim();
      const cant        = parseFloat($('#cantidad_otro').val());
      const precio      = parseFloat($('#precio_otro').val());
      const descripcion = $('#descripcion_otro').val().trim();
      if (!nombre||isNaN(cant)||cant<=0||isNaN(precio)||!descripcion)
        return alert('Complete todos los campos de "Otro".');
      // Buscar si ya existe un item "Otro" con el mismo tipo, nombre, precio y descripción para sumar cantidad
      const idx = itemsCotizacion.findIndex(i=>i.tipo===tipo&&i.nombre===nombre&&i.precioUnitario===precio&&i.descripcion===descripcion);
      if (idx>-1) {
        itemsCotizacion[idx].cantidad += cant;
        $('#modalOtros').modal('hide');
        $('#nombre_otro,#cantidad_otro,#precio_otro,#descripcion_otro').val(''); // Limpia los campos
        return actualizarDetalleCotizacion(); // Redibuja todo
      }
      // Guardar en BD y luego agregar al array
      $.post('insertar_otros.php', { tipo, nombre, precio, descripcion }, function(res) {
        if (!res.success) return alert('Error BD: '+res.error);
        // Asumiendo que res.id contiene el ID del item "Otro" insertado
        itemsCotizacion.push({ tipo, id:res.id, nombre, cantidad:cant, precioUnitario:precio, descripcion });
        $('#modalOtros').modal('hide');
        $('#nombre_otro,#cantidad_otro,#precio_otro,#descripcion_otro').val(''); // Limpia los campos
        actualizarDetalleCotizacion(); // Redibuja todo
      }, 'json').fail(xhr=>{
        alert('Error guardando en BD el item "Otro": '+(xhr.responseJSON?.error||xhr.statusText));
      });
    });


    // ── PREVIEW y GUARDAR ──
     $('#btnPreview').on('click', function(e) {
        e.preventDefault();
        // Recoger las descripciones de sección antes de ocultar los campos
        const descripcionProductos = $('#descripcion_cotizacion_productos').val();
        const descripcionServicios = $('#descripcion_cotizacion_servicios').val();

        const data = {
            cliente_id: $('#cliente_id').val(),
            nombre_cotizacion: $('#nombre_cotizacion').val(),
            fecha_cotizacion:  $('#fecha_cotizacion').val(),
            fecha_validez:     $('#fecha_validez').val(),
            estado:            $('#estado').val(),
            notas:             $('#notas').val(),
            igv:               igvOpciones,
            descuentos:        descuentos, // Incluye los descuentos fijos
            descripcion_cotizacion_productos: descripcionProductos, // Envía la descripción de productos
            descripcion_cotizacion_servicios: descripcionServicios, // Envía la descripción de servicios
            items:             itemsCotizacion
        };
        $.post('preview_cotizacion.php', data, html=>{
            $('#preview').html(html).show();
            $('#btnGuardar').show();
            // Ocultar secciones de edición
            $('#detalleCotizacion, #formCotizacion, #formProductos, #formServicios, #formOtros').hide(); // Oculta los divs de formularios y detalle
            $('#crearCotizacionForm').hide(); // Si hay un contenedor principal del formulario
            $('#volverEditar').show();
        }).fail(()=>alert('Error al generar preview.'));
    });

    $('#volverEditar').on('click', ()=> {
        $('#preview, #btnGuardar, #volverEditar').hide();
         // Mostrar secciones de edición
        $('#detalleCotizacion, #formCotizacion, #formProductos, #formServicios, #formOtros').show(); // Muestra los divs de formularios y detalle
        $('#crearCotizacionForm').show(); // Si hay un contenedor principal del formulario
        // Vuelve a renderizar el detalle para asegurar que los campos de descripción existen
        // (aunque los valores ya deberían estar en los inputs si no fueron borrados por el hide())
        // Si el hide() elimina los elementos, necesitarías guardar los valores en variables
        actualizarDetalleCotizacion(); // Esto redibujará el DOM, incluyendo los textareas de descripción
    });


  

   // ── CARGAR COTIZACIÓN EXISTENTE ──
const cot_id = <?= (int)($_GET['id'] ?? 0) ?>;
if (cot_id > 0) {
  $.getJSON('get_cotizacion.php', { id: cot_id }, data => {
    if (data.error) {
      return alert(data.error);
    }

    // 1) Carga los campos principales de la cotización
    $('#cliente_id').val(data.cliente_id);
    $('#nombre_cotizacion').val(data.nombre_cotizacion);
    $('#fecha_cotizacion').val(data.fecha_cotizacion);
    $('#fecha_validez').val(data.fecha_validez);
    $('#estado').val(data.estado);
    $('#notas').val(data.notas);

    // 2) Prepara las opciones de IGV y descuentos seccionales
    igvOpciones = data.igv || { producto: 0.18, servicio: 0.18 };
    descuentos  = data.descuentos || { producto: 0, servicio: 0 };

    // 3) Asigna los items de la cotización
    itemsCotizacion = data.items || [];

    // 4) Renderiza la sección de detalle (crea todos los inputs/selects,
    //    incluyendo los de descuento e IGV, basados en las variables globales)
    actualizarDetalleCotizacion();

    // 5) Ahora que los textareas (#descripcion_cotizacion_*) ya existen,
    //    carga sus valores desde el JSON
    $('#descripcion_cotizacion_productos').val(data.descripcion_cotizacion_productos || '');
    $('#descripcion_cotizacion_servicios').val(data.descripcion_cotizacion_servicios || '');

  }).fail(() => {
    alert('No se pudo cargar la cotización.');
  });
} else {
  // Para nueva cotización, renderiza vacío
  actualizarDetalleCotizacion();
}


$('#btnGuardar').on('click', function(e) {
  e.preventDefault();

  // Recoger las descripciones de sección justo antes de guardar
  const descripcionProductos = $('#descripcion_cotizacion_productos').val();
  const descripcionServicios = $('#descripcion_cotizacion_servicios').val();

  // Prepara el payload, incluyendo el ID si es edición
  const payloadObj = {
    id: cot_id,  // cot_id debe venir definido arriba (<?= (int)($_GET['id']??0) ?>)
    cliente_id: $('#cliente_id').val(),
    nombre_cotizacion: $('#nombre_cotizacion').val(),
    fecha_cotizacion: $('#fecha_cotizacion').val(),
    fecha_validez: $('#fecha_validez').val(),
    estado: $('#estado').val(),
    notas: $('#notas').val(),
    igv: igvOpciones,
    descuentos: descuentos,
    descripcion_cotizacion_productos: descripcionProductos,
    descripcion_cotizacion_servicios: descripcionServicios,
    items: itemsCotizacion
  };

  $.ajax({
    url: 'proceso_cotizacion_editar.php',
    type: 'POST',
    data: JSON.stringify(payloadObj),      // enviamos JSON crudo
    contentType: 'application/json; charset=utf-8',
    dataType: 'json'
  })
  .done(res => {
    if (res.success) {
      // Redirige a la vista de la cotización existente
      window.location.href = 'ver_cotizacion.php?id=' + res.id_cotizacion;
    } else {
      alert('Error al guardar: ' + res.error);
    }
  })
  .fail((xhr, status, error) => {
    const msg = xhr.responseJSON?.error || xhr.responseText || error;
    alert('Falló la petición de guardado: ' + status + ' – ' + msg);
  });
});


});
</script>


