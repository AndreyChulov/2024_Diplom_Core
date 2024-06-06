<?php

namespace Classes;

$currentFolder = dirname(__FILE__);
require_once "$currentFolder/Settings.php";
require_once "$currentFolder/Database.php";

use Classes\Database;
use Classes\Settings;

class Globals
{
    private const SETTINGS_KEY = "Settings";
    private const DATABASE_KEY = "Database";
    private Settings $_settings;
    private Database $_database;

    public function __construct(){
        if (isset($GLOBALS[self::SETTINGS_KEY]))
        {
            $this->_settings = $GLOBALS[self::SETTINGS_KEY];
        } else {
            $this->_settings = new Settings();
            $GLOBALS[self::SETTINGS_KEY] = $this->_settings;
        }

    }

    public function getSettings(): Settings{
        return $this->_settings;
    }

    public function getDatabase(): Database
    {
        if (isset($GLOBALS[self::DATABASE_KEY]))
        {
            if (!isset($this->_database)){
                $this->_database = $GLOBALS[self::DATABASE_KEY];
            }
        } else {
            $this->_database = new Database($this->_settings->DATABASE_FILE);
            $GLOBALS[self::DATABASE_KEY] = $this->_database;
        }

        return $this->_database;
    }}