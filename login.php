<?php

/**
 * Endpoint JSON que inicia sesión (LoginController).
 * Lo llama public/js-min/Index.min.js cuando se envía el formulario de login.
 *
 * Todos los archivos .php de esta carpeta son iguales: cargan el Bootstrap y lo arrancan.
 * El Bootstrap usa el nombre del archivo para elegir el controlador:
 * login.php -> App/Controller/LoginController.php
 *
 * Más detalles en docs/2-arquitectura.md
 */
include 'App/Bootstrap/Bootstrap.php';

\App\Bootstrap\Bootstrap::start();
