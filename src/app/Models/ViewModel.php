<?php

declare(strict_types=1);

namespace App\Models;

use App\Model;

/**
 * Extends ABC Model which initializes a PDODatabase proxy
 * The property is under: $pdoDB
 */
class ViewModel extends Model
{
    /**
     * This command returns all views in plum schema in psql cli:
     * ```bash
     * \dv plum.*
     * ```
     */
    public function select(string $viewName): array
    {
        $stmt = $this->pdoDB->query("SELECT * FROM plum." . $viewName);
        return $stmt->fetchAll();
    }
}
