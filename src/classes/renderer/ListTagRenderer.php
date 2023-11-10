<?php

namespace touiteur\app\renderer;

use touiteur\app\structure\lists\ListTag;

class ListTagRenderer implements Renderer {
    private ListTag $list;

    public function __construct(ListTag $list) {
        $this->list = $list;
    }

    public function render(int $selector): string {
        $r = '<div class="feed">';
        if ($this->list->nbTag === 0) {
            $r .= '<p>Aucun résultat</p>';
        }
        foreach ($this->list->list as $tag) {
            $tagRenderer = new TagRenderer($tag);
            $r .= $tagRenderer->render(Renderer::COMPACT);
        }
        $r .= '</div>';
        return $r;
    }
}