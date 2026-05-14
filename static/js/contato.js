document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("formContactoPublico");

    if (!form) return;

    form.addEventListener("submit", function (e) {

        e.preventDefault();

        const respuesta = document.getElementById("respuestaContactoPublico");
        const formData = new FormData(form);

        fetch("/UNRINCONDEPT/public/ajax_contacto_mensajes.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

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
            console.error("Error contacto:", error);

            respuesta.innerHTML = `
                <div class="alert alert-danger">
                    No se pudo enviar el mensaje.
                </div>
            `;
        });

    });

});