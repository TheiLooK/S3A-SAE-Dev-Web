<?php

namespace touiteur\app\action;

use touiteur\app\structure\user\User;

class UnfollowUser extends Action {

    public function execute(): string {
        if(!isset($_SESSION['users'])) {
            return '<script type="text/javascript">window.location.replace("?action=signin");</script>';
        }
        try {
            $userToUnfollow = User::getUser($_POST['user']);
            $followed = $this->checkFollow($userToUnfollow->__get('email'));
        } catch (\touiteur\app\Exception\InvalidUsernameException $e) {
            return "<h3>Utilisateur inconnu</h3>";
        }

        $current_user = unserialize($_SESSION['users']);

        if($followed) {
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
            $query = "DELETE FROM Follow WHERE emailSuiveur = ? AND emailSuivi = ?";
            $st = $connexion->prepare($query);
            $st->execute([$current_user->__get("email"), $userToUnfollow->__get("email")]);
            $st->closeCursor();
            $html = '<script type="text/javascript">window.alert("Vous ne suivez plus cet utilisateur");</script>';
        } else {
            $html = '<script type="text/javascript">window.alert("Vous ne suivez pas cet utilisateur");</script>';
        }

        $html .= '<script type="text/javascript">window.location.replace("?action=profil&user=' . $userToUnfollow->__get('username') . '");</script>';
        return $html;
    }

    private function checkFollow($userToFollow): bool {
        $followed = false;
        $followedUsers = unserialize($_SESSION['users'])->getFollowedUsers();
        foreach($followedUsers as $user) {
            if($user == $userToFollow) {
                $followed = true;
            }
        }
        return $followed;
    }
}