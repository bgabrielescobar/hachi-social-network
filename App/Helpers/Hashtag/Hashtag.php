<?php

namespace App\Helpers\Hashtag;

class Hashtag
{
    /**
     * A "#" at the start of a word followed by letters, numbers or "_",
     * with at least one letter (so "#1" is not a hashtag).
     */
    const PATTERN = '/(?<![\p{L}\p{N}_])#([\p{N}_]*\p{L}[\p{L}\p{N}_]*)/u';

    const MAX_LENGTH = 100;

    /**
     * Hashtags of the text, lowercase and without the "#".
     */
    public static function extract(string $text): array
    {
        preg_match_all(self::PATTERN, $text, $matches);

        $tags = [];
        foreach ($matches[1] as $tag) {
            if (mb_strlen($tag) <= self::MAX_LENGTH) {
                $tags[] = mb_strtolower($tag);
            }
        }

        return array_values(array_unique($tags));
    }

    /**
     * Escapes the text for HTML and turns its hashtags into links to their timeline.
     */
    public static function toHtml(string $text): string
    {
        // With PREG_SPLIT_DELIM_CAPTURE the even items are plain text and the odd ones hashtags.
        $parts = preg_split(self::PATTERN, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        $html = '';
        foreach ($parts as $index => $part) {
            if ($index % 2 == 0 || mb_strlen($part) > self::MAX_LENGTH) {
                $html .= htmlspecialchars(($index % 2 == 0 ? '' : '#') . $part);
            } else {
                $html .= '<a href="home.php?tag=' . urlencode(mb_strtolower($part)) . '" class="hashtag">#' . htmlspecialchars($part) . '</a>';
            }
        }

        return $html;
    }
}
