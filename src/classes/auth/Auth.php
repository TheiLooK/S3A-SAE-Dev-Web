<?php
declare(strict_types=1);
namespace touiteur\app\Auth;
use PDO;
use touiteur\app\db\ConnectionFactory;

class Auth
{
    public static function authentification(string $pwd, string $email): void
    {

        $query = "SELECT * from users where email=?";
        $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        $resultset = $connexion->prepare($query);
        $res = $resultset->execute([$email]);
        if (!$res) {
            throw new \touiteur\app\Exception\AuthException("Erreur : requetes");
        }

        $user = $resultset->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            throw new \touiteur\app\Exception\AuthException("Erreur :  authentification invalid");
        }
        if (!password_verify($pwd, $user['password'])) {
            throw new \touiteur\app\Exception\AuthException("Erreur : mot de passe invalid ");
        }
    }

    public static function register(string $pwd, string $email, string $pseudo, string $firstname, string $lastname): bool
    {

        if (!self::checkPasswordStrength($pwd, 1)) {
            throw new \touiteur\app\Exception\AuthException("password trop faible ");
        }

        $hash = password_hash($pwd, PASSWORD_DEFAULT, ['cost' => 12]);
        try {
            $connexion = \touiteur\app\db\ConnectionFactory::makeConnection();
        } catch (DBException $e) {
            throw new Exception($e->getMessage());
        }
        $query_email = "select * from users where email = ?";
        $resultset = $connexion->prepare($query_email);
        $res = $resultset->execute([$email]);
        if ($resultset->fetch()) {
            throw new \touiteur\app\Exception\AuthException("compte déjà existant");
        }
        try {
            $query = "insert into users(email,username,password,lastname,firstname,role) values (?,?,?,?,?,1)";
            $resultset = $connexion->prepare(($query));
            $res = $resultset->execute([$email, $pseudo, $hash, $lastname, $firstname]);
        } catch (PDOException $e) {
            throw new \touiteur\app\Exception\AuthException("Erreur lors de la création du compte");

        }
        return $res;
    }

    public static function checkPasswordStrength(string $pwd, int $min): bool
    {
        $length = (strlen($pwd) > $min);
        if (!$length) {
            return false;
        }
        return true;
    }

    public static function loadProfile(string $email): void
    {
        $connexion = ConnectionFactory::makeConnection();
        $query = "SELECT * from users where email = :email";
        $resultset = $connexion->prepare(($query));
        $resultset->execute(["$email"]);
        $user = $resultset->fetch(PDO::FETCH_ASSOC);

        $profile = new \touiteur\app\structure\user\User($user['email'], $user['password'], $user['username'], $user['lastname'], $user['firstname'], $user['role']);
        $_SESSION['users'] = serialize($profile);
    }

    /**
     * function used to check if the user has the required access level
     * @param int $required the access level needed
     * @return bool true if the user has the correct acces level
     */
    public static function checkAccessLevel(int $required) : bool{
        $connexion = ConnectionFactory::makeConnection();
        $user = unserialize($_SESSION['users']);
        if($user->role==$required){
            return true;
        }
        return false;
    }

    /**
     * Function used to check if the user given is the user in session
     * @param string $email the user to check
     * @return bool true if the user in session is the user given or an admin
     */
    public static function checkOwnership(string $email) : bool{
        $access = false;
        $user = unserialize($_SESSION['users']);
        if (isset($_SESSION['users']) && ($user->email === $email) || (($user->role == 100))) {
            $access = true;
        }
        return $access;
    }

    /**
     * Function used to check if the user is signed in or an anonym user
     * @return bool true if the user is signed in
     */
    public static function checkSignIn() : bool{
        return isset($_SESSION['users']);
    }
}