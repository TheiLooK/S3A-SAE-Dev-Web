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
        $thing="location.href='?action=displayTouite&id={$id}';";

        // image
        $html = "";
        if(!is_null($this->touite->image)){
            $html='<img src="'.$this->touite->image.'"/>';
        }

        // faire la structure, on utilise un onclick car des <a> dans des <a> sont illégaux
        $res = '<div class="Touite" onclick="'.$thing.'">
            <h4><a href="?action=profil&user='.$user.'">'.$user.'</a></h4>'.
            '<p>'.$message.'</p>'.$html;


        //si l'utilisateur est celui qui a publié le touite il peut le supprimer
        if(isset($_SESSION['users'])&&unserialize($_SESSION['users'])->username===$this->touite->user){
            $html = '<form method="POST" action="?action=supprimerTouite">';
            $html .= '<input type="hidden" name="id" value="' . $this->touite->id . '">';
            $html .= '<input class="suprBut icon" type="submit">';
            $html .= '</form>';
            $res .= $html;
        }
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
        return $res;
    }
    public function __get(string $at): mixed
    {
        if (!property_exists($this, $at)) {
            throw new InvalidPropertyNameException(get_called_class() . "invalid property : $at");
        }
        return $this->$at;
    }
}