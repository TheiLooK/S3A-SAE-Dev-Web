<?php

namespace touiteur\app\structure\user;

class User {
    public String $email;
    private String $password;
    private String $username;
    private String $nom;
    private String $prenom;
    public int $role;


    public function __construct(String $e,String $p, String $username, String $nom, String $prenom, int $r) {
        $this -> email = $e;
        $this -> password = $p;
        $this -> username = $username;
        $this -> nom = $nom;
        $this -> prenom = $prenom;
        $this -> role = $r;
    }

    public function __get($name): mixed {
        if (!property_exists($this, $name)) {
            throw new \touiteur\app\Exception\InvalidPropertyNameException("Property $name does not exist");
        }
        return $this->$name;
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

    public function getFollowedTags(): array {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query = "SELECT t.tag FROM Tag t INNER JOIN FollowTag f ON t.idTag = f.idTag WHERE f.emailSuiveur = ?";
        $st = $connexion->prepare($query);
        $st->execute([$this->email]);
        $res = $st->fetchAll(\PDO::FETCH_ASSOC);
        $st->closeCursor();

        $followedTags = [];
        foreach ($res as $row) {
            $followedTags[] = $row['tag'];
        }
        return $followedTags;
    }

    public static function getUser($username): User {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query = "SELECT * FROM Utilisateur WHERE username = ?";
        $st = $connexion->prepare($query);
        $st->execute([$username]);
        $res = $st->fetch(\PDO::FETCH_ASSOC);
        $st->closeCursor();
        if ($res === false) {
            throw new \touiteur\app\Exception\InvalidUsernameException("User $username does not exist");
        }
        return new User($res['email'], $res['password'], $res['username'], $res['nom'], $res['prenom'], $res['role']);
    }
}