<?php

namespace touiteur\app\action;

use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\renderer\TouiteRenderer;
use touiteur\app\structure\lists\Feed;
use touiteur\app\structure\touite\Touite;
use touiteur\app\structure\user\User;

class AfficherProfil extends Action
{

    public function execute(): string {
        try {
            $user = User::getUser($_GET['user']);
        } catch (\touiteur\app\Exception\InvalidUsernameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }
        try {
            $html = '<div class="profil">';
            $html .= '<div id="info"> <h3>' . $user->__get('prenom') . $user->__get("nom") . '</h3>';
            $html .= '<p>@' . $user->__get('username') . '</p></div>';
            // si l'utilisateur est connecté et que ce n'est pas son profil
            if (isset($_SESSION['users']) && unserialize($_SESSION['users'])->__get('username') != $user->__get('username')) {
                $html .= '<div id="followButton">';
                if (unserialize($_SESSION['users'])->checkFollow($user)) {
                    $html .= '<form method="POST" action="?action=unfollow">';
                    $html .= '<input type="hidden" name="user" value="' . $user->__get("username") . '">';
                    $html .= '<input type="submit" value="Unfollow">';
                } else {
                    $html .= '<form method="POST" action="?action=follow">';
                    $html .= '<input type="hidden" name="user" value="' . $user->__get("username") . '">';
                    $html .= '<input type="submit" value="Follow">';
                }
                $html .= '</form></div>';
            }
            $html .= '</div>';
        } catch (\touiteur\app\Exception\InvalidPropertyNameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }

        $feed = new Feed();
        $feed->getListeTouitePersonne($_GET['user']);
        $r = new FeedRenderer($feed);

        $html .= $r->render(Renderer::COMPACT);
        return $html;
    }
}