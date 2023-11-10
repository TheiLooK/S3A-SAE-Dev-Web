<?php

namespace touiteur\app\action;

use Couchbase\User;
use touiteur\app\auth\Auth;
use touiteur\app\renderer\FeedRenderer;
use touiteur\app\renderer\Renderer;
use touiteur\app\structure\lists\Feed;

class AdminAction extends Action {
    public function execute(): string {
        //if the user in session is not an admin he can't access the page
        if(!Auth::checkAccessLevel(100)){
            $pageContent = "<h1>Droits insuffisants</h1>";
            $pageContent .= '<p>Redirection vers la page d\'accueil dans 2 secondes</p></div>';
            $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 2000);</script>';
            return $pageContent;
        }

        $feed = isset($_GET['admin']) ? $_GET['admin'] : (isset($_POST['admin']) ? $_POST['admin'] : 'tendances');

        $html = '<div class="home">';
        if ($feed === 'tendances') {
            $html .= '<form method="POST"  class="active">';
        } else {
            $html .= '<form method="POST"  class="inactive">';
        }
        $html .= '<input type="hidden" name="admin" value="tendances">';
        $html .= '<input type="submit" value="Tendances">';
        $html .= '</form>';
        if ($feed === 'influenceurs') {
            $html .= '<form method="POST"  class="active">';
        } else {
            $html .= '<form method="POST"  class="inactive">';
        }
        $html .= '<input type="hidden" name="admin" value="influenceurs">';
        $html .= '<input type="submit" value="Influenceurs">';
        $html .= '</form>';
        $html .= '</div>';
        if ($feed === 'tendances') {
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
            $query = "SELECT t2.libelle, COUNT(t.idTag) AS nbTag FROM touiteToTag t INNER JOIN tag t2 ON t.idTag=t2.idTag GROUP BY libelle ORDER BY nbTag desc;";
            $st = $connexion->prepare($query);
            $st->execute();
            $tendances = [];
            while ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
                $tendances[] = $row;
            }
            $html .= '<div class = "feed">';
            foreach ($tendances as $tend) {
                if(isset($tend['libelle'])&&isset($tend['nbTag'])) {
                    $html .= "<div class='Touite'>";
                    $html .= "<p>" . $tend['libelle']  . "</p>";
                    $html .= "<p> Le nombre de fois où le tag apparaît : " .  $tend['nbTag'] . "</p>";
                    $html .= "</div>";
                }
            }
            $html .= '</div>';
        } else if ($feed === 'influenceurs') {
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
            $query = "SELECT u.username,u.email, count(f.emailSuivi) AS nbSuivi FROM follow f INNER JOIN users u ON f.emailSuivi= u.email GROUP BY u.email order by nbSuivi desc";
            $st = $connexion->prepare($query);
            $st->execute();
            $influenceurs=[];
            while ($row = $st->fetch(\PDO::FETCH_ASSOC)) {
                $influenceurs[] = $row;
            }
            $html .= '<div class = "feed">';
            foreach ($influenceurs as $inf) {
                if(isset($inf['email'])&&isset($inf['username'])&&isset($inf['nbSuivi'])) {
                    $html .= "<div class='Touite'>";
                    $html .= "<p>" . $inf['username']  . "</p>";
                    $html .= "<p>" . $inf['email'] . "</p>";
                    $html .= "<p> Le nombre de follower : " .  $inf['nbSuivi'] . "</p>";
                    $html .= "</div>";
                }
            }
            $html .= "</div>";
        }
        return $html;
    }
}