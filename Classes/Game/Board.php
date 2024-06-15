<?php

namespace Classes\Game;

$localFolder = dirname(__FILE__);

require_once "$localFolder/BoardRow.php";
require_once "$localFolder/BoardInitializeType.php";
require_once "$localFolder/ChessAddressTranslator.php";
require_once "$localFolder/MoveDirectionType.php";

use Classes\Game\BoardInitializeType;
use Classes\Game\BoardRow;
use Classes\Game\ChessAddressTranslator;
use Classes\Game\MoveDirectionType;
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

    public function LoadBoardState(array $jsonObject): void
    {
        foreach ($jsonObject as $index=>$row) {
            $this->_boardRows[$index]->LoadBoardRow($row);
        }
    }

    public function IsCheckerOnBoardCell(string $chessAddress): bool
    {
        $row = ChessAddressTranslator::GetRowIndex($chessAddress);
        $column = ChessAddressTranslator::GetColumnIndex($chessAddress);
        return $this->_boardRows[$row]->getBoardCells()[$column]->IsCheckerOnCell();
    }

    public function IsBlackCheckerOnAddress(string $address)
    {
        $row = ChessAddressTranslator::GetRowIndex($address);
        $column = ChessAddressTranslator::GetColumnIndex($address);

        return $this->_boardRows[$row]->getBoardCells()[$column]->getChecker()->getIsBlack();
    }

    public function IsMoveValid(string $move): bool
    {
        $fromAddress = substr($move, 0, 2);
        $availableMovies = $this->GetAvailableCheckerMoves($fromAddress);

        if (in_array($move, $availableMovies, true)) {
            return true;
        }

        return false;
    }

    public function IsPlayerWin(bool $isPlayerPlayBlack): bool
    {
        $isSearchBlackCheckers = !$isPlayerPlayBlack;

        foreach ($this->_boardRows as $boardRow) {
            foreach ($boardRow->getBoardCells() as $boardCell){
                if ($isSearchBlackCheckers === $boardCell->getChecker()->getIsBlack()){
                    return false;
                }
            }
        }

        return true;
    }

    public function ApplyMove(string $move): void
    {
        $fromAddress = substr($move, 0, 2);
        $moveType = $move[2];
        $toAddress = substr($move, 3);
        $fromRow = ChessAddressTranslator::GetRowIndex($fromAddress);
        $fromColumn = ChessAddressTranslator::GetColumnIndex($fromAddress);
        $toRow = ChessAddressTranslator::GetRowIndex($toAddress);
        $toColumn = ChessAddressTranslator::GetColumnIndex($toAddress);
        $fromCell = $this->_boardRows[$fromRow]->getBoardCells()[$fromColumn];
        $toCell = $this->_boardRows[$toRow]->getBoardCells()[$toColumn];

        $checker = $fromCell->getChecker();

        if ($toRow === ($checker->getIsBlack() ? 0 : 7)){
            $checker->setIsRoyal(true);
        }

        $fromCell->setChecker(null);
        $toCell->setChecker($checker);

        if ($moveType === "*"){
            $deadCheckerRow = $toRow + (($toRow - $fromRow) > 0 ? -1 : 1);
            $deadCheckerColumn = $toColumn + (($toColumn - $fromColumn) > 0 ? -1 : 1);
            $this->_boardRows[$deadCheckerRow]->getBoardCells()[$deadCheckerColumn]->setChecker(null);
        }
    }

    public function IsContinueMoveExist(string $move): bool
    {
        $toAddress = substr($move, 3);
        $acceptableMovies = $this->GetAvailableCheckerMoves($toAddress);

        foreach ($acceptableMovies as $movie) {
            if ($move[2] === "*"){
                return true;
            }
        }

        return false;
    }

    public function GetAvailableCheckerMoves(string $chessAddress): array
    {
        $row = ChessAddressTranslator::GetRowIndex($chessAddress);
        $column = ChessAddressTranslator::GetColumnIndex($chessAddress);
        $checker = $this->_boardRows[$row]->getBoardCells()[$column]->getChecker();
        $isBlackChecker = $checker->getIsBlack();
        $isRoyalChecker = $checker->getIsRoyal();
        $maxMoveLength = $isRoyalChecker ? 8 : 2;

        $topLeftMovies = $this->GetMovies($row, $column, $isBlackChecker, $maxMoveLength,
            MoveDirectionType::TOP_LEFT);
        $topRightMovies = $this->GetMovies($row, $column, $isBlackChecker, $maxMoveLength,
            MoveDirectionType::TOP_RIGHT);
        $bottomRightMovies = $this->GetMovies($row, $column, $isBlackChecker, $maxMoveLength,
            MoveDirectionType::BOTTOM_RIGHT);
        $bottomLeftMovies = $this->GetMovies($row, $column, $isBlackChecker, $maxMoveLength,
            MoveDirectionType::BOTTOM_LEFT);

        if ($isRoyalChecker){
            if ($isBlackChecker){
                return $this->ConstructRoyalCheckerMovies(
                    $topLeftMovies, $topRightMovies, $bottomRightMovies, $bottomLeftMovies);
            } else {
                return $this->ConstructRoyalCheckerMovies(
                    $bottomLeftMovies, $bottomRightMovies, $topRightMovies, $topLeftMovies);
            }
        } else {
            if ($isBlackChecker){
                return $this->ConstructRegularCheckerMovies(
                    $topLeftMovies, $topRightMovies, $bottomRightMovies, $bottomLeftMovies);
            } else {
                return $this->ConstructRegularCheckerMovies(
                    $bottomLeftMovies, $bottomRightMovies, $topRightMovies, $topLeftMovies);
            }
        }

        return $result;
    }

    public function GetAvailableMoves(bool $isBlackPlayer): array
    {
        $result = [];

        for ($rowCounter = 0; $rowCounter < 8; $rowCounter++) {
            for ($columnCounter = 0; $columnCounter < 8; $columnCounter++) {
                $address = ChessAddressTranslator::GetChessAddress($rowCounter, $columnCounter);
                $boardCell = $this->_boardRows[$rowCounter]->getBoardCells()[$columnCounter];

                if ($boardCell->IsCheckerOnCell() && $boardCell->getChecker()->getIsBlack() === $isBlackPlayer){
                    $result = $this->AddMovies($result, $this->GetAvailableCheckerMoves($address));
                }
            }
        }

        return $result;
    }

    private function ConstructRegularCheckerMovies(
        array $backwardLeftMovies, array $backwardRightMovies,
        array $forwardRightMovies, array $forwardLeftMovies): array
    {
        $result = [];

        $result = $this->AddMovies($result, $forwardLeftMovies, 1);
        $result = $this->AddMovies($result, $forwardRightMovies, 1);

        if (count($backwardLeftMovies) === 1){
            $move = $backwardLeftMovies[0];
            if (str_contains($move, "*")){
                $result[] = $move;
            }
        }

        if (count($backwardRightMovies) === 1){
            $move = $backwardRightMovies[0];
            if (str_contains($move, "*")){
                $result[] = $move;
            }
        }

        return $result;
    }

    private function ConstructRoyalCheckerMovies(
        array $backwardLeftMovies, array $backwardRightMovies,
        array $forwardRightMovies, array $forwardLeftMovies): array
    {
        $result = [];

        $result = $this->AddMovies($result, $forwardLeftMovies);
        $result = $this->AddMovies($result, $forwardRightMovies);
        $result = $this->AddMovies($result, $backwardLeftMovies);
        $result = $this->AddMovies($result, $backwardRightMovies);

        return $result;
    }

    private function AddMovies(array $movies, array $pushMovies, int $maxPushCount = 8*8*8):array
    {
        if (count($pushMovies) === 0){
            return $movies;
        }

        while (count($pushMovies) > $maxPushCount){
            array_pop($pushMovies);
        }

        foreach ($pushMovies as $movie){
            $movies[] = $movie;
        }

        return $movies;
    }

    private function GetMovies(
        int $checkerRow, int $checkerColumn,
        bool $isCheckerBlack, int $maxLength, MoveDirectionType $directionType):array
    {
        $result = [];
        $turn = "-";
        $checkerAddress = ChessAddressTranslator::GetChessAddress($checkerRow, $checkerColumn);
        $rowDirection =
            $directionType === MoveDirectionType::TOP_LEFT ||
            $directionType === MoveDirectionType::TOP_RIGHT ? 1 : -1;
        $columnDirection =
            $directionType === MoveDirectionType::BOTTOM_RIGHT ||
            $directionType === MoveDirectionType::TOP_RIGHT ? 1 : -1;

        for ($counter = 1; $counter <= $maxLength; $counter++) {
            $checkRow = $checkerRow + $rowDirection * $counter;
            $checkColumn = $checkerColumn + $columnDirection * $counter;

            if ($this->IsOutOfBoard($checkRow, $checkColumn)){
                break;
            }

            $boardCell = $this->_boardRows[$checkRow]->getBoardCells()[$checkColumn];

            if ($boardCell->IsFreeCell()){
                $result[] =
                    $checkerAddress.$turn.ChessAddressTranslator::GetChessAddress($checkRow, $checkColumn);

                if ($turn === "*"){
                    break;
                }

                continue;
            }

            if ($boardCell->getChecker()->getIsBlack() === $isCheckerBlack) {
                break;
            } else {
                if ($turn === "*"){
                    break;
                }
                $turn = "*";
            }
        }

        return $result;
    }

    private function IsOutOfBoard(int $row, int $column): bool
    {
        return $row < 0 || $column < 0 || $row > 7 || $column > 7;
    }

}