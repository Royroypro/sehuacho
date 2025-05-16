<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Factura</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Generar Factura</h1>
        <form action="proceso.php" method="POST">
            <div class="mb-4">
                <h4>Datos del Cliente</h4>
                <div class="form-group">
                    <label for="tipoDocCliente">Tipo de Documento</label>
                    <select id="tipoDocCliente" name="tipoDocCliente" class="form-control" required>
                        <option value="6">RUC</option>
                        <option value="1">DNI</option>
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label for="numDoc">Número de Documento</label>
                    <input type="text" id="numDoc" name="numDoc" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="rznSocial">Razón Social</label>
                    <input type="text" id="rznSocial" name="rznSocial" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <h4>Datos del Emisor</h4>
                <div class="form-group">
                    <label for="ruc">RUC</label>
                    <input type="text" id="ruc" name="ruc" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="razonSocial">Razón Social</label>
                    <input type="text" id="razonSocial" name="razonSocial" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="nombreComercial">Nombre Comercial</label>
                    <input type="text" id="nombreComercial" name="nombreComercial" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="ubigueo">Ubigueo</label>
                    <input type="text" id="ubigueo" name="ubigueo" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="departamento">Departamento</label>
                    <input type="text" id="departamento" name="departamento" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="provincia">Provincia</label>
                    <input type="text" id="provincia" name="provincia" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="distrito">Distrito</label>
                    <input type="text" id="distrito" name="distrito" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="urbanizacion">Urbanización</label>
                    <input type="text" id="urbanizacion" name="urbanizacion" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="codLocal">Código de Local</label>
                    <input type="text" id="codLocal" name="codLocal" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <h4>Datos de la Venta</h4>
                <div class="form-group">
                    <label for="tipoOperacion">Tipo de Operación</label>
                    <select id="tipoOperacion" name="tipoOperacion" class="form-control" required>
                        <option value="0101">Venta</option>
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label for="tipoDocVenta">Tipo de Documento</label>
                    <select id="tipoDocVenta" name="tipoDocVenta" class="form-control" required>
                        <option value="01">Factura</option>
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label for="serie">Serie</label>
                    <input type="text" id="serie" name="serie" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="correlativo">Correlativo</label>
                    <input type="text" id="correlativo" name="correlativo" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="fechaEmision">Fecha de Emisión</label>
                    <input type="datetime-local" id="fechaEmision" name="fechaEmision" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="formaPago">Forma de Pago</label>
                    <select id="formaPago" name="formaPago" class="form-control" required>
                        <option value="0101">Contado</option>
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label for="tipoMoneda">Tipo de Moneda</label>
                    <select id="tipoMoneda" name="tipoMoneda" class="form-control" required>
                        <option value="PEN">Soles</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <h4>Datos del Producto</h4>
                <div class="form-group">
                    <label for="codProducto">Código del Producto</label>
                    <input type="text" id="codProducto" name="codProducto" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="unidad">Unidad del Producto</label>
                    <select id="unidad" name="unidad" class="form-control" required>
                        <option value="NIU">Unidad</option>
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="valorUnitario">Valor Unitario</label>
                    <input type="number" step="0.01" id="valorUnitario" name="valorUnitario" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label for="descripcion">Descripción del Producto</label>
                    <input type="text" id="descripcion" name="descripcion" class="form-control" required>
                </div>

            <button type="submit" class="btn btn-primary">Generar Factura</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
