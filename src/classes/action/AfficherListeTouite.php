<?php

namespace touiteur\app\action;
use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Feed;

class AfficherListeTouite extends Action{

    public function execute() : string {

        $feed = new Feed();
        $feed->getListeTouite();
        $r = new FeedRenderer($feed);

        return $r->render(Renderer::COMPACT);
    }

}