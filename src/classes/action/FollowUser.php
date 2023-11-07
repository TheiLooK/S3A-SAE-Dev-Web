<?php

namespace touiteur\app\action;

use touiteur\app\action\Action;
use touiteur\app\structure\user\User;

class FollowUser extends Action {

    public function execute(): string {
        try {
            $userToFollow = User::getUser($_POST['user']);
            $followed = $this->checkFollow($userToFollow->__get('email'));
        } catch (\touiteur\app\Exception\InvalidUsernameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }

        $current_user = unserialize($_SESSION['user']);

        if($followed) {
            $html = '<script type="text/javascript">if(window.confirm("Voulez-vous vraiment arrêter de suivre cet utilisateur ?")){window.location.replace("?action=unfollow&user=' . $userToFollow->__get('username') . '");}</script>';
        } else {
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
            $query = "INSERT INTO Follow (emailSuiveur, emailSuivi) VALUES (?, ?)";
            $st = $connexion->prepare($query);
            $st->execute([$current_user->__get("email"), $userToFollow->__get("email")]);
            $st->closeCursor();
            $html = '<script type="text/javascript">window.alert("Vous suivez maintenant cet utilisateur");</script>';
        }
        $html .= '<script type="text/javascript">window.location.replace("?action=profil&user=' . $userToFollow->__get('username') . '");</script>';
        return $html;
    }

    private function checkFollow($userToFollow): bool {
        $followed = false;
        $followedUsers = unserialize($_SESSION['user'])->getFollowedUsers();
        foreach($followedUsers as $user) {
            if($user == $userToFollow) {
                $followed = true;
            }
        }
        return $followed;
    }
}