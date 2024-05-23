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
            unlink($databaseFilePath);
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
}