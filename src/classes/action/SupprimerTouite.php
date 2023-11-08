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
                        <p>Voulez vous vraiment supprimer votre touite ?</p>
                        <input type="hidden" name="id" value=$id>
                        <input type="submit" value="oui" formaction="?action=supprimerTouite&delete=true">
                        <input type="submit" value="non" formaction="?action=affiche">
                    </form>
                HTML;
        }else{
            $db = ConnectionFactory::makeConnection();

            $tabDelete = [];
            $tabDelete[] = "DELETE FROM `Touiter` WHERE idTouite = ?";
            $tabDelete[] = "DELETE FROM `ImageToTouite` WHERE idTouite = ?";
            $tabDelete[] = "DELETE FROM `TouiteTag` WHERE idTouite = ?";
            $tabDelete[] = "DELETE FROM `Evaluer` WHERE idTouite = ?";
            $tabDelete[] = "DELETE FROM `Touite` WHERE idTouite = ?";

            foreach ($tabDelete as $query) {
                $resultset = $db->prepare(($query));
                $res = $resultset ->execute([$id]);
            }
            $pageContent="le touite a bien été supprimé, retour a la page";
            $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=affiche");}, 1500);</script>';
        }

        return $pageContent;
    }
}