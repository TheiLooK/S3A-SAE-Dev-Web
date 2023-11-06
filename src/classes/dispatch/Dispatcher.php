<?php
declare(strict_types=1);
namespace touiteur\app\dispatch;

use touiteur\app\action\Action;
class Dispatcher {


    public static function run(): void {
        $action = "";
        if(isset($_GET['action'])) $action = $_GET['action'];
        switch ($action) {
            case 'signin':
                $result = (new \touiteur\app\action\SigninAction())->execute();
                break;
            case 'register':
                $result = (new \touiteur\app\action\RegisterAction())->execute();
                break;
            default :
                $result = "Accueil ";
        }
        Dispatcher::renderPage($result);
    }

    private static function renderPage(string $html): void {
        echo <<<END
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <title>Touiteur</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <header>
        <nav>
            <div> 
                <h1>touite</h1>
            </div>
            <ul>
                <li><a href="?action=signin">Connexion</a></li>
                <li><a href="?action=register">Inscription</a></li>
                <li><a href="?action=logout">Déconnexion</a></li>
                <li><a href="?action=profile">Profil</a></li>
            </ul>
        </nav>
    </header>
        <div>
            $html
        </div>
    </body>
    </html>
    END;
    }


}