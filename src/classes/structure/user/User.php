<?php

namespace touiteur\app\structure\user;

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
    public function getEmail(): string {
        return $this->email;
    }

    public function getFollowedUsers(): array {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query = "SELECT emailSuivi FROM Follow WHERE emailSuiveur = ?";
        $st = $connexion->prepare($query);
        $st->execute([$this->email]);
        $res = $st->fetchAll(\PDO::FETCH_ASSOC);
        $st->closeCursor();

        $followedUsers = [];
        foreach ($res as $row) {
            $followedUsers[] = $row['emailSuivi'];
        }
        return $followedUsers;
    }
}