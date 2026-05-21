# Un Rincón Maravilloso de PT

## 1. Descripción del proyecto

**Un Rincón Maravilloso de PT** es una aplicación web desarrollada como proyecto final del ciclo de Desarrollo de Aplicaciones Web.

La plataforma permite consultar, comprar y descargar recursos educativos digitales relacionados con la Pedagogía Terapéutica. También incluye recursos gratuitos, favoritos, reseñas, carrito, sistema de usuarios, panel de administración, soporte, sugerencias y gestión de mensajes de contacto.

El proyecto se ha desarrollado utilizando principalmente:

- PHP
- MySQL
- PDO
- JavaScript
- AJAX
- jQuery
- Bootstrap
- Bootstrap Icons
- Sass/SCSS
- HTML5
- CSS3

---

## 2. Objetivos principales

Los objetivos principales de la aplicación son:

- Ofrecer una tienda online de recursos educativos digitales.
- Permitir el registro e inicio de sesión de usuarios.
- Diferenciar permisos entre usuario cliente y administrador.
- Gestionar productos, recursos gratuitos, usuarios, compras y descargas.
- Permitir la comunicación mediante soporte, sugerencias y formulario de contacto.
- Aplicar validaciones en cliente y servidor.
- Utilizar AJAX para cargar y gestionar información sin recargar completamente la página.
- Mantener una estructura organizada y reutilizable mediante controladores, modelos, vistas y plantillas.

---

## 3. Tipos de usuario

La aplicación diferencia al menos dos tipos de usuario:

### Usuario cliente

Puede:

- Registrarse e iniciar sesión.
- Consultar productos.
- Añadir productos al carrito.
- Guardar favoritos.
- Comprar recursos.
- Descargar recursos adquiridos.
- Enviar reseñas.
- Crear tickets de soporte.
- Enviar sugerencias.

### Usuario administrador

Puede:

- Acceder al panel de administración.
- Crear, editar y eliminar productos.
- Gestionar contenido gratuito.
- Ver usuarios registrados.
- Consultar descargas, favoritos y reseñas de usuarios.
- Responder tickets de soporte.
- Revisar sugerencias.
- Revisar mensajes enviados desde el formulario de contacto.
- Exportar reportes en PDF.

---

## 4. Funcionalidades principales

### Parte pública

- Página de inicio.
- Tienda de recursos.
- Recursos destacados.
- Recursos gratuitos.
- Detalle de producto.
- Formulario de contacto.
- Login.
- Registro.
- Páginas legales:
  - Política de privacidad.
  - Política de cookies.
  - Términos de compra.

### Perfil de usuario

- Datos de cuenta.
- Recursos adquiridos.
- Descargas.
- Favoritos.
- Soporte.
- Sugerencias.
- Seguridad de cuenta.

### Panel administrador

- Dashboard.
- Gestión de productos.
- Gestión de contenido gratuito.
- Gestión de usuarios.
- Soporte.
- Sugerencias.
- Contacto web.
- Exportación de reportes.
- Subida de ficheros.

---

## 5. Tecnologías utilizadas

### Backend

- PHP
- MySQL
- PDO
- Sesiones PHP
- Cookies técnicas
- Programación orientada a objetos mediante modelos y controladores

### Frontend

- HTML5
- CSS3
- Sass/SCSS
- Bootstrap
- Bootstrap Icons
- JavaScript
- jQuery
- AJAX

### Herramientas

- XAMPP
- phpMyAdmin
- Git
- GitHub
- GitHub Pages
- Composer, si se usa librería PDF
- Librería PDF para exportación de reportes

---

## 6. Estructura general del proyecto

```txt
UNRINCONDEPT/
├── app/
│   ├── controladores/
│   ├── modelos/
│   └── vistas/
│
├── config/
│   └── conexion.php
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
└── docs/
//