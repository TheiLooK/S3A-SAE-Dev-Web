<?php

namespace touiteur\app\renderer;

use touiteur\app\structure\lists\Feed;

class FeedRenderer implements Renderer {
    private Feed $feed;

    public function __construct($feed) {
        $this->feed = $feed;
    }

    public function render(int $selector): string {
        $page = $_GET['page'] ?? 1;
        $nbPageMax = ceil($this->feed->__get('nbTouiteMax') / Feed::NBPARPAGEFEED);

        $r = '<div class="feed">';
        foreach ($this->feed->list as $touite) {
            $touiteRenderer = new TouiteRenderer($touite);
            $r .= $touiteRenderer->render(Renderer::COMPACT);
        }
        $r .= '</div>';

        $r .= '<div class="pagination">';
        if ($page > 1) {
            $r .= '<a href="' . $this->feed->__get('action') . '&page=1' . '">Premier</a>';
            $r .= '<a href="' . $this->feed->__get('action') . '&page=' . ($page - 1) . '">Précédent</a>';
        }
        $r .= '<span>Page ' . $page . ' sur ' . $nbPageMax . '</span>';
        if ($page < $nbPageMax) {
            $r .= '<a href="' . $this->feed->__get('action') . '&page=' . ($page + 1) . '">Suivant</a>';
            $r .= '<a href="' . $this->feed->__get('action') . '&page=' . ceil($this->feed->__get('nbTouiteMax') / Feed::NBPARPAGEFEED) . '">Dernier</a>';
        }
        $r .= '</div>';

        return $r;
    }
}