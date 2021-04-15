<?php

declare(strict_types=1);

namespace BinSoul\Net\Hal\Client\Exception;

use BinSoul\Net\Hal\Client\HalResource;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class BadResponseException extends RuntimeException
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var HalResource
     */
    private $resource;

    public function __construct(string $message, RequestInterface $request, ResponseInterface $response, HalResource $resource, ?Throwable $previous = null)
    {
        parent::__construct($message, $response->getStatusCode(), $previous);

        $this->request = $request;
        $this->response = $response;
        $this->resource = $resource;
    }

    public static function create(RequestInterface $request, ResponseInterface $response, HalResource $resource, ?Throwable $previous = null, ?string $message = null): self
    {
        if (! $message) {
            $code = $response->getStatusCode();

            if ($code >= 400 && $code < 500) {
                $message = 'Client error';
            } elseif ($code >= 500 && $code < 600) {
                $message = 'Server error';
            } else {
                $message = 'Unsuccessful response';
            }
        }

        $message = sprintf(
            '%s: %s %s  ->  %d (%s).',
            $message,
            $request->getMethod(),
            $request->getRequestTarget(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        return new self($message, $request, $response, $resource, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getResource(): HalResource
    {
        return $this->resource;
    }

    public function isClientError(): bool
    {
        return $this->getCode() >= 400 && $this->getCode() < 500;
    }

    public function isServerError(): bool
    {
        return $this->getCode() >= 500 && $this->getCode() < 600;
    }
}
