<?php

require_once dirname(__FILE__).'/../Classes/Status.php';

use Classes\Status;

$status = new Status();

?>
{
    "DB":<?php echo $status->IsDatabaseExists() ? "true" : "false" ?>,
    "SQLite3":<?php echo $status->IsSqlLite3ClassLoaded() ? "true" : "false" ?>
}