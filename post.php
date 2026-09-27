<?php

/**
 * Endpoint JSON para publicar, borrar y dar like a los posts (PostController).
 * Lo llama public/js-min/Home.min.js.
 *
 * Todos los archivos .php de esta carpeta son iguales: cargan el Bootstrap y lo arrancan.
 * El Bootstrap usa el nombre del archivo para elegir el controlador:
 * post.php -> App/Controller/PostController.php
 *
 * Más detalles en docs/2-arquitectura.md
 */
include 'App/Bootstrap/Bootstrap.php';

\App\Bootstrap\Bootstrap::start();
