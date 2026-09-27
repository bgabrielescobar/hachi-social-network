<?php

/**
 * El timeline, la página de un usuario (home.php?user=ID) y la de un hashtag
 * (home.php?tag=nombre) (HomeController).
 *
 * Todos los archivos .php de esta carpeta son iguales: cargan el Bootstrap y lo arrancan.
 * El Bootstrap usa el nombre del archivo para elegir el controlador:
 * home.php -> App/Controller/HomeController.php
 *
 * Más detalles en docs/2-arquitectura.md
 */
include 'App/Bootstrap/Bootstrap.php';

\App\Bootstrap\Bootstrap::start();
