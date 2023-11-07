<?php

namespace touiteur\app\action;

use touiteur\app\action\Action;

class FollowUser extends Action {

    public function execute(): string {
        $userToFollow = $_GET['user'];
        $followed = $this->checkFollow($userToFollow);

        $current_user = unserialize($_SESSION['user']);

        if($followed) {
            $html = '<script type="text/javascript">if(window.confirm("Voulez-vous vraiment arrêter de suivre cet utilisateur ?")){window.location.replace("?action=unfollow&user=' . $userToFollow . '");}</script>';
        } else {
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
            $query = "INSERT INTO Follow (emailSuiveur, emailSuivi) VALUES (?, ?)";
            $st = $connexion->prepare($query);
            $st->execute([$current_user->getEmail(), $userToFollow]);
            $st->closeCursor();
            $html = '<script type="text/javascript">window.alert("Vous suivez maintenant cet utilisateur");</script>';
        }

        $html .= '<a href="?action=display-user&user=' . $userToFollow . '">Retour à la page de l\'utilisateur</a>';
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