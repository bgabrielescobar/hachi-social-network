<?php

namespace App\Module;

use App\Module\Base\Module;

class IndexModule extends Module{

    public function indexModel($data)
    {
        $data['signin-image'] = 'public/img/signin-image.jpg';
        $data['signup-image'] = 'public/img/signup-image.jpg';

        $data['strong-alert'] = '';
        $data['message-alert']  = '';

        $this->addView('Alert');

        $this->render($data);
    }

}