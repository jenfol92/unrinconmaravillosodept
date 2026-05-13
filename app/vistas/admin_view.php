<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<main class="admin-page">

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar">

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

                <button class="admin-link" data-section="usuarios">
                    <i class="bi bi-people"></i>
                    Usuarios
                </button>

                <button class="admin-link" data-section="soporte">
                    <i class="bi bi-chat-dots"></i>
                    Soporte
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
                        <button class="btn btn-light">
                            <i class="bi bi-bar-chart"></i>
                            Exportar Reporte
                        </button>

                        <button class="btn btn-primary admin-open-section" data-section="subir">
                            <i class="bi bi-plus"></i>
                            Crear Recurso
                        </button>
                    </div>

                </div>


                <!-- TARJETAS ESTADÍSTICAS -->
                <div class="row g-4 mb-4">

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="admin-stat-card">
                            <span>Ventas Totales</span>
                            <h3><?= number_format($stats['ventas'], 2) ?>€</h3>
                            <small>Resumen general</small>
                            <i class="bi bi-bag stat-icon"></i>
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

                    <!-- TABLA PRODUCTOS -->
                    <div class="col-12 col-xl-8">

                        <div class="admin-card">

                            <div class="admin-card-header">
                                <div>
                                    <h2>Gestión de Recursos</h2>
                                    <p>Edita precios, categorías y visibilidad de tus fichas.</p>
                                </div>
                                <i class="bi bi-three-dots-vertical"></i>
                            </div>

                            <div class="table-responsive">

                                <table class="table align-middle admin-table">

                                    <thead>
                                        <tr>
                                            <th>Recurso</th>
                                            <th>Categoría</th>
                                            <th>Precio</th>
                                            <th>Descargas</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php foreach ($productos as $producto): ?>

                                            <tr>
                                                <td>
                                                    <div class="admin-product-info">
                                                        <img
                                                            src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($producto['imagen']) ?>"
                                                            alt="<?= htmlspecialchars($producto['titulo']) ?>">
                                                        <strong>
                                                            <?= htmlspecialchars($producto['titulo']) ?>
                                                        </strong>
                                                    </div>
                                                </td>

                                                <td>
                                                    <span class="admin-badge">
                                                        <?= htmlspecialchars($producto['categoria_nombre']) ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <strong>
                                                        <?= ((float)$producto['precio'] <= 0) ? 'Gratis' : number_format((float)$producto['precio'], 2) . '€' ?>
                                                    </strong>
                                                </td>

                                                <td>
                                                    <i class="bi bi-download"></i>
                                                    <?= $producto['total_descargas'] ?>
                                                </td>

                                                <td>
                                                    <span class="admin-status">
                                                        <?= htmlspecialchars($producto['estado']) ?>
                                                    </span>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>

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

    <div class="admin-header">
        <div>
            <h1 id="tituloFormularioProducto">Subir Recurso</h1>
            <p>Crea un nuevo recurso para la tienda o edita uno existente.</p>
        </div>
    </div>

    <div class="admin-card">

        <form id="formSubirRecurso" enctype="multipart/form-data">

            <!-- ID oculto: vacío = crear / con valor = editar -->
            <input type="hidden" name="id" id="productoId">

            <div class="row g-3">

                <!-- Título -->
                <div class="col-12 col-md-6">
                    <label class="form-label">Título</label>
                    <input 
                        type="text" 
                        name="titulo" 
                        id="productoTitulo" 
                        class="form-control" 
                        required
                    >
                </div>

                <!-- Precio -->
                <div class="col-12 col-md-6">
                    <label class="form-label">Precio</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="precio" 
                        id="productoPrecio" 
                        class="form-control" 
                        required
                    >
                </div>

                <!-- Descripción -->
                <div class="col-12">
                    <label class="form-label">Descripción</label>
                    <textarea 
                        name="descripcion" 
                        id="productoDescripcion" 
                        class="form-control" 
                        rows="4"
                    ></textarea>
                </div>

                <!-- Contenido -->
                <div class="col-12">
                    <label class="form-label">Contenido</label>
                    <textarea 
                        name="contenido" 
                        id="productoContenido" 
                        class="form-control" 
                        rows="6"
                    ></textarea>
                </div>

                <!-- Categoría -->
                <div class="col-12 col-md-6">

                    <label class="form-label">Categoría</label>

                    <div class="input-group">

                        <select 
                            name="categoria_id" 
                            id="productoCategoria" 
                            class="form-select"
                            required
                        >
                            <option value="">Selecciona categoría</option>

                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button 
                            type="button" 
                            class="btn btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalNuevaCategoria"
                            title="Crear nueva categoría"
                        >
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

                    <select 
                        name="nivel_id" 
                        id="productoNivel" 
                        class="form-select"
                        required
                    >
                        <option value="">Selecciona nivel</option>

                        <?php foreach ($niveles as $nivel): ?>
                            <option value="<?= $nivel['id'] ?>">
                                <?= htmlspecialchars($nivel['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <!-- Estado -->
                <div class="col-12 col-md-6">

                    <label class="form-label">Estado</label>

                    <select 
                        name="estado" 
                        id="productoEstado" 
                        class="form-select"
                        required
                    >
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>

                </div>

                <!-- Imagen -->
                <div class="col-12 col-md-6">

                    <label class="form-label">Imagen</label>

                    <input 
                        type="file" 
                        name="imagen" 
                        id="productoImagen"
                        class="form-control" 
                        accept="image/*"
                    >

                    <small class="text-muted">
                        JPG, PNG, WEBP. Si editas y no seleccionas nueva imagen, se conserva la actual.
                    </small>

                    <div id="previewImagenActual" class="mt-3 d-none">
                        <label class="small text-muted d-block mb-2">
                            Imagen actual
                        </label>

                        <img
                            id="productoImagenPreview"
                            src=""
                            alt="Imagen actual del producto"
                            class="img-fluid rounded shadow-sm"
                            style="max-width: 180px; max-height: 180px; object-fit: cover;"
                        >
                    </div>

                </div>

                <!-- Vídeo opcional -->
                <div class="col-12 col-md-6">

                    <label class="form-label">Vídeo de presentación opcional</label>

                    <input
                        type="file"
                        name="video"
                        id="productoVideo"
                        class="form-control"
                        accept="video/mp4,video/webm,video/ogg"
                    >

                    <small class="text-muted">
                        MP4, WEBM u OGG. Si editas y no seleccionas nuevo vídeo, se conserva el actual.
                    </small>

                    <div id="previewVideoActual" class="mt-3 d-none">

                        <label class="small text-muted d-block mb-2">
                            Vídeo actual
                        </label>

                        <video
                            id="productoVideoPreview"
                            controls
                            class="w-100 rounded shadow-sm"
                            style="max-width: 320px; max-height: 220px;"
                        >
                            <source src="" type="video/mp4">
                            Tu navegador no soporta la reproducción de vídeo.
                        </video>

                    </div>

                </div>

                <!-- Archivo recurso -->
                <div class="col-12 col-md-6">

                    <label class="form-label">Archivo PDF/ZIP</label>

                    <input 
                        type="file" 
                        name="archivo" 
                        id="productoArchivo"
                        class="form-control" 
                        accept=".pdf,.zip"
                    >

                    <small class="text-muted">
                        PDF o ZIP. Si editas y no subes nuevo archivo, se conserva el actual.
                    </small>

                </div>

                <!-- Botón guardar -->
                <div class="col-12">

                    <button 
                        type="submit" 
                        class="btn btn-primary"
                        id="btnGuardarProducto"
                    >
                        Guardar recurso
                    </button>

                    <button 
                        type="button" 
                        class="btn btn-outline-secondary ms-2 admin-open-section" 
                        data-section="productos"
                    >
                        Cancelar
                    </button>

                </div>

            </div>

        </form>

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
                <i class="bi bi-plus"></i> Nuevo producto
            </button>
        </div>

        <!-- Filtros -->
        <div class="admin-card mb-4">

            <div class="row g-3">

                <div class="col-12 col-md-4">
                    <input
                        type="text"
                        id="adminBuscarProducto"
                        class="form-control"
                        placeholder="Buscar producto...">
                </div>

                <div class="col-12 col-md-3">
                    <select id="adminFiltroCategoria" class="form-select">
                        <option value="">Todas las categorías</option>

                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <select id="adminFiltroEstado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activos</option>
                        <option value="inactivo">Inactivos</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <button
                        type="button"
                        class="btn btn-outline-primary w-100"
                        id="btnFiltrarProductosAdmin">
                        Filtrar
                    </button>
                </div>

            </div>

        </div>

        <!-- Tabla productos -->
        <div class="admin-card">

            <?php if (empty($productosAdmin)): ?>

                <div class="panel-empty">
                    No hay productos registrados.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table align-middle admin-table">

                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Descargas</th>
                                <th>Reseñas</th>
                                <th>Clics</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($productosAdmin as $p): ?>

                                <tr>

                                    <td>
                                        <div class="admin-product-info">
                                            <img
                                                src="/UNRINCONDEPT/static/images/img/<?= htmlspecialchars($p['imagen']) ?>"
                                                alt="<?= htmlspecialchars($p['titulo']) ?>">

                                            <div>
                                                <strong>
                                                    <?= htmlspecialchars($p['titulo']) ?>
                                                </strong>

                                                <div class="text-muted small">
                                                    ID #<?= $p['id'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="admin-badge">
                                            <?= htmlspecialchars($p['categoria_nombre']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= number_format((float)$p['precio'], 2) ?> €
                                        </strong>
                                    </td>

                                    <td>
                                        <i class="bi bi-download"></i>
                                        <?= (int)($p['total_descargas'] ?? 0) ?>
                                    </td>

                                    <td>
                                        <i class="bi bi-chat-square-text"></i>
                                        <?= (int)($p['total_resenas'] ?? 0) ?>
                                    </td>

                                    <td>
                                        <i class="bi bi-cursor"></i>
                                        <?= (int)($p['total_clicks'] ?? 0) ?>
                                    </td>

                                    <td>
                                        <?php if ($p['estado'] === 'activo'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary btn-editar-producto"

                                            data-id="<?= $p['id'] ?>"
                                            data-titulo="<?= htmlspecialchars($p['titulo'], ENT_QUOTES) ?>"
                                            data-precio="<?= htmlspecialchars($p['precio'], ENT_QUOTES) ?>"
                                            data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '', ENT_QUOTES) ?>"
                                            data-contenido="<?= htmlspecialchars($p['contenido'] ?? '', ENT_QUOTES) ?>"
                                            data-categoria="<?= $p['categoria_id'] ?>"
                                            data-nivel="<?= $p['nivel_id'] ?>"
                                            data-estado="<?= htmlspecialchars($p['estado'], ENT_QUOTES) ?>">
                                            <i class="bi bi-pencil"></i>
                                            Editar
                                        </button>

                                        <a
                                            href="/UNRINCONDEPT/public/detalle.php?id=<?= $p['id'] ?>"
                                            class="btn btn-sm btn-outline-secondary"
                                            target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

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

                <!-- Tabla responsive -->
                <div class="table-responsive">

                    <table class="table align-middle admin-table">

                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Fecha de Registro</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($usuarios as $usuario): ?>

                                <tr>

                                    <!-- Nombre y apellidos -->
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                                style="width:48px; height:48px;">
                                                <i class="bi bi-person-fill text-secondary"></i>
                                            </div>

                                            <div>
                                                <strong>
                                                    <?= htmlspecialchars(
                                                        trim($usuario['nombre'] . ' ' . $usuario['apellidos'])
                                                    ) ?>
                                                </strong>
                                                <div class="text-muted small">
                                                    ID #<?= $usuario['id'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Email -->
                                    <td>
                                        <?= htmlspecialchars($usuario['email']) ?>
                                    </td>

                                    <!-- Fecha de registro -->
                                    <td>
                                        <?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?>
                                    </td>

                                    <!-- Estado -->
                                    <td>
                                        <?php if ((int)$usuario['activo'] === 1): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                                Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                                Bloqueado
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2 flex-wrap">

                                            <!-- Ver perfil -->
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Ver perfil">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            <!-- Gestionar reseñas -->
                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Gestionar reseñas">
                                                <i class="bi bi-chat-square-text"></i>
                                            </button>

                                            <!-- Bloquear / Desbloquear -->
                                            <?php if ((int)$usuario['activo'] === 1): ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Bloquear usuario">
                                                    <i class="bi bi-lock"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success"
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

    <div class="admin-card">

        <?php if (empty($tickets)): ?>

            <div class="panel-empty">
                No hay tickets de soporte.
            </div>

        <?php else: ?>

            <?php foreach ($tickets as $ticket): ?>

                <div class="admin-ticket">

                    <div>
                        <strong>
                            <?= htmlspecialchars($ticket['asunto']) ?>
                        </strong>

                        <p>
                            <?= htmlspecialchars($ticket['usuario_nombre'] . ' ' . $ticket['usuario_apellidos']) ?>
                            · <?= htmlspecialchars($ticket['usuario_email']) ?>
                            · <?= date('d/m/Y H:i', strtotime($ticket['fecha'])) ?>
                        </p>

                        <span class="badge bg-info">
                            <?= htmlspecialchars($ticket['estado']) ?>
                        </span>
                    </div>

                    <button 
                        type="button"
                        class="btn btn-sm btn-primary btn-responder-ticket"
                        data-ticket-id="<?= $ticket['id'] ?>"
                        data-asunto="<?= htmlspecialchars($ticket['asunto'], ENT_QUOTES) ?>"
                    >
                        Responder
                    </button>

                </div>

            <?php endforeach; ?>

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
                    aria-label="Cerrar"
                ></button>

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
                        required
                    >

                    <div id="respuestaNuevaCategoria" class="mt-3"></div>

                </div>

                <div class="modal-footer">

                    <button 
                        type="button" 
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>

                    <button 
                        type="submit" 
                        class="btn btn-primary"
                    >
                        Guardar categoría
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
<!-- Modal responder soporte -->
<div class="modal fade" id="modalResponderTicket" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="modalTicketTitulo">
                    Responder ticket
                </h5>

                <button 
                    type="button" 
                    class="btn-close" 
                    data-bs-dismiss="modal"
                ></button>

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
                        required
                    ></textarea>

                    <button class="btn btn-primary mt-3">
                        Enviar respuesta
                    </button>

                </form>

                <div id="respuestaTicketAdmin" class="mt-3"></div>

            </div>

        </div>

    </div>

</div>
<script src="/UNRINCONDEPT/static/js/admin.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>