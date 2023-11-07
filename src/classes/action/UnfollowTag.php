<?php

namespace touiteur\app\action;

use touiteur\app\action\Action;

class UnfollowTag extends Action {

    public function execute(): string {
        if(!isset($_SESSION['users'])) {
            return '<script type="text/javascript">window.location.replace("?action=signin");</script>';
        }
        try {
            $tagToFollow = $_POST['tag'];
            $followed = $this->checkFollow($tagToFollow);
        } catch (\touiteur\app\Exception\InvalidTagNameException $e) {
            return "<h3>Tag inconnu</h3>";
        }

        $current_user = unserialize($_SESSION['users']);

        if($followed) {
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();

            $query = "SELECT idTag FROM Tag WHERE tag = ?";
            $st = $connexion->prepare($query);
            $st->execute([$tagToFollow]);
            $res = $st->fetchAll(\PDO::FETCH_ASSOC);
            $st->closeCursor();
            $idTagToUnfollow = $res[0]['idTag'];

            $query = "DELETE FROM FollowTag WHERE emailSuiveur = ? AND idTag = ?";
            $st = $connexion->prepare($query);
            $st->execute([$current_user->__get("email"), $idTagToUnfollow]);
            $st->closeCursor();
            $html = '<script type="text/javascript">window.alert("Vous ne suivez plus ce tag");</script>';
        } else {
            $html = '<script type="text/javascript">window.alert("Vous ne suivez pas ce tag");</script>';
        }
        $html .= '<script type="text/javascript">window.location.replace("?action=displayTouiteTag&tag=' . $tagToFollow . '");</script>';
        return $html;
    }

    private function checkFollow($tagToFollow): bool {
        $followed = false;
        $followedTags = unserialize($_SESSION['users'])->getFollowedTags();
        foreach($followedTags as $tag) {
            if($tag == $tagToFollow) {
                $followed = true;
            }
        }
        return $followed;
    }
}