<?php

namespace touiteur\app\action;

use touiteur\app\auth\Auth;
use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Feed;

class Home extends Action {

    public function execute(): string {
        if (!Auth::checkSignIn()) {
            $feed = $_GET['home'] ?? $_POST['home'] ?? 'general';
            if ($feed === 'personnel') {
                return '<script type="text/javascript">window.location.replace("?action=signin");</script>';
            }
        } else {
            $current_user = unserialize($_SESSION['users']);
            $feed = $_GET['home'] ?? $_POST['home'] ?? 'general';
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
        $action = '?action=home&home=' . $feed;
        if ($feed === 'general') {
            $feed = new Feed(Feed::LISTETOUITES, $action, null, null, null);
            $feed->getListe( $_GET['page'] ?? 1);
            $r = new FeedRenderer($feed);
            $html .= $r->render(Renderer::COMPACT);
        } else if ($feed === 'personnel') {
            $feed = new Feed(Feed::LISTETOUITESFOLLOWED, $action, $current_user->__get('email'), null, null);
            $feed->getListe( $_GET['page'] ?? 1);
            $r = new FeedRenderer($feed);
            $html .= $r->render(Renderer::COMPACT);;
        }
        return $html;
    }
}