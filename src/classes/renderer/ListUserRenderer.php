<?php

namespace touiteur\app\renderer;

use touiteur\app\structure\lists\ListUser;

class ListUserRenderer implements Renderer{

    private ListUser $list;

    public function __construct($list) {
        $this->list = $list;
    }

    public function render(int $selector): string {
        $r = '<div class="feed">';
        foreach ($this->list->list as $user) {
            $userRenderer = new UserRenderer($user);
            $r .= $userRenderer->render(Renderer::COMPACT);
        }
        return $r;
    }
}