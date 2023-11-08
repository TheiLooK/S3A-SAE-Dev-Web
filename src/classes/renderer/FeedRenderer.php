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
            $r .= '<a href="?page=1">Premier</a>';
            if ($this->feed->__get('home') !== null) {
                $r .= '<a href="?home=' . $this->feed->__get('home') . '&page=' . ($page - 1) . '">Précédent</a>';
            } else {
                $r .= '<a href="?page=' . ($page - 1) . '">Précédent</a>';
            }
        }
        $r .= '<span>Page ' . $page . ' sur ' . $nbPageMax . '</span>';
        if ($page < $nbPageMax) {
           if ($this->feed->__get('home') !== null) {
                $r .= '<a href="?home=' . $this->feed->__get('home') . '&page=' . ($page + 1) . '">Suivant</a>';
            } else {
                $r .= '<a href="?page=' . ($page + 1) . '">Suivant</a>';
            }
            $r .= '<a href="?page=' . ceil($this->feed->__get('nbTouiteMax') / Feed::NBPARPAGEFEED) . '">Dernier</a>';
        }
        $r .= '</div>';

        return $r;
    }
}