<?php
declare(strict_types=1);
namespace touiteur\app\structure\lists;

class feed extends Liste {


    public function __construct(){
        parent::__construct();
    }

    /**
     * Method to get the touites from the database and insert them into the list
     * @return void
     */
    public function getListeTouite(){
        //todo
    }
}