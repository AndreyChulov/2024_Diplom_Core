<?php

namespace Classes;

require_once dirname(__FILE__).'/Globals.php';

use Classes\Globals;

class Status
{
    private Globals $_globals;

    public function __construct()
    {
        $this->_globals = new Globals();
    }

    public function IsDatabaseExists(): bool{
        $databaseFile = $this->_globals->getSettings()->DATABASE_FILE;

        return file_exists($databaseFile);
    }

    public function IsSqlLite3ClassLoaded(): string{
        return class_exists("SQLite3");
    }
}