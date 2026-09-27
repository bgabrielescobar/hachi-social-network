<?php

namespace App\Helpers\Hashtag;

/**
 * Encuentra los #hashtags de un texto.
 *
 * Se usa en dos momentos:
 *   - Al publicar (PostController): extract() saca los hashtags para guardarlos y contar las tendencias.
 *   - Al mostrar (HomeModule): toHtml() convierte cada hashtag en un enlace a su página.
 */
class Hashtag
{
    /**
     * Expresión regular (regex) de un hashtag, por partes:
     *   (?<![\p{L}\p{N}_])   el "#" no puede ir pegado a una letra o número ("a#b" no cuenta)
     *   #                    el símbolo
     *   ([\p{N}_]*\p{L}[\p{L}\p{N}_]*)   el nombre: letras (con acentos), números o "_",
     *                        con al menos una letra ("#1" no es un hashtag)
     *   /u                   modo Unicode, para que "á" o "ñ" cuenten como letras
     */
    const PATTERN = '/(?<![\p{L}\p{N}_])#([\p{N}_]*\p{L}[\p{L}\p{N}_]*)/u';

    /**
     * Largo máximo de un hashtag (la columna tag es varchar(100)).
     */
    const MAX_LENGTH = 100;

    /**
     * Hashtags del texto, en minúsculas, sin el "#" y sin repetir.
     * Ejemplo: "Hola #PHP y #php, #Fútbol" -> ['php', 'fútbol']
     */
    public static function extract(string $text): array
    {
        preg_match_all(self::PATTERN, $text, $matches);

        // $matches[1] tiene lo capturado entre paréntesis en PATTERN: el nombre sin el "#".
        $tags = [];
        foreach ($matches[1] as $tag) {
            if (mb_strlen($tag) <= self::MAX_LENGTH) {
                $tags[] = mb_strtolower($tag);
            }
        }

        return array_values(array_unique($tags));
    }

    /**
     * Escapa el texto para HTML y convierte sus hashtags en enlaces a su página.
     * Ejemplo: "Hola #PHP" -> 'Hola <a href="home.php?tag=php" class="hashtag">#PHP</a>'
     */
    public static function toHtml(string $text): string
    {
        // preg_split corta el texto en cada hashtag. Con PREG_SPLIT_DELIM_CAPTURE el nombre
        // del hashtag también queda en la lista: los elementos pares son texto normal y los
        // impares son hashtags. "Hola #PHP!" -> ['Hola ', 'PHP', '!']
        $parts = preg_split(self::PATTERN, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $html = '';
        foreach ($parts as $index => $part) {
            // Todo lo que escribió el usuario pasa por htmlspecialchars (protección contra XSS).
            if ($index % 2 == 0 || mb_strlen($part) > self::MAX_LENGTH) {
                $html .= htmlspecialchars(($index % 2 == 0 ? '' : '#') . $part);
            } else {
                $html .= '<a href="home.php?tag=' . urlencode(mb_strtolower($part)) . '" class="hashtag">#' . htmlspecialchars($part) . '</a>';
            }
        }

        return $html;
    }
}
