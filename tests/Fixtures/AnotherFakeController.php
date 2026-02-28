<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Attributes\Get;
use App\Attributes\Post;

class AnotherFakeController
{
    #[Get("/anotherfake")]
    public function index(): string
    {
        return "anotherfake.html.twig";
    }

    #[Post("/anotherfake/store")]
    public function store(): string
    {
        return "anotherfake.html.twig";
    }
}
