<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="admin-page">
    <!-- HEADER MÓVIL DEL PANEL ADMIN -->
    <header class="admin-mobile-topbar">

        <button type="button"
            class="admin-mobile-menu-btn"
            id="btnAdminMobileMenu"
            aria-expanded="false"
            aria-controls="adminMobileSidebar">

            <span class="admin-mobile-menu-icon">
                <i class="bi bi-speedometer2"></i>
            </span>

            <span class="admin-mobile-menu-text">
                Dashboard
            </span>

            <i class="bi bi-chevron-down admin-mobile-menu-chevron"></i>
        </button>

        <a href="/UNRINCONDEPT/public/index.php"
            class="admin-mobile-home">
            <i class="bi bi-house"></i>
        </a>

    </header>

    <div class="admin-mobile-overlay" id="adminMobileOverlay"></div>

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar" id="adminMobileSidebar">

            <nav class="admin-menu">

                <button class="admin-link active" data-section="dashboard">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </button>

                <button class="admin-link" data-section="subir">
                    <i class="bi bi-file-earmark-plus"></i>
                    Subir Recurso
                </button>

                <button class="admin-link" data-section="productos">
                    <i class="bi bi-shop"></i>
                    Productos
                </button>
                <a href="javascript:void(0)"
                    class="admin-link"
                    data-section="gratuitos">
                    <i class="bi bi-gift"></i>
                    Contenido gratuito
                </a>

                <button class="admin-link" data-section="usuarios">
                    <i class="bi bi-people"></i>
                    Usuarios
                </button>

                <button class="admin-link" data-section="soporte">
                    <i class="bi bi-chat-dots"></i>
                    Soporte
                </button>
                <button class="admin-link" data-section="sugerencias">
                    <i class="bi bi-chat-heart"></i>
                    Sugerencias
                </button>
                <button class="admin-link" data-section="contacto">
                    <i class="bi bi-envelope-heart"></i>
                    Contacto web
                </button>

                <button class="admin-link" data-section="configuracion">
                    <i class="bi bi-gear"></i>
                    Configuración
                </button>

            </nav>
            <div>
                <a href="/UNRINCONDEPT/public/index.php" class="admin-back">
                    <i class="bi bi-house"></i>
                    Volver a la Web
                </a>
                <a href="/UNRINCONDEPT/public/logout.php" class="admin-back">
                    Cerrar sessión
                </a>
            </div>
        </aside>


        <!-- CONTENIDO -->
        <section class="admin-content">

            <!-- DASHBOARD -->
            <section id="admin-section-dashboard" class="admin-section active">

                <div class="admin-header">

                    <div>
                        <h1>Panel de Control</h1>
                        <p>Bienvenida de nuevo. Así va tu rincón hoy.</p>
                    </div>

                    <div class="admin-actions">
                        <a href="/UNRINCONDEPT/public/admin_exportar_reporte_pdf.php"
                            class="btn btn-outline-danger"
                            id="btnExportarReporte">
                            <i class="bi bi-filetype-pdf"></i>
                            Exportar reporte
                        </a>

                        <button class="btn btn-primary admin-open-section" data-section="subir">
                            <i class="bi bi-plus"></i>
                            Crear Recurso
                        </button>
                    </div>

                </div>
                <form method="GET" action="/UNRINCONDEPT/public/admin.php" class="row g-2 align-items-end mb-4">

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-bold">Periodo de ventas</label>
                        <select name="periodo_ventas" class="form-select" onchange="this.form.submit()">
                            <option value="mes_actual" <?= ($rangoVentas['periodo'] ?? '') === 'mes_actual' ? 'selected' : '' ?>>
                                Mes actual
                            </option>

                            <option value="hoy" <?= ($rangoVentas['periodo'] ?? '') === 'hoy' ? 'selected' : '' ?>>
                                Hoy
                            </option>

                            <option value="ultimos_7" <?= ($rangoVentas['periodo'] ?? '') === 'ultimos_7' ? 'selected' : '' ?>>
                                Últimos 7 días
                            </option>

                            <option value="ultimos_30" <?= ($rangoVentas['periodo'] ?? '') === 'ultimos_30' ? 'selected' : '' ?>>
                                Últimos 30 días
                            </option>

                            <option value="mes_anterior" <?= ($rangoVentas['periodo'] ?? '') === 'mes_anterior' ? 'selected' : '' ?>>
                                Mes anterior
                            </option>

                            <option value="anio_actual" <?= ($rangoVentas['periodo'] ?? '') === 'anio_actual' ? 'selected' : '' ?>>
                                Año actual
                            </option>

                            <option value="personalizado" <?= ($rangoVentas['periodo'] ?? '') === 'personalizado' ? 'selected' : '' ?>>
                                Personalizado
                            </option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date"
                            name="fecha_desde"
                            class="form-control"
                            value="<?= htmlspecialchars($rangoVentas['inicio'] ?? '') ?>">
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date"
                            name="fecha_hasta"
                            class="form-control"
                            value="<?= htmlspecialchars($rangoVentas['fin'] ?? '') ?>">
                    </div>

                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            Aplicar
                        </button>
                    </div>

                    <div class="col-12">
                        <small class="text-muted">
                            Periodo calculado:
                            <?= htmlspecialchars($rangoVentas['inicio'] ?? '') ?>
                            a
                            <?= htmlspecialchars($rangoVentas['fin'] ?? '') ?>
                        </small>
                    </div>
                </form>

                <!-- TARJETAS ESTADÍSTICAS -->
                <div class="row g-4 mb-4">
                    <!--Tarjeta de ventas-->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="admin-stat-card">
                            <div>
                                <span>Ventas del periodo</span>
                                <h3><?= number_format((float)$stats['ventas'], 2, ',', '.') ?> €</h3>
                                <small>
                                    <?= htmlspecialchars($rangoVentas['inicio'] ?? '') ?>
                                    -
                                    <?= htmlspecialchars($rangoVentas['fin'] ?? '') ?>
                                </small>
                            </div>
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="admin-stat-card">
                            <span>Nuevos Usuarios</span>
                            <h3><?= $stats['usuarios'] ?></h3>
                            <small>Usuarios registrados</small>
                            <i class="bi bi-people stat-icon"></i>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="admin-stat-card">
                            <span>Descargas Totales</span>
                            <h3><?= $stats['descargas'] ?></h3>
                            <small>Recursos descargados</small>
                            <i class="bi bi-download stat-icon"></i>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="admin-stat-card">
                            <span>Recursos Activos</span>
                            <h3><?= $stats['productos_activos'] ?></h3>
                            <small>Publicados actualmente</small>
                            <i class="bi bi-file-earmark-text stat-icon"></i>
                        </div>
                    </div>

                </div>


                <div class="row g-4">

                    <!-- TABLA PRODUCTOS MAS VENDIDOS -->
                    <div class="col-12 col-xl-8">

                        <div class="admin-card">

                            <div class="admin-card-header">
                                <h3>Recursos más vendidos</h3>
                                <p class="text-muted">
                                    Según el periodo seleccionado.
                                </p>
                                <i class="bi bi-three-dots-vertical"></i>
                            </div>

                            <div class="table-responsive">

                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Recurso</th>
                                            <th>Categoría</th>
                                            <th>Unidades vendidas</th>
                                            <th>Importe vendido</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if (empty($productos)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    No hay ventas en este periodo.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($productos as $p): ?>
                                                <tr>
                                                    <td>
                                                        <div class="admin-product-info">
                                                            <img src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($p['imagen'] ?? 'default.png') ?>"
                                                                alt="<?= htmlspecialchars($p['titulo']) ?>">

                                                            <div>
                                                                <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                                                                <div class="text-muted small">
                                                                    ID #<?= (int)$p['id'] ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <?= htmlspecialchars($p['categoria_nombre'] ?? '') ?>
                                                    </td>

                                                    <td>
                                                        <strong><?= (int)$p['unidades_vendidas'] ?></strong>
                                                    </td>

                                                    <td>
                                                        <strong>
                                                            <?= number_format((float)$p['importe_vendido'], 2, ',', '.') ?> €
                                                        </strong>
                                                    </td>

                                                    <td>
                                                        <?php if (($p['estado'] ?? '') === 'activo'): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>


                    <!-- SUBIDA RÁPIDA -->
                    <div class="col-12 col-xl-4">

                        <div class="admin-upload-box admin-open-section" data-section="subir">

                            <div class="upload-icon">
                                <i class="bi bi-cloud-arrow-up"></i>
                            </div>

                            <h3>Subir Nuevo Recurso</h3>

                            <p>
                                Arrastra tu archivo PDF aquí o haz clic para buscar en tu ordenador.
                            </p>

                            <button class="btn btn-primary">
                                Seleccionar Archivo
                            </button>

                            <small>
                                Formatos permitidos: PDF, ZIP. Máx. 50MB
                            </small>

                        </div>

                        <div class="admin-tip mt-4">
                            <strong>
                                <i class="bi bi-lightbulb"></i>
                                Tip del día
                            </strong>
                            <p>
                                Considera subir nuevas fichas de la categoría más visitada.
                            </p>
                        </div>

                    </div>

                </div>

            </section>


            <!-- SUBIR / EDITAR RECURSO -->
            <section id="admin-section-subir" class="admin-section">

                <div class="admin-header mb-4">
                    <div>
                        <h1>Subir / Editar recurso</h1>
                        <p>Gestiona recursos de tienda y contenido gratuito desde formularios separados.</p>
                    </div>
                </div>

                <div class="row g-4 align-items-start">

                    <!-- ===================================================== -->
                    <!-- COLUMNA IZQUIERDA: RECURSO DE TIENDA -->
                    <!-- ===================================================== -->
                    <div class="col-12 col-xl-6" id="colFormularioProducto">

                        <div class="admin-card h-100">

                            <h3 id="tituloFormularioProducto">Subir recurso de tienda</h3>
                            <p class="text-muted">
                                Crea un nuevo recurso de pago para la tienda o edita uno existente.
                            </p>

                            <form id="formSubirRecurso"
                                action="/UNRINCONDEPT/public/admin_guardar_producto.php"
                                method="POST"
                                enctype="multipart/form-data">

                                <input type="hidden" id="productoId" name="producto_id" value="">

                                <div class="row g-3">

                                    <!-- Título -->
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Título</label>
                                        <input type="text"
                                            name="titulo"
                                            id="productoTitulo"
                                            class="form-control"
                                            required>
                                    </div>

                                    <!-- Precio -->
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Precio</label>
                                        <input type="number"
                                            step="0.01"
                                            name="precio"
                                            id="productoPrecio"
                                            class="form-control"
                                            required>
                                    </div>

                                    <!-- Descripción -->
                                    <div class="col-12">
                                        <label class="form-label">Descripción</label>
                                        <textarea name="descripcion"
                                            id="productoDescripcion"
                                            class="form-control"
                                            rows="4"></textarea>
                                    </div>

                                    <!-- Contenido -->
                                    <div class="col-12">
                                        <label class="form-label">Contenido</label>
                                        <textarea name="contenido"
                                            id="productoContenido"
                                            class="form-control"
                                            rows="6"></textarea>
                                    </div>

                                    <!-- Categoría -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">Categoría</label>

                                        <div class="input-group">

                                            <select name="categoria_id"
                                                id="productoCategoria"
                                                class="form-select"
                                                required>
                                                <option value="">Selecciona categoría</option>

                                                <?php foreach ($categorias as $cat): ?>
                                                    <option value="<?= (int)$cat['id'] ?>">
                                                        <?= htmlspecialchars($cat['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <button type="button"
                                                class="btn btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalNuevaCategoria"
                                                title="Crear nueva categoría">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>

                                        </div>

                                        <small class="text-muted">
                                            Elige una categoría existente o crea una nueva.
                                        </small>

                                    </div>

                                    <!-- Nivel -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">Nivel educativo</label>

                                        <select name="nivel_id"
                                            id="productoNivel"
                                            class="form-select"
                                            required>
                                            <option value="">Selecciona nivel</option>

                                            <?php foreach ($niveles as $nivel): ?>
                                                <option value="<?= (int)$nivel['id'] ?>">
                                                    <?= htmlspecialchars($nivel['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                    </div>

                                    <!-- Estado -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">Estado</label>

                                        <select name="estado"
                                            id="productoEstado"
                                            class="form-select"
                                            required>
                                            <option value="activo">Activo</option>
                                            <option value="inactivo">Inactivo</option>
                                        </select>

                                    </div>

                                    <!-- Imagen -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">Imagen</label>

                                        <input type="file"
                                            name="imagen"
                                            id="productoImagen"
                                            class="form-control"
                                            accept=".jpg,.jpeg,.png,.webp,image/*">

                                        <small class="text-muted">
                                            JPG, PNG o WEBP. Si editas y no seleccionas nueva imagen, se conserva la actual.
                                        </small>

                                        <div class="mt-2" id="previewImagenProducto" style="display:none;">
                                            <small class="text-muted d-block">Imagen actual:</small>
                                            <img id="imgActualProducto"
                                                src=""
                                                alt="Imagen actual"
                                                style="width:90px;height:90px;object-fit:cover;border-radius:12px;">
                                        </div>

                                    </div>

                                    <!-- Vídeo opcional -->
                                    <div class="col-12 col-md-6">

                                        <label class="form-label">Vídeo de presentación opcional</label>

                                        <input type="file"
                                            name="video"
                                            id="productoVideo"
                                            class="form-control"
                                            accept="video/mp4,video/webm,video/ogg">

                                        <small class="text-muted">
                                            MP4, WEBM u OGG. Si editas y no seleccionas nuevo vídeo, se conserva el actual.
                                        </small>

                                        <div id="previewVideoActual" class="mt-3 d-none">

                                            <label class="small text-muted d-block mb-2">
                                                Vídeo actual
                                            </label>

                                            <video id="productoVideoPreview"
                                                controls
                                                class="w-100 rounded shadow-sm"
                                                style="max-width: 320px; max-height: 220px;">
                                                <source src="" type="video/mp4">
                                                Tu navegador no soporta la reproducción de vídeo.
                                            </video>

                                        </div>

                                    </div>

                                    <!-- Botones -->
                                    <div class="col-12">

                                        <button type="submit"
                                            class="btn btn-primary"
                                            id="btnGuardarProducto">
                                            Guardar recurso
                                        </button>

                                        <button type="button"
                                            class="btn btn-outline-secondary ms-2 admin-open-section"
                                            data-section="productos">
                                            Cancelar
                                        </button>

                                    </div>

                                </div>

                            </form>

                            <div id="respuestaGuardarProducto" class="mt-3"></div>

                            <!-- Asociar PDF/ZIP al producto de tienda -->
                            <div id="bloqueAsociarArchivo"
                                class="alert alert-success mt-4"
                                style="display: none;">

                                <h5 class="mb-2">
                                    Producto guardado correctamente
                                </h5>

                                <p class="mb-3">
                                    Ahora puedes asociar un archivo PDF o ZIP a este recurso.
                                </p>

                                <form id="formAsociarArchivo"
                                    action="/UNRINCONDEPT/public/admin_ajax_subir_recurso.php"
                                    method="POST"
                                    enctype="multipart/form-data">

                                    <input type="hidden" id="archivoProductoId" name="producto_id" value="">

                                    <div class="mb-3">

                                        <label class="form-label fw-bold">
                                            Archivo PDF/ZIP
                                        </label>

                                        <input type="file"
                                            name="archivo_recurso"
                                            id="archivoRecurso"
                                            class="form-control"
                                            accept=".pdf,.zip,application/pdf,application/zip"
                                            required>

                                        <small class="text-muted">
                                            Sube aquí el archivo descargable que recibirá el usuario tras la compra.
                                        </small>

                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cloud-upload"></i>
                                        Subir y asociar archivo
                                    </button>

                                </form>

                                <div id="respuestaArchivoRecurso" class="mt-3"></div>

                            </div>

                        </div>

                    </div>


                    <!-- ===================================================== -->
                    <!-- COLUMNA DERECHA: RECURSO GRATUITO -->
                    <!-- ===================================================== -->
                    <div class="col-12 col-xl-6" id="colFormularioGratuito">

                        <div class="admin-card h-100">

                            <h3 id="tituloFormularioGratuito">Recurso gratuito</h3>
                            <p class="text-muted">
                                Crea o edita recursos gratuitos enlazados a Google Drive.
                            </p>

                            <form id="formRecursoGratuito"
                                method="POST"
                                enctype="multipart/form-data">

                                <input type="hidden" id="gratuitoId" name="id" value="">

                                <!-- Aquí se guardará la URL devuelta por Google Drive -->
                                <input type="hidden" id="gratuitoDriveUrl" name="url_drive" value="">

                                <!-- Título -->
                                <div class="mb-3">

                                    <label class="form-label">Título</label>

                                    <input type="text"
                                        id="gratuitoTitulo"
                                        name="titulo"
                                        class="form-control"
                                        required>

                                </div>

                                <!-- Imagen -->
                                <div class="mb-3">

                                    <label class="form-label">Imagen</label>

                                    <input type="file"
                                        id="gratuitoImagen"
                                        name="imagen"
                                        class="form-control"
                                        accept=".jpg,.jpeg,.png,.webp,image/*">

                                    <small class="text-muted">
                                        Imagen de portada del recurso gratuito. Si editas y no seleccionas nueva imagen, se conserva la actual.
                                    </small>

                                    <div class="mt-2" id="previewImagenGratuito" style="display:none;">
                                        <small class="text-muted d-block">Imagen actual:</small>
                                        <img id="imgActualGratuito"
                                            src=""
                                            alt="Imagen actual"
                                            style="width:90px;height:90px;object-fit:cover;border-radius:12px;">
                                    </div>

                                </div>

                                <!-- Archivo Drive -->
                                <div class="mb-3">

                                    <label class="form-label">Archivo gratuito para Google Drive</label>

                                    <input type="file"
                                        id="gratuitoArchivoDrive"
                                        name="archivo_drive"
                                        class="form-control"
                                        accept=".pdf,.zip,application/pdf,application/zip">

                                    <small class="text-muted">
                                        El archivo se subirá a Google Drive y se guardará automáticamente el enlace en la base de datos.
                                    </small>

                                </div>

                                <!-- Categoría gratuita -->
                                <div class="mb-3">

                                    <label class="form-label">Categoría gratuita</label>

                                    <div class="input-group">

                                        <select id="gratuitoCategoria"
                                            name="categoria_id"
                                            class="form-select"
                                            required>
                                            <option value="">Selecciona categoría</option>

                                            <?php foreach ($categoriasGratuitas as $cat): ?>
                                                <option value="<?= (int)$cat['id'] ?>">
                                                    <?= htmlspecialchars($cat['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <button type="button"
                                            class="btn btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalNuevaCategoriaGratuita"
                                            title="Crear nueva categoría gratuita">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>

                                    </div>

                                </div>

                                <!-- Estado -->
                                <div class="mb-3">

                                    <label class="form-label">Estado</label>

                                    <select id="gratuitoEstado"
                                        name="estado"
                                        class="form-select">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>

                                </div>

                                <!-- Botones -->
                                <div class="d-flex gap-2 flex-wrap">

                                    <button type="submit" class="btn btn-primary">
                                        Guardar recurso gratuito
                                    </button>

                                    <button type="button"
                                        id="btnLimpiarGratuito"
                                        class="btn btn-outline-secondary">
                                        Limpiar
                                    </button>

                                </div>

                                <div id="respuestaRecursoGratuito" class="mt-3"></div>

                            </form>

                        </div>

                    </div>

                </div>

            </section>

            <!-- PRODUCTOS -->
            <section id="admin-section-productos" class="admin-section">

                <div class="admin-header">
                    <div>
                        <h1>Productos</h1>
                        <p>Gestiona recursos activos e inactivos, edita precios, contenido y visibilidad.</p>
                    </div>

                    <button
                        type="button"
                        class="btn btn-primary admin-open-section"
                        data-section="subir">
                        <i class="bi bi-plus"></i>
                        Nuevo producto
                    </button>
                </div>

                <!-- Filtros productos -->
                <div class="admin-filtros-panel mb-4">

                    <!-- Botón móvil -->
                    <button type="button"
                        class="admin-filtros-toggle"
                        data-filter-target="filtrosProductosBody">

                        <span class="admin-filtros-toggle-main">
                            <span class="admin-filtros-toggle-icon">
                                <i class="bi bi-funnel-fill"></i>
                            </span>

                            <span>
                                Filtros
                            </span>
                        </span>

                        <i class="bi bi-chevron-down admin-filtros-chevron"></i>
                    </button>

                    <!-- Cuerpo desplegable -->
                    <div id="filtrosProductosBody" class="admin-filtros-body">

                        <div class="admin-filtros-flex">

                            <div class="admin-filtro-item admin-filtro-grow">
                                <label class="form-label small mb-1">Buscar</label>
                                <input
                                    type="text"
                                    id="adminBuscarProducto"
                                    class="form-control"
                                    placeholder="Buscar producto...">
                            </div>

                            <div class="admin-filtro-item">
                                <label class="form-label small mb-1">Categoría</label>
                                <select id="adminFiltroCategoria" class="form-select">
                                    <option value="">Todas</option>

                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= (int)$cat['id'] ?>">
                                            <?= htmlspecialchars($cat['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="admin-filtro-item">
                                <label class="form-label small mb-1">Estado</label>
                                <select id="adminFiltroEstado" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="activo">Activos</option>
                                    <option value="inactivo">Inactivos</option>
                                </select>
                            </div>

                            <div class="admin-filtro-item">
                                <label class="form-label small mb-1">Periodo de datos</label>
                                <select id="adminFiltroPeriodoDatosProducto" class="form-select">
                                    <option value="todos">Desde el inicio</option>
                                    <option value="ultimos_7">Últimos 7 días</option>
                                    <option value="ultimos_30">Últimos 30 días</option>
                                    <option value="mes_anterior">Mes anterior</option>
                                    <option value="personalizado">Personalizado</option>
                                </select>
                            </div>

                            <div class="admin-filtro-item admin-fechas-producto">
                                <label class="form-label small mb-1">Desde</label>
                                <input type="date"
                                    id="adminDatosDesdeProducto"
                                    class="form-control">
                            </div>

                            <div class="admin-filtro-item admin-fechas-producto">
                                <label class="form-label small mb-1">Hasta</label>
                                <input type="date"
                                    id="adminDatosHastaProducto"
                                    class="form-control">
                            </div>

                            <div class="admin-filtro-item admin-filtro-button">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary w-100"
                                    id="btnFiltrarProductosAdmin">
                                    Aplicar
                                </button>
                            </div>

                        </div>

                        <small class="text-muted d-block mt-2">
                            El periodo afecta a compras, descargas usadas y clics. Las reseñas y el estado no se filtran por fecha.
                        </small>

                    </div>

                </div>

                <!-- Contenido productos -->
                <div class="admin-card">

                    <!-- TABLA PRODUCTOS ESCRITORIO / TABLET -->
                    <div class="table-responsive d-none d-md-block">

                        <table class="table align-middle admin-table">

                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Compras</th>
                                    <th>Descargas usadas</th>
                                    <th>Reseñas</th>
                                    <th>Clics</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>

                            <!-- Se carga por AJAX desde admin.js -->
                            <tbody id="adminProductosTbody">
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Cargando productos...
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                    </div>

                    <!-- TARJETAS PRODUCTOS MÓVIL -->
                    <div class="d-md-none">

                        <div id="adminProductosCardsMovil" class="admin-product-cards-mobile">

                            <div class="text-center text-muted py-4">
                                Cargando productos...
                            </div>

                        </div>

                    </div>

                    <!-- Paginación común -->
                    <div id="adminProductosPaginacion" class="mt-4 text-center"></div>

                </div>

            </section>
            <!--CONTENIDO GRATUITO-->
            <section id="admin-section-gratuitos" class="admin-section">

                <div class="admin-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3>Contenido gratuito</h3>
                            <p class="text-muted mb-0">
                                Gestión de recursos gratuitos enlazados a Drive.
                            </p>
                        </div>

                        <button type="button"
                            class="btn btn-primary admin-open-section"
                            data-section="subir">
                            Nuevo recurso gratuito
                        </button>
                    </div>
                    <!-- Filtros contenido gratuito -->
                    <div class="admin-filtros-panel mb-4">

                        <!-- Botón móvil -->
                        <button type="button"
                            class="admin-filtros-toggle"
                            data-filter-target="filtrosGratuitosBody">
                            <span class="admin-filtros-toggle-main">
                                <span class="admin-filtros-toggle-icon">
                                    <i class="bi bi-funnel-fill"></i>
                                </span>
                                <span>
                                    Filtros
                                </span>
                            </span>

                            <i class="bi bi-chevron-down admin-filtros-chevron"></i>
                        </button>

                        <!-- Cuerpo desplegable -->
                        <div id="filtrosGratuitosBody" class="admin-filtros-body">

                            <div class="admin-filtros-flex">

                                <div class="admin-filtro-item admin-filtro-grow">
                                    <label class="form-label small mb-1">Buscar</label>
                                    <input
                                        type="text"
                                        id="adminBuscarGratuito"
                                        class="form-control"
                                        placeholder="Buscar recurso gratuito...">
                                </div>

                                <div class="admin-filtro-item">
                                    <label class="form-label small mb-1">Categoría</label>
                                    <select id="adminFiltroCategoriaGratuito" class="form-select">
                                        <option value="">Todas</option>

                                        <?php foreach ($categoriasGratuitas as $cat): ?>
                                            <option value="<?= (int)$cat['id'] ?>">
                                                <?= htmlspecialchars($cat['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="admin-filtro-item">
                                    <label class="form-label small mb-1">Estado</label>
                                    <select id="adminFiltroEstadoGratuito" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="activo">Activos</option>
                                        <option value="inactivo">Inactivos</option>
                                    </select>
                                </div>

                                <div class="admin-filtro-item">
                                    <label class="form-label small mb-1">Periodo de datos</label>
                                    <select id="adminFiltroPeriodoGratuito" class="form-select">
                                        <option value="todos" <?= ($rangoMetricasGratuitas['periodo'] ?? '') === 'todos' ? 'selected' : '' ?>>
                                            Desde el inicio
                                        </option>

                                        <option value="ultimos_7" <?= ($rangoMetricasGratuitas['periodo'] ?? '') === 'ultimos_7' ? 'selected' : '' ?>>
                                            Últimos 7 días
                                        </option>

                                        <option value="ultimos_30" <?= ($rangoMetricasGratuitas['periodo'] ?? '') === 'ultimos_30' ? 'selected' : '' ?>>
                                            Últimos 30 días
                                        </option>

                                        <option value="mes_anterior" <?= ($rangoMetricasGratuitas['periodo'] ?? '') === 'mes_anterior' ? 'selected' : '' ?>>
                                            Mes anterior
                                        </option>

                                        <option value="personalizado" <?= ($rangoMetricasGratuitas['periodo'] ?? '') === 'personalizado' ? 'selected' : '' ?>>
                                            Personalizado
                                        </option>
                                    </select>
                                </div>

                                <div class="admin-filtro-item admin-fechas-gratuito">
                                    <label class="form-label small mb-1">Desde</label>
                                    <input type="date"
                                        id="gratisDesde"
                                        class="form-control"
                                        value="<?= htmlspecialchars($rangoMetricasGratuitas['inicio'] ?? '') ?>">
                                </div>

                                <div class="admin-filtro-item admin-fechas-gratuito">
                                    <label class="form-label small mb-1">Hasta</label>
                                    <input type="date"
                                        id="gratisHasta"
                                        class="form-control"
                                        value="<?= htmlspecialchars($rangoMetricasGratuitas['fin'] ?? '') ?>">
                                </div>

                                <div class="admin-filtro-item admin-filtro-button">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary w-100"
                                        id="btnFiltrarGratuitos">
                                        Filtrar
                                    </button>
                                </div>

                                <div class="admin-filtro-item admin-filtro-button">
                                    <button type="button"
                                        class="btn btn-primary w-100"
                                        id="btnAplicarPeriodoGratis">
                                        Aplicar periodo
                                    </button>
                                </div>

                                <div class="admin-filtro-item admin-filtro-button">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary w-100"
                                        id="btnLimpiarFiltrosGratuitos">
                                        Limpiar
                                    </button>
                                </div>

                            </div>

                            <small class="text-muted d-block mt-2">
                                El periodo afecta a clics y descargas del contenido gratuito.
                            </small>

                        </div>

                    </div>
                    <!-- TABLA CONTENIDO GRATUITO ESCRITORIO / TABLET -->
                    <div class="table-responsive d-none d-md-block">

                        <table class="table align-middle admin-table">

                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Clicks</th>
                                    <th>Descargas</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="tablaRecursosGratuitos">

                                <tr id="filaSinResultadosGratuitos" style="display:none;">
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No hay recursos gratuitos que coincidan con los filtros.
                                    </td>
                                </tr>

                                <?php if (empty($recursosGratuitosAdmin)): ?>

                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay recursos gratuitos.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($recursosGratuitosAdmin as $r): ?>

                                        <tr class="gratuito-row"
                                            data-id="<?= (int)($r['id'] ?? 0) ?>"
                                            data-titulo="<?= htmlspecialchars(mb_strtolower($r['titulo'] ?? ''), ENT_QUOTES) ?>"
                                            data-categoria="<?= (int)($r['categoria_id'] ?? 0) ?>"
                                            data-categoria-texto="<?= htmlspecialchars(mb_strtolower($r['categoria_nombre'] ?? ''), ENT_QUOTES) ?>"
                                            data-estado="<?= htmlspecialchars($r['estado'] ?? 'activo', ENT_QUOTES) ?>">

                                            <td>
                                                <img src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($r['imagen'] ?? 'default.png') ?>"
                                                    alt="<?= htmlspecialchars($r['titulo'] ?? 'Recurso gratuito') ?>"
                                                    style="width:60px;height:60px;object-fit:cover;border-radius:10px;"
                                                    onerror="this.onerror=null;this.src='/UNRINCONDEPT/static/images/img/default.png';">
                                            </td>

                                            <td>
                                                <strong><?= htmlspecialchars($r['titulo'] ?? 'Sin título') ?></strong>
                                                <div class="text-muted small">
                                                    ID #<?= (int)($r['id'] ?? 0) ?>
                                                </div>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($r['categoria_nombre'] ?? 'Sin categoría') ?>
                                            </td>

                                            <td>
                                                <i class="bi bi-cursor"></i>
                                                <?= (int)($r['clicks'] ?? 0) ?>
                                            </td>

                                            <td>
                                                <i class="bi bi-download"></i>
                                                <?= (int)($r['descargas'] ?? 0) ?>
                                            </td>

                                            <td>
                                                <?php if (($r['estado'] ?? '') === 'activo'): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-end">

                                                <div class="d-flex justify-content-end gap-2 flex-wrap">

                                                    <a href="<?= htmlspecialchars($r['url_drive'] ?? '#') ?>"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary btn-editar-gratuito"
                                                        data-id="<?= (int)($r['id'] ?? 0) ?>"
                                                        data-titulo="<?= htmlspecialchars($r['titulo'] ?? '', ENT_QUOTES) ?>"
                                                        data-categoria="<?= (int)($r['categoria_id'] ?? 0) ?>"
                                                        data-drive="<?= htmlspecialchars($r['url_drive'] ?? '', ENT_QUOTES) ?>"
                                                        data-estado="<?= htmlspecialchars($r['estado'] ?? 'activo', ENT_QUOTES) ?>"
                                                        data-imagen="<?= htmlspecialchars($r['imagen'] ?? 'default.png', ENT_QUOTES) ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-eliminar-gratuito"
                                                        data-id="<?= (int)($r['id'] ?? 0) ?>"
                                                        data-titulo="<?= htmlspecialchars($r['titulo'] ?? '', ENT_QUOTES) ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                </div>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>
                    <!-- TARJETAS CONTENIDO GRATUITO MÓVIL -->
                    <div class="d-md-none">

                        <div class="admin-free-cards-mobile" id="adminGratuitosCardsMovil">

                            <?php if (empty($recursosGratuitosAdmin)): ?>

                                <div class="text-center text-muted py-4">
                                    No hay recursos gratuitos.
                                </div>

                            <?php else: ?>

                                <?php foreach ($recursosGratuitosAdmin as $r): ?>

                                    <?php
                                    $activo = ($r['estado'] ?? '') === 'activo';
                                    ?>

                                    <article class="admin-free-card-mobile gratuito-card-mobile"
                                        data-id="<?= (int)($r['id'] ?? 0) ?>"
                                        data-titulo="<?= htmlspecialchars(mb_strtolower($r['titulo'] ?? ''), ENT_QUOTES) ?>"
                                        data-categoria="<?= (int)($r['categoria_id'] ?? 0) ?>"
                                        data-categoria-texto="<?= htmlspecialchars(mb_strtolower($r['categoria_nombre'] ?? ''), ENT_QUOTES) ?>"
                                        data-estado="<?= htmlspecialchars($r['estado'] ?? 'activo', ENT_QUOTES) ?>">

                                        <div class="admin-free-card-mobile__top">

                                            <div class="admin-free-card-mobile__image">
                                                <img src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($r['imagen'] ?? 'default.png') ?>"
                                                    alt="<?= htmlspecialchars($r['titulo'] ?? 'Recurso gratuito') ?>"
                                                    onerror="this.onerror=null;this.src='/UNRINCONDEPT/static/images/img/default.png';">
                                            </div>

                                            <div class="admin-free-card-mobile__info">
                                                <h6><?= htmlspecialchars($r['titulo'] ?? 'Sin título') ?></h6>
                                                <span>ID #<?= (int)($r['id'] ?? 0) ?></span>

                                                <div class="admin-free-card-mobile__category">
                                                    <i class="bi bi-tag"></i>
                                                    <?= htmlspecialchars($r['categoria_nombre'] ?? 'Sin categoría') ?>
                                                </div>
                                            </div>

                                            <div class="dropdown">
                                                <button class="btn admin-free-card-mobile__menu"
                                                    type="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">

                                                    <li>
                                                        <a href="<?= htmlspecialchars($r['url_drive'] ?? '#') ?>"
                                                            target="_blank"
                                                            class="dropdown-item">
                                                            <i class="bi bi-eye me-2"></i>
                                                            Ver recurso
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item btn-editar-gratuito"
                                                            data-id="<?= (int)($r['id'] ?? 0) ?>"
                                                            data-titulo="<?= htmlspecialchars($r['titulo'] ?? '', ENT_QUOTES) ?>"
                                                            data-categoria="<?= (int)($r['categoria_id'] ?? 0) ?>"
                                                            data-drive="<?= htmlspecialchars($r['url_drive'] ?? '', ENT_QUOTES) ?>"
                                                            data-estado="<?= htmlspecialchars($r['estado'] ?? 'activo', ENT_QUOTES) ?>"
                                                            data-imagen="<?= htmlspecialchars($r['imagen'] ?? 'default.png', ENT_QUOTES) ?>">
                                                            <i class="bi bi-pencil me-2"></i>
                                                            Editar
                                                        </button>
                                                    </li>

                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>

                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item text-danger btn-eliminar-gratuito"
                                                            data-id="<?= (int)($r['id'] ?? 0) ?>"
                                                            data-titulo="<?= htmlspecialchars($r['titulo'] ?? '', ENT_QUOTES) ?>">
                                                            <i class="bi bi-trash me-2"></i>
                                                            Eliminar
                                                        </button>
                                                    </li>

                                                </ul>
                                            </div>

                                        </div>

                                        <div class="admin-free-card-mobile__stats">

                                            <div class="admin-free-card-mobile__stat">
                                                <i class="bi bi-cursor"></i>
                                                <strong><?= (int)($r['clicks'] ?? 0) ?></strong>
                                                <span>Clics</span>
                                            </div>

                                            <div class="admin-free-card-mobile__stat">
                                                <i class="bi bi-download"></i>
                                                <strong><?= (int)($r['descargas'] ?? 0) ?></strong>
                                                <span>Descargas</span>
                                            </div>

                                        </div>

                                        <div class="admin-free-card-mobile__footer">
                                            <?php if ($activo): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </div>

                                    </article>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                    </div>
                </div>

            </section>

            <!-- USUARIOS -->

            <section id="admin-section-usuarios" class="admin-section">

                <!-- Cabecera -->
                <div class="admin-header">
                    <div>
                        <h1>Usuarios Registrados</h1>
                        <p>Gestiona las cuentas de clientes y controla su acceso a la plataforma.</p>
                    </div>
                </div>

                <!-- Tarjeta principal -->
                <div class="admin-card">

                    <?php if (empty($usuarios)): ?>

                        <!-- Sin usuarios -->
                        <div class="panel-empty">
                            <i class="bi bi-people fs-1 d-block mb-3"></i>
                            <p>No hay usuarios registrados.</p>
                        </div>

                    <?php else: ?>

                        <!-- TABLA ESCRITORIO / TABLET -->
                        <div class="table-responsive d-none d-md-block">

                            <table class="table align-middle admin-table">

                                <thead>
                                    <tr>
                                        <th>Fecha de Registro</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Recursos adquiridos</th>
                                        <th>Descargas</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php foreach ($usuarios as $usuario): ?>

                                        <?php
                                        $nombreCompleto = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));

                                        $fechaRegistro = !empty($usuario['fecha_registro'])
                                            ? date('d/m/Y', strtotime($usuario['fecha_registro']))
                                            : 'Sin fecha';

                                        $totalRecursos = (int)($usuario['total_recursos_adquiridos'] ?? 0);
                                        $totalDescargas = (int)($usuario['total_descargas'] ?? 0);
                                        $activo = (int)($usuario['activo'] ?? 1) === 1;
                                        ?>

                                        <tr>

                                            <td>
                                                <?= htmlspecialchars($fechaRegistro) ?>
                                            </td>

                                            <td>
                                                <div class="admin-user-cell">

                                                    <div class="admin-user-avatar">
                                                        <i class="bi bi-person-fill"></i>
                                                    </div>

                                                    <div>
                                                        <div class="admin-user-name">
                                                            <?= htmlspecialchars($nombreCompleto ?: 'Usuario sin nombre') ?>
                                                        </div>

                                                        <div class="admin-user-id">
                                                            ID #<?= (int)($usuario['id'] ?? 0) ?>
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($usuario['email'] ?? '') ?>
                                            </td>

                                            <td>
                                                <strong><?= $totalRecursos ?></strong>
                                            </td>

                                            <td>
                                                <strong><?= $totalDescargas ?></strong>
                                            </td>

                                            <td>
                                                <?php if ($activo): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                                        Activo
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                                        Bloqueado
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-end">
                                                <div class="admin-user-actions">
                                                    <!-- Ver recursos adquiridos -->
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary btn-ver-descargas-usuario"
                                                        data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                        data-usuario-nombre="<?= htmlspecialchars($nombreCompleto, ENT_QUOTES) ?>"
                                                        title="Ver recursos adquiridos">
                                                        <i class="bi bi-eye"></i>
                                                    </button>

                                                    <!-- Ver favoritos -->
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-ver-favoritos-usuario"
                                                        data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                        data-usuario-nombre="<?= htmlspecialchars($nombreCompleto, ENT_QUOTES) ?>"
                                                        title="Ver favoritos">
                                                        <i class="bi bi-heart"></i>
                                                    </button>

                                                    <!-- Ver reseñas -->
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-warning btn-ver-resenas-usuario"
                                                        data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                        data-usuario-nombre="<?= htmlspecialchars($nombreCompleto, ENT_QUOTES) ?>"
                                                        title="Ver reseñas">
                                                        <i class="bi bi-star"></i>
                                                    </button>

                                                    <!-- Bloquear / desbloquear -->
                                                    <?php if ($activo): ?>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger btn-cambiar-estado-usuario"
                                                            data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                            data-activo="1"
                                                            title="Bloquear usuario">
                                                            <i class="bi bi-lock"></i>
                                                        </button>

                                                    <?php else: ?>

                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-success btn-cambiar-estado-usuario"
                                                            data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                            data-activo="0"
                                                            title="Desbloquear usuario">
                                                            <i class="bi bi-unlock"></i>
                                                        </button>

                                                    <?php endif; ?>

                                                </div>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>


                        <!-- TARJETAS MÓVIL -->
                        <div class="d-md-none">

                            <div class="admin-user-cards-mobile">

                                <?php foreach ($usuarios as $usuario): ?>

                                    <?php
                                    $nombreCompleto = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));

                                    $fechaRegistro = !empty($usuario['fecha_registro'])
                                        ? date('d/m/Y', strtotime($usuario['fecha_registro']))
                                        : 'Sin fecha';

                                    $totalRecursos = (int)($usuario['total_recursos_adquiridos'] ?? 0);
                                    $totalDescargas = (int)($usuario['total_descargas'] ?? 0);
                                    $activo = (int)($usuario['activo'] ?? 1) === 1;
                                    ?>

                                    <article class="admin-user-card">

                                        <!-- Cabecera -->
                                        <div class="admin-user-card__top">

                                            <div class="admin-user-card__date">
                                                <i class="bi bi-calendar3"></i>
                                                <span><?= htmlspecialchars($fechaRegistro) ?></span>
                                            </div>

                                            <span class="admin-user-card__status <?= $activo ? 'is-active' : 'is-blocked' ?>">
                                                <?= $activo ? 'Activo' : 'Bloqueado' ?>
                                            </span>

                                        </div>

                                        <!-- Cuerpo -->
                                        <div class="admin-user-card__body">

                                            <div class="admin-user-card__avatar">
                                                <i class="bi bi-person-fill"></i>
                                            </div>

                                            <div class="admin-user-card__info">

                                                <h6 class="admin-user-card__name">
                                                    <?= htmlspecialchars($nombreCompleto ?: 'Usuario sin nombre') ?>
                                                </h6>

                                                <div class="admin-user-card__id">
                                                    ID #<?= (int)($usuario['id'] ?? 0) ?>
                                                </div>

                                                <div class="admin-user-card__email">
                                                    <i class="bi bi-envelope"></i>
                                                    <span><?= htmlspecialchars($usuario['email'] ?? '') ?></span>
                                                </div>

                                            </div>

                                            <!-- Menú acciones -->
                                            <div class="dropdown">

                                                <button class="btn admin-user-card__menu"
                                                    type="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end">

                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item btn-ver-descargas-usuario"
                                                            data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                            data-usuario-nombre="<?= htmlspecialchars($nombreCompleto, ENT_QUOTES) ?>">
                                                            <i class="bi bi-eye me-2"></i>
                                                            Ver recursos adquiridos
                                                        </button>
                                                    </li>

                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item btn-ver-favoritos-usuario"
                                                            data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                            data-usuario-nombre="<?= htmlspecialchars($nombreCompleto, ENT_QUOTES) ?>">
                                                            <i class="bi bi-heart me-2"></i>
                                                            Ver favoritos
                                                        </button>
                                                    </li>

                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item btn-ver-resenas-usuario"
                                                            data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                            data-usuario-nombre="<?= htmlspecialchars($nombreCompleto, ENT_QUOTES) ?>">
                                                            <i class="bi bi-star me-2"></i>
                                                            Ver reseñas
                                                        </button>
                                                    </li>

                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>

                                                    <li>
                                                        <?php if ($activo): ?>

                                                            <button type="button"
                                                                class="dropdown-item text-danger btn-cambiar-estado-usuario"
                                                                data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                                data-activo="1">
                                                                <i class="bi bi-lock me-2"></i>
                                                                Bloquear usuario
                                                            </button>

                                                        <?php else: ?>

                                                            <button type="button"
                                                                class="dropdown-item text-success btn-cambiar-estado-usuario"
                                                                data-usuario-id="<?= (int)($usuario['id'] ?? 0) ?>"
                                                                data-activo="0">
                                                                <i class="bi bi-unlock me-2"></i>
                                                                Desbloquear usuario
                                                            </button>

                                                        <?php endif; ?>
                                                    </li>

                                                </ul>

                                            </div>

                                        </div>

                                        <!-- Métricas -->
                                        <div class="admin-user-card__stats">

                                            <div class="admin-user-card__stat">
                                                <div class="admin-user-card__stat-icon">
                                                    <i class="bi bi-bag-check"></i>
                                                </div>

                                                <div class="admin-user-card__stat-text">
                                                    <strong><?= $totalRecursos ?></strong>
                                                    <span>Compras</span>
                                                </div>
                                            </div>

                                            <div class="admin-user-card__stat">
                                                <div class="admin-user-card__stat-icon">
                                                    <i class="bi bi-download"></i>
                                                </div>

                                                <div class="admin-user-card__stat-text">
                                                    <strong><?= $totalDescargas ?></strong>
                                                    <span>Descargas</span>
                                                </div>
                                            </div>

                                        </div>

                                    </article>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </section>


         <!-- SOPORTE -->
<section id="admin-section-soporte" class="admin-section">

    <div class="admin-header">
        <div>
            <h1>Soporte</h1>
            <p>Consulta y responde mensajes enviados por los usuarios.</p>
        </div>
    </div>

    <div class="tickets-admin-wrapper">

        <?php if (empty($tickets)): ?>

            <div class="tickets-admin-empty">
                <i class="bi bi-chat-dots"></i>
                <h3>No hay tickets de soporte</h3>
                <p>Cuando un usuario envíe una consulta, aparecerá aquí.</p>
            </div>

        <?php else: ?>

            <div class="tickets-admin-list">

                <?php foreach ($tickets as $ticket): ?>

                    <?php
                    /*
                        Construimos el nombre completo del usuario.
                        Si por algún motivo no existen nombre o apellidos,
                        mostramos "Usuario".
                    */
                    $nombreCompleto = trim(
                        ($ticket['usuario_nombre'] ?? '') . ' ' . ($ticket['usuario_apellidos'] ?? '')
                    );

                    if ($nombreCompleto === '') {
                        $nombreCompleto = 'Usuario';
                    }

                    /*
                        Creamos una inicial para el avatar visual.
                    */
                    $inicial = mb_strtoupper(mb_substr($nombreCompleto, 0, 1));

                    /*
                        Normalizamos el estado del ticket para asignar
                        una clase visual distinta.
                    */
                    $estado = strtolower(trim($ticket['estado'] ?? 'abierto'));

                    $estadoClass = 'estado-pendiente';

                    if ($estado === 'respondido') {
                        $estadoClass = 'estado-respondido';
                    } elseif ($estado === 'cerrado') {
                        $estadoClass = 'estado-cerrado';
                    }

                    /*
                        Formateamos la fecha del ticket.
                    */
                    $fechaFormateada = !empty($ticket['fecha'])
                        ? date('d/m/Y H:i', strtotime($ticket['fecha']))
                        : 'Fecha no disponible';
                    ?>

                    <article class="ticket-admin-card">

                        <div class="ticket-admin-main">

                            <!-- Avatar -->
                            <div class="ticket-admin-avatar">
                                <?= htmlspecialchars($inicial) ?>
                            </div>

                            <!-- Contenido principal -->
                            <div class="ticket-admin-content">

                                <div class="ticket-admin-top">

                                    <h3 class="ticket-admin-title">
                                        <?= htmlspecialchars($ticket['asunto'] ?? 'Sin asunto') ?>
                                    </h3>

                                    <span class="ticket-admin-badge <?= $estadoClass ?>">
                                        <?= htmlspecialchars(ucfirst($estado)) ?>
                                    </span>

                                </div>

                                <div class="ticket-admin-meta">

                                    <span>
                                        <i class="bi bi-person"></i>
                                        <?= htmlspecialchars($nombreCompleto) ?>
                                    </span>

                                    <span>
                                        <i class="bi bi-envelope"></i>
                                        <?= htmlspecialchars($ticket['usuario_email'] ?? 'Email no disponible') ?>
                                    </span>

                                    <span>
                                        <i class="bi bi-calendar3"></i>
                                        <?= htmlspecialchars($fechaFormateada) ?>
                                    </span>

                                </div>

                                <?php if (!empty($ticket['usuario_localidad']) || !empty($ticket['usuario_cp'])): ?>

                                    <div class="ticket-admin-extra">
                                        <i class="bi bi-geo-alt"></i>

                                        <?= htmlspecialchars($ticket['usuario_localidad'] ?? 'Localidad no indicada') ?>

                                        <?php if (!empty($ticket['usuario_cp'])): ?>
                                            · CP <?= htmlspecialchars($ticket['usuario_cp']) ?>
                                        <?php endif; ?>
                                    </div>

                                <?php endif; ?>

                            </div>

                            <!-- Acción -->
                            <div class="ticket-admin-actions">

                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm btn-responder-ticket"
                                    data-ticket-id="<?= (int)$ticket['id'] ?>"
                                    data-asunto="<?= htmlspecialchars($ticket['asunto'] ?? 'Consulta', ENT_QUOTES) ?>">
                                    <i class="bi bi-reply-fill"></i>
                                    Responder
                                </button>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>
            <!-- CONTACTO WEB -->
<section id="admin-section-contacto" class="admin-section">

    <div class="admin-header">
        <div>
            <h1>Contacto web</h1>
            <p>Mensajes enviados desde la página de contacto por usuarios invitados o no registrados.</p>
        </div>
    </div>

    <div class="contacto-admin-wrapper">

        <?php if (empty($mensajesContacto)): ?>

            <div class="contacto-admin-empty">
                <i class="bi bi-envelope-heart"></i>
                <h3>No hay mensajes de contacto todavía</h3>
                <p>Cuando alguien escriba desde el formulario de contacto, aparecerá aquí.</p>
            </div>

        <?php else: ?>

            <div class="contacto-admin-list">

                <?php foreach ($mensajesContacto as $mensaje): ?>

                    <?php
                    /*
                        Preparamos datos visuales para la tarjeta.
                        Si no hay nombre, mostramos "Invitado".
                    */
                    $nombreContacto = trim($mensaje['nombre'] ?? '');

                    if ($nombreContacto === '') {
                        $nombreContacto = 'Invitado';
                    }

                    /*
                        Inicial para el avatar.
                    */
                    $inicialContacto = mb_strtoupper(mb_substr($nombreContacto, 0, 1));

                    /*
                        Fecha formateada.
                    */
                    $fechaContacto = !empty($mensaje['fecha'])
                        ? date('d/m/Y H:i', strtotime($mensaje['fecha']))
                        : 'Fecha no disponible';

                    /*
                        Asunto seguro para el enlace mailto.
                    */
                    $asuntoRespuesta = 'Respuesta: ' . ($mensaje['asunto'] ?? 'Consulta web');
                    ?>

                    <article class="contacto-admin-card">

                        <div class="contacto-admin-main">

                            <!-- Avatar -->
                            <div class="contacto-admin-avatar">
                                <?= htmlspecialchars($inicialContacto) ?>
                            </div>

                            <!-- Contenido del mensaje -->
                            <div class="contacto-admin-content">

                                <div class="contacto-admin-top">

                                    <div>
                                        <h3 class="contacto-admin-title">
                                            <?= htmlspecialchars($mensaje['asunto'] ?? 'Sin asunto') ?>
                                        </h3>

                                        <div class="contacto-admin-meta">

                                            <span>
                                                <i class="bi bi-person"></i>
                                                <?= htmlspecialchars($nombreContacto) ?>
                                            </span>

                                            <span>
                                                <i class="bi bi-envelope"></i>
                                                <?= htmlspecialchars($mensaje['email'] ?? 'Email no disponible') ?>
                                            </span>

                                            <span>
                                                <i class="bi bi-calendar3"></i>
                                                <?= htmlspecialchars($fechaContacto) ?>
                                            </span>

                                        </div>
                                    </div>

                                    <?php if (isset($mensaje['leido'])): ?>
                                        <?php if ((int)$mensaje['leido'] === 1): ?>
                                            <span class="contacto-admin-status is-read">
                                                <i class="bi bi-check2-circle"></i>
                                                Leído
                                            </span>
                                        <?php else: ?>
                                            <span class="contacto-admin-status is-new">
                                                <i class="bi bi-stars"></i>
                                                Nuevo
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </div>

                                <?php if (!empty($mensaje['producto_titulo'])): ?>

                                    <div class="contacto-admin-product">
                                        <i class="bi bi-box-seam"></i>
                                        Consulta sobre:
                                        <strong><?= htmlspecialchars($mensaje['producto_titulo']) ?></strong>
                                    </div>

                                <?php endif; ?>

                                <div class="contacto-admin-message">
                                    <?= nl2br(htmlspecialchars($mensaje['mensaje'] ?? '')) ?>
                                </div>

                            </div>

                            <!-- Acción -->
                            <div class="contacto-admin-actions">

                                <a
                                    href="mailto:<?= htmlspecialchars($mensaje['email'] ?? '') ?>?subject=<?= urlencode($asuntoRespuesta) ?>"
                                    class="btn btn-primary btn-sm">
                                    <i class="bi bi-reply-fill"></i>
                                    Responder
                                </a>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>
            <section id="admin-section-sugerencias" class="admin-section">

    <div class="admin-header">
        <div>
            <h1>Sugerencias</h1>
            <p>Ideas y propuestas enviadas por los usuarios para mejorar la web y los recursos.</p>
        </div>
    </div>

    <div class="sugerencias-admin-wrapper">

        <?php if (empty($sugerencias)): ?>

            <div class="sugerencias-admin-empty">
                <i class="bi bi-chat-heart"></i>
                <h3>No hay sugerencias todavía</h3>
                <p>Cuando un usuario envíe una propuesta, aparecerá aquí.</p>
            </div>

        <?php else: ?>

            <div class="sugerencias-admin-grid">

                <?php foreach ($sugerencias as $s): ?>

                    <?php
                    $nombreCompleto = trim(($s['nombre'] ?? '') . ' ' . ($s['apellidos'] ?? ''));

                    if ($nombreCompleto === '') {
                        $nombreCompleto = 'Usuario';
                    }

                    $inicial = mb_strtoupper(mb_substr($nombreCompleto, 0, 1));
                    ?>

                    <article class="sugerencia-admin-card">

                        <div class="sugerencia-admin-top">

                            <div class="sugerencia-admin-avatar">
                                <?= htmlspecialchars($inicial) ?>
                            </div>

                            <div>
                                <h3>
                                    <?= htmlspecialchars($nombreCompleto) ?>
                                </h3>

                                <span>
                                    <?= !empty($s['localidad']) ? htmlspecialchars($s['localidad']) : 'Localidad no indicada' ?>
                                </span>
                            </div>

                        </div>

                        <div class="sugerencia-admin-message">
                            <?= nl2br(htmlspecialchars($s['mensaje'])) ?>
                        </div>

                        <div class="sugerencia-admin-footer">

                            <div>
                                <i class="bi bi-envelope"></i>
                                <?= htmlspecialchars($s['email'] ?? 'Email no disponible') ?>
                            </div>

                            <div>
                                <i class="bi bi-calendar-heart"></i>
                                <?= !empty($s['fecha']) ? date('d/m/Y H:i', strtotime($s['fecha'])) : 'Fecha no disponible' ?>
                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>
            <!-- CONFIGURACIÓN -->
            <section id="admin-section-configuracion" class="admin-section">
                <h1>Configuración</h1>
                <p>Ajustes generales del panel.</p>

                <div class="admin-card">
                    Próximamente.
                </div>
            </section>

    </div>

</main>
<!-- Modal nueva categoría -->
<div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Nueva categoría
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>

            </div>

            <form id="formNuevaCategoria">

                <div class="modal-body">

                    <label class="form-label">
                        Nombre de la categoría
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nuevaCategoriaNombre"
                        class="form-control"
                        placeholder="Ej: Lectoescritura"
                        required>

                    <div id="respuestaNuevaCategoria" class="mt-3"></div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Guardar categoría
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!--MODALES-->

<!-- Modal responder soporte -->
<div class="modal fade" id="modalResponderTicket" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="modalTicketTitulo">
                    Responder ticket
                </h5>

                <button type="submit" class="btn btn-primary">
                    Enviar respuesta
                </button>

            </div>

            <div class="modal-body">

                <input type="hidden" id="ticketIdRespuesta">

                <div id="ticketMensajes" class="soporte-chat-admin mb-3">
                    Cargando mensajes...
                </div>

                <form id="formResponderTicket">

                    <label class="form-label">
                        Respuesta
                    </label>

                    <textarea
                        id="mensajeRespuestaTicket"
                        name="mensaje"
                        class="form-control"
                        rows="4"
                        required></textarea>

                    <button class="btn btn-primary mt-3">
                        Enviar respuesta
                    </button>

                </form>

                <div id="respuestaTicketAdmin" class="mt-3"></div>

            </div>

        </div>

    </div>

</div>

<!-- ====================================== -->
<!-- MODAL USUARIO: DESCARGAS Y RESEÑAS -->
<!-- ====================================== -->
<div class="modal fade" id="modalUsuarioDetalle" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-xl">

        <div class="modal-content admin-modal-soft">

            <!-- Cabecera -->
            <div class="modal-header border-0 pb-0">

                <h4 class="modal-title" id="modalUsuarioDetalleTitulo">
                    Detalle del usuario
                </h4>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>

            <!-- Cuerpo -->
            <div class="modal-body pt-3">

                <div id="modalUsuarioDetalleContenido">

                    <div class="text-center py-5 text-muted">
                        Cargando información...
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<!--MODAL PARA USUARIOS EN PANEL ADMIN-->

<div class="modal fade" id="modalAdminUsuarios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 id="modalAdminUsuariosTitulo" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="modalAdminUsuariosContenido">
                    Cargando...
                </div>
            </div>

        </div>
    </div>
</div>


<!--MODAL PARA VER TODAS LAS RESEÑAS DE UN PRODUCTO-->

<div class="modal fade" id="modalResenasProductoAdmin" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 id="modalResenasTitulo" class="modal-title">
                    Reseñas del producto
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div id="contenedorResenasProductoAdmin">
                    Cargando reseñas...
                </div>

            </div>

        </div>

    </div>

</div>

<!--MODAL PARA CATEGORIA GRATUITA-->

<div class="modal fade" id="modalNuevaCategoriaGratuita" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formNuevaCategoriaGratuita">

                <div class="modal-header">
                    <h5 class="modal-title">Nueva categoría gratuita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Nombre de la categoría</label>
                    <input type="text"
                        name="nombre"
                        id="nombreCategoriaGratuita"
                        class="form-control"
                        required>

                    <div id="respuestaCategoriaGratuita" class="mt-3"></div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        Crear categoría
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script src="/UNRINCONDEPT/static/js/admin.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>