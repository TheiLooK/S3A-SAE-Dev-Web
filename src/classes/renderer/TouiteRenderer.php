<?php

namespace touiteur\app\renderer;

use touiteur\app\auth\Auth;
use touiteur\app\exception\InvalidPropertyNameException;
use touiteur\app\structure\touite\Touite;

class TouiteRenderer implements Renderer {
    private Touite $touite;

    public function __construct(Touite $touite) {
        $this->touite = $touite;
    }
    public function render(int $selector): string {
        switch ($selector) {
            case Renderer::LONG:
                return $this->renderLong();
            case Renderer::COMPACT:
                return $this->renderCompact();
            default:
                return "Erreur : format de rendu inconnu";
        }
    }
    protected function renderCompact(): string {

        // recuparation variables
        $id = $this->touite->id;
        $user = $this->touite->user;
        $message = $this->touite->prepareHtml();
        $onclick="location.href='?action=displayTouite&id={$id}';";

        // image
        $html = "";
        if(!is_null($this->touite->image)){
            $html='<p><i>+ image</i></p>';
        }
        // faire la structure, on utilise un onclick car des <a> dans des <a> sont illégaux
        $res = '<div class="Touite" onclick="'.$onclick.'">
            <h4><a href="?action=profil&user='.$user.'">'.$user.'</a></h4>'.
            '<p>'.$message.'</p>'.$html;

        $res.=$this->createButton();
        $res.='</div>';
        return $res;
    }
    protected function renderLong(): string {
        $user = $this->touite->user;

        $res = '<div class="Touite">';
        $res .= '<h4><a href="?action=profil&user='.$user.'">'.$user.'</a>'."  |  ".$this->touite->date.'</h4>';
        $res.= '<p>'.$this->touite->prepareHtml().'</p>';

        if(!is_null($this->touite->image)){
            $res.='<img src="'.$this->touite->image.'"/>';
        }

        $res.= '<div><p class = "score"> score : '.$this->touite->scoreUp-$this->touite->scoreDown.'</p>';
        $res.=$this->createButton() . "</div>";
        $res.="</div>";
        return $res;
    }
    public function __get(string $at): mixed
    {
        if (!property_exists($this, $at)) {
            throw new InvalidPropertyNameException(get_called_class() . "invalid property : $at");
        }
        return $this->$at;
    }


    /**
     * Function used to create the buttons upvote, downvote and delete according to the user state(signed in, owner, admin...)
     * @return string the html of the buttons
     */
    private function createButton() : string{
        // if the user is not sign in he cant see the button
        if(!Auth::checkSignIn()) return "";

        $user = unserialize($_SESSION['users']);

        //Create the upvote / downvote Button
        $button = '<form method="POST" class="buttons">';
        //we add hidden values used in the actions of the buttons
        $button .= '<input type="hidden" name="idTouite" value="' . $this->touite->id . '">';
        $button .= '<input type="hidden" name="emailTouite" value="' . $this->touite->email . '">';
        $button .= '<input type="hidden" name="url" value="' .$_SERVER['REQUEST_URI']. '">';
        $classUp = "icon ";
        $classDown = "icon ";
        //if the user has upvoted the button,
        if(array_key_exists($this->touite->id, $user->touiteNoter)){
            if(($user->touiteNoter)[$this->touite->id]===-1){
                $classDown.="selectedIcon";
            }else{
                $classUp.="selectedIcon";
            }
        }
        $button .= "<p>{$this->touite->scoreUp}</p>";
        $button .= '<input type="image" class="'.$classUp.'" src="images/site/up.png" alt="Submit" formaction="?action=EvaluerAction&note=up">';
        $button .= "<p>{$this->touite->scoreDown}</p>";
        $button .= '<input type="image" class="'.$classDown.'" src="images/site/down.png" alt="Submit" formaction="?action=EvaluerAction&note=down">';

        // create delete button if the user is the creator of the touite
        if(Auth::checkOwnership($this->touite->email)){
            $button .= '<input type="image" class="icon" src="images/site/supprimer.png" alt="Submit" formaction="?action=supprimerTouite">';
        }
        $button .= '</form>';

       return $button;
    }
}