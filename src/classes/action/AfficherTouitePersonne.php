<?php

namespace touiteur\app\action;

use touiteur\app\renderer\Renderer;
use touiteur\app\renderer\TouiteRenderer;
use touiteur\app\structure\touite\Touite;

class AfficherTouitePersonne extends Action
{

    public function execute(): string
    {
        $touite = Touite::getTouiteByNom($_GET['Nom']);
        $r = new TouiteRenderer($touite);
        return $r->render(Renderer::LONG);

    }

}