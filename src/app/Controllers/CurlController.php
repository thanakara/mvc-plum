<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Attributes\Get;
use App\Contracts\EmailValidationInterface;

class CurlController
{
    public function __construct(
        private EmailValidationInterface $emailService
    ) {}

    #[Get("/curl")]
    public function index(): void
    {
        header("Content-Type: application/json");

        $email = "alice@yahoo.com"; // hardcode for now
        $result = $this->emailService->verify($email);

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
