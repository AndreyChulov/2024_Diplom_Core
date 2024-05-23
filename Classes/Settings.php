<?php

namespace Classes;

class Settings
{
    public string $DATABASE_FILE;

    public function __construct()
    {
        $this->DATABASE_FILE = dirname(__FILE__).'/../Database/db.sqlite';
    }
}