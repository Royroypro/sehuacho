
<!-- Modal Productos -->
<div class="modal fade" id="modalProductos" tabindex="-1" role="dialog" aria-labelledby="modalProductosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Producto</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="producto_id_modal">Producto</label>
                    <select id="producto_id_modal" class="form-control">
                        <option value="" disabled selected>Seleccione un producto</option>
                        <?php foreach ($productosModal as $p): ?>
                            <option value="<?= $p['ProductoID'] ?>" data-precio="<?= $p['PrecioUnitario'] ?>"><?= htmlspecialchars("{$p['Nombre']} - {$p['Descripcion']}") ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cantidad_producto_modal">Cantidad</label>
                    <input type="number" id="cantidad_producto_modal" class="form-control" value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="agregarProductoModal">Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Servicios -->
<div class="modal fade" id="modalServicios" tabindex="-1" role="dialog" aria-labelledby="modalServiciosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Servicio</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="servicio_id_modal">Servicio</label>
                    <select id="servicio_id_modal" class="form-control">
                        <option value="" disabled selected>Seleccione un servicio</option>
                        <?php foreach ($serviciosModal as $s): ?>
                            <option value="<?= $s['ServicioID'] ?>" data-precio="<?= $s['Precio'] ?>"><?= htmlspecialchars("{$s['Nombre']} - {$s['Descripcion']}") ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cantidad_servicio_modal">Cantidad</label>
                    <input type="number" id="cantidad_servicio_modal" class="form-control" value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="agregarServicioModal">Agregar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Otros -->
<div class="modal fade" id="modalOtros" tabindex="-1" role="dialog" aria-labelledby="modalOtrosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Otro</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="tipo_otro">Tipo</label>
                    <select id="tipo_otro" class="form-control">
                        <option value="producto">Producto</option>
                        <option value="servicio">Servicio</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nombre_otro">Nombre</label>
                    <input type="text" id="nombre_otro" class="form-control" placeholder="Nombre del ítem">
                </div>
                <div class="form-group">
                    <label for="cantidad_otro">Cantidad</label>
                    <input type="number" id="cantidad_otro" class="form-control" value="1" min="1">
                </div>
                
                <div class="form-group">
                    <label for="precio_otro">Precio Unitario</label>
                    <input type="number" id="precio_otro" class="form-control" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="descripcion_otro">Descripción</label>
                    <textarea id="descripcion_otro" class="form-control" placeholder="Descripción del ítem"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="agregarOtro">Agregar</button>
            </div>
        </div>
    </div>
</div>
