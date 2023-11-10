<?php

namespace touiteur\app\structure\lists;

use touiteur\app\db\ConnectionFactory;
use touiteur\app\structure\user\User;

class ListUser{

    private array $list;
    private string $user;
    private int $nbUser;

    public function __construct(string $user){
        $this->user=$user;
        $this->list=[];
        $this->nbUser=0;
    }

    public function getFollower() : void{
        $connexion = ConnectionFactory::makeConnection();
        $query = "select * from users u inner join follow f on u.email = f.emailSuiveur where f.emailSuivi like ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$this->user]);
        while($data = $resultset->fetch()){
            $u = new User($data['email'], $data['password'], $data['username'], $data['lastname'], $data['firstname'], $data['role']);
            $this->list[] = $u;
            $this->nbUser++;
        }
    }

    public function getFollowing() : void{
        $connexion = ConnectionFactory::makeConnection();
        $query = "select * from users u inner join follow f on u.email = f.emailSuivi where f.emailSuiveur like ?";
        $resultset = $connexion->prepare(($query));
        $res = $resultset ->execute([$this->user]);
        while($data = $resultset->fetch()){
            $u = new User($data['email'], $data['password'], $data['username'], $data['lastname'], $data['firstname'], $data['role']);
            $this->list[] = $u;
            $this->nbUser++;
        }
    }

    public function __get($name): mixed {
        if (!property_exists($this, $name)) {
            throw new \touiteur\app\Exception\InvalidPropertyNameException("Property $name does not exist");
        }
        return $this->$name;
    }
}