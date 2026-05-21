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

.code-box {
    background: #1f2d33;
    color: #e9fbff;
    border-radius: 18px;
    padding: 1rem;
    overflow-x: auto;
    font-size: .92rem;
}

.status-ok {
    color: #198754;
    font-weight: 900;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: rgba(250, 204, 21, .18);
    border: 1px solid rgba(250, 204, 21, .35);
    color: #9a6b00;
    padding: .35rem .75rem;
    border-radius: 999px;
    font-weight: 800;
    font-size: .84rem;
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
    <div class="doc-badge">⚡ DWEC · Entorno Cliente</div>

    <h1>Desarrollo Web en <span>Entorno Cliente</span></h1>

    <p>
        Esta sección documenta las funcionalidades implementadas en JavaScript dentro del proyecto
        <strong>Un Rincón Maravilloso de PT</strong>: validaciones, eventos, manipulación del DOM,
        jQuery, AJAX, carruseles, uso del objeto Date y mejora de la experiencia de usuario.
    </p>

    <div class="doc-nav">
        <a href="index.html">← Volver al índice</a>
        <a href="04-despliegue.html" class="secondary">Ir a despliegue →</a>
    </div>
</section>

<section class="doc-section">
    <h2>1. Archivos JavaScript principales</h2>

    <p>
        El proyecto utiliza varios archivos JavaScript para separar responsabilidades y mantener
        el código organizado.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>fecha.js</strong>
            <p>Gestiona fechas del footer, año actual, última visita y última actualización de la tienda.</p>
        </div>

        <div class="doc-card">
            <strong>validaciones.js</strong>
            <p>Valida formularios en cliente mediante expresiones regulares, eventos y DOM.</p>
        </div>

        <div class="doc-card">
            <strong>efectos-jquery.js</strong>
            <p>Aplica efectos visuales con jQuery: filtros, mensajes animados y chat de soporte.</p>
        </div>

        <div class="doc-card">
            <strong>admin.js</strong>
            <p>Gestiona funcionalidades dinámicas del panel administrador mediante AJAX.</p>
        </div>

        <div class="doc-card">
            <strong>tienda.js</strong>
            <p>Gestiona filtros, búsqueda, favoritos y acciones relacionadas con la tienda.</p>
        </div>

        <div class="doc-card">
            <strong>contacto.js</strong>
            <p>Procesa el formulario de contacto público mediante JavaScript y AJAX.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>2. Uso del objeto Date</h2>

    <p>
        El archivo <strong>fecha.js</strong> utiliza el objeto <code>Date</code> de JavaScript para trabajar
        con fechas reales del sistema.
    </p>

    <ul class="doc-list">
        <li>Mostrar automáticamente el año actual en el footer.</li>
        <li>Mostrar la fecha actual en formato español.</li>
        <li>Guardar y mostrar la última visita del usuario mediante <code>localStorage</code>.</li>
        <li>Mostrar la última actualización de la tienda.</li>
    </ul>

    <div class="code-box">
<pre>const fechaActual = new Date();

elementoAnio.textContent = fechaActual.getFullYear();

elementoFecha.textContent = fechaActual.toLocaleDateString("es-ES", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric"
});</pre>
    </div>
</section>

<section class="doc-section">
    <h2>3. Validación de formularios</h2>

    <p>
        El archivo <strong>validaciones.js</strong> valida los formularios principales antes de enviarlos
        al servidor. Esto mejora la experiencia del usuario y reduce errores.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Registro</strong>
            <p>Nombre, apellidos, email, localidad, código postal, contraseña y confirmación.</p>
        </div>

        <div class="doc-card">
            <strong>Login</strong>
            <p>Email válido y contraseña obligatoria.</p>
        </div>

        <div class="doc-card">
            <strong>Contacto</strong>
            <p>Nombre, email, asunto y mensaje.</p>
        </div>

        <div class="doc-card">
            <strong>Fechas</strong>
            <p>Validación para impedir fechas futuras cuando corresponda.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>4. Expresiones regulares</h2>

    <p>
        Se utilizan expresiones regulares para validar formatos específicos.
    </p>

    <div class="code-box">
<pre>const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const REGEX_CP_ES = /^(0[1-9]|[1-4][0-9]|5[0-2])[0-9]{3}$/;
const REGEX_TELEFONO_ES = /^[6789]\d{2}\s?\d{3}\s?\d{3}$/;
const REGEX_LOCALIDAD = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s.'\-\/]{2,100}$/;</pre>
    </div>

    <ul class="doc-list">
        <li><strong>Email:</strong> comprueba que el correo tenga formato válido.</li>
        <li><strong>Código postal:</strong> valida códigos postales españoles de cinco cifras.</li>
        <li><strong>Teléfono:</strong> preparado como campo opcional pero válido.</li>
        <li><strong>Localidad:</strong> permite letras, espacios, tildes, guiones y caracteres habituales.</li>
    </ul>
</section>

<section class="doc-section">
    <h2>5. Eventos utilizados</h2>

    <p>
        Se utilizan eventos para reaccionar a las acciones del usuario y mejorar la interacción.
    </p>

    <div class="doc-grid">
        <div class="doc-card"><strong>DOMContentLoaded</strong><p>Ejecuta código cuando la página ya está cargada.</p></div>
        <div class="doc-card"><strong>submit</strong><p>Valida formularios antes de enviarlos.</p></div>
        <div class="doc-card"><strong>input</strong><p>Valida mientras el usuario escribe.</p></div>
        <div class="doc-card"><strong>blur</strong><p>Valida cuando el usuario sale de un campo.</p></div>
        <div class="doc-card"><strong>change</strong><p>Valida cambios en campos de fecha o selección.</p></div>
        <div class="doc-card"><strong>click</strong><p>Gestiona botones, filtros, navegación y acciones dinámicas.</p></div>
    </div>
</section>

<section class="doc-section">
    <h2>6. Manipulación del DOM</h2>

    <p>
        El proyecto modifica elementos HTML desde JavaScript para mostrar errores, actualizar contenido
        y cambiar la interfaz sin recargar la página.
    </p>

    <div class="code-box">
<pre>document.getElementById("formRegistro");
formulario.querySelector("[name='email']");
campo.classList.add("is-invalid");
document.createElement("div");
contenedor.appendChild(mensajeError);
mensajeError.textContent = mensaje;</pre>
    </div>
</section>

<section class="doc-section">
    <h2>7. Uso de jQuery</h2>

    <p>
        El archivo <strong>efectos-jquery.js</strong> utiliza jQuery para cumplir los requisitos de efectos
        visuales y manipulación dinámica.
    </p>

    <ul class="doc-list">
        <li>Mostrar y ocultar filtros del panel administrador.</li>
        <li>Animar mensajes de éxito y error.</li>
        <li>Mejorar la visualización del chat de soporte.</li>
        <li>Desplazar automáticamente el chat al último mensaje.</li>
        <li>Modificar textos, iconos y estados de botones.</li>
    </ul>

    <div class="code-box">
<pre>$(".admin-filtros-toggle").on("click", function () {
    panel.slideToggle(300);
});

$(".alert").hide().fadeIn(350);

contenedor.animate({
    scrollTop: contenedor.prop("scrollHeight")
}, 350);</pre>
    </div>
</section>

<section class="doc-section">
    <h2>8. AJAX</h2>

    <p>
        AJAX se utiliza para enviar y recibir información sin recargar completamente la página.
    </p>

    <div class="doc-grid">
        <div class="doc-card"><strong>Productos</strong><p>Carga, filtrado y paginación en el panel admin.</p></div>
        <div class="doc-card"><strong>Favoritos</strong><p>Añadir o quitar productos favoritos.</p></div>
        <div class="doc-card"><strong>Contacto</strong><p>Envío del formulario de contacto público.</p></div>
        <div class="doc-card"><strong>Soporte</strong><p>Lectura y respuesta de conversaciones.</p></div>
        <div class="doc-card"><strong>Reseñas</strong><p>Gestión de reseñas y estados desde administración.</p></div>
        <div class="doc-card"><strong>Carrito</strong><p>Acciones dinámicas relacionadas con productos.</p></div>
    </div>
</section>

<section class="doc-section">
    <h2>9. Slideshow / carrusel</h2>

    <p>
        La página principal incluye carruseles Bootstrap que funcionan como slideshow de imágenes.
    </p>

    <ul class="doc-list">
        <li><code>homeDestacadosCarousel</code>: recursos destacados.</li>
        <li><code>homeGratuitosCarousel</code>: recursos gratuitos.</li>
        <li>Incluyen indicadores, controles anterior/siguiente y cambio automático.</li>
        <li>El comportamiento se adapta a la versión móvil de la web.</li>
    </ul>
</section>

<section class="doc-section">
    <h2>10. Requisitos DWEC cubiertos</h2>

    <div class="doc-grid">
        <div class="doc-card"><span class="status-ok">✓</span> JavaScript organizado y funcional.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Uso del objeto Date.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Validaciones en cliente.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Expresiones regulares.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Eventos y DOM.</div>
        <div class="doc-card"><span class="status-ok">✓</span> jQuery y efectos visuales.</div>
        <div class="doc-card"><span class="status-ok">✓</span> AJAX.</div>
        <div class="doc-card"><span class="status-ok">✓</span> Slideshow de imágenes.</div>
    </div>
</section>

</div>
