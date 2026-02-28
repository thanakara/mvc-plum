<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Attributes\Get;
use App\Attributes\Post;

class FakeController
{
    #[Get("/fake")]
    public function index(): string
    {
        return "fake.html.twig";
    }

    #[Post("/fake/store")]
    public function store(): string
    {
        return "store.html.twig";
    }

    public function notARoute(): string
    {
        return "[Err]: NoAttrHere";
    }
}
