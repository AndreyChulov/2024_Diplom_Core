<?php

namespace Classes\GameHistory;

require_once dirname(__FILE__).'/InitTurnType.php';
require_once dirname(__FILE__).'/../Game/ChessAddressTranslator.php';

use Classes\GameHistory\InitTurnType;
use Classes\Game\ChessAddressTranslator;

class Turn implements \JsonSerializable
{
    private string $_from;
    private string $_to;
    private bool $_isTakeTurn;
    private ?bool $_isBlackTurn;

    public function setIsBlackTurn(bool $isBlackTurn): void
    {
        $this->_isBlackTurn = $isBlackTurn;
    }

    public function getFrom(): string
    {
        return $this->_from;
    }

    public function getTo(): string
    {
        return $this->_to;
    }

    public function getIsTakeTurn(): bool
    {
        return $this->_isTakeTurn;
    }

    public function getIsBlackTurn(): ?bool
    {
        return $this->_isBlackTurn ??
            throw new \Error("[Turn->getIsBlackTurn]Black turn not initialized");
    }

    public function __construct(InitTurnType $initTurnType, string $turn, ?bool $isBlackTurn = null){
        switch ($initTurnType){
            case InitTurnType::Move:
                $this->_from = substr($turn, 0, 2);
                $this->_isTakeTurn = $turn[2] == "*";
                $this->_to = substr($turn, 3);
                $this->_isBlackTurn = $isBlackTurn;
                break;
            case InitTurnType::Serialized:
                $this->_from = substr($turn, 2, 2);
                $this->_to = substr($turn, 5, 2);
                $this->_isTakeTurn = $turn[4] == "*";
                $this->_isBlackTurn = $turn[0] == "B";
                break;
        }
    }

    /**
     * @throws \Exception
     */
    public function jsonSerialize(): string
    {
        if (!isset($this->_isBlackTurn)){
            throw new \Exception("[Turn->jsonSerialize()] Black turn not initialized");
        }

        return
            ($this->_isBlackTurn ? "B" : "W") .
            "|" .
            $this->_from .
            ($this->_isTakeTurn ? "*" : "-") .
            $this->_to .
            "|";
    }
}