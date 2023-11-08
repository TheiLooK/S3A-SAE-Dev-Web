<?php

namespace touiteur\app\action;

use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Feed;

class Admin extends Action
{

    public function execute(): string
    {
        if (!isset($_SESSION['users'])) {
            $feed = $_GET['admin'] ?? $_POST['admin'] ?? 'tendances';
            if ($feed === 'influenceurs') {
                return '<script type="text/javascript">window.location.replace("?action=signin");</script>';
            }
        } else {
            $current_user = unserialize($_SESSION['users']);
            $feed = $_GET['admin'] ?? $_POST['admin'] ?? 'influenceurs';
        }

        $html = '<div class="home">';
        if ($feed === 'tendances') {
            $html .= '<form method="POST" action="./" class="active">';
        } else {
            $html .= '<form method="POST" action="./" class="inactive">';
        }
        $html .= '<input type="hidden" name="home" value="tendances">';
        $html .= '<input type="submit" value="tendances">';
        $html .= '</form>';
        if ($feed === 'influenceurs') {
            $html .= '<form method="POST" action="./" class="active">';
        } else {
            $html .= '<form method="POST" action="./" class="inactive">';
        }
        $html .= '<input type="hidden" name="home" value="influenceurs">';
        $html .= '<input type="submit" value="influenceurs">';
        $html .= '</form>';
        $html .= '</div>';
        $action = '?action=admin&admin=' . $feed;
        if ($feed === 'tendances') {
            // a faire
        } else if ($feed === 'influenceurs') {
            // a faire
        }
        return $html;
    }
}