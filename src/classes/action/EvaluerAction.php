<?php

namespace touiteur\app\action;
use touiteur\app\db\ConnectionFactory;
use touiteur\app\structure\user\User;

class EvaluerAction
{
    public function execute(): string {
        // if idTouite is not set then its an access via modifing the url
        if(!isset($_POST['idTouite'])){
            $pageContent = "<h1>Access Interdit via modification Url</h1>";
            $pageContent .= '<p>Redirection vers la page d\'accueil dans 2 secondes</p></div>';
            $pageContent .= '<script type="text/javascript">window.setTimeout(function(){window.location.replace("?action=home");}, 2000);</script>';
            return $pageContent;
        }


        $user = unserialize($_SESSION['users']);
        $id=$_POST['idTouite'];




        $email=$user->email;

        // si l'utilisateur est l'auteur du touite on ne fait rien
        $sql = "SELECT email FROM touite WHERE idTouite = ?";
        $connexion = ConnectionFactory::makeConnection();
        $resultset = $connexion->prepare(($sql));
        $resultset ->execute([$id]);
        $data = $resultset->fetch();
        if($data['email'] == $email){
            header("Location:{$_POST['url']}");
            exit();
        }

        // if the id is in the touite note by the user we update
        if(array_key_exists($id, $user->touiteNoter)){
            if ($user->touiteNoter[$id] == 1 && $_GET['note'] == "up"){
                $this->supprimerNote($email, $id, $user);
            }
            elseif ($user->touiteNoter[$id] == -1 && $_GET['note'] == "down"){
                $this->supprimerNote($email, $id, $user);
            }
            else {
                $this->updateNote($_GET['note'],$email, $id, $user);
            }
        }else{
            $this->insertNote($_GET['note'],$email, $id, $user);
        }
        header("Location:{$_POST['url']}");
        //return useless but we need it so we don't get any errors;
        return '';
    }

    private function updateNote(string $type, string $email, int $id, User $user): void {
        $connexion = ConnectionFactory::makeConnection();
        $query = "UPDATE notation SET note = ? WHERE email LIKE ? AND idTouite = ?";
        $resultset = $connexion->prepare(($query));
        switch ($type){
            case "up":
                $resultset ->execute([1,$email,$id]);
                $user->changeNote($id, 1);
                break;
            case "down":
                $resultset ->execute([-1,$email,$id]);
                $user->changeNote($id, -1);
                break;
        }
        $_SESSION['users']=serialize($user);
    }

    private function insertNote(string $type, string $email, int $id, User $user): void {
        $connexion = ConnectionFactory::makeConnection();
        $query = "INSERT INTO notation (email, idTouite, note) VALUES (?,?,?)";
        $resultset = $connexion->prepare(($query));
        switch ($type){
            case "up":
                $resultset ->execute([$email, $id, 1]);
                $user->changeNote($id, 1);
                break;
            case "down":
                $resultset ->execute([$email, $id, -1]);
                $user->changeNote($id, -1);
                break;
        }
    }

    private function supprimerNote(string $email, int $id, User $user): void {
        $connexion = ConnectionFactory::makeConnection();
        $query = "DELETE FROM notation WHERE email = ? AND idTouite = ?";
        $resultset = $connexion->prepare(($query));
        $resultset ->execute([$email, $id]);
        $user->removeNote($id);
    }
}