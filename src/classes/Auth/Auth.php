<?php

namespace touiteur\app\Auth;

class Auth
{
    public static function authentification(String $pwd,String $email) : void {

        $query = "SELECT * from user where email=?";
        $connexion = ConnectionFactory::makeConnection();
        $resultset = $connexion->prepare($query);
        $res = $resultset->execute([$email]);
        if(!$res) {throw new \iutnc\deefy\Exception\AuthException("Erreur : requetes");}

        $user = $resultset->fetch(PDO::FETCH_ASSOC);
        if(!$user) { throw new \iutnc\deefy\Exception\AuthException("Erreur :  authentification invalid"); }
        if(!password_verify($pwd,$user['passwd'])){ throw new \iutnc\deefy\Exception\AuthException("Erreur : mot de passe invalid "); }
    }

    public static function register(string $pwd, string $email) : bool {

        if(!self::checkPasswordStrength($pwd,10)) {
            throw new \iutnc\deefy\Exception\AuthException("password trop faible ");
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
            throw new \iutnc\deefy\Exception\AuthException("compte déjà existant");
        }

        try{
            $query ="insert into user(email,passwd,role) values (?, ?, 1)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset ->execute([$email,$hash]);
        } catch (PDOException $e) {
            throw new \iutnc\deefy\Exception\AuthException("Erreur lors de la création du compte");

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
}