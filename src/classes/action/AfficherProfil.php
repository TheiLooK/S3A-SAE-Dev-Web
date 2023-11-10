<?php

namespace touiteur\app\action;

use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\ListUserRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Feed;
use touiteur\app\structure\lists\ListUser;
use touiteur\app\structure\user\User;

class AfficherProfil extends Action {
    public function execute(): string {
        try {
            $user = User::getUser($_GET['user']);
        } catch (\touiteur\app\exception\InvalidUsernameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }
        $action = '?action=profil&user=' . $_GET['user'];
        try {
            $html = '<div class="profil">';
            $html .= '<div id="info"> <h3>' . $user->__get('prenom'). " " . $user->__get("nom") . '</h3>';
            $html .= '<p>@' . $user->__get('username') . '</p>';
            $html .= '<p>Score moyen : ' . $user->getScoreMoyen() . '</p>';
            $html .= '<div class="infoProfil"><p>Touites : ' . Feed::getNbTouitePersonne($user->email) . '</p>';
            $html .= '<p>Abonnés : ' . $user->getNbFollowers() . '</p>';
            $html .= '<p>Abonnement : ' . $user->getNbFollowing() . '</p></div>';
            $html .= '</div>';
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
                $html .= '</form></div></div>';
            }
            else {
                $content = $_POST['content'] ?? 'feed';
                $html .= '</div><div class=home>';
                if ($content === 'feed') {
                    $html .= '<form method="POST" action="' . $action . '" class="active">';
                } else {
                    $html .= '<form method="POST" action="' . $action . '" class="inactive">';
                }
                $html .= '<input type="hidden" name="content" value="feed">';
                $html .= '<input type="submit" value="Touites ">';
                $html .= '</form>';

                if ($content === 'follower') {
                    $html .= '<form method="POST" action="' . $action . '" class="active">';
                } else {
                    $html .= '<form method="POST" action="' . $action . '" class="inactive">';
                }
                $html .= '<input type="hidden" name="content" value="follower">';
                $html .= '<input type="submit" value="Followers">';
                $html .= '</form>';

                if ($content === 'following') {
                    $html .= '<form method="POST" action="' . $action . '" class="active">';
                } else {
                    $html .= '<form method="POST" action="' . $action . '" class="inactive">';
                }
                $html .= '<input type="hidden" name="content" value="following">';
                $html .= '<input type="submit" value="Following">';
                $html .= '</form></div>';
            }
        } catch (\touiteur\app\exception\InvalidPropertyNameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }

        // récupérer POST content
        $content = $_POST['content'] ?? 'feed';
        switch ($content) {
            case 'follower':
                $listUser = new ListUser($user->email);
                $listUser->getFollower();
                $r = new ListUserRenderer($listUser);
                $html .= $r->render(Renderer::COMPACT);
                break;
            case 'following':
                $listUser = new ListUser($user->email);
                $listUser->getFollowing();
                $r = new ListUserRenderer($listUser);
                $html .= $r->render(Renderer::COMPACT);
                break;
            case 'feed':
                $feed = new Feed(Feed::LISTETOUITESPERSONNE, $action, $_GET['user'], null);
                $feed->getListe($_GET['page'] ?? 1);
                $r = new FeedRenderer($feed);
                $html .= $r->render(Renderer::COMPACT);
                break;
        }

        return $html;
    }
}