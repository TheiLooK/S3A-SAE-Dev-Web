<?php

namespace touiteur\app\action;

class RegisterAction extends Action{

    public function execute() : string {
        $pageContent = "";
        if ($this->http_method === 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $pwd = filter_var($_POST['pwd'], FILTER_SANITIZE_STRING);
            $pseudo = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
            $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
            try {
                \touiteur\app\auth\Auth::register($pwd,$email,$pseudo,$firstname,$lastname,"0/0/0");
                $pageContent.= "<h4> Inscription réussie pour {$_POST['email']}</h4>";
                \touiteur\app\auth\Auth::loadProfile($_POST['email']);
                $pageContent .= '<p>Redirection vers la page d\'accueil dans 2 secondes</p>';
                $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 2000);</script>';
            } catch(\touiteur\app\Exception\AuthException $e) {
                $pageContent .= "<h4> échec inscription : {$e->getMessage()}</h4>";
            }


        } else {
            if (isset($_SESSION['users'])) {
                $pageContent .= "<div id='already-connected'><h3>Vous êtes déjà connecté !</h3>";
                $pageContent .= '<a href="?action=disconnect">Se déconnecter</a></div>';
                return $pageContent;
            }
            $pageContent = '
            <form method="POST" action="?action=register">
                <h1>Créer votre compte</h1>
                <input type="text" id="username" name="username" placeholder="Pseudo" required>
                <label for="username">Pseudo</label>
                <input type="text" id="lastname" name="lastname" placeholder="Nom" required>
                <label for="lastname">Nom</label>
                <input type="text" id="firstname" name="firstname" placeholder="Prenom" required>
                <label for="firstname">Prenom</label>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <label for="email">Email</label>
                <input type="password" id="pwd" name="pwd" placeholder="Mot de passe" required>
                <label for="pwd">Mot de passe</label>
                <input type="password" id="pwd" name="pwd" placeholder="Confirmer le mot de passe" required>
                <label for="date">Confirmer le mot de passe</label>
                <input type="submit" value="Valider">
                <a href="?action=signin">Déjà enregistré ? Connectez-vous</a>
            </form>';
        }
        return $pageContent;
    }

}