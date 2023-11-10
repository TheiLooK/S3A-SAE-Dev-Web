<?php

namespace touiteur\app\action;

use touiteur\app\action\Action;
use touiteur\app\auth\Auth;

class DisconnectAction extends Action {

    public function execute(): string {
        $html='<script type="text/javascript">window.location.replace("?action=home");</script>';;
        if (Auth::checkSignIn()){
            session_destroy();
            $html = '<div><h3>Vous êtes déconnecté !</h3>';
            $html .= '<p>Redirection vers la page précedente dans 2 secondes</p></div>';
            $html .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("' . $_SERVER['HTTP_REFERER'] . '");}, 2000);</script>';
        }
        return $html;
    }

}