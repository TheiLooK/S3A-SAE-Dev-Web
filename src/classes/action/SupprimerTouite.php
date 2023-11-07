<?php

namespace touiteur\app\action;

use touiteur\app\db\ConnectionFactory;
use touiteur\app\renderer\Renderer;

class SupprimerTouite extends Action
{
    public function execute(): string
    {
        $id = $_POST['id'];
        $db = ConnectionFactory::makeConnection();


        $tabDelete = [];
        $tabDelete[] = "DELETE FROM `Touiter` WHERE idTouite = ?";
        $tabDelete[] = "DELETE FROM `ImageToTouite` WHERE idTouite = ?";
        $tabDelete[] = "DELETE FROM `TouiteTag` WHERE idTouite = ?";
        $tabDelete[] = "DELETE FROM `Touite` WHERE idTouite = ?";

        foreach ($tabDelete as $query) {
            $resultset = $db->prepare(($query));
            $res = $resultset ->execute([$id]);
        }

        $pageContent="le touite a bien été supprimer";
        $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 1000);</script>';
        return $pageContent;
    }
}