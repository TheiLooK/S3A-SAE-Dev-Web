<?php

namespace touiteur\app\info\user;

class User {
    public String $email;
    private String $password;
    private String $pseudo;
    public int $role;


    public function __construct(String $e,String $p, String $pseudo, int $r) {
        $this -> email = $e;
        $this -> password = $p;
        $this -> pseudo = $pseudo;
        $this -> role = $r;
    }

    /**
     * @return String
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}