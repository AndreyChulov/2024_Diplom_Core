<?php

namespace Classes\Game;

$localFolder = dirname(__FILE__);

require_once "$localFolder/BoardCell.php";
require_once "$localFolder/BoardRowInitializeType.php";

use Classes\Game\BoardCell;
use Classes\Game\BoardRowInitializeType;
use JsonSerializable;

class BoardRow implements JsonSerializable
{
    private Array $_boardCells;

    public function __construct(BoardRowInitializeType $rowType = BoardRowInitializeType::EMPTY){

        switch ($rowType){
            case BoardRowInitializeType::FIRST_BLACK:
                $this->_boardCells = [
                    new BoardCell(BoardCellInitializeType::BLACK),//1
                    new BoardCell(BoardCellInitializeType::EMPTY),//2
                    new BoardCell(BoardCellInitializeType::BLACK),//3
                    new BoardCell(BoardCellInitializeType::EMPTY),//4
                    new BoardCell(BoardCellInitializeType::BLACK),//5
                    new BoardCell(BoardCellInitializeType::EMPTY),//6
                    new BoardCell(BoardCellInitializeType::BLACK),//7
                    new BoardCell(BoardCellInitializeType::EMPTY),//8
                ];
                break;
            case BoardRowInitializeType::SECOND_BLACK:
                $this->_boardCells = [
                    new BoardCell(BoardCellInitializeType::EMPTY),//1
                    new BoardCell(BoardCellInitializeType::BLACK),//2
                    new BoardCell(BoardCellInitializeType::EMPTY),//3
                    new BoardCell(BoardCellInitializeType::BLACK),//4
                    new BoardCell(BoardCellInitializeType::EMPTY),//5
                    new BoardCell(BoardCellInitializeType::BLACK),//6
                    new BoardCell(BoardCellInitializeType::EMPTY),//7
                    new BoardCell(BoardCellInitializeType::BLACK),//8
                ];
                break;
            case BoardRowInitializeType::FIRST_WHITE:
                $this->_boardCells = [
                    new BoardCell(BoardCellInitializeType::WHITE),//1
                    new BoardCell(BoardCellInitializeType::EMPTY),//2
                    new BoardCell(BoardCellInitializeType::WHITE),//3
                    new BoardCell(BoardCellInitializeType::EMPTY),//4
                    new BoardCell(BoardCellInitializeType::WHITE),//5
                    new BoardCell(BoardCellInitializeType::EMPTY),//6
                    new BoardCell(BoardCellInitializeType::WHITE),//7
                    new BoardCell(BoardCellInitializeType::EMPTY),//8
                ];
                break;
            case BoardRowInitializeType::SECOND_WHITE:
                $this->_boardCells = [
                    new BoardCell(BoardCellInitializeType::EMPTY),//1
                    new BoardCell(BoardCellInitializeType::WHITE),//2
                    new BoardCell(BoardCellInitializeType::EMPTY),//3
                    new BoardCell(BoardCellInitializeType::WHITE),//4
                    new BoardCell(BoardCellInitializeType::EMPTY),//5
                    new BoardCell(BoardCellInitializeType::WHITE),//6
                    new BoardCell(BoardCellInitializeType::EMPTY),//7
                    new BoardCell(BoardCellInitializeType::WHITE),//8
                ];
                break;
            case BoardRowInitializeType::EMPTY:
                $this->_boardCells = [
                    new BoardCell(BoardCellInitializeType::EMPTY),//1
                    new BoardCell(BoardCellInitializeType::EMPTY),//2
                    new BoardCell(BoardCellInitializeType::EMPTY),//3
                    new BoardCell(BoardCellInitializeType::EMPTY),//4
                    new BoardCell(BoardCellInitializeType::EMPTY),//5
                    new BoardCell(BoardCellInitializeType::EMPTY),//6
                    new BoardCell(BoardCellInitializeType::EMPTY),//7
                    new BoardCell(BoardCellInitializeType::EMPTY),//8
                ];
                break;
        }
    }

    public function jsonSerialize(): array
    {
        return $this->_boardCells;
    }
}