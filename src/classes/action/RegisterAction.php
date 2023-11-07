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
            $date = $_POST['date'];
            try {
                \touiteur\app\auth\Auth::register($pwd,$email,$pseudo,$firstname,$lastname,$date);
                $pageContent.= "<h4> inscription réussie pour {$_POST['email']}</h4>";

            } catch(\touiteur\app\Exception\AuthException $e) {
                $pageContent .= "<h4> échec inscription : {$e->getMessage()}</h4>";
            }


        } else {
            $pageContent = '
            <form method="POST" action="?action=register">
                <h1>Créer votre compte</h1>
                <input type="text" id="username" name="username" placeholder="Pseudo" >
                <label for="username">Pseudo : </label>
                <input type="text" id="lastname" name="lastname" placeholder="Nom" >
                <label for="lastname">Nom : </label>
                <input type="text" id="firstname" name="firstname" placeholder="Prenom" >
                <label for="firstname">Prenom :  </label>
                <input type="email" id="email" name="email" placeholder="Email" >
                <label for="email">Email : </label>
                <input type="text" id="pwd" name="pwd" placeholder="Mot de passe" >
                <label for="pwd">Mot de passe : </label>
                <input type="date" id="date" name="date"placeholder="Date de naissance" >
                <label for="date">Date de naissance : </label><input type="submit" value="Inscription">
                <a href="?action=signin">Déjà enregistré ? Connectez-vous</a>
            </form>';
        }
        return $pageContent;
    }

}