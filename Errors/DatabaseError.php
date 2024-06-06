<?php

namespace Errors;

use \SQLite3;

class DatabaseError extends \Error
{
    public function __construct(SQLite3 $sqLite3, string $message){
        $lastSqlLite3Error = $sqLite3->lastErrorMsg();
        $errorMessage = <<<MESSAGE
        
            DatabaseError:
                message: $message, 
                lastSqlLite3Error: $lastSqlLite3Error
        MESSAGE;

        parent::__construct($errorMessage);
    }
}