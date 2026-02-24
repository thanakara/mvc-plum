<?php

declare(strict_types=1);

namespace App;

// use App\Databases\PDODatabase;
use App\Databases\DBALDatabase;


abstract class Model
{
    // protected PDODatabase $pdoDB;
    protected DBALDatabase $dbalDB;

    public function __construct()
    {
        // $this->pdoDB = App::proxy();
        $this->dbalDB = App::proxy();
    }
}
