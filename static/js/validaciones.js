/**
 * Archivo: validaciones.js
 * ---------------------------------------------------------
 * Funcionalidad:
 * - Valida formularios en el lado del cliente.
 * - Usa expresiones regulares para email, localidad, código postal y teléfono.
 * - Comprueba contraseña mínima y confirmación de contraseña.
 * - Valida que una fecha introducida no sea futura.
 * - Usa eventos: submit, input, blur, change.
 * - Modifica el DOM mostrando errores y clases visuales.
 *
 * Este archivo sirve para cumplir requisitos del módulo DWEC.
 */

document.addEventListener("DOMContentLoaded", function () {
    inicializarFormularioRegistro();
    inicializarFormularioLogin();
    inicializarFormularioContacto();
    inicializarFechasNoFuturas();
});

const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const REGEX_CP_ES = /^(0[1-9]|[1-4][0-9]|5[0-2])[0-9]{3}$/;
const REGEX_TELEFONO_ES = /^[6789]\d{2}\s?\d{3}\s?\d{3}$/;
const REGEX_LOCALIDAD = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s.'\-\/]{2,100}$/;

function inicializarFormularioRegistro() {
    const formRegistro = document.getElementById("formRegistro");

    if (!formRegistro) {
        return;
    }

    const campos = [
        formRegistro.querySelector("[name='nombre']"),
        formRegistro.querySelector("[name='apellidos']"),
        formRegistro.querySelector("[name='email']"),
        formRegistro.querySelector("[name='localidad']"),
        formRegistro.querySelector("[name='cp']"),
        formRegistro.querySelector("[name='telefono']"),
        formRegistro.querySelector("[name='password']"),
        formRegistro.querySelector("[name='password_confirm']")
    ].filter(Boolean);

    campos.forEach(function (campo) {
        campo.addEventListener("input", function () {
            validarCampoRegistro(campo, formRegistro);
        });

        campo.addEventListener("blur", function () {
            validarCampoRegistro(campo, formRegistro);
        });
    });

    formRegistro.addEventListener("submit", function (event) {
        let valido = true;

        campos.forEach(function (campo) {
            if (!validarCampoRegistro(campo, formRegistro)) {
                valido = false;
            }
        });

        const terms = formRegistro.querySelector("[name='terms']");

        if (terms && !terms.checked) {
            mostrarError(terms, "Debes aceptar las políticas y términos.");
            valido = false;
        }

        if (!valido) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);
}

function validarCampoRegistro(campo, formulario) {
    const valor = campo.value.trim();

    switch (campo.name) {
        case "nombre":
            return validarTextoObligatorio(campo, valor, "El nombre es obligatorio.");

        case "apellidos":
            return validarTextoObligatorio(campo, valor, "Los apellidos son obligatorios.");

        case "email":
            return validarEmail(campo, valor);

        case "localidad":
            return validarLocalidad(campo, valor);

        case "cp":
            return validarCodigoPostal(campo, valor);

        case "telefono":
            return validarTelefonoOpcional(campo, valor);

        case "password":
            return validarPassword(campo, valor, formulario);

        case "password_confirm":
            return validarConfirmacionPassword(campo, formulario);

        default:
            return true;
    }
}

function inicializarFormularioLogin() {
    const formLogin = document.getElementById("formLogin");

    if (!formLogin) {
        return;
    }

    const email = formLogin.querySelector("[name='email']");
    const password = formLogin.querySelector("[name='password']");

    [email, password].filter(Boolean).forEach(function (campo) {
        campo.addEventListener("input", function () {
            validarCampoLogin(campo);
        });

        campo.addEventListener("blur", function () {
            validarCampoLogin(campo);
        });
    });

    formLogin.addEventListener("submit", function (event) {
        let valido = true;

        if (email && !validarEmail(email, email.value.trim())) {
            valido = false;
        }

        if (password && !validarTextoObligatorio(password, password.value.trim(), "La contraseña es obligatoria.")) {
            valido = false;
        }

        if (!valido) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);
}

function validarCampoLogin(campo) {
    const valor = campo.value.trim();

    if (campo.name === "email") {
        return validarEmail(campo, valor);
    }

    if (campo.name === "password") {
        return validarTextoObligatorio(campo, valor, "La contraseña es obligatoria.");
    }

    return true;
}

function inicializarFormularioContacto() {
    const formContacto = document.getElementById("formContactoPublico");

    if (!formContacto) {
        return;
    }

    const campos = [
        formContacto.querySelector("[name='nombre']"),
        formContacto.querySelector("[name='email']"),
        formContacto.querySelector("[name='asunto']"),
        formContacto.querySelector("[name='mensaje']")
    ].filter(Boolean);

    campos.forEach(function (campo) {
        campo.addEventListener("input", function () {
            validarCampoContacto(campo);
        });

        campo.addEventListener("blur", function () {
            validarCampoContacto(campo);
        });
    });

    formContacto.addEventListener("submit", function (event) {
        let valido = true;

        campos.forEach(function (campo) {
            if (!validarCampoContacto(campo)) {
                valido = false;
            }
        });

        if (!valido) {
            event.preventDefault();
            event.stopImmediatePropagation();

            const respuesta = document.getElementById("respuestaContactoPublico");

            if (respuesta) {
                respuesta.innerHTML = `
                    <div class="alert alert-danger">
                        Revisa los campos marcados antes de enviar el mensaje.
                    </div>
                `;
            }
        }
    }, true);
}

function validarCampoContacto(campo) {
    const valor = campo.value.trim();

    switch (campo.name) {
        case "nombre":
            return validarTextoObligatorio(campo, valor, "El nombre es obligatorio.");

        case "email":
            return validarEmail(campo, valor);

        case "asunto":
            return validarLongitudMinima(campo, valor, 3, "El asunto debe tener al menos 3 caracteres.");

        case "mensaje":
            return validarLongitudMinima(campo, valor, 10, "El mensaje debe tener al menos 10 caracteres.");

        default:
            return true;
    }
}

function inicializarFechasNoFuturas() {
    const camposFecha = document.querySelectorAll("input[type='date'][data-no-futura='true']");

    camposFecha.forEach(function (campoFecha) {
        campoFecha.addEventListener("change", function () {
            validarFechaNoFutura(campoFecha);
        });

        campoFecha.addEventListener("blur", function () {
            validarFechaNoFutura(campoFecha);
        });
    });
}

function validarFechaNoFutura(campo) {
    if (!campo.value) {
        limpiarEstado(campo);
        return true;
    }

    const fechaIntroducida = new Date(campo.value);
    const hoy = new Date();

    hoy.setHours(0, 0, 0, 0);
    fechaIntroducida.setHours(0, 0, 0, 0);

    if (fechaIntroducida > hoy) {
        mostrarError(campo, "La fecha no puede ser futura.");
        return false;
    }

    mostrarValido(campo);
    return true;
}

function validarTextoObligatorio(campo, valor, mensaje) {
    if (valor.length === 0) {
        mostrarError(campo, mensaje);
        return false;
    }

    mostrarValido(campo);
    return true;
}

function validarLongitudMinima(campo, valor, minimo, mensaje) {
    if (valor.length < minimo) {
        mostrarError(campo, mensaje);
        return false;
    }

    mostrarValido(campo);
    return true;
}

function validarEmail(campo, valor) {
    if (!REGEX_EMAIL.test(valor)) {
        mostrarError(campo, "Introduce un correo electrónico válido.");
        return false;
    }

    mostrarValido(campo);
    return true;
}

function validarLocalidad(campo, valor) {
    if (!REGEX_LOCALIDAD.test(valor)) {
        mostrarError(campo, "Introduce una localidad válida.");
        return false;
    }

    mostrarValido(campo);
    return true;
}

function validarCodigoPostal(campo, valor) {
    if (!REGEX_CP_ES.test(valor)) {
        mostrarError(campo, "Introduce un código postal español válido de 5 cifras.");
        return false;
    }

    mostrarValido(campo);
    return true;
}

function validarTelefonoOpcional(campo, valor) {
    if (valor.length === 0) {
        limpiarEstado(campo);
        return true;
    }

    if (!REGEX_TELEFONO_ES.test(valor)) {
        mostrarError(campo, "Introduce un teléfono válido o deja el campo vacío.");
        return false;
    }

    mostrarValido(campo);
    return true;
}

function validarPassword(campo, valor, formulario) {
    if (valor.length < 4) {
        mostrarError(campo, "La contraseña debe tener al menos 4 caracteres.");
        return false;
    }

    mostrarValido(campo);

    const passwordConfirm = formulario.querySelector("[name='password_confirm']");

    if (passwordConfirm && passwordConfirm.value.trim().length > 0) {
        validarConfirmacionPassword(passwordConfirm, formulario);
    }

    return true;
}

function validarConfirmacionPassword(campo, formulario) {
    const password = formulario.querySelector("[name='password']");

    if (!password) {
        return true;
    }

    if (campo.value.trim() !== password.value.trim()) {
        mostrarError(campo, "Las contraseñas no coinciden.");
        return false;
    }

    mostrarValido(campo);
    return true;
}

function mostrarError(campo, mensaje) {
    campo.classList.add("is-invalid");
    campo.classList.remove("is-valid");

    const contenedor = obtenerContenedorMensaje(campo);
    let mensajeError = contenedor.querySelector(".mensaje-validacion");

    if (!mensajeError) {
        mensajeError = document.createElement("div");
        mensajeError.className = "mensaje-validacion invalid-feedback d-block";
        contenedor.appendChild(mensajeError);
    }

    mensajeError.textContent = mensaje;
}

function mostrarValido(campo) {
    campo.classList.remove("is-invalid");
    campo.classList.add("is-valid");

    const contenedor = obtenerContenedorMensaje(campo);
    const mensajeError = contenedor.querySelector(".mensaje-validacion");

    if (mensajeError) {
        mensajeError.remove();
    }
}

function limpiarEstado(campo) {
    campo.classList.remove("is-invalid");
    campo.classList.remove("is-valid");

    const contenedor = obtenerContenedorMensaje(campo);
    const mensajeError = contenedor.querySelector(".mensaje-validacion");

    if (mensajeError) {
        mensajeError.remove();
    }
}

function obtenerContenedorMensaje(campo) {
    return campo.closest(".auth-field") ||
           campo.closest(".auth-check") ||
           campo.closest(".col-12") ||
           campo.closest(".col-md-6") ||
           campo.parentElement;
}