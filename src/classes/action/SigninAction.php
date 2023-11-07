<?php

namespace touiteur\app\action;

class SigninAction extends Action {
    public function execute() : string {
        $pageContent ="";
        if($this->http_method === 'GET') {
            if (isset($_SESSION['users'])) {
                $pageContent .= "<div id='already-connected'><h3>Vous êtes déjà connecté !</h3>";
                $pageContent .= '<a class="lien" href="?action=disconnect">Se déconnecter</a></div>';
                return $pageContent;
            }
            $pageContent = '
            <form method="POST" action="?action=signin">
                <input type ="text" id="email" placeholder="Email" name ="email" required>
                <label for="email">Email</label>               
                <input type ="password" id="pass" placeholder="Mot de passe" name ="pass" required>
                <label for="pass">Mot de passe</label>
                <input type="submit" value="Valider">
                <a href="?action=register">Pas encore enregistré ? Créer un compte</a>
            </form>';
        } else {
            try {
                \touiteur\app\auth\Auth::authentification($_POST['pass'],$_POST['email']);
                $pageContent.= "<div><h4> Connexion réussie pour {$_POST['email']}</h4>";
                \touiteur\app\auth\Auth::loadProfile($_POST['email']);
                $authenticatedUser = unserialize($_SESSION['users']);
                //page d'accueil
                $pageContent .= '<p>Redirection vers la page d\'accueil dans 2 secondes</p></div>';
                $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 2000);</script>';
            } catch(\touiteur\app\Exception\AuthException $e) {
                $pageContent .= "<h4> échec authentification : {$e->getMessage()}</h4>";
            }

        }
        return $pageContent;
    }
}