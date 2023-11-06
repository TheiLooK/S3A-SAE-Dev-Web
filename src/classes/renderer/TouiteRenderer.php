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
        $res = '<h4>'.$this->touite->__get('user').'</h4>';
        if(!is_null($this->touite->__get('image'))){
            echo"img";
            $res.='<img src="'.$this->touite->__get('image').'"/>';
        }
        $res.= '<p>'.$this->touite->__get('message').'</p>';

        return $res;
    }

    protected function renderLong(): string {
        return "TODO";
    }

    public function __get(string $at): mixed
    {
        if (!property_exists($this, $at)) {
            throw new InvalidPropertyNameException(get_called_class() . "invalid property : $at");
        }
        return $this->$at;
    }
}