<?php

namespace App\Module\Base;

use App\Config\Settings;

abstract class Module {


    const PATH_VIEWS = '/App/View/{view-template}.view.php';

    private $viewList = [
        '/App/View/Head.view.php'
    ];

    protected $data;

    public function render($data)
    {
        $this->data = $data;
        $this->addRequestModule();
        $this->addResources();
        $this->renderViews($this->data);
    }

    protected function addRequestModule()
    {
        $this->addView(Settings::get('controller'));
    }
    
    protected function addView(string $view) : void
    {
        $pathView = str_replace('{view-template}', $view, Module::PATH_VIEWS);
        $this->viewList[] = $pathView;
    }

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


    private function addResources(): void
    {

        $this->data['css'][] = str_replace(
            '{cssView}',
             Settings::get('controller'),
             Settings::get('css-min-path')
            );

        $this->data['css'][] = Settings::get('master-css');

        $this->data['js'][] = str_replace(
            '{jsView}',
             Settings::get('controller'),
             Settings::get('js-min-path')
            );

        $this->data['js'][] = Settings::get('master-js');
    }

 }