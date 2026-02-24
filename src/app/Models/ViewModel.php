<?php

declare(strict_types=1);

namespace App\Models;

use App\Model;
use InvalidArgumentException;

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
        $allowedViews = ["active_users"];
        if (!in_array($viewName, $allowedViews, true)) {
            throw new InvalidArgumentException("Invalid view name: $viewName");
        }

        // $stmt = $this->pdoDB->query("SELECT * FROM plum." . $viewName);
        $builder = $this->dbalDB->createQueryBuilder();
        $stmt = $builder
            ->select("*")
            ->from("plum." . $viewName);

        // return $stmt->fetchAll();
        return $stmt->fetchAllAssociative();
    }
}
