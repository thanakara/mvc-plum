<?php

declare(strict_types=1);

namespace App\Services\Emailable;

use App\Contracts\EmailValidationInterface;

class EmailValidationService implements EmailValidationInterface
{
    private string $baseUrl = "https://api.emailable.com/v1/";

    public function __construct(private string $apiKey) {}

    public function verify(string $email): array
    {
        $params = [
            "api_key" => $this->apiKey,
            "email" => $email,
        ];

        $url = $this->baseUrl . "verify?" . http_build_query($params);

        $handle = curl_init();
        // set CurlHandle options
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $content = curl_exec($handle); // JSON-Response
        if ($content != false) {
            return json_decode($content, associative: true);
        }
        return [];
    }
}
