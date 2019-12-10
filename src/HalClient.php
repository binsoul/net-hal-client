<?php

declare(strict_types=1);

namespace BinSoul\Net\Hal\Client;

use BinSoul\Net\Hal\Client\Exception\BadResponseException;
use BinSoul\Net\Hal\Client\Exception\HttpClientException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Throwable;

/**
 *  Sends requests and returns resources.
 */
class HalClient
{
    /**
     * @var string[]
     */
    private static $validContentTypes = [
        'application/hal+json',
        'application/json',
        'application/vnd.error+json',
        'application/problem+json',
    ];

    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var HalResourceFactory
     */
    private $resourceFactory;
    /**
     * @var UriInterface
     */
    private $endPointUri;
    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;
    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    /**
     * Constructs an instance of this class.
     *
     * @param string|UriInterface $endPointUri
     */
    public function __construct(
        $endPointUri,
        HalResourceFactory $halResourceFactory,
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory
    ) {
        $this->endPointUri = $endPointUri instanceof UriInterface ? $endPointUri : $uriFactory->createUri($endPointUri);
        $this->client = $client;
        $this->resourceFactory = $halResourceFactory;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
    }

    /**
     * @param mixed[] $options
     */
    public function get(string $uri, array $options = []): HalResource
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * Executes a GET-Request and returns the resource.
     *
     * @param mixed[] $options
     */
    public function post(string $uri, array $options = []): HalResource
    {
        return $this->request('POST', $uri, $options);
    }

    /**
     * Executes a PUT-Request and returns the resource.
     *
     * @param mixed[] $options
     */
    public function put(string $uri, array $options = []): HalResource
    {
        return $this->request('PUT', $uri, $options);
    }

    /**
     * Executes a DELETE-Request and returns the resource.
     *
     * @param mixed[] $options
     */
    public function delete(string $uri, array $options = []): HalResource
    {
        return $this->request('DELETE', $uri, $options);
    }

    /**
     * Executes a request with the given HTTP method and returns the resource.
     *
     * @param mixed[] $options
     */
    public function request(string $method, string $uri, array $options = []): HalResource
    {
        $request = $this->createRequest($method, $uri, $options);

        try {
            $response = $this->client->sendRequest($request);
        } catch (Throwable $e) {
            throw HttpClientException::create($request, $e);
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode === 204) {
            return $this->resourceFactory->createResource([]);
        }

        if ($statusCode >= 300 && $statusCode < 400) {
            if ($response->hasHeader('Location')) {
                return $this->request('GET', $response->getHeader('Location')[0]);
            }

            throw new BadResponseException('No location found in redirect.', $request, $response, $this->resourceFactory->createResource([]));
        }

        try {
            $body = $response->getBody()->getContents();
        } catch (Throwable $e) {
            throw new BadResponseException(sprintf('Error getting response body: %s.', $e->getMessage()), $request, $response, $this->resourceFactory->createResource([]), $e);
        }

        $data = [];
        if (trim($body) !== '') {
            $data = @json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                if ($this->isValidContentType($response)) {
                    throw new BadResponseException(sprintf('JSON parse error: %s.', json_last_error_msg()), $request, $response, $this->resourceFactory->createResource([]));
                }

                $data = [];
            }
        }

        if ($statusCode >= 200 && $statusCode < 300) {
            if ($statusCode === 201 && count($data) === 0 && $response->hasHeader('Location')) {
                return $this->request('GET', $response->getHeader('Location')[0]);
            }

            if (!$this->isValidContentType($response)) {
                $types = $response->getHeader('Content-Type') ?: ['none'];

                throw new BadResponseException(sprintf('Invalid content type: %s.', implode(', ', $types)), $request, $response, $this->resourceFactory->createResource([]));
            }

            return $this->resourceFactory->createResource($data);
        }

        if ($this->isValidContentType($response)) {
            $resource = $this->resourceFactory->createResource($data);
        } else {
            $resource = $this->resourceFactory->createResource([]);
        }

        throw BadResponseException::create($request, $response, $resource);
    }

    /**
     * Generates a request object for the given parameters.
     *
     * @param mixed[] $options
     */
    private function createRequest(string $method, string $uri, array $options = []): RequestInterface
    {
        $basePath = $this->endPointUri->getPath();
        $targetPath = $basePath.'/'.ltrim(str_replace($basePath, '', $uri), '/');

        $request = $this->requestFactory->createRequest($method, $this->endPointUri->withPath($targetPath))
            ->withHeader('User-Agent', get_class($this))
            ->withHeader('Accept', implode(', ', self::$validContentTypes));

        if (isset($options['version'])) {
            $request = $request->withProtocolVersion($options['version']);
        }

        if (isset($options['query'])) {
            $currentUri = $request->getUri();

            if (!is_array($options['query'])) {
                parse_str($options['query'], $options['query']);
            }

            parse_str($currentUri->getQuery(), $newQuery);
            $newQuery = array_merge($newQuery, $options['query']);

            $request = $request->withUri($currentUri->withQuery(http_build_query($newQuery, '', '&')));
        }

        if (isset($options['headers'])) {
            foreach ($options['headers'] as $name => $value) {
                $request = $request->withHeader($name, $value);
            }
        }

        if (isset($options['body'])) {
            if (is_array($options['body']) || is_object($options['body'])) {
                $body = @json_encode($options['body']);
            } else {
                $body = $options['body'];
            }

            if (!$request->hasHeader('Content-Type')) {
                $request = $request->withHeader('Content-Type', 'application/json');
            }

            $request->getBody()->write($body);
        }

        return $request;
    }

    /**
     * Determines if the response has a valid content type.
     */
    private function isValidContentType(ResponseInterface $response): bool
    {
        $contentTypeHeaders = $response->getHeader('Content-Type');
        foreach ($contentTypeHeaders as $index => $header) {
            $parts = explode(';', $header);
            $contentTypeHeaders[$index] = $parts[0];
        }

        foreach (self::$validContentTypes as $validContentType) {
            if (in_array($validContentType, $contentTypeHeaders, true)) {
                return true;
            }
        }

        return false;
    }
}
