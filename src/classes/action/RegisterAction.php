<?php

namespace touiteur\app\action;

class registerAction extends Action{

    public function execute() : string {
        $pageContent = "";
        if ($this->http_method === 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $pwd = filter_var($_POST['pwd'], FILTER_SANITIZE_STRING);
            $pseudo = filter_var($_POST['pseudo'], FILTER_SANITIZE_STRING);

            try {
                \touiteur\app\auth\Auth::register($email,$pwd,$pseudo);
                $pageContent.= "<h4> inscription réussie pour {$_POST['email']}</h4>";

            } catch(\touiteur\app\Exception\AuthException $e) {
                $pageContent .= "<h4> échec inscription : {$e->getMessage()}</h4>";
            }


        } else {
            $pageContent = '
            <form method="POST" action="?action=register">
                <label for="pseudo">Pseudo : </label>
                <input type="text" id="pseudo" name="pseudo" ><br><br>
                <label for="email">Email : </label>
                <input type="email" id="email" name="email" ><br><br>
                <label for="pwd">Mot de passe : </label>
                <input type="text" id="pwd" name="pwd" ><br><br>
                <input type="submit" value="Inscription">
            </form>';
        }
        return $pageContent;
    }

}