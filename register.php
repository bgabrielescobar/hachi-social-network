<?php

/**
 * Endpoint JSON que crea una cuenta nueva (RegisterController).
 * Lo llama public/js-min/Index.min.js cuando se envía el formulario de registro.
 *
 * Todos los archivos .php de esta carpeta son iguales: cargan el Bootstrap y lo arrancan.
 * El Bootstrap usa el nombre del archivo para elegir el controlador:
 * register.php -> App/Controller/RegisterController.php
 *
 * Más detalles en docs/2-arquitectura.md
 */
include 'App/Bootstrap/Bootstrap.php';

\App\Bootstrap\Bootstrap::start();
