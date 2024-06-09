<?php

namespace DataModels;

class GameInfoDataModel
{
    private string $_whitePlayerLogin;
    private string $_blackPlayerLogin;
    private string $_gameKey;
    private string $_gameStatus;
    private string $_board;
    private string $_gameCreated;

    public function getWhitePlayerLogin(): string
    {
        return $this->_whitePlayerLogin;
    }

    public function getBlackPlayerLogin(): string
    {
        return $this->_blackPlayerLogin;
    }

    public function getGameKey(): string
    {
        return $this->_gameKey;
    }

    public function getGameStatus(): string
    {
        return $this->_gameStatus;
    }

    public function getBoard(): string
    {
        return $this->_board;
    }

    public function getGameCreated(): string
    {
        return $this->_gameCreated;
    }

    public function __construct(
        string $whitePlayerLogin = "",
        string $blackPlayerLogin = "",
        string $gameKey = "",
        string $gameStatus = "",
        string $board = "",
        string $gameCreated = "")
    {
        $this->_whitePlayerLogin = $whitePlayerLogin;
        $this->_blackPlayerLogin = $blackPlayerLogin;
        $this->_gameKey = $gameKey;
        $this->_gameStatus = $gameStatus;
        $this->_board = $board;
        $this->_gameCreated = $gameCreated;
    }
}