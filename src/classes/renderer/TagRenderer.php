<?php

namespace touiteur\app\renderer;

class TagRenderer implements Renderer {
    private string $tag;

    public function __construct($tag) {
        $this->tag = $tag;
    }

    public function render(int $selector): string {
        $res = '<h4><a href="?action=displayTouiteTag&tag=' . $this->tag . '">#' . $this->tag . '</a></h4>';
        return $res;
    }
}