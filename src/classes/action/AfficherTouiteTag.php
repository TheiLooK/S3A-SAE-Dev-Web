<?php

namespace touiteur\app\action;

use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Tag;

class AfficherTouiteTag extends Action
{
    public function execute(): string
    {
        $tag = new Tag($_GET['tag']);
        $tag->getListeTouiteTag();
        $r = new FeedRenderer($tag);

        return $r->render(Renderer::COMPACT);

    }
}