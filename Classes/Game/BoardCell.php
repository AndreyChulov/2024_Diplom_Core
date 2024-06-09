<?php

namespace Classes\Game;

$localFolder = dirname(__FILE__);

require_once "$localFolder/Checker.php";
require_once "$localFolder/BoardCellInitializeType.php";

use Classes\Game\Checker;
use JsonSerializable;

class BoardCell implements JsonSerializable
{
    private ?Checker $_checker;

    public function getChecker(): ?Checker
    {
        return $this->_checker;
    }

    public function setChecker(?Checker $_checker): void
    {
        $this->_checker = $_checker;
    }

    public function __construct(?BoardCellInitializeType $initializeType){
        switch ($initializeType){
            case BoardCellInitializeType::BLACK:
                $this->_checker = new Checker(false);
                break;
            case BoardCellInitializeType::WHITE:
                $this->_checker = new Checker(true);
                break;
            case BoardCellInitializeType::EMPTY:
                $this->_checker = null;
                break;
        }

    }

    public function IsCheckerOnCell():bool
    {
        return isset($this->_checker);
    }

    public function IsFreeCell():bool
    {
        return is_null($this->_checker);
    }

    public function jsonSerialize(): ?Checker
    {
        return $this->_checker;
    }
}