<?php

namespace Classes;

class Settings
{
    public string $DATABASE_FILE;
    public int $AUTHORIZATION_KEY_VALID_HOURS = 3;

    public function __construct()
    {
        $this->DATABASE_FILE = dirname(__FILE__).'/../Database/db.sqlite';
    }
}