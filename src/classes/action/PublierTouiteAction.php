<?php

namespace touiteur\app\action;
use PDO;
use touiteur\app\auth\Auth;

class PublierTouiteAction extends Action {

    public function execute(): string {

        // if the user is not sign in, He can't publish a touite, so we redirect him
        if(!Auth::checkSignIn()) {
            header("Location: index.php?action=signin");
            exit();
        }

        $pageContent = "";
        if ($this->http_method === 'POST') {
            $touite = filter_var($_POST['twt'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

            if(strlen($touite)>325){
                // the size is over 325 char
                $pageContent = '
                    <form method="POST" enctype="multipart/form-data">
                        <p>Message supérieur à 325 caractères veuillez racourcir le message</p>
                        <input type="text" id="twt" name="twt" placeholder="Message">
                        <label for="twt">Message</label>
                        <input type="file" id="file" name="file" accept="images/*" />
                        <input type="submit" value="publier" onsubmit="this.disabled = true; return true;">
                    </form>';


            }else{
                $idI = null;
                //upload the images if the file is there;
                if($_FILES['file']['size'] > 0){
                    $idI=$this->uploadImage();
                }

                // add the touite to the database
                $id = $this->insertIntoDB($touite, $idI);

                // check for tags
                $this->traiterTags($touite, $id);

                header("Location: index.php?action=home");
                exit();
            }
        } else {
            $pageContent = '
            <form method="POST" action="?action=publie" enctype="multipart/form-data" onsubmit="button.disabled = true; return true;">
                <input type="text" id="twt" name="twt" placeholder="Message" required>
                <label for="twt">Message</label>
                <input type="file" id="file" name="file" accept="images/*" />
                <input type="submit" value="Publier" name="button">
            </form>';
        }
        return $pageContent;
    }

    /**
     * Function used to insert a Touite in the database
     * @param string $touite
     * @return int $id the id of the touite in the database
     */
    private function insertIntoDB(string $touite, ?int $idI): int {
        $email = unserialize($_SESSION['users'])->__get('email');
        $date = date("Y-m-d H:i:s");

        // we insert the touite into the database
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="INSERT INTO touite(texte, dateTouite, idImage, email) VALUES (?, ?, ?, ?)";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$touite,$date, $idI, $email]);

        // we get the id of the touite to insert in the table touiteToTag
        $query ="SELECT idTouite FROM touite ORDER BY idTouite DESC";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute();
        // we get the id
        $id = ($resultset->fetch(PDO::FETCH_ASSOC))['idTouite'];

        return $id;
    }

    /**
     * Function used to upload an images and insert the data in the database
     * @return void
     */
    private function uploadImage(): int {
        $idI=-2;
        // we check if the images is the right type
        $extensions=array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp');
        if(in_array($_FILES['file']['type'], $extensions)){
            // we upload the file
            $upload_dir ='images/upload/';
            $tmp = $_FILES['file']['tmp_name'];
            $dest="";
            if (($_FILES['file']['error'] === UPLOAD_ERR_OK)) {
                $dest = $upload_dir.$_FILES['file']['name'];
                move_uploaded_file($tmp, $dest);
            }

            //we do the right insert into the database
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
            $query ="INSERT INTO image(urlImage) VALUES (?)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$dest]);

            // we get the id of the Image to insert in the table touite
            $query ="SELECT idImage FROM image ORDER BY idImage DESC";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute();
            // we get the id
            $idI = ($resultset->fetch(PDO::FETCH_ASSOC))['idImage'];
        }
        return $idI;
    }

    /**
     * Function used to find tags like #test and insert the correct data in the database
     * @param string $touite the touite to check
     * @return void
     */
    private function traiterTags(string $touite, int $id): void {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $matches = [];
        preg_match_all('/#(\w+)/', $touite, $matches);

        foreach ($matches[0] as $match) {
            $match = str_replace('#', '', $match);
            //we verify if the tag is not already in the database
            $query ="SELECT * FROM tag WHERE libelle LIKE ?";
            $resultset = $connexion->prepare(($query));
            $resultset ->execute([$match]);


            if(!$resultset->fetch(PDO::FETCH_ASSOC)){
                //we do the right insert into the database
                $query ="INSERT INTO tag(libelle) VALUES (?)";
                $resultset = $connexion->prepare(($query));
                $res = $resultset ->execute([$match]);
            }

            // we get the id of the Tag to insert in the table touiteToTag
            $query ="SELECT idTag FROM tag WHERE libelle LIKE ?";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$match]);
            // we get the id
            $idT = ($resultset->fetch(PDO::FETCH_ASSOC))['idTag'];

            // we insert into the database
            $query ="INSERT INTO touiteToTag(idTouite, idTag) VALUES (?, ?)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$id,$idT]);
        }

    }
}