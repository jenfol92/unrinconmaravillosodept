/**
 * Archivo: validaciones.js
 * ---------------------------------------------------------
 * Funcionalidad general:
 *
 * Este archivo centraliza las validaciones del lado del cliente
 * para distintos formularios de la web:
 *
 * - Formulario de registro.
 * - Formulario de login.
 * - Formulario público de contacto.
 * - Campos de fecha que no deben permitir fechas futuras.
 *
 * Las validaciones se realizan mediante:
 *
 * - Expresiones regulares.
 * - Eventos de formulario.
 * - Comprobaciones de campos obligatorios.
 * - Comprobaciones de longitud mínima.
 * - Comparación entre contraseña y confirmación.
 * - Validación de fecha no futura.
 *
 * Además, modifica visualmente los campos usando clases de Bootstrap:
 *
 * - is-invalid
 * - is-valid
 * - invalid-feedback
 *
 * Este archivo sirve también para cumplir requisitos del módulo DWEC,
 * ya que trabaja validación de formularios, eventos y manipulación del DOM.
 */


/**
 * Evento principal que se ejecuta cuando el documento HTML
 * ya está completamente cargado.
 *
 * Desde aquí se inicializan todas las validaciones disponibles.
 */
document.addEventListener("DOMContentLoaded", function () {
    inicializarFormularioRegistro();
    inicializarFormularioLogin();
    inicializarFormularioContacto();
    inicializarFechasNoFuturas();
});


/**
 * Expresión regular para validar emails.
 *
 * Comprueba que:
 * - No haya espacios.
 * - Exista una arroba.
 * - Exista un dominio.
 * - La extensión tenga al menos 2 caracteres.
 */
const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;


/**
 * Expresión regular para validar códigos postales españoles.
 *
 * Permite códigos postales de 5 cifras comprendidos entre:
 * - 01000
 * - 52999
 */
const REGEX_CP_ES = /^(0[1-9]|[1-4][0-9]|5[0-2])[0-9]{3}$/;


/**
 * Expresión regular para validar teléfonos españoles.
 *
 * Permite números que comiencen por:
 * - 6
 * - 7
 * - 8
 * - 9
 *
 * También permite espacios entre bloques:
 * - 600123123
 * - 600 123 123
 */
const REGEX_TELEFONO_ES = /^[6789]\d{2}\s?\d{3}\s?\d{3}$/;


/**
 * Expresión regular para validar localidades.
 *
 * Permite:
 * - Letras mayúsculas y minúsculas.
 * - Tildes.
 * - Ñ.
 * - Espacios.
 * - Puntos.
 * - Apóstrofes.
 * - Guiones.
 * - Barras.
 *
 * La longitud permitida va de 2 a 100 caracteres.
 */
const REGEX_LOCALIDAD = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s.'\-\/]{2,100}$/;


/**
 * Inicializa la validación del formulario de registro.
 *
 * Busca el formulario con id "formRegistro".
 * Si existe, localiza sus campos principales y les añade eventos
 * para validar en tiempo real:
 *
 * - input: mientras el usuario escribe.
 * - blur: cuando el usuario sale del campo.
 * - submit: cuando el usuario intenta enviar el formulario.
 *
 * Si algún campo no es válido, se cancela el envío.
 *
 * @returns {void}
 */
function inicializarFormularioRegistro() {
    const formRegistro = document.getElementById("formRegistro");

    // Si no existe el formulario de registro en la página actual, no hacemos nada.
    if (!formRegistro) {
        return;
    }

    // Campos del formulario que se van a validar.
    // filter(Boolean) elimina los campos que no existan para evitar errores.
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

    // Añadimos validación en tiempo real a cada campo.
    campos.forEach(function (campo) {
        campo.addEventListener("input", function () {
            validarCampoRegistro(campo, formRegistro);
        });

        campo.addEventListener("blur", function () {
            validarCampoRegistro(campo, formRegistro);
        });
    });

    // Validación final antes de enviar el formulario.
    formRegistro.addEventListener("submit", function (event) {
        let valido = true;

        // Validamos todos los campos del formulario.
        campos.forEach(function (campo) {
            if (!validarCampoRegistro(campo, formRegistro)) {
                valido = false;
            }
        });

        // Validamos la aceptación de términos y condiciones.
        const terms = formRegistro.querySelector("[name='terms']");

        if (terms && !terms.checked) {
            mostrarError(terms, "Debes aceptar las políticas y términos.");
            valido = false;
        }

        // Si hay algún error, impedimos el envío del formulario.
        if (!valido) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);
}


/**
 * Valida un campo concreto del formulario de registro.
 *
 * Dependiendo del atributo name del campo, se llama a una función
 * de validación específica.
 *
 * @param {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} campo
 * Campo del formulario que se quiere validar.
 *
 * @param {HTMLFormElement} formulario
 * Formulario completo de registro. Se usa especialmente para comparar
 * contraseña y confirmación de contraseña.
 *
 * @returns {boolean}
 * Devuelve true si el campo es válido y false si no lo es.
 */
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


/**
 * Inicializa la validación del formulario de login.
 *
 * Busca el formulario con id "formLogin".
 * Si existe, valida:
 *
 * - Email.
 * - Contraseña obligatoria.
 *
 * La validación se ejecuta tanto en tiempo real como antes del envío.
 *
 * @returns {void}
 */
function inicializarFormularioLogin() {
    const formLogin = document.getElementById("formLogin");

    // Si no existe el formulario de login en la página actual, no hacemos nada.
    if (!formLogin) {
        return;
    }

    const email = formLogin.querySelector("[name='email']");
    const password = formLogin.querySelector("[name='password']");

    // Añadimos eventos de validación en tiempo real.
    [email, password].filter(Boolean).forEach(function (campo) {
        campo.addEventListener("input", function () {
            validarCampoLogin(campo);
        });

        campo.addEventListener("blur", function () {
            validarCampoLogin(campo);
        });
    });

    // Validación final antes del envío del formulario.
    formLogin.addEventListener("submit", function (event) {
        let valido = true;

        if (email && !validarEmail(email, email.value.trim())) {
            valido = false;
        }

        if (password && !validarTextoObligatorio(password, password.value.trim(), "La contraseña es obligatoria.")) {
            valido = false;
        }

        // Si algún campo no es válido, se bloquea el envío.
        if (!valido) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);
}


/**
 * Valida un campo concreto del formulario de login.
 *
 * @param {HTMLInputElement} campo
 * Campo que se quiere validar.
 *
 * @returns {boolean}
 * Devuelve true si el campo es válido y false si no lo es.
 */
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


/**
 * Inicializa la validación del formulario público de contacto.
 *
 * Busca el formulario con id "formContactoPublico".
 * Si existe, valida:
 *
 * - Nombre obligatorio.
 * - Email válido.
 * - Asunto con longitud mínima.
 * - Mensaje con longitud mínima.
 *
 * Si hay errores al enviar, muestra un aviso dentro del contenedor
 * con id "respuestaContactoPublico".
 *
 * @returns {void}
 */
function inicializarFormularioContacto() {
    const formContacto = document.getElementById("formContactoPublico");

    // Si no existe el formulario de contacto en la página actual, no hacemos nada.
    if (!formContacto) {
        return;
    }

    // Campos principales del formulario de contacto.
    const campos = [
        formContacto.querySelector("[name='nombre']"),
        formContacto.querySelector("[name='email']"),
        formContacto.querySelector("[name='asunto']"),
        formContacto.querySelector("[name='mensaje']")
    ].filter(Boolean);

    // Validación en tiempo real.
    campos.forEach(function (campo) {
        campo.addEventListener("input", function () {
            validarCampoContacto(campo);
        });

        campo.addEventListener("blur", function () {
            validarCampoContacto(campo);
        });
    });

    // Validación antes del envío.
    formContacto.addEventListener("submit", function (event) {
        let valido = true;

        campos.forEach(function (campo) {
            if (!validarCampoContacto(campo)) {
                valido = false;
            }
        });

        // Si no es válido, se bloquea el envío y se muestra un mensaje general.
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


/**
 * Valida un campo concreto del formulario de contacto público.
 *
 * @param {HTMLInputElement|HTMLTextAreaElement} campo
 * Campo que se quiere validar.
 *
 * @returns {boolean}
 * Devuelve true si el campo es válido y false si no lo es.
 */
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


/**
 * Inicializa la validación de fechas que no pueden ser futuras.
 *
 * Busca todos los inputs de tipo date que tengan el atributo:
 *
 * data-no-futura="true"
 *
 * Ejemplo HTML:
 *
 * <input type="date" name="fecha_nacimiento" data-no-futura="true">
 *
 * @returns {void}
 */
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


/**
 * Valida que una fecha introducida no sea posterior al día actual.
 *
 * Si el campo está vacío, se limpia su estado visual y se considera válido.
 *
 * @param {HTMLInputElement} campo
 * Campo de tipo date que se quiere validar.
 *
 * @returns {boolean}
 * Devuelve true si la fecha es válida o está vacía.
 * Devuelve false si la fecha es futura.
 */
function validarFechaNoFutura(campo) {
    if (!campo.value) {
        limpiarEstado(campo);
        return true;
    }

    const fechaIntroducida = new Date(campo.value);
    const hoy = new Date();

    // Se ponen ambas fechas a las 00:00 para comparar solo el día.
    hoy.setHours(0, 0, 0, 0);
    fechaIntroducida.setHours(0, 0, 0, 0);

    if (fechaIntroducida > hoy) {
        mostrarError(campo, "La fecha no puede ser futura.");
        return false;
    }

    mostrarValido(campo);
    return true;
}


/**
 * Valida que un campo de texto obligatorio no esté vacío.
 *
 * @param {HTMLElement} campo
 * Campo que se quiere validar.
 *
 * @param {string} valor
 * Valor del campo ya procesado con trim().
 *
 * @param {string} mensaje
 * Mensaje que se mostrará si el campo está vacío.
 *
 * @returns {boolean}
 * Devuelve true si el campo contiene texto.
 * Devuelve false si está vacío.
 */
function validarTextoObligatorio(campo, valor, mensaje) {
    if (valor.length === 0) {
        mostrarError(campo, mensaje);
        return false;
    }

    mostrarValido(campo);
    return true;
}


/**
 * Valida que un texto tenga una longitud mínima.
 *
 * @param {HTMLElement} campo
 * Campo que se quiere validar.
 *
 * @param {string} valor
 * Valor del campo ya procesado con trim().
 *
 * @param {number} minimo
 * Número mínimo de caracteres requeridos.
 *
 * @param {string} mensaje
 * Mensaje de error que se mostrará si no alcanza la longitud mínima.
 *
 * @returns {boolean}
 * Devuelve true si cumple la longitud mínima.
 * Devuelve false si no la cumple.
 */
function validarLongitudMinima(campo, valor, minimo, mensaje) {
    if (valor.length < minimo) {
        mostrarError(campo, mensaje);
        return false;
    }

    mostrarValido(campo);
    return true;
}


/**
 * Valida que el valor introducido tenga formato de email correcto.
 *
 * @param {HTMLElement} campo
 * Campo que se quiere validar.
 *
 * @param {string} valor
 * Email introducido por el usuario.
 *
 * @returns {boolean}
 * Devuelve true si el email es válido.
 * Devuelve false si el email no cumple el formato.
 */
function validarEmail(campo, valor) {
    if (!REGEX_EMAIL.test(valor)) {
        mostrarError(campo, "Introduce un correo electrónico válido.");
        return false;
    }

    mostrarValido(campo);
    return true;
}


/**
 * Valida que la localidad tenga un formato correcto.
 *
 * @param {HTMLElement} campo
 * Campo que se quiere validar.
 *
 * @param {string} valor
 * Localidad introducida por el usuario.
 *
 * @returns {boolean}
 * Devuelve true si la localidad es válida.
 * Devuelve false si no cumple el formato esperado.
 */
function validarLocalidad(campo, valor) {
    if (!REGEX_LOCALIDAD.test(valor)) {
        mostrarError(campo, "Introduce una localidad válida.");
        return false;
    }

    mostrarValido(campo);
    return true;
}


/**
 * Valida que el código postal tenga formato español válido.
 *
 * @param {HTMLElement} campo
 * Campo que se quiere validar.
 *
 * @param {string} valor
 * Código postal introducido por el usuario.
 *
 * @returns {boolean}
 * Devuelve true si el código postal es válido.
 * Devuelve false si no cumple el formato.
 */
function validarCodigoPostal(campo, valor) {
    if (!REGEX_CP_ES.test(valor)) {
        mostrarError(campo, "Introduce un código postal español válido de 5 cifras.");
        return false;
    }

    mostrarValido(campo);
    return true;
}


/**
 * Valida un teléfono opcional.
 *
 * Si el campo está vacío, se considera válido y se limpia su estado visual.
 * Si contiene valor, debe cumplir el formato de teléfono español.
 *
 * @param {HTMLElement} campo
 * Campo que se quiere validar.
 *
 * @param {string} valor
 * Teléfono introducido por el usuario.
 *
 * @returns {boolean}
 * Devuelve true si el campo está vacío o si el teléfono es válido.
 * Devuelve false si contiene un teléfono con formato incorrecto.
 */
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


/**
 * Valida la contraseña del formulario de registro.
 *
 * Actualmente exige una longitud mínima de 4 caracteres.
 *
 * Además, si el usuario ya ha escrito algo en el campo de confirmación,
 * vuelve a validar la confirmación para actualizar el estado visual.
 *
 * @param {HTMLElement} campo
 * Campo de contraseña.
 *
 * @param {string} valor
 * Contraseña introducida por el usuario.
 *
 * @param {HTMLFormElement} formulario
 * Formulario completo de registro.
 *
 * @returns {boolean}
 * Devuelve true si la contraseña cumple la longitud mínima.
 * Devuelve false si no la cumple.
 */
function validarPassword(campo, valor, formulario) {
    if (valor.length < 4) {
        mostrarError(campo, "La contraseña debe tener al menos 4 caracteres.");
        return false;
    }

    mostrarValido(campo);

    const passwordConfirm = formulario.querySelector("[name='password_confirm']");

    // Si ya hay texto en la confirmación, se vuelve a validar para comprobar coincidencia.
    if (passwordConfirm && passwordConfirm.value.trim().length > 0) {
        validarConfirmacionPassword(passwordConfirm, formulario);
    }

    return true;
}


/**
 * Valida que la confirmación de contraseña coincida con la contraseña.
 *
 * @param {HTMLElement} campo
 * Campo de confirmación de contraseña.
 *
 * @param {HTMLFormElement} formulario
 * Formulario completo de registro.
 *
 * @returns {boolean}
 * Devuelve true si ambas contraseñas coinciden.
 * Devuelve false si no coinciden.
 */
function validarConfirmacionPassword(campo, formulario) {
    const password = formulario.querySelector("[name='password']");

    // Si no existe el campo password, no se puede comparar y se considera válido.
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


/**
 * Muestra un mensaje de error asociado a un campo.
 *
 * Acciones que realiza:
 *
 * - Añade la clase Bootstrap "is-invalid".
 * - Elimina la clase "is-valid".
 * - Busca un contenedor adecuado para insertar el mensaje.
 * - Si no existe un mensaje previo, lo crea.
 * - Inserta el texto del error.
 *
 * @param {HTMLElement} campo
 * Campo donde se ha producido el error.
 *
 * @param {string} mensaje
 * Texto del error que se mostrará al usuario.
 *
 * @returns {void}
 */
function mostrarError(campo, mensaje) {
    campo.classList.add("is-invalid");
    campo.classList.remove("is-valid");

    const contenedor = obtenerContenedorMensaje(campo);
    let mensajeError = contenedor.querySelector(".mensaje-validacion");

    // Si el mensaje todavía no existe, se crea dinámicamente.
    if (!mensajeError) {
        mensajeError = document.createElement("div");
        mensajeError.className = "mensaje-validacion invalid-feedback d-block";
        contenedor.appendChild(mensajeError);
    }

    mensajeError.textContent = mensaje;
}


/**
 * Marca visualmente un campo como válido.
 *
 * Acciones que realiza:
 *
 * - Elimina la clase "is-invalid".
 * - Añade la clase "is-valid".
 * - Elimina el mensaje de validación si existía.
 *
 * @param {HTMLElement} campo
 * Campo que se quiere marcar como válido.
 *
 * @returns {void}
 */
function mostrarValido(campo) {
    campo.classList.remove("is-invalid");
    campo.classList.add("is-valid");

    const contenedor = obtenerContenedorMensaje(campo);
    const mensajeError = contenedor.querySelector(".mensaje-validacion");

    if (mensajeError) {
        mensajeError.remove();
    }
}


/**
 * Limpia el estado visual de un campo.
 *
 * Se usa, por ejemplo, cuando un campo opcional está vacío.
 *
 * Acciones que realiza:
 *
 * - Elimina "is-invalid".
 * - Elimina "is-valid".
 * - Elimina el mensaje de validación si existía.
 *
 * @param {HTMLElement} campo
 * Campo que se quiere limpiar.
 *
 * @returns {void}
 */
function limpiarEstado(campo) {
    campo.classList.remove("is-invalid");
    campo.classList.remove("is-valid");

    const contenedor = obtenerContenedorMensaje(campo);
    const mensajeError = contenedor.querySelector(".mensaje-validacion");

    if (mensajeError) {
        mensajeError.remove();
    }
}


/**
 * Obtiene el contenedor donde debe mostrarse el mensaje de validación.
 *
 * Se intenta localizar primero un contenedor concreto según la estructura
 * habitual del proyecto:
 *
 * - .auth-field
 * - .auth-check
 * - .col-12
 * - .col-md-6
 *
 * Si no encuentra ninguno, usa el elemento padre directo del campo.
 *
 * Esto permite que el mensaje de error se coloque en una zona visualmente
 * correcta sin depender de una única estructura HTML.
 *
 * @param {HTMLElement} campo
 * Campo desde el que se busca el contenedor.
 *
 * @returns {HTMLElement}
 * Contenedor donde se insertará o eliminará el mensaje de validación.
 */
function obtenerContenedorMensaje(campo) {
    return campo.closest(".auth-field") ||
           campo.closest(".auth-check") ||
           campo.closest(".col-12") ||
           campo.closest(".col-md-6") ||
           campo.parentElement;
}