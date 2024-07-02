<?php

namespace Classes\GameHistory;

require_once dirname(__FILE__)."/Turn.php";

use Classes\GameHistory\Turn;

class GameHistory implements \JsonSerializable
{
    private array $_history;

    public function __construct()
    {
        $this->_history = [];
    }

    public function getIsBlackLastTurn(): ?bool
    {
        $historyLength = count($this->_history);

        if ($historyLength === 0){
            return null;
        }

        /**
         * @var Turn $lastTurn
         */
        $lastTurn = $this->_history[$historyLength-1];

        return $lastTurn->getIsBlackTurn();
    }

    public function getLastTurnToAddress(): ?string
    {
        $historyLength = count($this->_history);

        if ($historyLength === 0){
            return null;
        }

        /**
         * @var Turn $lastTurn
         */
        $lastTurn = $this->_history[$historyLength-1];

        return $lastTurn->getTo();
    }

    public function AddTurn(string $turn, bool $isBlackTurn): void
    {
        $this->_history[] = new Turn(InitTurnType::Move, $turn, $isBlackTurn);
    }

    public function LoadFromJsonObject(array $data): void
    {
        for ($counter = 0; $counter < count($data); $counter++) {
            $this->_history[] = new Turn(InitTurnType::Serialized, $data[$counter]);
        }
    }

    public function jsonSerialize(): array
    {
        return $this->_history;
    }
}