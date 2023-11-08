<?php
declare(strict_types=1);
namespace touiteur\app\structure\lists;

use touiteur\app\Exception\InvalidPropertyNameException;
use touiteur\app\structure\touite\Touite;

class Liste{
    protected iterable $list;
    protected int $touitesCount;

    public function __construct(){
        $this->list = [];
        $this->touitesCount=0;
    }

    /**
     * Method used to add a touite to the list
     * @param Touite $touite
     * @return void
     */
    public function ajouterTouite(Touite $touite):void{
        $this->list[] = $touite;
        $this->touitesCount++;
    }

    /**
     * Method used to remove a touite to the list
     * @param int $indice the index of the touite in the list
     * @return void
     */
    public function suprimerTouite(int $indice):void{
        unset($this->list[$indice]);
        $this->touitesCount--;
    }

    /**
     * Method used to get an argument
     * @param String $arg the name of the argument
     * @return mixed the argument if it exists
     * @throws InvalidPropertyNameException
     */
    public function __get(String $arg):mixed{
        if(property_exists($this, $arg)) return $this->$arg;
        throw new InvalidPropertyNameException("$arg: invalid property");
    }
}