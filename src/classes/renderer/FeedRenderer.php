<?php

namespace touiteur\app\renderer;

use touiteur\app\structure\lists\Feed;

class FeedRenderer implements Renderer {
    private Feed $feed;

    public function __construct($feed) {
        $this->feed = $feed;
    }

    public function render(int $selector): string {
        $r = '<div class="feed">';
        foreach ($this->feed->list as $touite) {
            $touiteRenderer = new TouiteRenderer($touite);
            $r .= $touiteRenderer->render(Renderer::COMPACT);
        }
        $r .= "</div>";
        return $r;
    }
}