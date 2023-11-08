<?php

namespace touiteur\app\renderer;

use touiteur\app\structure\lists\Feed;
use touiteur\app\structure\lists\Liste;

class FeedRenderer implements Renderer {
    private Liste $feed;

    public function __construct($feed) {
        $this->feed = $feed;
    }

    public function render(int $selector): string {
        $page = $_GET['page'] ?? 1;
        $this->feed->getListeTouite();

        $r = '<div class="feed">';
        for ($i = ($page - 1) * Renderer::NBPARPAGEFEED; $i < $page * Renderer::NBPARPAGEFEED && $i < count($this->feed->list); $i++) {
            $touiteRenderer = new TouiteRenderer($this->feed->list[$i]);
            $r .= $touiteRenderer->render($selector);
        }
        $r .= "</div>";
        $r .= '<div class="pagination">';
        $r .= '<a href="?page=1">Premier</a>';
        if ($page > 1) {
            $r .= '<a href="?page=' . ($page - 1) . '">Précédent</a>';
        }
        $r .= '<span>Page ' . $page . ' sur ' . ceil(count($this->feed->list) / Renderer::NBPARPAGEFEED) . '</span>';
        if ($page < ceil(count($this->feed->list) / Renderer::NBPARPAGEFEED)) {
            $r .= '<a href="?page=' . ($page + 1) . '">Suivant</a>';
        }
        $r .= '<a href="?page=' . ceil(count($this->feed->list) / Renderer::NBPARPAGEFEED) . '">Dernier</a>';
        $r .= '</div>';

        return $r;
    }
}