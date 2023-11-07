<?php

namespace touiteur\app\action;

use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Tag;
use touiteur\app\structure\user\User;

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
            if($this->checkFollow()) {
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


        $tag = new Tag($_GET['tag']);
        $tag->getListeTouiteTag();
        $r = new FeedRenderer($tag);

        $html .= $r->render(Renderer::COMPACT);

        return $html;

    }

    private function checkFollow() {
        if(!isset($_SESSION['users'])) {
            return false;
        }
        $followed = false;
        $followedTags = unserialize($_SESSION['users'])->getFollowedTags();
        foreach($followedTags as $tag) {
            if($tag == $_GET['tag']) {
                $followed = true;
            }
        }
        return $followed;
    }
}