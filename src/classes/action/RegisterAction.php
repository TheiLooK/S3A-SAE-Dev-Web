<?php

namespace touiteur\app\action;

use touiteur\app\db\ConnectionFactory;

class RegisterAction extends Action{

    public function execute() : string {
        $pageContent = "";
        if ($this->http_method === 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $pwd = filter_var($_POST['pwd'], FILTER_SANITIZE_STRING);
            $pseudo = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
            $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);

            $validate = $this->validateInscription($email, $pseudo);

            //switch to do action according the valdation
            switch ($validate){
                case 0:
                    try {
                        \touiteur\app\auth\Auth::register($pwd,$email,$pseudo,$firstname,$lastname,);
                        $pageContent.= "<div><h4> Inscription réussie pour {$_POST['email']}</h4>";
                        \touiteur\app\auth\Auth::loadProfile($_POST['email']);
                        $authenticatedUser = unserialize($_SESSION['users']);
                        //page d'accueil
                        $pageContent .= '<p>Redirection vers la page d\'accueil dans 2 secondes</p></div>';
                        $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 2000);</script>';
                    } catch(\touiteur\app\Exception\AuthException $e) {
                        $pageContent .= "<h4> échec inscription : {$e->getMessage()}</h4>";
                    }
                    break;
                case 1:
                    $pageContent.= "<div><h4> échec inscription : le pseudo est déjà utilisé</h4></div>";
                    $pageContent.= $this->getForm();
                    break;
                case 2:
                    $pageContent.= "<div><h4> échec inscription : l'email est déjà utilisé</h4></div>";
                    $pageContent.= $this->getForm();
                    break;
            }

        } else {
            if (isset($_SESSION['users'])) {
                $pageContent .= "<div id='already-connected'><h3>Vous êtes déjà connecté !</h3>";
                $pageContent .= '<a class="lien" href="?action=disconnect">Se déconnecter</a></div>';
                return $pageContent;
            }
            $pageContent = $this->getForm();
        }
        return $pageContent;
    }


    /**
     * function used to get the form to register
     * @return string the form
     */
    private function getForm() : string{
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
                <a class="lien" href="?action=signin">Déjà inscrit ? Connectez-vous</a>
            </form>';
        return $pageContent;
    }

    /**
     * Methode to know if the username or email is already used
     * @param string $email the email in the form
     * @param string $username the username in the form
     * @return int 0 if its valid, 1 if the username is taken, 2 if the email is taken
     */
    private function validateInscription(string $email, string $username) : int{
        $db = ConnectionFactory::makeConnection();
        $query = "SELECT * FROM users  WHERE email like ? or username like ? ";
        $resultset = $db->prepare(($query));
        $res = $resultset ->execute([$email, $username]);

        if($data = $resultset->fetch()){
            if($data['username'] === $username){
                return 1;
            }else{
                return 2;
            }
        }
        return 0;
    }
}
