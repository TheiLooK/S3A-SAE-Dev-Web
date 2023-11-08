<?php

namespace touiteur\app\structure\user;

use touiteur\app\db\ConnectionFactory;

class User {
    public String $email;
    private String $password;
    private String $username;
    private String $nom;
    private String $prenom;
    private int $role;
    private array $touiteNote;


    public function __construct(String $e,String $p, String $username, String $nom, String $prenom, int $r) {
        $this -> email = $e;
        $this -> password = $p;
        $this -> username = $username;
        $this -> nom = $nom;
        $this -> prenom = $prenom;
        $this -> role = $r;
        $this -> touiteNote = $this->getTouiteNote();
    }

    public function __get($name): mixed {
        if (!property_exists($this, $name)) {
            throw new \touiteur\app\Exception\InvalidPropertyNameException("Property $name does not exist");
        }
        return $this->$name;
    }

    private function getFollowedUsers(): array {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query = "SELECT emailSuivi FROM follow WHERE emailSuiveur = ?";
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

    public function checkFollow(User $userToFollow): bool {
        $followed = false;
        $followedUsers = $this->getFollowedUsers();
        foreach($followedUsers as $user) {
            if($user == $userToFollow->__get('email')) {
                $followed = true;
            }
        }
        return $followed;
    }

    private function getFollowedTags(): array {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query = "SELECT t.libelle FROM tag t INNER JOIN followTag f ON t.idTag = f.idTag WHERE f.emailSuiveur = ?";
        $st = $connexion->prepare($query);
        $st->execute([$this->email]);
        $res = $st->fetchAll(\PDO::FETCH_ASSOC);
        $st->closeCursor();

        $followedTags = [];
        foreach ($res as $row) {
            $followedTags[] = $row['libelle'];
        }
        return $followedTags;
    }

    public function checkFollowTag(String $tagToFollow): bool {
        $followed = false;
        $followedTags = $this->getFollowedTags();
        foreach($followedTags as $tag) {
            if($tag == $tagToFollow) {
                $followed = true;
            }
        }
        return $followed;
    }

    public static function getUser($username): User {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query = "SELECT * FROM users WHERE username = ?";
        $st = $connexion->prepare($query);
        $st->execute([$username]);
        $res = $st->fetch(\PDO::FETCH_ASSOC);
        $st->closeCursor();
        if ($res === false) {
            throw new \touiteur\app\Exception\InvalidUsernameException("User $username does not exist");
        }
        return new User($res['email'], $res['password'], $res['username'], $res['lastname'], $res['firstname'], $res['role']);
    }

    private function getTouiteNote() : array {
        $tab = [];
        $connexion = ConnectionFactory::makeConnection();
        $query = "Select * from notation where email like ?";
        $resultset = $connexion->prepare(($query));
        $resultset ->execute([$this->email]);

        while($data = $resultset->fetch()){
            $tab[$data['idTouite']] = $data['note'];
        }

        return $tab;
    }

    public function changeNote(int $id, int $note) : void{
        $this->touiteNote[$id] = $note;
        $_SESSION['users']=serialize($this);
    }
}