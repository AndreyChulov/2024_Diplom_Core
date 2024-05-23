<?php
require_once dirname(__FILE__).'/../Classes/DatabaseInitializer.php';

use Classes\DatabaseInitializer;

$databaseInitializer = new DatabaseInitializer();
$databaseInitializer->InitDatabase();
?>
{
    "status":"done"
}
