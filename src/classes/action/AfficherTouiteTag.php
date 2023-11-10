<?php

namespace touiteur\app\action;

use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Feed;

class AfficherTouiteTag extends Action {
    public function execute(): string {
        try {
            $tag = $_GET['tag'];
        } catch (\touiteur\app\exception\InvalidUsernameException $e) {
            return "<h3>Tag inconnu</h3>";
        }
        try {
            $html = '<div class="tag">';
            $html .= '<div id="info"> <h3>#' . $tag . '</h3>';
            $html .= '<div class="infoProfil">';
            $nbFollowers = Feed::getNbFollowersTag($tag);
            if ($nbFollowers == 0) {
                $html .= '<p>Aucun follower</p>';
            } elseif ($nbFollowers <= 1) {
                $html .= '<p>' . $nbFollowers . ' follower</p>';
            } else {
                $html .= '<p>' . $nbFollowers . ' followers</p>';
            }
            $nbTouites = Feed::getNbTouiteTag($tag);
            if ($nbTouites == 0) {
                $html .= '<p>Aucun touite</p>';
            } elseif ($nbTouites == 1) {
                $html .= '<p>' . $nbTouites . ' touite</p>';
            } else {
                $html .= '<p>' . $nbTouites . ' touites</p>';
            }
            $html .= '</div></div>';
            $html .= '<div id="followButton">';
            if(isset($_SESSION['users']) && unserialize($_SESSION['users'])->checkFollowTag($tag)) {
                $html .= '<form method="POST" action="?action=unfollowTag">';
                $html .= '<input type="hidden" name="tag" value="' . $tag . '">';
                $html .= '<input type="submit" value="Unfollow">';
                $html .= '</form></div></div>';
            } else {
                $html .= '<form method="POST" action="?action=followTag">';
                $html .= '<input type="hidden" name="tag" value="' . $tag . '">';
                $html .= '<input type="submit" value="Follow">';
                $html .= '</form></div></div>';
            }

        } catch (\touiteur\app\exception\InvalidPropertyNameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }

        $action = '?action=tag&tag=' . $_GET['tag'];

        $feed = new Feed(Feed::LISTETOUITESTAG, $action, null, $tag, null);
        $feed->getListe($_GET['page'] ?? 1);
        $r = new FeedRenderer($feed);

        $html .= $r->render(Renderer::COMPACT);

        return $html;
    }
}