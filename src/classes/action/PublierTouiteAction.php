<?php

namespace touiteur\app\action;
use PDO;
class PublierTouiteAction extends Action{

    public function execute() : string {

        // if the user is not sign in, He can't publish a touite, so we redirect him
        if(!isset($_SESSION['users'])) {
            header("Location: index.php?action=signin");
            exit();
        }

        $pageContent = "";
        if ($this->http_method === 'POST') {
            $touite = filter_var($_POST['twt'], FILTER_SANITIZE_STRING);

            if(strlen($touite)>325){
                // the size is over 325 char
                $pageContent = '
                    <form method="POST" action="?action=publie" enctype="multipart/form-data">
                        <p>Message supérieur à 325 caractères veuillez racourcir le message</p>
                        <label for="twt">Message : </label>
                        <input type="text" id="twt" name="twt" placeholder="message">
                        <label for="twt">Image : </label>
                        <input type="file" id="file" name="file" accept="images/*" />
                        <input type="submit" value="publier">
                    </form>';


            }else{
                // add the touite to the database
                $id = $this->insertIntoDB($touite);

                // check for tags
                $this->treatTags($touite, $id);

                //upload the images if the file is there;
                if(isset($_FILES['file'])){
                    $this->treatImage($id);
                }
            }
        } else {
            $pageContent = '
            <form method="POST" action="?action=publie" enctype="multipart/form-data">
                <label for="twt">Message : </label>
                <input type="text" id="twt" name="twt" placeholder="message">
                <label for="twt">Image : </label>
                <input type="file" id="file" name="file" accept="images/*" />
                <input type="submit" value="publier">
            </form>';
        }
        return $pageContent;
    }

    /**
     * Function used to insert a Touite in the database
     * @param string $touite
     * @return int $id the id of the touite in the database
     */
    private function insertIntoDB(string $touite) : int{
        $email = unserialize($_SESSION['users'])->getEmail();
        $date = date("Y-m-d H:i:s");

        // we insert the touite into the database
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="insert into Touite(texte, dateTouite) values (?, ?)";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$touite,$date]);

        // we get the id of the touite to insert in the table Touiter
        $query ="select idTouite from Touite order by idTouite desc";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute();
        // we get the id
        $id = ($resultset->fetch(PDO::FETCH_ASSOC))['idTouite'];

        // we insert into the database
        $query ="insert into Touiter(idTouite, email) values (?, ?)";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$id,$email]);
        return $id;
    }

    /**
     * Function used to upload an images and insert the data in the database
     * @return void
     */
    private function treatImage(int $id) : void{
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
            $query ="insert into Image(Image) values (?)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$dest]);

            // we get the id of the Image to insert in the table ImageToTouite
            $query ="select idImage from Image order by idImage desc";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute();
            // we get the id
            $idI = ($resultset->fetch(PDO::FETCH_ASSOC))['idImage'];

            // we insert into the database
            $query ="insert into ImageToTouite(idTouite, idImage) values (?, ?)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$id,$idI]);
        }
    }

    /**
     * Function used to find tags like #test and insert the correct data in the database
     * @param string $touite the touite to check
     * @return void
     */
    private function treatTags(string $touite, int $id){
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $matches = [];
        preg_match_all('/#(\w+)/', $touite, $matches);

        foreach ($matches[0] as $match) {
            $match = str_replace('#', '', $match);
            //we verify if the tag is not already in the database
            $query ="select * from Tag where tag like ?";
            $resultset = $connexion->prepare(($query));
            $resultset ->execute([$match]);


            if(!$resultset->fetch(PDO::FETCH_ASSOC)){
                //we do the right insert into the database
                $query ="insert into Tag(tag) values (?)";
                $resultset = $connexion->prepare(($query));
                $res = $resultset ->execute([$match]);
            }

            // we get the id of the Tag to insert in the table TouiteTag
            $query ="select idTag from Tag where tag like ?";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$match]);
            // we get the id
            $idT = ($resultset->fetch(PDO::FETCH_ASSOC))['idTag'];

            // we insert into the database
            $query ="insert into TouiteTag(idTouite, idTag) values (?, ?)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$id,$idT]);
        }

    }
}