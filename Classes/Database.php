<?php

namespace Classes;

require_once dirname(__FILE__).'/../Errors/DatabaseError.php';

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
}