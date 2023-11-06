<?php
declare(strict_types=1);
namespace touiteur\app\structure\lists;

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
    public function getListeTouiteTag(){
        //TODO
    }
}