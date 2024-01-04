<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\App\Payum\HttpClient;

use Payum\Core\HttpClientInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class HttpClient implements HttpClientInterface
{
    /** @var ClientInterface */
    private $client;
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
