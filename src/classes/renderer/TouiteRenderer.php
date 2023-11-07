<?php

namespace touiteur\app\renderer;

use touiteur\app\Exception\InvalidPropertyNameException;
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
        $res = '<h4><a href="?action=profil&user='.$user.'">'.$user.'</a>'." | ".$this->touite->date.'</h4>';
        $res.= '<p>'.$this->touite->prepareHtml().'</p>';
        if(!is_null($this->touite->image)){
            $res.='<img src="'.$this->touite->image.'"/>';
        }
        $res.= '<p> score : '.$this->touite->score.'</p>';
        $res.=$this->createButton();
        return $res;
    }
    public function __get(string $at): mixed
    {
        if (!property_exists($this, $at)) {
            throw new InvalidPropertyNameException(get_called_class() . "invalid property : $at");
        }
        return $this->$at;
    }

    private function createButton() : string{
        //Create the upvote / downvote Button
        $button = '<form method="POST" class="buttons">';
        $button .= '<input type="image" class="icon" src="images/site/up.png" alt="Submit" formaction="?action=home">';
        $button .= '<input type="image" class="icon" src="images/site/down.png" alt="Submit" formaction="?action=home">';
        // create delete button if the user is the creator of the touite
        if(isset($_SESSION['users'])&&unserialize($_SESSION['users'])->username===$this->touite->user){
            $button .= '<input type="hidden" name="id" value="' . $this->touite->id . '">';
            $button .= '<input type="image" class="icon" src="images/site/supprimer.png" alt="Submit" formaction="?action=supprimerTouite">';
        }
        $button .= '</form>';

        return $button;
    }
}