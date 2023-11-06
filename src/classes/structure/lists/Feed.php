<?php
declare(strict_types=1);
namespace touiteur\app\structure\lists;
use PDO;
use touiteur\app\structure\touite\Touite;

class Feed extends Liste {

    public function __construct(){
        parent::__construct();
    }

    /**
     * Method to get the touites from the database and insert them into the list
     * @return void
     */
    public function getListeTouite() : void{
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT * from Touite t inner join Touiter t2 on t.idTouite = t2.idTouite 
                                        inner join Utilisateur u on t2.email = u.email";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute();

        while ($data = $resultset->fetch(PDO::FETCH_ASSOC)){
            // we get the image if there is one
            $query ="SELECT image from ImageToTouite i inner join Image i2 on i.idImage = i2.idImage where i.idTouite = ?";
            $resultsetImage = $connexion->prepare(($query));
            $resultsetImage ->execute([$data['idTouite']]);
            $imgTab = $resultsetImage->fetchall(PDO::FETCH_ASSOC);

            // we create the touite object
            $touite = null;
            if(sizeof($imgTab)>0){
                $touite = new Touite($data['texte'],$data['username'],$imgTab[0]['image'], $data['dateTouite']);
            }else{
                $touite = new Touite($data['texte'],$data['username'],null, $data['dateTouite']);
            }
            $this->ajouterTouite($touite);
        }
    }
}