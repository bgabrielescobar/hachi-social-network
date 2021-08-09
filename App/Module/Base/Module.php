<?php

namespace App\Module\Base;

use App\Config\Settings;

abstract class Module {

    protected $data; 

    const EXTENSION_VIEW = '.view.php';

    public function render($data)
    {
        array_push($this->data, $data);
        $this->addResources();
        $this->renderViews($this->data);
    }
    
    protected function addView(string $view) : void
    {
        $pathView = str_replace('{view-template}', $view, Settings::getSetting('path-view'));
        Settings::putSetting('bases-view', $pathView);
    }

    private function renderViews($data)
    {
        foreach (Settings::getSetting('bases-view') as $view) 
        {
            if (file_exists($view)) {
                include $view;
            }
        }
    }


    private function addResources(): void
    {

        $this->data['css'][] = str_replace(
            '{cssView}',
             Settings::getSetting('controller'), 
             Settings::getSetting('css-min-path')
            );

        $this->data['css'] = array_merge($this->data['css'], Settings::getSetting('external-css'));

        $this->data['js'][] = str_replace(
            '{jsView}',
             Settings::getSetting('controller'), 
             Settings::getSetting('js-min-path')
            );
    }

 }