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
            case 'publie':
                $result = (new \touiteur\app\action\PublierTouiteAction())->execute();
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
    </head>
    <body>
    <header>
        <nav>
            <div> 
                <h1>touite</h1>
            </div>
            <ul>
                <li><a href="?action=signin">Connexion</a></li>
                <li><a href="?action=register">Inscription</a></li>
                <li><a href="?action=publie">publier</a></li>
            </ul>
        </nav>
    </header>
    <div></div>
        <div>
            $html
        </div>

    </body>
    </html>
    END;
    }


}