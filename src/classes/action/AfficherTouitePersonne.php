<?php

namespace touiteur\app\action;

use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\renderer\TouiteRenderer;
use touiteur\app\structure\lists\Feed;
use touiteur\app\structure\touite\Touite;

class AfficherTouitePersonne extends Action
{

    public function execute(): string
    {
        $feed = new Feed();
        $feed->getListeTouitePersonne($_GET['user']);
        $r = new FeedRenderer($feed);

        return $r->render(Renderer::COMPACT);

    }

}