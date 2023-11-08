<?php

namespace touiteur\app\renderer;

interface Renderer {
    const COMPACT = 1;
    const LONG = 2;
    const NBPARPAGEFEED = 5;

    public function render(int $selector): string;
}