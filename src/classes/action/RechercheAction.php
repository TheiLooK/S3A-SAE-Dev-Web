<?php

namespace touiteur\app\action;

use touiteur\app\action\Action;
use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\ListTagRenderer;
use touiteur\app\renderer\ListUserRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Feed;
use touiteur\app\structure\lists\ListTag;
use touiteur\app\structure\lists\ListUser;

class RechercheAction extends Action {

    public function execute(): string {
        if (isset($_POST['search'])) {
            $search = filter_var($_POST['search'], FILTER_SANITIZE_STRING);
            $value = $search;
        } elseif (isset($_GET['search'])) {
            $search = filter_var($_GET['search'], FILTER_SANITIZE_STRING);
            $value = $search;
        } else {
            $search = null;
            $value = '';
        }

        $html = '<h1>Recherche</h1>';
        $html .= '<p>Recherchez un utilisateur avec @username ou un tag avec #tag ou un touite avec un mot</p>';
        $html .= '<form method="POST" action="?action=recherche" class="recherche">';
        $html .= '<div>';
        $html .= '<input type="text" id="recherche" name="search" placeholder="Recherche" value="' . $value . '">';
        $html .= '<label for="recherche">Recherche</label>';
        $html .= '</div>';
        $html .= '<input type="image" src="./images/site/loupe.png" class="icon" alt="Logo loupe" title="Rechercher">';
        $html .= '</form>';

        if ($search !== null) {
            if (preg_match('/\s/', $search)) {
                $html .= "<p>La recherche ne doit pas contenir d'espace</p>";
            } else {
                // vérifier si c'est un tag
                if (preg_match('/^#/', $search)) {
                    $search = substr($search, 1);
                    $html .= "<h2>Résultats de la recherche pour #$search</h2>";
                    $listTag = new ListTag();
                    $listTag->getTagLike($search);
                    $r = new ListTagRenderer($listTag);
                } elseif (preg_match('/^@/', $search)) {
                    $search = substr($search, 1);
                    $html .= "<h2>Résultats de la recherche pour @$search</h2>";
                    $listUser = new ListUser(null);
                    $listUser->getUserLike($search);
                    $r = new ListUserRenderer($listUser);
                } else {
                    $html .= "<h2>Résultats de la recherche pour $search</h2>";
                    $action = '?action=recherche&search=' . $search;
                    $feed = new Feed(Feed::LISTETOUITESLIKE, $action, null, null, $search);
                    $feed->getListe($_GET['page'] ?? 1);
                    $r = new FeedRenderer($feed);
                }
                $html .= $r->render(Renderer::COMPACT);
            }
        }
        return $html;
    }
}