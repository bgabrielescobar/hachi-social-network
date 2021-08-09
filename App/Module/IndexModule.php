<?php

namespace App\Module;

use App\Module\Base\Module;

class IndexModule extends Module{

    public function indexModel($data)
    {
        $this->data['signin-image'] = 'public/img/signin-image.jpg';
        $this->data['signup-image'] = 'public/img/signup-image.jpg';

        $this->data['strong-alert'] = 'Nice!';
        $this->data['message-alert'] = 'Register nice';

        $this->addView('Alert');
        $this->addView('Index');

        $this->render($data);
    }

}