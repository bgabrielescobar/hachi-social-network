<?php

namespace App\Module\Base;

use App\Config\Settings;

/**
 * Clase base de los módulos (App/Module).
 *
 * El módulo recibe los datos del controlador, los prepara para mostrarlos
 * y arma la página con las vistas (App/View), en este orden:
 *   1. Head.view.php       el <head> con el CSS y el JS
 *   2. las que agregue el módulo con addView(), por ejemplo Alert.view.php
 *   3. la vista de la página, con el mismo nombre del controlador (Home.view.php)
 *   4. Footer.view.php     cierra el HTML
 */
abstract class Module {


    const PATH_VIEWS = '/App/View/{view-template}.view.php';

    private $viewList = [
        '/App/View/Head.view.php'
    ];

    protected $data;

    /**
     * Muestra la página con los datos preparados.
     */
    public function render($data)
    {
        $this->data = $data;
        $this->addRequestModule();
        $this->addView('Footer');
        $this->addResources();
        $this->renderViews($this->data);
    }

    /**
     * Agrega la vista de la página actual: "Home" -> App/View/Home.view.php
     */
    protected function addRequestModule()
    {
        $this->addView(Settings::get('controller'));
    }

    /**
     * Agrega una vista a la lista: "Alert" -> App/View/Alert.view.php
     */
    protected function addView(string $view) : void
    {
        $pathView = str_replace('{view-template}', $view, Module::PATH_VIEWS);
        $this->viewList[] = $pathView;
    }

    /**
     * Las vistas son archivos de HTML con partes de PHP. Al hacer el require dentro
     * de este método, las vistas pueden usar la variable $data.
     */
    private function renderViews($data)
    {
        foreach ($this->viewList as $view)
        {
            $viewPath = dirname(__DIR__, 3 ). $view;
            if (file_exists($viewPath)) {
                require_once $viewPath;
            }
        }
    }


    /**
     * Llena $data['css'] y $data['js'] con los archivos que Head.view.php pone en el <head>:
     * primero los externos y los compartidos (master), después los de la página.
     */
    private function addResources(): void
    {
        $root = dirname(__DIR__, 3) . '/';

        foreach (Settings::get('external-css') as $cssPath) {
            $this->data['css'][] = $cssPath;
        }

        $this->data['css'][] = Settings::get('master-css');
        $this->data['js'][] = Settings::get('master-js');

        // El CSS y el JS de la página son opcionales: solo se agregan si el archivo existe.
        $cssPath = str_replace('{cssView}', Settings::get('controller'), Settings::get('css-min-path'));
        if (file_exists($root . $cssPath)) {
            $this->data['css'][] = $cssPath;
        }

        $jsPath = str_replace('{jsView}', Settings::get('controller'), Settings::get('js-min-path'));
        if (file_exists($root . $jsPath)) {
            $this->data['js'][] = $jsPath;
        }
    }

 }
