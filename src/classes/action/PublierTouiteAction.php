<?php

namespace touiteur\app\action;

class PublierTouiteAction extends Action{

    public function execute() : string {
        $pageContent = "";
        if ($this->http_method === 'POST') {
            $touite = filter_var($_POST['twt'], FILTER_SANITIZE_STRING);
            //$email = serialize($_SESSION['users'])->getEmail();
            $date = date("Y-m-d H:i:s");

            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
            $query ="insert into Touite(texte, dateTouite) values (?, ?)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$touite,$date]);

        } else {
            $pageContent = '
            <form method="POST" action="?action=publie">
                <label for="twt">Message : </label>
                <input type="text" id="twt" name="twt" placeholder="message"><br><br>
                <input type="submit" value="publier">
            </form>';
        }
        return $pageContent;
    }

}