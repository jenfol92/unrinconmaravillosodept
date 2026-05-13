<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../templates/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="font-titles">Tienda de Recursos</h1>
            <p>¡Bienvenid@ a nuestra tienda de recursos educativos! Aquí encontrarás propuestas didácticas listas para aplicar en el aula. Todos los materiales han sido diseñados con un enfoque práctico, manipulativo y adaptado a las necesidades reales del alumnado.

                ¿Te interesa algún recurso? ¡Recíbelos al instante en tu email!</p>
        </div>

        <!--Filtros Div lateral con selección checkbox-->

        <!-- FILTROS -->
        <div class="col-md-3">
            <div class="card p-3">

                <h5>Filtrar por</h5>

                <!-- BUSCADOR -->
                <input type="text" id="busqueda" class="form-control mb-3" placeholder="Buscar recurso...">

                <!-- CATEGORÍAS -->
                <h6>Categorías</h6>
                <?php foreach ($categorias as $cat) { ?>
                    <div>
                        <input type="checkbox" class="filtro-categoria" value="<?= $cat['id'] ?>">
                        <?= $cat['nombre'] ?>
                    </div>
                <?php } ?>

                <!-- NIVELES -->
                <h6 class="mt-3">Nivel educativo</h6>
                <?php foreach ($niveles as $nivel) { ?>
                    <div>
                        <input type="checkbox" class="filtro-nivel" value="<?= $nivel['id'] ?>">
                        <?= $nivel['nombre'] ?>
                    </div>
                <?php } ?>


            </div>
        </div>
     
        <!--Listado de productos-->
        <div class="col-md-9">
            <div id="contenedor-productos" class="row">
            </div>
                
            <!-- PAGINACIÓN -->
        <div id="paginacion" class="mt-4 text-center">

        </div>

        </div>
    </div>
   <script src="/UNRINCONDEPT/static/js/tienda.js"></script>  

    <?php
    require_once __DIR__ . '/../../templates/footer.php';
    ?>