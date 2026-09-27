<?php

namespace App\Module;

use App\Helpers\Hashtag\Hashtag;
use App\Module\Base\Module;

/**
 * Prepara los datos del timeline (App/View/Home.view.php).
 *
 * La base de datos devuelve datos "crudos" (first_name, created_at...). Aquí se calcula
 * lo que la vista necesita mostrar: el nombre completo, las iniciales del avatar,
 * la fecha corta ("5m"), el texto con los hashtags como enlaces, etc.
 */
class HomeModule extends Module {

    public function indexModel($data)
    {
        $data['user'] = $this->withProfile($data['user']);

        if ($data['author']) {
            $data['author'] = $this->withProfile($data['author']);
        }

        // "&$post" (con &) permite modificar cada post dentro del foreach.
        foreach ($data['posts'] as &$post) {
            $post = $this->withProfile($post);
            $post['date'] = $this->timeAgo($post['created_at']);
            $post['content_html'] = Hashtag::toHtml($post['content']);
            // Solo los posts propios muestran el botón "Delete".
            $post['is_mine'] = $post['user_id'] == $data['user']['user_id'];
        }
        // Después de un foreach con & hay que hacer unset, si no $post sigue apuntando al último post.
        unset($post);

        foreach ($data['trends'] as &$trend) {
            $trend['label'] = $trend['posts'] . ($trend['posts'] == 1 ? ' post' : ' posts');
        }
        unset($trend);

        $this->addView('Alert');

        $this->render($data);
    }

    /**
     * Agrega a una fila de usuario o de post: name (nombre completo),
     * initials (las letras del avatar) y color (el color del avatar).
     */
    private function withProfile(array $row): array
    {
        $firstName = (string) $row['first_name'];
        $lastName = (string) $row['last_name'];

        $row['name'] = trim($firstName . ' ' . $lastName);
        $row['initials'] = mb_strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1)) ?: '?';
        // Cada usuario tiene siempre el mismo color: el id elige un tono (0-360) de la rueda de colores.
        // Multiplicar por 137 hace que usuarios con ids seguidos tengan colores bien distintos.
        $row['color'] = 'hsl(' . ($row['user_id'] * 137 % 360) . ', 55%, 55%)';

        return $row;
    }

    /**
     * Fecha corta como en Twitter: "now", "5m", "3h", "Sep 27" o "Sep 27, 2025".
     */
    private function timeAgo(string $utcDate): string
    {
        // Las fechas se guardan en UTC, por eso se agrega " UTC" al convertirlas.
        $timestamp = strtotime($utcDate . ' UTC');
        $seconds = time() - $timestamp;

        if ($seconds < 60) {
            return 'now';
        }

        if ($seconds < 3600) {
            return floor($seconds / 60) . 'm';
        }

        if ($seconds < 86400) {
            return floor($seconds / 3600) . 'h';
        }

        return gmdate('Y', $timestamp) == gmdate('Y') ? gmdate('M j', $timestamp) : gmdate('M j, Y', $timestamp);
    }

}
