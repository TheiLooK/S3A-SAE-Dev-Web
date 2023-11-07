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

        //recuparation variables
        $id = $this->touite->__get('id');
        $user = $this->touite->__get('user');
        $message = $this->touite->prepareHtml();
        $thing="location.href='?action=displayTouite&id={$id}';";

        $html = "";
        if(!is_null($this->touite->__get('image'))){
            $html='<img src="'.$this->touite->__get('image').'"/>';
        }

        $res = '<div class="Touite" onclick="'.$thing.'">
            <h4><a href="?action=profil&user='.$user.'">'.$user.'</a></h4>'.
            '<p>'.$message.'</p>'.$html.
            '</div>';

        return $res;
    }

    protected function renderLong(): string {
        $res = '<h4>'.$this->touite->__get('user')." | ".$this->touite->__get('date').'</h4>';
        $res.= '<p>'.$this->touite->__get('message').'</p>';
        if(!is_null($this->touite->__get('image'))){
            $res.='<img src="'.$this->touite->__get('image').'"/>';
        }
        $res.= '<p> score : '.$this->touite->__get('score').'</p>';
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