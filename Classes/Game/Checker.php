<?php

namespace Classes\Game;

use JsonSerializable;

class Checker implements JsonSerializable
{
    private bool $_isWhite;
    private bool $_isRoyal;

    public function setIsRoyal(bool $isRoyal): void
    {
        $this->_isRoyal = $isRoyal;
    }

    public function getIsRoyal(): bool
    {
        return $this->_isRoyal;
    }

    public function getIsWhite(): bool
    {
        return $this->_isWhite;
    }

    public function getIsBlack(): bool
    {
        return !$this->_isWhite;
    }

    public function __construct(bool $isWhite){
        $this->_isWhite = $isWhite;
        $this->_isRoyal = false;
    }

    public function jsonSerialize(): string
    {
        return ($this->_isWhite ? "W":"B").($this->_isRoyal ? "R" : "");
    }
}