<?php

/**
 * Cierra la sesión y vuelve a index.php (LogoutController).
 *
 * Todos los archivos .php de esta carpeta son iguales: cargan el Bootstrap y lo arrancan.
 * El Bootstrap usa el nombre del archivo para elegir el controlador:
 * logout.php -> App/Controller/LogoutController.php
 *
 * Más detalles en docs/2-arquitectura.md
 */
include 'App/Bootstrap/Bootstrap.php';

\App\Bootstrap\Bootstrap::start();
