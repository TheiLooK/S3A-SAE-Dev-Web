<?php
declare(strict_types=1);
namespace touiteur\app\structure\lists;

use touiteur\app\structure\touite\Touite;

class Tag extends Liste {
    private string $nom;

    public function __construct(String $nom){
        parent::__construct();
        $this->nom  =$nom;
    }

    /**
     * Method to get the touites with this tag from the database and insert them into the list
     * @return void
     */
    public function getListeTouiteTag() : void{
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT * from Touite t inner join TouiteTag t2 on t.idTouite = t2.idTouite 
                                        inner join tag tag on t2.idTag = tag.idTag
                                        where tag.Tag = ?
                           order by dateTouite desc";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$this->nom]);

        while ($data = $resultset->fetch(PDO::FETCH_ASSOC)){
            $this->ajouterTouite(Touite::getTouiteById($data['idTouite']));
        }
    }
}