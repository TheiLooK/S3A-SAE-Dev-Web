<?php

namespace touiteur\app\action;

use touiteur\app\renderer\Renderer;
use touiteur\app\renderer\TouiteRenderer;
use touiteur\app\structure\touite\Touite;

class AfficherTouite extends Action {
    public function execute(): string {
        $touite = Touite::getTouiteById($_GET['id']);
        $r = new TouiteRenderer($touite);
        return $r->render(Renderer::LONG);
    }
}