<?php

declare(strict_types=1);

namespace App;

use App\Databases\PDODatabase;


abstract class Model
{
    protected PDODatabase $pdoDB;

    public function __construct()
    {
        $this->pdoDB = App::proxy();
    }
}
