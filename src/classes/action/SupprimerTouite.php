<?php

namespace touiteur\app\action;

use touiteur\app\Auth\Auth;
use touiteur\app\db\ConnectionFactory;
use touiteur\app\renderer\Renderer;

class SupprimerTouite extends Action
{
    public function execute(): string
    {
        $pageContent="";
        // if emailTouite is not set then its an access via modifing the url
        if(!isset($_POST['emailTouite'])){
            $pageContent = "<h1>Access Interdit via modification Url</h1>";
            $pageContent .= '<p>Redirection vers la page d\'accueil dans 2 secondes</p></div>';
            $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 2000);</script>';
            return $pageContent;
        }


        $email = $_POST['emailTouite'];
        //we check if the user is the owner of the touite or an admin
        if(!Auth::checkOwnership($email)){
            $pageContent = "<h1>Droits insuffisants</h1>";
            $pageContent .= '<p>Redirection vers la page d\'accueil dans 2 secondes</p></div>';
            $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 2000);</script>';
            return $pageContent;
        }


        $id = $_POST['idTouite'];
        if(!isset($_GET['delete'])){
            $mail = $_POST['emailTouite'];
            $pageContent=<<<HTML
                     <form method="POST">
                        <p>Voulez-vous vraiment supprimer votre touite ?</p>
                        <input type="hidden" name="idTouite" value=$id>
                        <input type="hidden" name="emailTouite" value=$mail>;
                        <input type="submit" value="Oui" formaction="?action=supprimerTouite&delete=true">
                        <input type="submit" value="Non" formaction="?action=affiche">
                    </form>
                HTML;
        } else {
            $db = ConnectionFactory::makeConnection();

            // récupérer idImage de la table touite
            $query = "SELECT i.idImage, i.urlImage FROM touite t inner join image i on t.idImage = i.idImage WHERE t.idTouite = ?";
            $resultset = $db->prepare(($query));
            $res = $resultset ->execute([$id]);

            if($data = $resultset->fetch()){
                $idImage = $data['idImage'];
                $urlImage = $data['urlImage'];

                // supprimer l'image
                $query = "DELETE FROM image WHERE idImage = ?";
                $resultset = $db->prepare(($query));
                $res = $resultset ->execute([$idImage]);

                // supprimer le fichier de l'image du serveur dans le dossier image
                $upload_dir ='images/upload/';
                unlink($upload_dir.$urlImage);
            }

            $tabDelete = [];
            $tabDelete[] = "DELETE FROM notation WHERE idTouite = ?";
            $tabDelete[] = "DELETE FROM touiteToTag WHERE idTouite = ?";
            $tabDelete[] = "DELETE FROM touite WHERE idTouite = ?";

            foreach ($tabDelete as $query) {
                $resultset = $db->prepare(($query));
                $res = $resultset ->execute([$id]);
            }
            $pageContent="le touite a bien été supprimé, retour à la page";
            $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 1500);</script>';
        }

        return $pageContent;
    }
}