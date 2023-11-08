<?php

namespace touiteur\app\action;

use touiteur\app\action\Action;
use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Feed;
use touiteur\app\structure\user\User;

class Home extends Action {

    public function execute(): string {
        if (!isset($_SESSION['users'])) {
            $feed = $_POST['home'] ?? 'general';
            if ($feed === 'personnel') {
                return '<script type="text/javascript">window.location.replace("?action=signin");</script>';
            }
        } else {
            $current_user = unserialize($_SESSION['users']);
            $feed = $_POST['home'] ?? 'personnel';
        }

        $html = '<div class="home">';
        if ($feed === 'general') {
            $html .= '<form method="POST" action="./" class="active">';
        } else {
            $html .= '<form method="POST" action="./" class="inactive">';
        }
        $html .= '<input type="hidden" name="home" value="general">';
        $html .= '<input type="submit" value="Général">';
        $html .= '</form>';
        if ($feed === 'personnel') {
            $html .= '<form method="POST" action="./" class="active">';
        } else {
            $html .= '<form method="POST" action="./" class="inactive">';
        }
        $html .= '<input type="hidden" name="home" value="personnel">';
        $html .= '<input type="submit" value="Pour vous">';
        $html .= '</form>';
        $html .= '</div>';
        if ($feed === 'general') {
            $feed = new Feed();
            $feed->getListeTouite();
            $r = new FeedRenderer($feed);
            $html .= $r->render(Renderer::COMPACT);
        } else if ($feed === 'personnel') {
            $feed = new Feed();
            $feed->getListeTouiteFollowed($current_user->__get('email'));
            $r = new FeedRenderer($feed);
            $html .= $r->render(Renderer::COMPACT);;
        }
        return $html;
    }
}