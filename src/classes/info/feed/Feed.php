<?php

namespace touiteur\app\info\feed;

use touiteur\app\Exception\InvalidPropertyNameException;
use touiteur\app\info\touite\Touite;

class Feed {
    protected array $touites;
    protected int $touitesCount = 0;

    public function __construct(array $touites = [])
    {
        $this->touites = $touites;
        $this->touitesCount = count($this->touites);
    }

    public function __get(string $at): mixed
    {
        if (!property_exists($this, $at)) {
            throw new InvalidPropertyNameException(get_called_class() . "invalid property : $at");
        }
        return $this->$at;
    }

    public function addTouite(Touite $touite): void {
        $this->touites[] = $touite;
        $this->touitesCount++;
    }
}