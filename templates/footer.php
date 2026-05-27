<!-- =====================================================
     FOOTER PRINCIPAL DE LA WEB
     -----------------------------------------------------
     Este archivo contiene el pie común de la aplicación.

     Incluye:
     - Logo y marca.
     - Enlace a Instagram.
     - Enlaces a tienda y recursos gratuitos.
     - Enlaces legales.
     - Información dinámica de fechas.
     - Carga de scripts globales.

     IMPORTANTE:
     Las rutas internas usan BASE_URL para que funcionen tanto:
     - En local: http://localhost/UNRINCONDEPT/
     - En producción: https://tudominio.es/
====================================================== -->

<footer class="footer-web">

    <div class="container footer-container py-4">

        <div class="row align-items-start gy-4">

            <!-- =====================================================
                 MARCA / IDENTIDAD
                 -----------------------------------------------------
                 Bloque izquierdo del footer con:
                 - Logo.
                 - Nombre de la web.
                 - Enlace a Instagram.
            ====================================================== -->
            <div class="col-12 col-md-5">

                <div class="footer-brand d-flex align-items-center mb-3">

                    <!-- 
                        Logo de la web.
                        Usamos BASE_URL para evitar rutas absolutas fijas.
                    -->
                    <img src="<?= BASE_URL ?>static/images/logo/logo.jpeg"
                        alt="Logo Un Rincón Maravilloso de PT"
                        class="footer-logo">

                    <h5 class="mb-0">
                        Un Rincón Maravilloso de PT
                    </h5>

                </div>

                <!-- 
                    Enlace externo a Instagram.
                    Este enlace no usa BASE_URL porque no pertenece
                    a nuestra aplicación.
                -->
                <a href="https://www.instagram.com/unrinconmaravillosodept"
                    target="_blank"
                    class="footer-social"
                    aria-label="Instagram de Un Rincón Maravilloso de PT">

                    <i class="bi bi-instagram"></i>
                    @unrinconmaravillosodePT

                </a>

            </div>


            <!-- =====================================================
                 RECURSOS
                 -----------------------------------------------------
                 Enlaces principales relacionados con la tienda.
            ====================================================== -->
            <div class="col-6 col-md-3">

                <h6 class="footer-title">
                    Recursos
                </h6>

                <ul class="footer-list">

                    <!-- Enlace a la tienda -->
                    <li>
                        <a href="<?= BASE_URL ?>public/tienda.php">
                            Tienda
                        </a>
                    </li>

                    <!-- Enlace a material gratuito -->
                    <li>
                        <a href="<?= BASE_URL ?>public/tienda.php?precio=gratis">
                            Material gratuito
                        </a>
                    </li>

                </ul>

            </div>


            <!-- =====================================================
                 LEGAL
                 -----------------------------------------------------
                 Enlaces a páginas legales de la web.
            ====================================================== -->
            <div class="col-6 col-md-4">

                <h6 class="footer-title">
                    Legal
                </h6>

                <ul class="footer-list">

                    <li>
                        <a href="<?= BASE_URL ?>public/politica-privacidad.php">
                            Política de privacidad
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>public/politica-cookies.php">
                            Política de cookies
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>public/terminos-compra.php">
                            Términos de compra
                        </a>
                    </li>

                </ul>

            </div>

        </div>


        <!-- Línea separadora del footer -->
        <hr class="footer-divider">


        <!-- =====================================================
             PARTE INFERIOR DEL FOOTER
             -----------------------------------------------------
             Incluye información dinámica que se rellena con JS:
             - Año actual.
             - Fecha actual.
             - Última visita.
             - Última actualización de la tienda.
        ====================================================== -->
        <div class="footer-bottom">

            <div>

                <p class="mb-1">
                    © <span id="footer-anio"></span>
                    Un Rincón Maravilloso de PT. Todos los derechos reservados.
                </p>

                <p class="mb-0">
                    Fecha actual:
                    <span id="footer-fecha-actual"></span>
                </p>

            </div>

            <div>

                <p class="mb-1">
                    Tu última visita fue:
                    <span id="footer-ultima-visita"></span>
                </p>

                <p class="mb-0">
                    Última actualización de la tienda:

                    <!-- 
                        La fecha se guarda en data-fecha-actualizacion.
                        El archivo fecha.js puede leer este atributo
                        para mostrarla formateada.
                    -->
                    <span
                        id="footer-ultima-actualizacion"
                        data-fecha-actualizacion="2026-05-21">
                    </span>
                </p>

            </div>

        </div>

    </div>

</footer>


<!-- =====================================================
     SCRIPTS GLOBALES
     -----------------------------------------------------
     Se cargan al final del body para que el HTML ya esté
     disponible cuando JavaScript intente acceder al DOM.
====================================================== -->


<!-- jQuery desde CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<!-- Bootstrap local -->
<script src="<?= BASE_URL ?>static/bootstrap-5.3.8-dist/js/bootstrap.bundle.js"></script>


<!-- Validaciones generales de formularios -->
<script src="<?= BASE_URL ?>static/js/validaciones.js"></script>


<!-- Efectos jQuery personalizados -->
<script src="<?= BASE_URL ?>static/js/efectos-jquery.js"></script>


<!-- Gestión de fechas del footer -->
<script src="<?= BASE_URL ?>static/js/fecha.js"></script>

</body>

</html>