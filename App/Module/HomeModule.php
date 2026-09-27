<?php

namespace App\Module;

use App\Module\Base\Module;

class HomeModule extends Module {

    public function indexModel($data)
    {
        $data['user'] = $this->withProfile($data['user']);

        if ($data['author']) {
            $data['author'] = $this->withProfile($data['author']);
        }

        foreach ($data['posts'] as &$post) {
            $post = $this->withProfile($post);
            $post['date'] = $this->timeAgo($post['created_at']);
            $post['is_mine'] = $post['user_id'] == $data['user']['user_id'];
        }
        unset($post);

        $this->addView('Alert');

        $this->render($data);
    }

    /**
     * Adds the display name, initials and avatar color of a user row.
     */
    private function withProfile(array $row): array
    {
        $firstName = (string) $row['first_name'];
        $lastName = (string) $row['last_name'];

        $row['name'] = trim($firstName . ' ' . $lastName);
        $row['initials'] = mb_strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1)) ?: '?';
        $row['color'] = 'hsl(' . ($row['user_id'] * 137 % 360) . ', 55%, 55%)';

        return $row;
    }

    /**
     * Short Twitter-like date: "now", "5m", "3h", "Sep 27" or "Sep 27, 2025".
     */
    private function timeAgo(string $utcDate): string
    {
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
