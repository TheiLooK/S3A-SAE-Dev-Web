<?php

namespace touiteur\app\action;

use touiteur\app\action\Action;
use touiteur\app\Auth\Auth;

class FollowTag extends Action {

    public function execute(): string {
        if (!Auth::checkSignIn()) {
            return '<script type="text/javascript">window.location.replace("?action=signin");</script>';
        }
        try {
            $current_user = unserialize($_SESSION['users']);
            $tagToFollow = $_POST['tag'];
            $followed = $current_user->checkFollowTag($tagToFollow);
        } catch (\touiteur\app\Exception\InvalidUsernameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }

        if ($followed) {
            $html = '<script type="text/javascript">if(window.confirm("Voulez-vous vraiment arrêter de suivre ce tag ?")){window.location.replace("?action=unfollow&tag=' . $tagToFollow . '");}</script>';
        } else {
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
            $query = "SELECT idTag FROM tag WHERE libelle = ?";
            $st = $connexion->prepare($query);
            $st->execute([$tagToFollow]);
            $res = $st->fetchAll(\PDO::FETCH_ASSOC);
            $st->closeCursor();
            $idTagToFollow = $res[0]['idTag'];

            $query = "INSERT INTO followTag (emailSuiveur, idTag) VALUES (?, ?)";
            $st = $connexion->prepare($query);
            $st->execute([$current_user->__get("email"), $idTagToFollow]);
            $st->closeCursor();
            $html = '<script type="text/javascript">window.alert("Vous suivez maintenant ce tag");</script>';
        }
        $html .= '<script type="text/javascript">window.location.replace("?action=displayTouiteTag&tag=' . $tagToFollow . '");</script>';
        return $html;
    }
}