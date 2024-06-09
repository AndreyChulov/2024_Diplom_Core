<?php

namespace Classes;

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../Errors/DatabaseError.php";
require_once "$currentFolder/../DataModels/ProfileDataModel.php";
require_once "$currentFolder/../DataModels/GameInfoDataModel.php";
require_once "$currentFolder/../Classes/Strings.php";

use DataModels\ProfileDataModel;
use DataModels\GameInfoDataModel;
use Classes\Strings;
use \Errors\DatabaseError;
use \SQLite3;

class Database
{
    private SQLite3 $_sqlite;
    private string $databaseFile;

    public function __construct(string $databaseFile){
        $this->_sqlite = new SQLite3($databaseFile);
    }

    public function __destruct()
    {
        $this->_sqlite->close();
    }

    public function IsProfileExists(string $login):bool
    {
        $query = <<<QUERY
            SELECT count(*)
            FROM Profiles
            WHERE Login = "$login"
        QUERY;

        $result = $this->_sqlite->query($query);

        if ($result === false){
            throw new DatabaseError($this->_sqlite, "[IsProfileExists] Unexpected sqLite3 answer");
        }

        return $result->fetchArray()[0] > 0;
    }

    public function CreateProfile(string $userName, string $login, string $passwordHash, string $telephone):void
    {
        $query = <<<QUERY
            BEGIN TRANSACTION;        

            INSERT INTO Profiles(Name, Login, Phone)
            VALUES('$userName', '$login', '$telephone');
            
            INSERT INTO Credentials(PassHash, ProfileId)
            VALUES('$passwordHash', last_insert_rowid());
            
            COMMIT;
        QUERY;

        $result = $this->_sqlite->exec($query);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[CreateProfile] Unexpected sqLite3 answer");
        }
    }

    public function ValidateCredentials(string $login, string $passwordHash):bool
    {
        $query = <<<QUERY
            SELECT count(*)
            FROM Profiles AS p, Credentials AS c
            WHERE p.Login = '$login' AND c.PassHash = '$passwordHash' AND p.Id = c.ProfileId;
        QUERY;

        $result = $this->_sqlite->query($query);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[ValidateCredentials] Unexpected sqLite3 answer");
        }

        return $result->fetchArray()[0] === 1;
    }

    public function ValidateAuthorizationKey(string $login, string $key):bool
    {
        $query = <<<QUERY
            SELECT count(*)
            FROM Profiles AS p, AuthorizeKeys AS ak
            WHERE p.Login = '$login' AND ak.Key = '$key' AND p.Id = ak.ProfileId;
        QUERY;

        $result = $this->_sqlite->query($query);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[ValidateAuthorizationKey] Unexpected sqLite3 answer");
        }

        return $result->fetchArray()[0] === 1;
    }

    public function AddAuthorizeKey(string $login, string $key, int $keyValidHours):void
    {
        $query = <<<QUERY
            BEGIN TRANSACTION;
            
                INSERT INTO AuthorizeKeys(ProfileId, Key, Created, ValidUntil)
                SELECT Id AS ProfileId, 
                    '$key' AS Key, 
                    DATETIME('NOW') AS Created, 
                    DATETIME('NOW', '+$keyValidHours hour') AS ValidUntil
                FROM Profiles 
                WHERE Login = '$login';
        
            COMMIT;
        QUERY;

        $result = $this->_sqlite->exec($query);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[AddAuthorizeKey] Unexpected sqLite3 answer");
        }
    }

    public function GetAuthorizeKeyTimeout(string $login, string $key):string
    {
        $query = <<<QUERY
            SELECT TIME(STRFTIME('%s', ak.ValidUntil) - STRFTIME('%s', 'NOW'), 'unixepoch')
            FROM Profiles AS p, AuthorizeKeys AS ak
            WHERE p.Login = '$login' AND ak.Key = '$key' AND p.Id = ak.ProfileId;
        QUERY;

        $result = $this->_sqlite->querySingle($query);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[GetAuthorizeKeyTimeout] Unexpected sqLite3 answer");
        }

        return $result;
    }

    public function RemoveAuthorizeKeys(string $login):void
    {
        $query = <<<QUERY
            DELETE FROM AuthorizeKeys
            WHERE ProfileId IN (
                SELECT Id
                FROM Profiles
                WHERE Login = '$login');
        QUERY;

        $result = $this->_sqlite->exec($query);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[RemoveAuthorizeKeys] Unexpected sqLite3 answer");
        }
    }

    public function GetProfileInfo(string $login):ProfileDataModel
    {
        $query = <<<QUERY
            SELECT *
            FROM Profiles
            WHERE Login = '$login'   
        QUERY;

        $result = $this->_sqlite->querySingle($query, true);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[RemoveAuthorizeKeys] Unexpected sqLite3 answer");
        }

        return new ProfileDataModel($result['Name'], $result['Login'], $result['Phone']);
    }

    public function CreateGame(string $login, string $gameKey, string $board):void
    {
        $status = Strings::$GAME_STATUS_WAIT_FOR_PLAYER;
        $query = <<<QUERY
            INSERT INTO Games(WhitePlayerProfileId, GameKey, Status, Board, Created)
            SELECT 
                Id AS WhitePlayerProfileId, 
                '$gameKey' AS GameKey,
                '$status' AS Status, 
                '$board' AS Board, 
                DATETIME('NOW') AS Created
            FROM Profiles
            WHERE Login = '$login'   
        QUERY;

        $result = $this->_sqlite->querySingle($query, true);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[RemoveAuthorizeKeys] Unexpected sqLite3 answer");
        }
    }

    public function IsGameExist(string $login):bool
    {
        $blackWinStatus = Strings::$GAME_STATUS_BLACK_WIN;
        $whiteWinStatus = Strings::$GAME_STATUS_WHITE_WIN;
        $finishedByAdminStatus = Strings::$GAME_STATUS_FINISHED_BY_ADMIN;

        $query = <<<QUERY
            SELECT count(*)
            FROM Games AS g, Profiles AS p
            WHERE p.Login = '$login' AND 
                  p.Id IN (g.WhitePlayerProfileId, g.BlackPlayerProfileId) AND
                  g.Status NOT IN ('$blackWinStatus', '$whiteWinStatus', '$finishedByAdminStatus');
        QUERY;

        $result = $this->_sqlite->querySingle($query);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[RemoveAuthorizeKeys] Unexpected sqLite3 answer");
        }

        return $result > 0;
    }

    public function GetGameInfo(string $login):GameInfoDataModel
    {
        $blackWinStatus = Strings::$GAME_STATUS_BLACK_WIN;
        $whiteWinStatus = Strings::$GAME_STATUS_WHITE_WIN;
        $finishedByAdminStatus = Strings::$GAME_STATUS_FINISHED_BY_ADMIN;

        $query = <<<QUERY
            SELECT 
                pw.Login AS WhitePlayerLogin, 
                pb.Login AS BlackPlayerLogin, 
                g.GameKey AS GameKey, 
                g.Status AS GameStatus, 
                g.Board AS Board, 
                g.Created AS GameCreated
            FROM Games AS g, Profiles AS p, Profiles AS pw, Profiles AS pb
            WHERE p.Login = '$login' AND 
                  p.Id IN (g.WhitePlayerProfileId, g.BlackPlayerProfileId) AND
                  g.Status NOT IN ('$blackWinStatus', '$whiteWinStatus', '$finishedByAdminStatus') AND
                  pw.Id = g.WhitePlayerProfileId AND 
                  (pb.Id = g.BlackPlayerProfileId OR g.BlackPlayerProfileId IS NULL);
        QUERY;

        $result = $this->_sqlite->querySingle($query, true);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[RemoveAuthorizeKeys] Unexpected sqLite3 answer");
        }

        return new GameInfoDataModel(
            $result['WhitePlayerLogin'],
            $result['GameStatus'] === Strings::$GAME_STATUS_WAIT_FOR_PLAYER ?
                "" : $result['BlackPlayerLogin'],
            $result['GameKey'],
            $result['GameStatus'],
            $result['Board'],
            $result['GameCreated'],
        );
    }

    public function ForceFinishGames(string $login):void
    {
        $blackWinStatus = Strings::$GAME_STATUS_BLACK_WIN;
        $whiteWinStatus = Strings::$GAME_STATUS_WHITE_WIN;
        $finishedByAdminStatus = Strings::$GAME_STATUS_FINISHED_BY_ADMIN;

        $query = <<<QUERY
            UPDATE Games
            SET Status = '$finishedByAdminStatus'
            FROM Profiles AS p
            WHERE p.Login = '$login' AND p.Id IN (WhitePlayerProfileId, BlackPlayerProfileId) AND
                  Status NOT IN ('$blackWinStatus', '$whiteWinStatus', '$finishedByAdminStatus');
        QUERY;

        $result = $this->_sqlite->exec($query);

        if ($result === false){
            throw new DatabaseError($this->_sqlite,
                "[ForceFinishGames] Unexpected sqLite3 answer");
        }
    }
}