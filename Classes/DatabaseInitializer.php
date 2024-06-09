<?php

namespace Classes;

require_once dirname(__FILE__).'/../Classes/Globals.php';

use Classes\Globals;
use SQLite3;

class DatabaseInitializer
{
    private Globals $_globals;

    public function __construct(){
        $this->_globals = new Globals();
    }

    public function InitDatabase(): void
    {
        $this->DeleteDatabaseFile();
        $this->CreateDatabaseFile();
        $this->ConstructDatabase();
    }

    private function DeleteDatabaseFile(): void
    {
        $databaseFilePath = $this->_globals->getSettings()->DATABASE_FILE;
        if (file_exists($databaseFilePath)) {
            unlink(realpath($databaseFilePath));
        }
    }

    private function CreateDatabaseFile(): void
    {
        $databaseFilePath = $this->_globals->getSettings()->DATABASE_FILE;
        if (!file_exists($databaseFilePath)) {
            $file = fopen($databaseFilePath, "w");
            fclose($file);
        }
    }

    private function ConstructDatabase(): void
    {
        $this->CreateProfilesTable();
        $this->CreateCredentialsTable();
        $this->CreateAuthorizeKeysTable();
        $this->CreateGamesTable();
    }

    private function CreateCredentialsTable(): void
    {
        $database = new SQLite3($this->_globals->getSettings()->DATABASE_FILE);
        $query = <<<QUERY
            CREATE TABLE IF NOT EXISTS Credentials (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                PassHash TEXT,
                ProfileId INTEGER NOT NULL REFERENCES Profiles(Id)
            )
        QUERY;
        $database->exec($query);
        $database->close();
    }

    private function CreateProfilesTable(): void
    {
        $database = new SQLite3($this->_globals->getSettings()->DATABASE_FILE);
        $query = <<<QUERY
            CREATE TABLE IF NOT EXISTS Profiles (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                Name TEXT,
                Login TEXT,
                Phone TEXT
            )
        QUERY;
        $database->exec($query);
        $database->close();
    }

    private function CreateAuthorizeKeysTable(): void
    {
        $database = new SQLite3($this->_globals->getSettings()->DATABASE_FILE);
        $query = <<<QUERY
            CREATE TABLE IF NOT EXISTS AuthorizeKeys (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                ProfileId INTEGER NOT NULL REFERENCES Profiles(Id),
                Key TEXT,
                Created TIME,
                ValidUntil TIME
            )
        QUERY;
        $database->exec($query);
        $database->close();
    }

    private function CreateGamesTable(): void
    {
        $database = new SQLite3($this->_globals->getSettings()->DATABASE_FILE);
        $query = <<<QUERY
            CREATE TABLE IF NOT EXISTS Games (
                Id INTEGER PRIMARY KEY AUTOINCREMENT,
                WhitePlayerProfileId INTEGER NOT NULL REFERENCES Profiles(Id),
                BlackPlayerProfileId INTEGER NULL REFERENCES Profiles(Id),
                GameKey TEXT,
                Status TEXT,
                Board TEXT,
                Created TIME
            )
        QUERY;
        $database->exec($query);
        $database->close();
    }
}