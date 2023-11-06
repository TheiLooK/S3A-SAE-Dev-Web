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


            //check the size of msg
            //TODO


            // add the touite to the database
            $this->insertIntoDB($touite);

            //upload the image if the file is there;
            if(isset($_FILES['file'])){
                $this->treatImage();
            }
        } else {
            $pageContent = '
            <form method="POST" action="?action=publie" enctype="multipart/form-data">
                <label for="twt">Message : </label>
                <input type="text" id="twt" name="twt" placeholder="message"><br><br>
                <label for="twt">Image : </label>
                <input type="file" id="file" name="file" accept="image/*" /><br><br>
                <input type="submit" value="publier">
            </form>';
        }
        return $pageContent;
    }

    private function insertIntoDB(string $touite) : void{
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
    }

    private function treatImage() : void{
        // we check if the image is the right type

        $extensions=array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp');
        if(in_array($_FILES['file']['type'], $extensions)){
            // we upload the file
            $upload_dir ='image/';
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

            // we get the id of the touite to insert in the table ImageToTouite
            $query ="select idTouite from Touite order by idTouite desc";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute();
            // we get the id
            $idT = ($resultset->fetch(PDO::FETCH_ASSOC))['idTouite'];

            // we insert into the database
            $query ="insert into ImageToTouite(idTouite, idImage) values (?, ?)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$idT,$idI]);
        }
    }

    private function checkForTags(string $touite){

    }
}