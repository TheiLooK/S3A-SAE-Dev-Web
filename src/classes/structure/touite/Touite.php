<?php

namespace touiteur\app\structure\touite;

use touiteur\app\Exception\InvalidPropertyNameException;
use PDO;
class Touite {
    private string $message;
    private string $date;
    private string $user;
    private int $id;
    private int $score;
    private ?string $image;

    public function __construct(string $message, string $user, ?string $image, string $date, int $id) {
        $this->message = $message;
        $this->date = $date;
        $this->user = $user;
        $this->score = 0;
        $this->image = $image;
        $this->id = $id;
    }

    public function __get($name): mixed {
        if (!property_exists($this, $name)) {
            throw new InvalidPropertyNameException("Invalid property name : $name");
        }
        return $this->$name;
    }

    public function addScore(int $score): void {
        $this->score += $score;
    }


    /**
     * Method used to create a touite from its id in the database
     * @param int $id the touite's id
     * @return Touite the touite object created
     */
    public static function getTouiteById(int $id) : Touite{
        // we select the info of the touite
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query ="SELECT * from Image i right outer join ImageToTouite i2 on i.idImage = i2.idImage
			                            RIGHT outer join Touite t on i2.idTouite=t.idTouite 
                                        right outer join Touiter t2 on t.idTouite = t2.idTouite 
                                        right outer join Utilisateur u on t2.email = u.email 
                        where t.idTouite = ?";
        $resultset = $connexion->prepare(($query));
        $resultset ->execute([$id]);

        // we fetch the data of the touite
        $data = $resultset->fetch(PDO::FETCH_ASSOC);

        // we create the touite object
        $touite = null;
        if(isset($data['image'])){
            $touite = new Touite($data['texte'],$data['username'],$data['image'], $data['dateTouite'], $data['idTouite']);
        }else{
            $touite = new Touite($data['texte'],$data['username'],null, $data['dateTouite'], $data['idTouite']);
        }

        return $touite;
    }


}