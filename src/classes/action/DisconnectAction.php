<?php

namespace touiteur\app\action;

use touiteur\app\action\Action;

class DisconnectAction extends Action {

    public function execute(): string {
        session_destroy();
        $html = '<div><h3>Vous êtes déconnecté !</h3>';
        $html .= '<a href="?action=display-home">Retour à l\'accueil</a></div>';
        return $html;
    }

}