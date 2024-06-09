<?php

namespace Classes\Game;

$localFolder = dirname(__FILE__);

require_once "$localFolder/BoardRow.php";
require_once "$localFolder/BoardInitializeType.php";

use Classes\Game\BoardInitializeType;
use Classes\Game\BoardRow;
use JsonSerializable;

class Board implements JsonSerializable
{
    private Array $_boardRows;

    public function __construct(BoardInitializeType $initializeType = BoardInitializeType::EMPTY_BOARD){
        switch ($initializeType){
            case BoardInitializeType::EMPTY_BOARD:
                $this->_boardRows = [
                    new BoardRow(BoardRowInitializeType::EMPTY),//1
                    new BoardRow(BoardRowInitializeType::EMPTY),//2
                    new BoardRow(BoardRowInitializeType::EMPTY),//3
                    new BoardRow(BoardRowInitializeType::EMPTY),//4
                    new BoardRow(BoardRowInitializeType::EMPTY),//5
                    new BoardRow(BoardRowInitializeType::EMPTY),//6
                    new BoardRow(BoardRowInitializeType::EMPTY),//7
                    new BoardRow(BoardRowInitializeType::EMPTY),//8
                ];
                break;
            case BoardInitializeType::GAME_START:
                $this->_boardRows = [
                    new BoardRow(BoardRowInitializeType::FIRST_WHITE),//1
                    new BoardRow(BoardRowInitializeType::SECOND_WHITE),//2
                    new BoardRow(BoardRowInitializeType::FIRST_WHITE),//3
                    new BoardRow(BoardRowInitializeType::EMPTY),//4
                    new BoardRow(BoardRowInitializeType::EMPTY),//5
                    new BoardRow(BoardRowInitializeType::SECOND_BLACK),//6
                    new BoardRow(BoardRowInitializeType::FIRST_BLACK),//7
                    new BoardRow(BoardRowInitializeType::SECOND_BLACK),//8
                ];
                break;
        }
    }

    public function jsonSerialize(): array
    {
        return $this->_boardRows;
    }
}