<?php
declare(strict_types=1);
namespace touiteur\app\Auth;

class Auth
{
    public static function authentification(String $pwd,String $email) : void {

        $query = "SELECT * from user where email=?";
        $connexion = ConnectionFactory::makeConnection();
        $resultset = $connexion->prepare($query);
        $res = $resultset->execute([$email]);
        if(!$res) {throw new \touiteur\app\Exception\AuthException("Erreur : requetes");}

        $user = $resultset->fetch(PDO::FETCH_ASSOC);
        if(!$user) { throw new \iutnc\deefy\Exception\AuthException("Erreur :  authentification invalid"); }
        if(!password_verify($pwd,$user['passwd'])){ throw new \iutnc\deefy\Exception\AuthException("Erreur : mot de passe invalid "); }
    }

    public static function register(string $pwd, string $email, string $pseudo) : bool {

        if(!self::checkPasswordStrength($pwd,10)) {
            throw new \touiteur\app\Exception\AuthException("password trop faible ");
        }

        $hash = password_hash($pwd, PASSWORD_DEFAULT, ['cost' => 12]);
        try {
            $connexion = ConnectionFactory::makeConnection();
        } catch(DBException $e) {
            throw new Exception($e->getMessage());
        }
        $query_email = "select * from user where email = ?";
        $resultset = $connexion->prepare($query_email);
        $res = $resultset ->execute([$email]);
        if($resultset->fetch()) {
            throw new \touiteur\app\Exception\AuthException("compte déjà existant");
        }

        try{
            $query ="insert into user(email,passwd,pseudo,role) values (?, ?, 1)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$email,$hash]);
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
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT * from user where email = :email";
        $resultset = $connexion->prepare(($query));
        $resultset ->execute(["$email"]);
        $user =$resultset->fetch(PDO::FETCH_ASSOC);

        $profile = new User($user['email'], $user['passwd'], $user['role']);
        $_SESSION['users'] = serialize($profile);
    }
}