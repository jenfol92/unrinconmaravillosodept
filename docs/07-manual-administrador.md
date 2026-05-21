<style>
:root {
    --turquesa: #56B3AD;
    --turquesa-oscuro: #078991;
    --amarillo: #FACC15;
    --naranja: #F28C6F;
    --fondo: #f4fbfc;
    --texto: #263238;
    --muted: #667781;
    --blanco: #ffffff;
    --borde: #d6edf1;
    --sombra: rgba(0, 0, 0, 0.08);
}

body {
    background: linear-gradient(135deg, #f4fbfc 0%, #fff9db 100%);
}

.doc-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1rem 4rem;
    color: var(--texto);
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    line-height: 1.7;
}

.doc-hero {
    background:
        radial-gradient(circle at top left, rgba(86, 179, 173, 0.24), transparent 34%),
        radial-gradient(circle at bottom right, rgba(250, 204, 21, 0.22), transparent 34%),
        var(--blanco);
    border: 1px solid var(--borde);
    border-radius: 30px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 16px 40px var(--sombra);
}

.doc-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    background: rgba(86, 179, 173, 0.14);
    color: var(--turquesa-oscuro);
    border: 1px solid rgba(86, 179, 173, 0.30);
    border-radius: 999px;
    padding: .45rem 1rem;
    font-weight: 800;
    font-size: .9rem;
    margin-bottom: 1rem;
}

.doc-hero h1 {
    margin: 0;
    font-size: clamp(2rem, 5vw, 3.4rem);
    line-height: 1.08;
}

.doc-hero h1 span {
    color: var(--turquesa);
}

.doc-hero p {
    color: var(--muted);
    max-width: 850px;
    font-size: 1.06rem;
}

.doc-nav {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    margin-top: 1.5rem;
}

.doc-nav a {
    background: var(--turquesa);
    color: white;
    text-decoration: none;
    font-weight: 800;
    padding: .75rem 1rem;
    border-radius: 14px;
    box-shadow: 0 8px 18px rgba(86, 179, 173, .25);
}

.doc-nav a.secondary {
    background: var(--amarillo);
    color: #1f2d33;
}

.doc-section {
    background: var(--blanco);
    border: 1px solid var(--borde);
    border-radius: 24px;
    padding: 1.5rem;
    margin-bottom: 1.3rem;
    box-shadow: 0 10px 28px rgba(0,0,0,.055);
}

.doc-section h2 {
    margin-top: 0;
    color: var(--turquesa-oscuro);
    font-size: 1.55rem;
}

.doc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 1rem;
}

.doc-card {
    background: #f8fdff;
    border: 1px solid #e2f1f3;
    border-radius: 18px;
    padding: 1rem;
}

.doc-card strong {
    color: var(--turquesa-oscuro);
}

.doc-list {
    padding-left: 1.2rem;
}

.doc-list li {
    margin-bottom: .45rem;
}

.module-card {
    background: linear-gradient(135deg, #ffffff, #f8fdff);
    border: 1px solid #e2f1f3;
    border-radius: 20px;
    padding: 1.1rem;
    box-shadow: 0 8px 20px rgba(0,0,0,.045);
}

.module-card h3 {
    margin-top: 0;
    color: var(--turquesa-oscuro);
}

.status-ok {
    color: #198754;
    font-weight: 900;
}

.alert-box {
    background: #fff8df;
    border: 1px solid rgba(250, 204, 21, .35);
    border-radius: 18px;
    padding: 1rem;
    color: #6b4e00;
}

@media (max-width: 650px) {
    .doc-hero {
        padding: 1.6rem;
        border-radius: 24px;
    }

    .doc-section {
        padding: 1.1rem;
    }

    .doc-nav a {
        width: 100%;
        text-align: center;
    }
}
</style>

<div class="doc-page">

<section class="doc-hero">
    <div class="doc-badge">🛠️ Manual de administrador</div>

    <h1>Gestión del <span>Panel Admin</span></h1>

    <p>
        Este manual explica las principales funciones disponibles para el administrador del proyecto:
        gestión de productos, recursos gratuitos, usuarios, soporte, sugerencias, contacto web, reportes
        y configuración general de la tienda.
    </p>

    <div class="doc-nav">
        <a href="index.html">← Volver al índice</a>
        <a href="08-mejoras-futuras.html" class="secondary">Ir a mejoras futuras →</a>
    </div>
</section>

<section class="doc-section">
    <h2>1. Acceso al panel</h2>

    <p>
        El administrador accede al panel mediante el formulario de login. El sistema comprueba la sesión
        y el rol del usuario antes de permitir el acceso.
    </p>

    <div class="alert-box">
        El panel administrador está protegido. Un usuario cliente no debe poder acceder a <code>admin.php</code>.
    </div>
</section>

<section class="doc-section">
    <h2>2. Dashboard</h2>

    <p>
        El dashboard ofrece una visión general de la actividad de la tienda.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Ventas</strong>
            <p>Resumen económico del periodo seleccionado.</p>
        </div>

        <div class="doc-card">
            <strong>Usuarios</strong>
            <p>Nuevos usuarios registrados y actividad general.</p>
        </div>

        <div class="doc-card">
            <strong>Descargas</strong>
            <p>Control de recursos descargados por los clientes.</p>
        </div>

        <div class="doc-card">
            <strong>Recursos activos</strong>
            <p>Número de productos y recursos gratuitos visibles.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>3. Gestión de productos</h2>

    <div class="module-card">
        <h3>Productos de la tienda</h3>

        <p>
            El administrador puede crear, editar y gestionar productos digitales de pago.
        </p>

        <ul class="doc-list">
            <li>Crear nuevos productos.</li>
            <li>Editar título, descripción, precio y categoría.</li>
            <li>Subir imagen principal.</li>
            <li>Asociar archivos PDF o ZIP.</li>
            <li>Configurar enlaces de descarga segura.</li>
            <li>Activar o desactivar productos.</li>
            <li>Consultar ventas y descargas.</li>
        </ul>
    </div>
</section>

<section class="doc-section">
    <h2>4. Recursos gratuitos</h2>

    <p>
        El panel permite administrar contenido gratuito para atraer usuarios y ofrecer materiales de muestra.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Crear recurso</strong>
            <p>Añadir título, descripción, categoría e imagen.</p>
        </div>

        <div class="doc-card">
            <strong>Subir archivo</strong>
            <p>Asociar PDF, ZIP o enlace externo.</p>
        </div>

        <div class="doc-card">
            <strong>Filtrar</strong>
            <p>Buscar recursos gratuitos por categoría, estado o texto.</p>
        </div>

        <div class="doc-card">
            <strong>Publicar</strong>
            <p>Controlar si un recurso está activo o no visible.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>5. Gestión de usuarios</h2>

    <p>
        El administrador puede consultar usuarios registrados y revisar información asociada.
    </p>

    <ul class="doc-list">
        <li>Nombre y apellidos.</li>
        <li>Email.</li>
        <li>Localidad.</li>
        <li>Código postal.</li>
        <li>Fecha de registro.</li>
        <li>Recursos adquiridos.</li>
        <li>Número de descargas.</li>
        <li>Estado activo o bloqueado.</li>
    </ul>
</section>

<section class="doc-section">
    <h2>6. Soporte</h2>

    <p>
        La sección de soporte permite revisar consultas enviadas por usuarios registrados.
    </p>

    <div class="module-card">
        <h3>Funciones de soporte</h3>

        <ul class="doc-list">
            <li>Ver listado de tickets.</li>
            <li>Consultar asunto, usuario, email, localidad y fecha.</li>
            <li>Abrir conversación en modal.</li>
            <li>Responder al usuario.</li>
            <li>Diferenciar mensajes de usuario y administración.</li>
            <li>Visualizar el chat con estilo de burbujas.</li>
            <li>Cambiar o consultar estado del ticket.</li>
        </ul>
    </div>
</section>

<section class="doc-section">
    <h2>7. Sugerencias</h2>

    <p>
        Los usuarios registrados pueden enviar sugerencias para proponer mejoras, nuevos recursos o cambios.
        El administrador puede revisarlas desde el panel.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Usuario</strong>
            <p>Se muestra la persona que envió la sugerencia.</p>
        </div>

        <div class="doc-card">
            <strong>Mensaje</strong>
            <p>Se muestra la idea o propuesta enviada.</p>
        </div>

        <div class="doc-card">
            <strong>Fecha</strong>
            <p>Permite saber cuándo fue enviada.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>8. Contacto web</h2>

    <p>
        La sección de contacto web muestra mensajes enviados desde el formulario público por usuarios invitados
        o no registrados.
    </p>

    <ul class="doc-list">
        <li>Nombre de la persona que contacta.</li>
        <li>Email de contacto.</li>
        <li>Asunto.</li>
        <li>Mensaje.</li>
        <li>Producto relacionado, si existe.</li>
        <li>Fecha de envío.</li>
        <li>Respuesta mediante email.</li>
    </ul>
</section>

<section class="doc-section">
    <h2>9. Reseñas</h2>

    <p>
        El administrador puede revisar reseñas enviadas por los usuarios sobre productos adquiridos.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Producto</strong>
            <p>Recurso al que pertenece la reseña.</p>
        </div>

        <div class="doc-card">
            <strong>Usuario</strong>
            <p>Persona que ha enviado la valoración.</p>
        </div>

        <div class="doc-card">
            <strong>Estado</strong>
            <p>Permite aprobar, revisar o controlar la visibilidad.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>10. Exportación PDF</h2>

    <p>
        El panel administrador incluye exportación de reportes en PDF.
    </p>

    <ul class="doc-list">
        <li>Permite generar documentación de datos del panel.</li>
        <li>Sirve para cumplir el requisito de exportación de listados.</li>
        <li>Puede utilizar librerías PHP como Dompdf, FPDF o similares.</li>
    </ul>
</section>

<section class="doc-section">
    <h2>11. Seguridad del panel</h2>

    <p>
        El panel está protegido mediante control de sesiones y roles.
    </p>

    <div class="doc-grid">
        <div class="doc-card"><span class="status-ok">✓</span> Comprobación de sesión activa.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Comprobación de rol administrador.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Consultas preparadas con PDO.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Contraseñas cifradas.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Validaciones en servidor.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Archivos sensibles excluidos de Git.</div>
    </div>
</section>

</div>
