<?php

namespace App\Module;

use App\Module\Base\Module;

/**
 * Prepara la página de login y registro (App/View/Index.view.php).
 */
class IndexModule extends Module{

    public function indexModel($data)
    {
        $data['signin-image'] = 'public/img/signin-image.jpg';
        $data['signup-image'] = 'public/img/signup-image.jpg';

        // La alerta donde el JavaScript muestra los errores, por ejemplo "Wrong email or password".
        $this->addView('Alert');

        $this->render($data);
    }

}
