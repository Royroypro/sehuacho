<?php
include_once '../app/config.php';




// Obtener la lista de TODOS los clientes activos para selección
$stmt = $pdo->prepare("SELECT id_cliente, nombre, apellido_paterno, apellido_materno, dni_ruc FROM clientes WHERE estado = 1 ORDER BY nombre, apellido_paterno");
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de impuestos (por si se usa en el futuro) - Keep this as it might be needed
$stmt = $pdo->prepare("SELECT ImpuestoID, Nombre FROM impuestos");
$stmt->execute();
$impuestos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Productos activos - Keep this for selection in modals
$stmt = $pdo->prepare("SELECT ProductoID, Nombre, PrecioUnitario, Descripcion FROM productos WHERE estado = 1");
$stmt->execute();
$productosModal = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Servicios activos - Keep this for selection in modals
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
                        <h4 class="card-title">Crear Nueva Cotización</h4>
                        <h6 class="card-subtitle">Complete la información básica y agregue productos/servicios para la nueva cotización</h6>
                        <form id="crearCotizacionForm" class="form-horizontal m-t-30">

                            <div class="form-group">
                                <label for="nombre_cotizacion">Nombre de la Cotización</label>
                                <input type="text" id="nombre_cotizacion" name="nombre_cotizacion" class="form-control" placeholder="Nombre de la cotización" required>
                            </div>

                            <div class="form-group">
                                <label for="cliente_id_select">Cliente</label>
                                <select id="cliente_id_select" name="cliente_id" class="form-control" required>
                                    <option value="">Seleccione un cliente</option>
                                    <?php foreach ($clientes as $cliente):
                                        $nombreCompleto = htmlspecialchars($cliente['nombre'])
                                            . ($cliente['apellido_paterno'] !== null ? ' ' . htmlspecialchars($cliente['apellido_paterno']) : '')
                                            . ($cliente['apellido_materno'] !== null ? ' ' . htmlspecialchars($cliente['apellido_materno']) : '');
                                        $dniRuc = $cliente['dni_ruc'] !== null ? htmlspecialchars($cliente['dni_ruc']) : 'SIN NUMERO DE IDENTIDAD';
                                        ?>
                                        <option value="<?= $cliente['id_cliente'] ?>">
                                            <?= $nombreCompleto ?> (<?= $dniRuc ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                </div>

                            <div class="form-group">
                                <label for="fecha_cotizacion">Fecha de Cotización</label>
                                <input type="date" id="fecha_cotizacion" name="fecha_cotizacion" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_validez">Fecha de Validez (Opcional)</label>
                                <input type="date" id="fecha_validez" name="fecha_validez" class="form-control"
                                       value="<?= date('Y-m-d', strtotime('+1 month')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado" class="form-control" required>
                                    <option value="Pendiente" selected>Pendiente</option>
                                    <option value="Aprobado">Aprobado</option>
                                    <option value="Rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="notas">Notas (Opcional)</label>
                                <textarea id="notas" name="notas" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalProductos">Agregar Producto</button>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalServicios">Agregar Servicio</button>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalOtros">Agregar Otro</button>

                            <hr>
                            <h4>Detalle de la Cotización</h4>
                            <div id="detalleCotizacion">
                                </div>

                            <button id="btnPreview" type="button" class="btn btn-warning">Previsualizar</button>

                        </form>

                        <div
                            id="preview"
                            class="mt-4"
                            style="display: none; border: 1px solid #ccc; padding: 1em;">
                            </div>

                        <button
                            id="volverEditar"
                            class="btn btn-secondary mt-3"
                            type="button"
                            style="display: none;">
                            ← Volver a editar
                        </button>
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
    <?php
    include_once '../layout/parte2.php';
    // Assume 'modal.php' contains the modals for adding products, services, others
    include_once 'modal.php';
    ?>
</div>
<script>
$(document).ready(function() {
    // Start with empty items array
    let itemsCotizacion = [];
    // Default IGV and discount options for a new cotization
    let igvOpciones = { producto: 0.18, servicio: 0.18 }; // Default to 18% IGV
    let descuentos = { producto: 0, servicio: 0 }; // Default to 0 discount

    // ── Function principal para renderizar el detalle ──
    // This function remains largely the same, but it now renders from an empty state initially.
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
            // Selecciona la opción de IGV correcta al renderizar (uses the default/current value)
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
                  <textarea id="descripcion_cotizacion_productos" class="form-control" rows="3">Los productos llegan de 1 a dos dias despues de pago</textarea>
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
            // Selecciona la opción de IGV correcta al renderizar (uses the default/current value)
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
                  <textarea id="descripcion_cotizacion_servicios" class="form-control" rows="3">El proyecto se iniciará con el 50% y se cancelará el otro 50% terminado el proyecto</textarea>
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

         // Ensure description textareas exist and can receive values, even if empty
        $('#descripcion_cotizacion_productos').val($('#descripcion_cotizacion_productos').val() || '');
        $('#descripcion_cotizacion_servicios').val($('#descripcion_cotizacion_servicios').val() || '');
    }

    // Helper function to calculate totals without full re-render - Keep this logic
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
             // The net total for products doesn't change with discount/IGV, no update needed here
        }


        // Actualizar totales de Servicios (si la sección existe)
        if (servicios.length) {
             $detalleDiv.find('.descuento-servicios-display').text(`S/ ${montoDescuentoS.toFixed(2)}`);
             $detalleDiv.find('.monto-igv-servicios').text(`S/ ${montoIgvS.toFixed(2)}`);
             $detalleDiv.find('.total-con-igv-servicios').text(`S/ ${totalServFinal.toFixed(2)}`);
             // The net total for services doesn't change with discount/IGV, no update needed here
        }

        // Actualizar Total General
        $('#totalGeneralDisplay').text(`Total General: S/ ${totalGeneral.toFixed(2)}`);
    }


    // ── Eventos de interacción ──
    // These events trigger a full re-render because they change table structure/content
    $('#detalleCotizacion')
      .on('click', '.aumentarCantidad', function() {
          const idx = +$(this).data('index');
          itemsCotizacion[idx].cantidad++;
          actualizarDetalleCotizacion(); // Redraws everything
      })
      .on('click', '.disminuirCantidad', function() {
          const idx = +$(this).data('index');
          if (itemsCotizacion[idx].cantidad > 1) {
              itemsCotizacion[idx].cantidad--;
              actualizarDetalleCotizacion(); // Redraws everything
          }
      })
      .on('click', '.eliminarItem', function() {
          const idx = +$(this).data('index');
          itemsCotizacion.splice(idx, 1);
          actualizarDetalleCotizacion(); // Redraws everything
      })
      // Event for changing IGV - Uses updateCalculationsAndDisplay
      .on('change', '.selectIGV', function() {
          const tipo = $(this).data('tipo');
          igvOpciones[tipo] = parseFloat($(this).val());
          updateCalculationsAndDisplay(); // Only updates calculations and display
      })
      // Events for discount inputs - Uses updateCalculationsAndDisplay
      .on('input', '.descuento-productos-input', function() { // Uses the new selector
          descuentos.producto = parseFloat($(this).val()) || 0;
          if (descuentos.producto < 0) descuentos.producto = 0;
          updateCalculationsAndDisplay(); // Only updates calculations and display
      })
      .on('input', '.descuento-servicios-input', function() { // Uses the new selector
          descuentos.servicio = parseFloat($(this).val()) || 0;
           if (descuentos.servicio < 0) descuentos.servicio = 0;
          updateCalculationsAndDisplay(); // Only updates calculations and display
      });


    // ── Modales AGREGAR PRODUCTO/SERVICIO/OTRO ──
    // These events add items and SHOULD trigger a full re-render to show the new item in the table
    $('#agregarProductoModal').on('click', function(e) {
      e.preventDefault();
      const id     = $('#producto_id_modal').val();
      const cant   = parseFloat($('#cantidad_producto_modal').val());
      const nombre = $('#producto_id_modal option:selected').text();
      const precio = parseFloat($('#producto_id_modal option:selected').data('precio'));
      if (id && cant > 0) {
        // For creation, simply add the item. FindIndex logic might be useful if you want to
        // combine identical items added multiple times, but for a new cotization, just adding is fine.
        // The findIndex logic for summing quantities is useful here to avoid duplicate rows for the same item.
         const existingItemIndex = itemsCotizacion.findIndex(item => item.tipo === 'producto' && item.id === id);
         if (existingItemIndex > -1) {
             itemsCotizacion[existingItemIndex].cantidad += cant;
         } else {
             itemsCotizacion.push({ tipo:'producto', id, nombre, cantidad:cant, precioUnitario:precio });
         }

        $('#modalProductos').modal('hide');
        $('#cantidad_producto_modal').val(1); // Reset quantity for next add
        actualizarDetalleCotizacion(); // Redraws everything
      } else alert('Seleccione producto y cantidad válidos.');
    });

    $('#agregarServicioModal').on('click', function(e) {
      e.preventDefault();
      const id     = $('#servicio_id_modal').val();
      const cant   = parseFloat($('#cantidad_servicio_modal').val());
      const nombre = $('#servicio_id_modal option:selected').text();
      const precio = parseFloat($('#servicio_id_modal option:selected').data('precio'));
      if (id && cant > 0) {
         // Find if service already exists by ID to sum quantity
         const existingItemIndex = itemsCotizacion.findIndex(item => item.tipo === 'servicio' && item.id === id);
         if (existingItemIndex > -1) {
             itemsCotizacion[existingItemIndex].cantidad += cant;
         } else {
             itemsCotizacion.push({ tipo:'servicio', id, nombre, cantidad:cant, precioUnitario:precio });
         }

        $('#modalServicios').modal('hide');
         $('#cantidad_servicio_modal').val(1); // Reset quantity for next add
        actualizarDetalleCotizacion(); // Redraws everything
      } else alert('Seleccione servicio y cantidad válidos.');
    });

    $('#agregarOtro').on('click', function(e) {
      e.preventDefault();
      const tipo        = $('#tipo_otro').val(); // Assuming this identifies if it's 'producto' or 'servicio' or something else
      const nombre      = $('#nombre_otro').val().trim();
      const cant        = parseFloat($('#cantidad_otro').val());
      const precio      = parseFloat($('#precio_otro').val());
      const descripcion = $('#descripcion_otro').val().trim();

      // Validation slightly adjusted
      if (!nombre || isNaN(cant) || cant <= 0 || isNaN(precio) || precio < 0 || !descripcion) // Ensure price is non-negative
          return alert('Complete todos los campos de "Otro" correctamente (cantidad y precio deben ser números positivos).');

       // For 'Otro', we might want to add as a new item even if name/price/description match
       // unless we want to sum quantity for identical "Otros". Let's keep the summing logic
       // if type, name, price, AND description match.
        const existingItemIndex = itemsCotizacion.findIndex(item =>
            item.tipo === tipo &&
            item.nombre === nombre &&
            item.precioUnitario === precio &&
            item.descripcion === descripcion
        );

        if (existingItemIndex > -1) {
            itemsCotizacion[existingItemIndex].cantidad += cant;
            $('#modalOtros').modal('hide');
            $('#nombre_otro,#cantidad_otro,#precio_otro,#descripcion_otro').val(''); // Clear fields
            return actualizarDetalleCotizacion(); // Redraw
        }

        // If it's a new 'Otro' item (doesn't match existing exactly), add it.
        // You mentioned saving to DB first (`insertar_otros.php`).
        // This implies 'Otros' items might need their own ID management.
        // Let's keep the existing logic for saving to DB first, assuming it returns an ID.
        $.post('insertar_otros.php', { tipo: tipo, nombre: nombre, precio: precio, descripcion: descripcion }, function(res) {
          if (!res.success) return alert('Error BD: '+res.error);
          // Assuming res.id contains the ID of the "Otro" item inserted
          itemsCotizacion.push({ tipo: tipo, id: res.id, nombre: nombre, cantidad: cant, precioUnitario: precio, descripcion: descripcion }); // Store the returned ID
          $('#modalOtros').modal('hide');
          $('#nombre_otro,#cantidad_otro,#precio_otro,#descripcion_otro').val(''); // Clear fields
          actualizarDetalleCotizacion(); // Redraws everything
        }, 'json').fail(xhr=>{
          alert('Error guardando en BD el item "Otro": '+(xhr.responseJSON?.error||xhr.statusText));
        });
    });


    // ── PREVIEW y GUARDAR ──
      $('#btnPreview').on('click', function(e) {
          e.preventDefault();

           // Basic validation: Check if at least one item has been added
          if (itemsCotizacion.length === 0) {
              alert('Debe agregar al menos un producto o servicio a la cotización.');
              return; // Stop the preview process
          }
           // Basic validation: Check if a client is selected
           if (!$('#cliente_id_select').val()) {
               alert('Debe seleccionar un cliente.');
               return; // Stop the preview process
           }


          // Recoger las descripciones de sección antes de ocultar los campos
          const descripcionProductos = $('#descripcion_cotizacion_productos').val();
          const descripcionServicios = $('#descripcion_cotizacion_servicios').val();

          const data = {
              // Get client_id from the new select dropdown
              cliente_id: $('#cliente_id_select').val(),
              nombre_cotizacion: $('#nombre_cotizacion').val(),
              fecha_cotizacion:  $('#fecha_cotizacion').val(),
              fecha_validez:     $('#fecha_validez').val(),
              estado:            $('#estado').val(),
              notas:             $('#notas').val(),
              igv:               igvOpciones,
              descuentos:        descuentos, // Includes fixed discounts
              descripcion_cotizacion_productos: descripcionProductos, // Send product description
              descripcion_cotizacion_servicios: descripcionServicios, // Send service description
              items:             itemsCotizacion
          };

          // Use a separate preview script if editing and creation previews differ,
          // but preview logic is likely similar. Let's assume preview_cotizacion.php
          // can handle data for a new cotization.
          $.post('preview_cotizacion.php', data, html=>{ // Keep 'preview_cotizacion_editar.php' if it's generic
              $('#preview').html(html).show();
              $('#btnGuardar').show();
              // Hide editing sections
              // Check for the presence of these form groups before hiding, they might not exist if no items are added yet
              $('#crearCotizacionForm').find('.form-group, .btn').not('#btnPreview').hide(); // Hide most form elements
              $('#detalleCotizacion').hide(); // Hide the item details area
              $('#btnPreview').hide(); // Hide the preview button itself

              $('#volverEditar').show();
          }).fail((xhr, status, error)=>alert('Error al generar preview: ' + status + ' - ' + (xhr.responseJSON?.error || xhr.responseText || error))); // More detailed error message
      });

    $('#volverEditar').on('click', ()=> {
        $('#preview, #btnGuardar, #volverEditar').hide();
        // Show editing sections
        $('#crearCotizacionForm').find('.form-group, .btn').not('#btnGuardar, #volverEditar').show(); // Show form elements
        $('#detalleCotizacion').show(); // Show the item details area
        $('#btnPreview').show(); // Show the preview button again

        // Re-render the detail section to ensure description textareas are visible and values are retained
        actualizarDetalleCotizacion(); // This redraws the DOM, including description textareas
    });


    $('#btnGuardar').on('click', function(e) {
        e.preventDefault();

         // Basic validation: Check if at least one item has been added
        if (itemsCotizacion.length === 0) {
            alert('Debe agregar al menos un producto o servicio a la cotización.');
            return; // Stop the save process
        }
        // Basic validation: Check if a client is selected
        if (!$('#cliente_id_select').val()) {
            alert('Debe seleccionar un cliente.');
            return; // Stop the save process
        }


        // Recoger las descripciones de sección justo antes de guardar
        const descripcionProductos = $('#descripcion_cotizacion_productos').val();
        const descripcionServicios = $('#descripcion_cotizacion_servicios').val();


        const payload = JSON.stringify({
            // Get client_id from the new select dropdown
            cliente_id: $('#cliente_id_select').val(),
            nombre_cotizacion: $('#nombre_cotizacion').val(),
            fecha_cotizacion:  $('#fecha_cotizacion').val(),
            fecha_validez:     $('#fecha_validez').val(),
            estado:            $('#estado').val(),
            notas:             $('#notas').val(),
            igv:               igvOpciones,
            descuentos:        descuentos, // Includes fixed discounts
            descripcion_cotizacion_productos: descripcionProductos, // Send product description
            descripcion_cotizacion_servicios: descripcionServicios, // Send service description
            items:             itemsCotizacion
        });

        $.ajax({
            // CHANGE URL to a script specifically for creating
            url: 'proceso_cotizacion.php', // <--- CHANGED URL HERE
            type: 'POST',
            data: { payload: payload }, // Send as an object with 'payload' key
            dataType: 'json' // Expect JSON response
             // contentType is removed because jQuery handles it automatically for data objects,
             // sending it as 'application/x-www-form-urlencoded'. If your server expects
             // raw JSON, you would keep contentType: 'application/json' and send `data: payload`.
             // Let's assume the server expects x-www-form-urlencoded with a 'payload' key containing the JSON string.
        }).done(res=>{
            if (res.success) {
                 $('#mensajeExitoCotizacion').text('Cotización creada con éxito. Redireccionando...').show();
                 // Redirect to the view page for the newly created cotization
                 window.location.href = 'ver_cotizacion.php?id=' + res.id_cotizacion;
            }
            else {
                 $('#mensajeExitoCotizacion').hide(); // Hide success message on error
                 alert('Error al guardar: '+res.error);
            }
        }).fail((xhr, status, error)=> {
             $('#mensajeExitoCotizacion').hide(); // Hide success message on error
             alert('Falló la petición de guardado: ' + status + ' - ' + (xhr.responseJSON?.error || xhr.responseText || error)); // More detailed error message
         });
    });


   
    actualizarDetalleCotizacion();


    // Optional: Set today's date as default for Fecha de Cotización
     $('#fecha_cotizacion').val(new Date().toISOString().slice(0,10));


});
</script>
<?php
// The modal.php file is included here, assuming it contains the HTML for the modals.
// You need to ensure modal.php includes the necessary HTML for:
// - #modalProductos (with #producto_id_modal select and #cantidad_producto_modal input)
// - #modalServicios (with #servicio_id_modal select and #cantidad_servicio_modal input)
// - #modalOtros (with #tipo_otro, #nombre_otro, #cantidad_otro, #precio_otro, #descripcion_otro inputs)
// and the respective buttons with IDs like #agregarProductoModal, #agregarServicioModal, #agregarOtro.
// The PHP loops to populate #producto_id_modal and #servicio_id_modal should also be in modal.php
?>