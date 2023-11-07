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
                                        inner join Utilisateur u on t2.email = u.email 
                           order by dateTouite desc";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute();

        while ($data = $resultset->fetch(PDO::FETCH_ASSOC)){

            $this->ajouterTouite(Touite::getTouiteById($data['idTouite']));
        }
    }

    public function getListeTouitePersonne(string $user) : void{
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT * from Touite t inner join Touiter t2 on t.idTouite = t2.idTouite 
                                        inner join Utilisateur u on t2.email = u.email
                                        where u.username = ?
                           order by dateTouite desc";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$user]);

        while ($data = $resultset->fetch(PDO::FETCH_ASSOC)){

            $this->ajouterTouite(Touite::getTouiteById($data['idTouite']));
        }
    }

}