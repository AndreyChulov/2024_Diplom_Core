<?php

namespace Classes;

require_once dirname(__FILE__).'/Settings.php';

use Classes\Settings;

class Globals
{
    private const SETTINGS_KEY = "Settings";
    private Settings $_settings;

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
}