<?php

namespace touiteur\app\action;

use touiteur\app\db\ConnectionFactory;
use touiteur\app\renderer\Renderer;

class SupprimerTouite extends Action
{
    public function execute(): string
    {
        $pageContent="";
        $id = $_POST['id'];
        if(!isset($_GET['delete'])){
            $pageContent=<<<HTML
                     <form method="POST">
                        <p>Voulez-vous vraiment supprimer votre touite ?</p>
                        <input type="hidden" name="id" value=$id>
                        <input type="submit" value="Oui" formaction="?action=supprimerTouite&delete=true">
                        <input type="submit" value="Non" formaction="?action=affiche">
                    </form>
                HTML;
        }else{
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
            $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("./");}, 1500);</script>';
        }

        return $pageContent;
    }
}