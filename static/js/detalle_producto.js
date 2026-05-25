document.addEventListener("DOMContentLoaded", function () {

    // Buscamos el formulario real del modal de producto
    const form = document.getElementById("formChatProducto");

    // Si no existe, salimos
    if (!form) return;

    form.addEventListener("submit", function (e) {

        // Evitamos que recargue la página
        e.preventDefault();

        // Recogemos todos los campos del formulario:
        // producto_id, asunto y mensaje
        const formData = new FormData(form);

        fetch("/UNRINCONDEPT/public/ajax_soporte_producto.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            // Div de respuesta real que tienes en el modal
            const respuesta = document.getElementById("respuestaChatProducto");

            if (!data.ok) {
                respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        ${data.error}
                    </div>
                `;
                return;
            }

            respuesta.innerHTML = `
                <div class="alert alert-success">
                    ${data.mensaje}
                </div>
            `;

            form.reset();
        })
        .catch(error => {
            console.error("Error soporte producto:", error);

            const respuesta = document.getElementById("respuestaChatProducto");

            if (respuesta) {
                respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        Error al enviar la consulta.
                    </div>
                `;
            }
        });
    });
});

window.mostrarVideoProducto = function () {
    const imagen = document.getElementById('productoImagenPrincipal');
    const video = document.getElementById('productoVideoPrincipal');
    const thumbImagen = document.getElementById('thumbImagenProducto');
    const thumbVideo = document.getElementById('thumbVideoProducto');

    if (!imagen || !video) {
        console.warn('No se ha encontrado la imagen o el vídeo principal.');
        return;
    }

    const caja = video.closest('.producto-img-box');

    imagen.classList.add('d-none');
    video.classList.remove('d-none');

    if (caja) {
        caja.classList.add('modo-video');
    }

    if (thumbImagen) {
        thumbImagen.classList.remove('active');
    }

    if (thumbVideo) {
        thumbVideo.classList.add('active');
    }

    video.play().catch(() => {
        console.log('El navegador ha bloqueado la reproducción automática.');
    });
};

window.mostrarImagenProducto = function () {
    const imagen = document.getElementById('productoImagenPrincipal');
    const video = document.getElementById('productoVideoPrincipal');
    const thumbImagen = document.getElementById('thumbImagenProducto');
    const thumbVideo = document.getElementById('thumbVideoProducto');

    if (!imagen) {
        console.warn('No se ha encontrado la imagen principal.');
        return;
    }

    if (video) {
        const caja = video.closest('.producto-img-box');

        video.pause();
        video.currentTime = 0;
        video.classList.add('d-none');

        if (caja) {
            caja.classList.remove('modo-video');
        }
    }

    imagen.classList.remove('d-none');

    if (thumbVideo) {
        thumbVideo.classList.remove('active');
    }

    if (thumbImagen) {
        thumbImagen.classList.add('active');
    }
};