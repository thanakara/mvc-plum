<?php

declare(strict_types=1);

namespace App\Services\AbstractApi;

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ConnectException;
use App\Contracts\EmailValidationInterface;

class GuzzleEmailValidationService implements EmailValidationInterface
{
    private string $baseUrl = "https://emailreputation.abstractapi.com/v1/";

    public function __construct(private string $apiKey) {}

    public function verify(string $email): array
    {
        $stack = HandlerStack::create();
        $maxRetry = 3;
        $stack->push(middleware: $this->getRetryMiddleware($maxRetry));

        $client = new Client(
            [
                "base_uri" => $this->baseUrl,
                "timeout" => 5,
                "handler" => $stack,
            ]
        );

        $params = [
            "api_key" => $this->apiKey,
            "email" => $email,
        ];

        $response = $client->get(
            uri: "",
            options: ["query" => $params]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    private function getRetryMiddleware(int $maxRetry)
    {
        return Middleware::retry(
            decider: function (
                int $retries,
                RequestInterface $request,
                ?ResponseInterface $response = null,
                ?\RuntimeException $e = null
            ) use ($maxRetry) {
                if ($retries > $maxRetry) {
                    return false;
                }

                if ($response && in_array($response->getStatusCode(), [249, 429, 503])) {
                    // echo "Retrying [" . $retries . "], Status: " . $response->getStatusCode() . "<br />";

                    return true;
                }

                if ($e instanceof ConnectException) {
                    // echo "Retrying [" . $retries . "], Connection Error<br />";

                    return true;
                }
                // echo "Not Retrying <br />";

                return false;
            }
        );
    }
}
