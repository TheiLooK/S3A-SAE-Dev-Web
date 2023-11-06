<?php

namespace touiteur\app\structure\touite;

use touiteur\app\Exception\InvalidPropertyNameException;

class Touite {
    private string $message;
    private string $date;
    private string $user;
    private int $score;
    private ?string $image;

    public function __construct(string $message, string $user, ?string $image, string $date) {
        $this->message = $message;
        $this->date = $date;
        $this->user = $user;
        $this->score = 0;
        $this->image = $image;
    }

    public function __get($name): mixed {
        if (!property_exists($this, $name)) {
            throw new InvalidPropertyNameException("Invalid property name : $name");
        }
        return $this->$name;
    }

    public function addScore(int $score): void {
        $this->score += $score;
    }
}