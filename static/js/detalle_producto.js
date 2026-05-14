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