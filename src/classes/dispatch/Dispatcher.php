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
            case 'affiche':
                $result = (new \touiteur\app\action\AfficherListeTouite())->execute();
                break;
            case 'displayTouite':
                $result = (new \touiteur\app\action\AfficherTouite())->execute();
                break;
            case 'follow':
                $result = (new \touiteur\app\action\FollowUser())->execute();
                break;
            case 'unfollow':
                $result = (new \touiteur\app\action\UnfollowUser())->execute();
                break;
            case 'disconnect':
                $result = (new \touiteur\app\action\DisconnectAction())->execute();
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
        <link rel="stylesheet" href="./css/style.css">
        <title>Touiteur</title>
    </head>
    <body>
    <div class="partiegauche">
        <nav>
            <h1>Touitteur</h1>
            <div>
                <a href="./">
                    <div class="bouton">
                        <img src="./images/site/home.png">
                        <span>Accueil</span>
                    </div>
                </a>
                
                <a href="?action=signin">
                    <div class="bouton">
                        <img src="./images/site/connexion.png">
                        <span>Connexion</span>
                    </div>
                </a>
                
                <a href="?action=register">
                    <div class="bouton">
                        <img src="./images/site/inscription.png">
                        <span>Inscription</span>
                    </div>
                </a>
                
                <a href="?action=affiche">
                    <div class="bouton">
                        <img src="./images/site/loupe.png">
                        <span>Afficher</span>
                    </div>
                </a>
                
                <a href="?action=publie">
                    <div class="publier">
                        <span>Publier</span>
                    </div>
                </a>
            </div>
        </nav>
    </div>
    <div class="partiecentrale">
        $html
    </div>
    <div class="partiedroite"></div>
    </body>
    </html>
    END;
    }
}