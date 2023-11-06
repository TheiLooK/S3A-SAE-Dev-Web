<?php
declare(strict_types=1);
namespace touiteur\app\Auth;
use PDO;
class Auth
{
    public static function authentification(String $pwd,String $email) : void {

        $query = "SELECT * from Utilisateur where email=?";
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $resultset = $connexion->prepare($query);
        $res = $resultset->execute([$email]);
        if(!$res) {throw new \touiteur\app\Exception\AuthException("Erreur : requetes");}

        $user = $resultset->fetch(PDO::FETCH_ASSOC);
        if(!$user) { throw new \touiteur\app\Exception\AuthException("Erreur :  authentification invalid"); }
        if(!password_verify($pwd,$user['password'])){ throw new \touiteur\app\Exception\AuthException("Erreur : mot de passe invalid "); }
    }

    public static function register(string $pwd, string $email, string $pseudo,string $firstname, string $lastname, string $date) : bool {

        if(!self::checkPasswordStrength($pwd,1)) {
            throw new \touiteur\app\Exception\AuthException("password trop faible ");
        }

        $hash = password_hash($pwd, PASSWORD_DEFAULT, ['cost' => 12]);
        try {
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        } catch(DBException $e) {
            throw new Exception($e->getMessage());
        }
        $query_email = "select * from Utilisateur where email = ?";
        $resultset = $connexion->prepare($query_email);
        $res = $resultset ->execute([$email]);
        if($resultset->fetch()) {
            throw new \touiteur\app\Exception\AuthException("compte déjà existant");
        }
        try{
            $query ="insert into Utilisateur(email,username,password,role,nom,prenom,datenaissance) values (?,?,?,1,?,?,?)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$email,$pseudo,$hash,$lastname,$firstname,$date]);
        } catch (PDOException $e) {
            throw new \touiteur\app\Exception\AuthException("Erreur lors de la création du compte");

        }
        return $res;
    }

    public static function checkPasswordStrength(string $pwd, int $min) :bool {
        $length = (strlen($pwd)> $min);
        if(!$length) {
            return false;
        }
        return true;
    }


    public static function loadProfile(String $email) : void {
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $query = "SELECT * from Utilisateur where email = :email";
        $resultset = $connexion->prepare(($query));
        $resultset ->execute(["$email"]);
        $user =$resultset->fetch(PDO::FETCH_ASSOC);

        $profile = new \touiteur\app\db\User($user['email'], $user['password'],$user['username'], $user['role']);
        $_SESSION['users'] = serialize($profile);
    }
}