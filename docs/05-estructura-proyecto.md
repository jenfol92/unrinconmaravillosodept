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

.code-box {
    background: #1f2d33;
    color: #e9fbff;
    border-radius: 18px;
    padding: 1rem;
    overflow-x: auto;
    font-size: .92rem;
}

.doc-list {
    padding-left: 1.2rem;
}

.doc-list li {
    margin-bottom: .45rem;
}

.folder-pill {
    display: inline-flex;
    align-items: center;
    background: rgba(86, 179, 173, .13);
    color: var(--turquesa-oscuro);
    border: 1px solid rgba(86, 179, 173, .28);
    border-radius: 999px;
    padding: .35rem .75rem;
    font-weight: 800;
    font-size: .85rem;
    margin: .2rem;
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
    <div class="doc-badge">📁 Organización del proyecto</div>

    <h1>Estructura del <span>Proyecto</span></h1>

    <p>
        Esta sección explica cómo está organizado el proyecto <strong>Un Rincón Maravilloso de PT</strong>,
        qué contiene cada carpeta y cómo se separan responsabilidades entre controladores, modelos, vistas,
        plantillas, archivos públicos, recursos estáticos y documentación.
    </p>

    <div class="doc-nav">
        <a href="index.html">← Volver al índice</a>
        <a href="06-manual-usuario.html" class="secondary">Ir al manual de usuario →</a>
    </div>
</section>

<section class="doc-section">
    <h2>1. Estructura general</h2>

    <p>
        El proyecto se organiza en carpetas diferenciadas para mantener una arquitectura clara y facilitar
        el mantenimiento del código.
    </p>

    <div class="code-box">
<pre>UNRINCONDEPT/
├── app/
│   ├── controladores/
│   ├── modelos/
│   └── vistas/
│
├── config/
│   ├── conexion.php
│   ├── r2.php
│   └── stripe.php
│
├── includes/
│   └── session.php
│
├── public/
│   ├── index.php
│   ├── login.php
│   ├── registro.php
│   ├── tienda.php
│   ├── admin.php
│   └── archivos AJAX
│
├── static/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── bootstrap-5.3.8-dist/
│
├── templates/
│   ├── header.php
│   └── footer.php
│
├── docs/
├── README.md
└── .gitignore</pre>
    </div>
</section>

<section class="doc-section">
    <h2>2. Carpetas principales</h2>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>app/</strong>
            <p>Contiene la lógica principal del proyecto: controladores, modelos y vistas.</p>
        </div>

        <div class="doc-card">
            <strong>config/</strong>
            <p>Incluye archivos de configuración, conexión a base de datos y claves externas.</p>
        </div>

        <div class="doc-card">
            <strong>includes/</strong>
            <p>Guarda archivos comunes, como la gestión de sesiones.</p>
        </div>

        <div class="doc-card">
            <strong>public/</strong>
            <p>Contiene archivos accesibles desde el navegador y endpoints AJAX.</p>
        </div>

        <div class="doc-card">
            <strong>static/</strong>
            <p>Contiene CSS, JavaScript, imágenes, Bootstrap y recursos estáticos.</p>
        </div>

        <div class="doc-card">
            <strong>templates/</strong>
            <p>Contiene partes reutilizables como cabecera y footer.</p>
        </div>

        <div class="doc-card">
            <strong>docs/</strong>
            <p>Contiene la documentación técnica publicada mediante GitHub Pages.</p>
        </div>

        <div class="doc-card">
            <strong>README.md</strong>
            <p>Archivo de presentación general del repositorio en GitHub.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>3. Carpeta app</h2>

    <p>
        La carpeta <strong>app</strong> agrupa el núcleo de la aplicación.
    </p>

    <div>
        <span class="folder-pill">app/controladores</span>
        <span class="folder-pill">app/modelos</span>
        <span class="folder-pill">app/vistas</span>
    </div>

    <h3>Controladores</h3>
    <p>
        Los controladores reciben las peticiones del usuario, aplican lógica de negocio y cargan modelos o vistas.
    </p>

    <ul class="doc-list">
        <li><strong>AuthController:</strong> gestiona login y registro.</li>
        <li><strong>AdminController:</strong> gestiona el panel de administración.</li>
        <li><strong>UsuarioController:</strong> gestiona el perfil del usuario.</li>
        <li><strong>ProductoController:</strong> gestiona productos y recursos.</li>
    </ul>

    <h3>Modelos</h3>
    <p>
        Los modelos contienen las consultas a la base de datos mediante PDO.
    </p>

    <ul class="doc-list">
        <li><strong>Usuario:</strong> registro, login, usuarios, compras y descargas.</li>
        <li><strong>Producto:</strong> productos, favoritos, recursos y tienda.</li>
        <li><strong>Soporte:</strong> tickets y mensajes de soporte.</li>
        <li><strong>Contacto:</strong> mensajes enviados desde la página pública de contacto.</li>
    </ul>

    <h3>Vistas</h3>
    <p>
        Las vistas contienen el HTML dinámico que se entrega al navegador.
    </p>
</section>

<section class="doc-section">
    <h2>4. Carpeta public</h2>

    <p>
        La carpeta <strong>public</strong> contiene los archivos accesibles directamente desde el navegador.
    </p>

    <div class="code-box">
<pre>public/
├── index.php
├── tienda.php
├── producto.php
├── login.php
├── registro.php
├── perfil.php
├── admin.php
├── contacto.php
├── politica-privacidad.php
├── politica-cookies.php
├── terminos-compra.php
└── archivos AJAX</pre>
    </div>

    <p>
        También contiene endpoints AJAX utilizados para actualizar información sin recargar la página completa.
    </p>
</section>

<section class="doc-section">
    <h2>5. Carpeta static</h2>

    <p>
        La carpeta <strong>static</strong> almacena archivos estáticos utilizados por la interfaz.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>static/css</strong>
            <p>Contiene el CSS compilado y el archivo SCSS principal.</p>
        </div>

        <div class="doc-card">
            <strong>static/js</strong>
            <p>Contiene JavaScript del proyecto: validaciones, AJAX, efectos, tienda y panel admin.</p>
        </div>

        <div class="doc-card">
            <strong>static/images</strong>
            <p>Contiene imágenes, logos y recursos gráficos.</p>
        </div>

        <div class="doc-card">
            <strong>Bootstrap local</strong>
            <p>Bootstrap se utiliza para estilos, componentes y comportamiento responsive.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>6. Plantillas</h2>

    <p>
        Las plantillas permiten reutilizar partes comunes en diferentes páginas.
    </p>

    <div class="code-box">
<pre>templates/
├── header.php
└── footer.php</pre>
    </div>

    <ul class="doc-list">
        <li><strong>header.php:</strong> incluye metadatos, CSS, Bootstrap Icons y cabecera.</li>
        <li><strong>footer.php:</strong> incluye enlaces legales, redes sociales, fecha actual y scripts.</li>
    </ul>
</section>

<section class="doc-section">
    <h2>7. Configuración y seguridad</h2>

    <p>
        Los archivos de configuración se mantienen separados del resto del proyecto.
    </p>

    <ul class="doc-list">
        <li><strong>conexion.php:</strong> conexión a base de datos.</li>
        <li><strong>stripe.php:</strong> configuración de pasarela de pago.</li>
        <li><strong>r2.php:</strong> configuración de Cloudflare R2 para archivos.</li>
    </ul>

    <p>
        Los archivos con claves sensibles se excluyen del repositorio mediante <code>.gitignore</code>.
    </p>
</section>

<section class="doc-section">
    <h2>8. Documentación</h2>

    <p>
        La carpeta <strong>docs</strong> contiene la documentación técnica publicada con GitHub Pages.
    </p>

    <div class="code-box">
<pre>docs/
├── index.html
├── 01-descripcion-general.md
├── 02-dawes-servidor.md
├── 03-dwec-cliente.md
├── 04-despliegue.md
├── 05-estructura-proyecto.md
├── 06-manual-usuario.md
├── 07-manual-administrador.md
└── 08-mejoras-futuras.md</pre>
    </div>
</section>

</div>
