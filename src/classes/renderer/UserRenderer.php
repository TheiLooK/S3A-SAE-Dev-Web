<?php

namespace touiteur\app\renderer;

use touiteur\app\structure\user\User;

class UserRenderer implements Renderer {

    private User $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function render(int $selector): string {
        $res = "<div>";
        $res = '<h4><a href="?action=profil&user='.$this->user.'">'.$this->user.'</a></h4>';
        return $res."</div>";
    }
}