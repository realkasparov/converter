<?php
declare(strict_types=1);

namespace App\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for http based services.
 */
abstract class HttpService
{
    /**
     * The client.
     *
     * @var ClientInterface
     */
    private ClientInterface $httpClient;

    /**
     * @param ClientInterface|null $httpClient
     */
    public function __construct(ClientInterface $httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = new Client();
        } elseif (!$httpClient instanceof ClientInterface) {
            throw new \LogicException('Client must be an instance of GuzzleHttp\\ClientInterface');
        }

        $this->httpClient = $httpClient;
    }

    /**
     * Fetches the content of the given url.
     *
     * @param string $url
     *
     * @return string
     */
    protected function request(string $url): string
    {
        return $this->getResponse($url)->getBody()->__toString();
    }

    /**
     * Fetches the content of the given url.
     *
     * @param string $url
     *
     * @return ResponseInterface
     */
    protected function getResponse($url): ResponseInterface
    {
        return $this->httpClient->get($url);
    }
}