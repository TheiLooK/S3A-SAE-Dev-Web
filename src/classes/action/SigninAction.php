<?php

namespace touiteur\app\action;

class SigninAction extends Action {
    public function execute() : string {
        $pageContent ="";
        if($this->http_method === 'GET') {
            $pageContent = '
            <form method="POST" action="?action=signin">
                <label for="email">Email :</label>
                <input type ="text" id="email" placeholder="Email" name ="email"><br><br>
                <label for="pass">Mot de passe :</label>
                <input type ="text" id="pass" placeholder="Mot de passe" name ="pass"><br><br>
                <input type="submit" value="Valider">
            </form>';
        } else {
            try {

                \touiteur\app\auth\Auth::authentification($_POST['pass'],$_POST['email']);
                $pageContent.= "<h4> connexion réussie pour {$_POST['email']}</h4>";
                \touiteur\app\auth\Auth::loadProfile($_POST['email']);
                $authenticatedUser = unserialize($_SESSION['users']);
            } catch(\touiteur\app\Exception\AuthException $e) {
                $pageContent .= "<h4> échec authentification : {$e->getMessage()}</h4>";
            }

        }
        return $pageContent;
    }
}