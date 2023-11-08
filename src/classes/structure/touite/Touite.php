<?php

namespace touiteur\app\structure\touite;

use touiteur\app\db\ConnectionFactory;
use touiteur\app\Exception\InvalidPropertyNameException;
use PDO;
class Touite {
    private string $message;
    private string $date;
    private string $user;
    private int $id;
    private int $scoreDown;
    private int $scoreUp;
    private ?string $image;

    public function __construct(string $message, string $user, ?string $image, string $date, int $id) {
        $this->message = $message;
        $this->date = $date;
        $this->user = $user;
        $this->image = $image;
        $this->id = $id;
        $this->scoreUp = 0;
        $this->scoreDown = 0;
        $this->getScore();
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

    public function prepareHtml() : string {
        $res = $this->message;
        $tag = [];
        preg_match_all('/#(\w+)/', $res, $tag);

        foreach($tag[0] as $t) {
            $string = '<a class="lien" href="?action=displayTouiteTag&tag='.str_replace('#', '', $t).'">'.$t."</a>";
            $res = str_replace($t, $string, $res);
        }
        return $res;
    }

    private function getScore() : void{



        $connexion = ConnectionFactory::makeConnection();
        $query = "select
                    sum(case when note = 1 then 1 else 0 end) as up,
                    sum(case when note = -1 then 1 else 0 end) as down
                    from Evaluer
                    where idTouite = ?";
        $resultset = $connexion->prepare(($query));
        $resultset ->execute([$this->id]);
        $data  = $resultset->fetch();

        if(is_null($data['up'])){
            $this->scoreUp=0;
            $this->scoreDown=0;
        }else{
            $this->scoreUp=$data['up'];
            $this->scoreDown=$data['down'];
        }
    }
}