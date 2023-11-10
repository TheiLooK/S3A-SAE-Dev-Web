<?php

namespace touiteur\app\structure\lists;

use touiteur\app\db\ConnectionFactory;

class ListTag {

        private array $list;
        private int $nbTag;

        public function __construct(){
            $this->list=[];
            $this->nbTag=0;
        }

        public function getTagLike(string $name): void {
            $connexion = ConnectionFactory::makeConnection();
            $query = "SELECT * FROM tag WHERE libelle LIKE ?";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$name]);
            while($data = $resultset->fetch()){
                $this->list[] = $data['libelle'];
                $this->nbTag++;
            }
        }

        public function __get($name): mixed {
            if (!property_exists($this, $name)) {
                throw new \touiteur\app\exception\InvalidPropertyNameException("Property $name does not exist");
            }
            return $this->$name;
        }
}