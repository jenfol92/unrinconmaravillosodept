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
        radial-gradient(circle at bottom right, rgba(242, 140, 111, 0.20), transparent 34%),
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

.code-box {
    background: #1f2d33;
    color: #e9fbff;
    border-radius: 18px;
    padding: 1rem;
    overflow-x: auto;
    font-size: .92rem;
}

.hosting-selected {
    background: linear-gradient(135deg, rgba(86, 179, 173, .14), rgba(250, 204, 21, .18));
    border: 1px solid rgba(86, 179, 173, .30);
    border-radius: 22px;
    padding: 1.2rem;
    margin-top: 1rem;
}

.status-ok {
    color: #198754;
    font-weight: 900;
}

.status-pending {
    color: #d97706;
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
    <div class="doc-badge">🚀 Despliegue · GitHub · Hosting</div>

    <h1>Documentación de <span>Despliegue</span></h1>

    <p>
        Esta sección describe cómo se gestiona el proyecto con Git y GitHub, cómo funciona en local,
        cómo se publica la documentación mediante GitHub Pages y cuál será el hosting previsto para
        desplegar la aplicación PHP + MySQL.
    </p>

    <div class="doc-nav">
        <a href="index.html">← Volver al índice</a>
        <a href="05-estructura-proyecto.html" class="secondary">Ir a estructura →</a>
    </div>
</section>

<section class="doc-section">
    <h2>1. Entornos del proyecto</h2>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Entorno local</strong>
            <p>La aplicación se desarrolla y prueba en local mediante XAMPP, Apache, PHP y MySQL/MariaDB.</p>
        </div>

        <div class="doc-card">
            <strong>Repositorio GitHub</strong>
            <p>El código se almacena en GitHub para mantener control de versiones y compartir el proyecto.</p>
        </div>

        <div class="doc-card">
            <strong>GitHub Pages</strong>
            <p>Se utiliza para publicar la documentación estática del proyecto ubicada en la carpeta <code>docs/</code>.</p>
        </div>

        <div class="doc-card">
            <strong>Hosting real</strong>
            <p>La tienda PHP + MySQL se desplegará en un hosting compatible con PHP, base de datos, SSL y subida de archivos.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>2. Funcionamiento en local</h2>

    <p>
        Durante el desarrollo, el proyecto se ejecuta en local con XAMPP.
    </p>

    <div class="code-box">
<pre>Ruta local:
C:/xampp/htdocs/UNRINCONDEPT

URL local:
http://localhost/UNRINCONDEPT/public/index.php</pre>
    </div>

    <h3>Pasos para ejecutar en local</h3>

    <ol class="doc-list">
        <li>Copiar el proyecto dentro de la carpeta <code>htdocs</code> de XAMPP.</li>
        <li>Iniciar Apache y MySQL desde el panel de XAMPP.</li>
        <li>Crear la base de datos en phpMyAdmin.</li>
        <li>Importar el archivo SQL del proyecto.</li>
        <li>Revisar la conexión en <code>config/conexion.php</code>.</li>
        <li>Abrir la aplicación desde el navegador.</li>
    </ol>
</section>

<section class="doc-section">
    <h2>3. Uso de Git</h2>

    <p>
        Git se utiliza para controlar los cambios realizados durante el desarrollo.
    </p>

    <div class="code-box">
<pre>git status
git add .
git commit -m "Mensaje del commit"
git push origin main
git pull origin main</pre>
    </div>

    <p>
        Estos comandos permiten revisar cambios, preparar archivos, crear commits, subir el proyecto
        a GitHub y descargar cambios remotos.
    </p>
</section>

<section class="doc-section">
    <h2>4. Uso de ramas</h2>

    <p>
        Para organizar el trabajo se pueden utilizar ramas de desarrollo.
    </p>

    <div class="code-box">
<pre>git checkout -b feature/documentacion
git checkout -b feature/validaciones
git checkout -b feature/jquery-efectos
git checkout -b feature/despliegue</pre>
    </div>

    <p>
        El uso de ramas permite separar funcionalidades y mantener más ordenado el historial del proyecto.
    </p>
</section>

<section class="doc-section">
    <h2>5. Uso de etiquetas o tags</h2>

    <p>
        Para marcar una versión estable del proyecto se puede crear una etiqueta.
    </p>

    <div class="code-box">
<pre>git tag v1.0-presentacion
git push origin v1.0-presentacion</pre>
    </div>

    <p>
        Esta etiqueta sirve como referencia para la versión presentada del proyecto.
    </p>
</section>

<section class="doc-section">
    <h2>6. GitHub Pages</h2>

    <p>
        GitHub Pages se utiliza para publicar la documentación técnica del proyecto.
    </p>

    <div class="code-box">
<pre>Carpeta publicada:
docs/

Página inicial:
docs/index.html</pre>
    </div>

    <div class="alert-box">
        <strong>Importante:</strong> GitHub Pages no ejecuta PHP ni MySQL. Por tanto, no sirve para alojar
        la tienda real. Solo se utiliza para publicar documentación estática.
    </div>

    <h3>Configuración prevista</h3>

    <ul class="doc-list">
        <li>Repositorio público en GitHub.</li>
        <li>GitHub Pages activado desde la rama <code>main</code>.</li>
        <li>Carpeta seleccionada: <code>/docs</code>.</li>
        <li>Archivo de portada: <code>index.html</code>.</li>
    </ul>
</section>

<section class="doc-section">
    <h2>7. Hosting elegido para el despliegue</h2>

    <div class="hosting-selected">
        <h3>Hosting previsto: Hostinger</h3>

        <p>
            El proyecto se desplegará previsiblemente en <strong>Hostinger</strong>, utilizando un plan de hosting
            compatible con PHP y MySQL. La elección se realiza porque el proyecto necesita un entorno sencillo
            para una tienda pequeña de recursos digitales, con base de datos, SSL, subida de archivos y posibilidad
            de configurar servicios externos como Stripe y Cloudflare R2.
        </p>
    </div>

    <h3>Motivos de elección</h3>

    <ul class="doc-list">
        <li>Es una opción económica para comenzar.</li>
        <li>Permite trabajar con PHP y bases de datos MySQL/MariaDB.</li>
        <li>Incluye SSL en sus planes de hosting.</li>
        <li>Permite gestionar archivos y bases de datos desde un panel sencillo.</li>
        <li>Puede ser suficiente para una primera versión de una tienda pequeña de recursos digitales.</li>
    </ul>

    <h3>Alternativas valoradas</h3>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Dinahosting</strong>
            <p>
                Alternativa más profesional, con soporte en español, SSL, backups, FTP, SSH/Git y bases de datos ilimitadas
                en determinados planes.
            </p>
        </div>

        <div class="doc-card">
            <strong>IONOS</strong>
            <p>
                Alternativa económica inicial, con SSL, dominio y correo incluidos en sus packs, aunque se deben revisar
                los precios de renovación.
            </p>
        </div>

        <div class="doc-card">
            <strong>Hostinger</strong>
            <p>
                Opción seleccionada por relación entre precio, facilidad de uso y prestaciones suficientes para la primera versión.
            </p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>8. Requisitos técnicos del hosting</h2>

    <p>
        Para alojar correctamente la aplicación, el hosting debe cumplir los siguientes requisitos:
    </p>

    <div class="doc-grid">
        <div class="doc-card"><span class="status-ok">✓</span> PHP 8 o superior.</div>
        <div class="doc-card"><span class="status-ok">✓</span> MySQL o MariaDB.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Certificado SSL.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Acceso a phpMyAdmin o gestor similar.</div>
        <div class="doc-card"><span class="status-ok">✓</span> FTP o SFTP.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Soporte para archivos subidos.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Posibilidad de configurar Stripe.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Posibilidad de conectar Cloudflare R2.</div>
    </div>
</section>

<section class="doc-section">
    <h2>9. Archivos sensibles y .gitignore</h2>

    <p>
        No deben subirse al repositorio archivos que contengan claves privadas o credenciales.
    </p>

    <div class="code-box">
<pre>/vendor/
node_modules/

.env
*.log

/config/r2.php
/config/stripe.php

.DS_Store
Thumbs.db</pre>
    </div>

    <p>
        Los archivos de configuración de Stripe y Cloudflare R2 deben mantenerse fuera del repositorio público.
    </p>
</section>

<section class="doc-section">
    <h2>10. Base de datos</h2>

    <p>
        Para desplegar la aplicación en el hosting será necesario exportar la base de datos local e importarla
        en el servidor final.
    </p>

    <ol class="doc-list">
        <li>Exportar la base de datos desde phpMyAdmin en formato <code>.sql</code>.</li>
        <li>Crear una base de datos en el hosting.</li>
        <li>Importar el archivo SQL.</li>
        <li>Actualizar los datos de conexión.</li>
        <li>Probar login, registro, tienda, compras, descargas y panel admin.</li>
    </ol>
</section>

<section class="doc-section">
    <h2>11. Estado del despliegue</h2>

    <div class="doc-grid">
        <div class="doc-card"><span class="status-ok">✓</span> Proyecto funcionando en local.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Repositorio creado en GitHub.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Documentación preparada para GitHub Pages.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Uso de Git y commits.</div>
        <div class="doc-card"><span class="status-pending">◷</span> Activación final de GitHub Pages.</div>
        <div class="doc-card"><span class="status-pending">◷</span> Despliegue final en Hostinger.</div>
    </div>
</section>

</div>
