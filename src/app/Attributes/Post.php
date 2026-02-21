<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;


#[Attribute(Attribute::TARGET_METHOD)]
class Post extends Route
{
    public function __construct(public string $route)
    {
        parent::__construct(route: $route, method: "post");
    }
}
