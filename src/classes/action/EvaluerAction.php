<?php

namespace touiteur\app\action;
use touiteur\app\db\ConnectionFactory;
use touiteur\app\structure\user\User;

class EvaluerAction
{
    public function execute() : string {
        $user = unserialize($_SESSION['users']);
        $id=$_POST['id'];
        $email=$user->email;
        // if the id is in the touite note by the user we update
        if(array_key_exists($id, $user->touiteNoter)){
            $this->updateNote($_GET['note'],$email, $id, $user);
        }else{
            $this->insertNote($_GET['note'],$email, $id, $user);
        }
        header("Location:{$_POST['url']}");
        exit();

        //return useless but we need it so we don't get any errors.
        return "";
    }




    private function updateNote(string $type, string $email, int $id, User $user){
        $connexion = ConnectionFactory::makeConnection();
        $query = "UPDATE notation set note = ? where email like ? and idTouite = ?";
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

    private function insertNote(string $type, string $email, int $id, User $user){
        $connexion = ConnectionFactory::makeConnection();
        $query = "insert into notation (email, idTouite, note) values (?,?,?)";
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
}