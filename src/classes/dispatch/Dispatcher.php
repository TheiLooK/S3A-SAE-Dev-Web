<?php
declare(strict_types=1);
namespace touiteur\app\dispatch;

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
            case 'profil':
                $result = (new \touiteur\app\action\AfficherProfil())->execute();
                break;
            case 'displayTouiteTag':
                $result = (new \touiteur\app\action\AfficherTouiteTag())->execute();
                break;
            case 'followTag':
                $result = (new \touiteur\app\action\FollowTag())->execute();
                break;
            case 'unfollowTag':
                $result = (new \touiteur\app\action\UnfollowTag())->execute();
                break;
            case 'supprimerTouite':
                $result = (new \touiteur\app\action\SupprimerTouite())->execute();
                break;
            default :
                $result = (new \touiteur\app\action\Home())->execute();
                break;
        }
        Dispatcher::renderPage($result);
    }

    private static function renderPage(string $html): void {
        $pagecontent = <<<END
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <link rel="stylesheet" href="./css/style.css">
                <meta http-equiv="refresh" content="60">
                <title>Touiteur</title>
            </head>
            <body>
            <div class="partiegauche">
                <nav>
                    <h1>Touiteur</h1>
                    <div>
                        <a href="./">
                            <div class="bouton">
                                <img src="./images/site/home.png">
                                <span>Accueil</span>
                            </div>
                        </a>
                        <a href="?action=affiche">
                            <div class="bouton">
                                <img src="./images/site/loupe.png">
                                <span>Afficher</span>
                            </div>
                        </a>
        END;
        if (isset($_SESSION['users'])) {
            $user = unserialize($_SESSION['users'])->username;
            $pagecontent .= <<<END
                <a href="?action=profil&user=$user">
                    <div class="bouton">
                        <img src="./images/site/connexion.png">
                        <span>Votre Profil</span>
                    </div>
                </a>
                <a href="?action=disconnect">
                    <div class="bouton">
                        <img src="./images/site/deconnexion.png">
                        <span>Déconnexion</span>
                    </div>
                </a>
            END;
        }
        else {
            $pagecontent .= <<<END
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
            END;
        }
        $pagecontent .= <<<END
                    <a href="?action=publie">
                        <div class="publier">
                            <span>Publier</span>
                            <img src="./images/site/publier.png">
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
        echo $pagecontent;
    }
}