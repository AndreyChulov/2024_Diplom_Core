<?php

namespace Classes\Game;

class ChessAddressTranslator
{
    static array $_rowAddressToIndexDictionary = [
        "A" => 0,
        "B" => 1,
        "C" => 2,
        "D" => 3,
        "E" => 4,
        "F" => 5,
        "G" => 6,
        "H" => 7
    ];
    static array $_rowIndexToAddressDictionary =
        ["A", "B", "C", "D", "E", "F", "G", "H"];
    static array $_columnAddressToIndexDictionary = [
        "1" => 0,
        "2" => 1,
        "3" => 2,
        "4" => 3,
        "5" => 4,
        "6" => 5,
        "7" => 6,
        "8" => 7
    ];
    static array $_columnIndexToAddressDictionary =
        ["1", "2", "3", "4", "5", "6", "7", "8"];

    public static function GetRowIndex(string $chessAddress):int
    {
        return self::$_rowAddressToIndexDictionary[$chessAddress[0]];
    }

    public static function GetColumnIndex(string $chessAddress):int
    {
        return self::$_columnAddressToIndexDictionary[$chessAddress[1]];
    }

    public static function GetChessAddress(int $row, int $column):string
    {
        return self::$_rowIndexToAddressDictionary[$row].self::$_columnIndexToAddressDictionary[$column];
    }

    public static function IsChessAddressValid(string $chessAddress):bool
    {
        return
            strlen($chessAddress) === 2 &&
            in_array($chessAddress[0], self::$_rowIndexToAddressDictionary) &&
            in_array($chessAddress[1], self::$_columnIndexToAddressDictionary);
    }
}