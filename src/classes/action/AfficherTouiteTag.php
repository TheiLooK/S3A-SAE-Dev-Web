<?php

namespace touiteur\app\action;

use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Feed;

class AfficherTouiteTag extends Action
{
    public function execute(): string
    {
        try {
            $tag = $_GET['tag'];
        } catch (\touiteur\app\Exception\InvalidUsernameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }
        try {
            $html = '<div class="tag">';
            $html .= '<div id="info"> <h3>#' . $tag . '</h3></div>';
            $html .= '<div id="followButton">';
            if(unserialize($_SESSION['users'])->checkFollowTag($tag)) {
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

        } catch (\touiteur\app\Exception\InvalidPropertyNameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }


        $feed = new Feed(Feed::LISTETOUITESTAG, null, $tag);
        $feed->getListe($_GET['page'] ?? 1);
        $r = new FeedRenderer($feed);

        $html .= $r->render(Renderer::COMPACT);

        return $html;

    }
}