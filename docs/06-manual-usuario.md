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

.step {
    display: flex;
    gap: .9rem;
    align-items: flex-start;
    margin-bottom: .9rem;
    background: #f8fdff;
    border: 1px solid #e2f1f3;
    border-radius: 18px;
    padding: 1rem;
}

.step-number {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--turquesa);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    flex-shrink: 0;
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
    <div class="doc-badge">👤 Manual de usuario</div>

    <h1>Uso de la web para el <span>cliente</span></h1>

    <p>
        Este manual explica cómo un usuario cliente puede utilizar la aplicación: registrarse, iniciar sesión,
        consultar productos, guardar favoritos, comprar recursos, descargar materiales, enviar reseñas,
        abrir tickets de soporte y enviar sugerencias.
    </p>

    <div class="doc-nav">
        <a href="index.html">← Volver al índice</a>
        <a href="07-manual-administrador.html" class="secondary">Ir al manual administrador →</a>
    </div>
</section>

<section class="doc-section">
    <h2>1. Acceso a la web</h2>

    <p>
        El usuario puede acceder a la página principal de la tienda y navegar por los recursos disponibles.
        Desde el menú principal puede entrar en la tienda, consultar recursos gratuitos, iniciar sesión
        o registrarse.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Página de inicio</strong>
            <p>Muestra recursos destacados, recursos gratuitos y acceso a las secciones principales.</p>
        </div>

        <div class="doc-card">
            <strong>Tienda</strong>
            <p>Permite consultar recursos educativos digitales y aplicar filtros.</p>
        </div>

        <div class="doc-card">
            <strong>Contacto</strong>
            <p>Permite enviar consultas desde el formulario público.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>2. Registro de usuario</h2>

    <p>
        Para crear una cuenta, el usuario debe completar el formulario de registro.
    </p>

    <ul class="doc-list">
        <li>Nombre.</li>
        <li>Apellidos.</li>
        <li>Email.</li>
        <li>Localidad.</li>
        <li>Código postal.</li>
        <li>Contraseña.</li>
        <li>Confirmación de contraseña.</li>
        <li>Aceptación de políticas y términos.</li>
    </ul>

    <div class="alert-box">
        El formulario aplica validación en cliente con JavaScript y validación en servidor con PHP.
    </div>
</section>

<section class="doc-section">
    <h2>3. Inicio de sesión</h2>

    <p>
        El usuario puede iniciar sesión introduciendo su email y contraseña.
    </p>

    <div class="step">
        <div class="step-number">1</div>
        <div>
            <strong>Introducir email</strong>
            <p>El sistema comprueba que el email tenga un formato válido.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-number">2</div>
        <div>
            <strong>Introducir contraseña</strong>
            <p>El sistema verifica la contraseña mediante <code>password_verify()</code>.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-number">3</div>
        <div>
            <strong>Crear sesión</strong>
            <p>Si los datos son correctos, se crea una sesión PHP para identificar al usuario.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>4. Tienda de recursos</h2>

    <p>
        La tienda permite consultar los recursos disponibles. Cada producto incluye información como título,
        imagen, precio, descripción y acceso a su ficha de detalle.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Filtros</strong>
            <p>El usuario puede filtrar recursos por categoría, nivel, precio u otros criterios disponibles.</p>
        </div>

        <div class="doc-card">
            <strong>Búsqueda</strong>
            <p>Permite localizar recursos por texto.</p>
        </div>

        <div class="doc-card">
            <strong>Favoritos</strong>
            <p>Los usuarios registrados pueden guardar productos como favoritos.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>5. Detalle de producto</h2>

    <p>
        En la ficha de producto se muestra la información completa del recurso.
    </p>

    <ul class="doc-list">
        <li>Imagen principal del producto.</li>
        <li>Título y descripción.</li>
        <li>Precio.</li>
        <li>Contenido o características del recurso.</li>
        <li>Botón para añadir al carrito.</li>
        <li>Botón de favorito.</li>
        <li>Reseñas de otros usuarios.</li>
        <li>Productos relacionados.</li>
    </ul>
</section>

<section class="doc-section">
    <h2>6. Carrito y compra</h2>

    <p>
        El usuario puede añadir productos al carrito y revisar el contenido antes de realizar la compra.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Añadir productos</strong>
            <p>El usuario puede seleccionar recursos desde la tienda o desde la ficha de producto.</p>
        </div>

        <div class="doc-card">
            <strong>Revisar carrito</strong>
            <p>El carrito muestra productos seleccionados e importe total.</p>
        </div>

        <div class="doc-card">
            <strong>Pago</strong>
            <p>La pasarela de pago se conecta mediante Stripe.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>7. Descargas</h2>

    <p>
        Tras adquirir un recurso, el usuario puede acceder a sus descargas desde el perfil.
    </p>

    <ul class="doc-list">
        <li>Listado de recursos adquiridos.</li>
        <li>Fecha de compra.</li>
        <li>Número de descargas disponibles.</li>
        <li>Control de descargas máximas.</li>
        <li>Acceso seguro al archivo.</li>
    </ul>
</section>

<section class="doc-section">
    <h2>8. Perfil de usuario</h2>

    <p>
        El perfil centraliza la información personal y la actividad del usuario.
    </p>

    <div class="doc-grid">
        <div class="doc-card">
            <strong>Datos de cuenta</strong>
            <p>Nombre, apellidos, email, localidad, código postal y fecha de registro.</p>
        </div>

        <div class="doc-card">
            <strong>Recursos adquiridos</strong>
            <p>Listado de productos comprados y acceso a descargas.</p>
        </div>

        <div class="doc-card">
            <strong>Favoritos</strong>
            <p>Recursos guardados por el usuario.</p>
        </div>

        <div class="doc-card">
            <strong>Soporte</strong>
            <p>Consultas abiertas con la administración.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>9. Soporte</h2>

    <p>
        El usuario puede abrir tickets de soporte para resolver dudas o incidencias.
    </p>

    <div class="step">
        <div class="step-number">1</div>
        <div>
            <strong>Crear consulta</strong>
            <p>El usuario introduce el asunto y el mensaje.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-number">2</div>
        <div>
            <strong>Enviar ticket</strong>
            <p>El sistema guarda el ticket en la base de datos.</p>
        </div>
    </div>

    <div class="step">
        <div class="step-number">3</div>
        <div>
            <strong>Consultar respuestas</strong>
            <p>El usuario puede ver las respuestas de administración en formato conversación.</p>
        </div>
    </div>
</section>

<section class="doc-section">
    <h2>10. Sugerencias y contacto</h2>

    <p>
        El usuario puede enviar sugerencias desde su perfil para proponer mejoras, nuevos materiales
        o ideas relacionadas con la web.
    </p>

    <p>
        Además, cualquier visitante puede utilizar el formulario público de contacto para enviar consultas
        generales, incluso sin estar registrado.
    </p>
</section>

</div>
