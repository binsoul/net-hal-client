<?php

declare(strict_types=1);

namespace BinSoul\Test\Net\Hal\Client;

use BinSoul\Net\Hal\Client\DefaultHalResourceFactory;
use BinSoul\Net\Hal\Client\Exception\BadResponseException;
use BinSoul\Net\Hal\Client\Exception\HttpClientException;
use BinSoul\Net\Hal\Client\HalClient;
use BinSoul\Net\Hal\Client\HalLink;
use BinSoul\Net\Hal\Client\HalResource;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\RequestException;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HalClientTest extends TestCase
{
    public function test_basic(): void
    {
        $client = $this->buildClient(new Response(200, ['Content-Type' => 'application/hal+json'], file_get_contents(__DIR__.'/fixtures/basic.json')));
        $resource = $client->get('/customer/123456');

        $this->assertTrue($resource->hasLinks());
        $this->assertCount(4, $resource->getLinks());

        $this->assertFalse($resource->hasLink('foobar'));
        $this->assertCount(0, $resource->getLink('foobar'));

        $this->assertTrue($resource->hasLink('self'));
        $this->assertTrue($resource->hasLink('ns:parent'));
        $this->assertTrue($resource->hasLink('ns:users'));
        $this->assertTrue($resource->hasLink('curies'));
        $this->assertCount(2, $resource->getLink('curies'));
        $this->assertInstanceOf(HalLink::class, $resource->getFirstLink('self'));

        $this->assertTrue($resource->hasProperties());
        $this->assertCount(5, $resource->getProperties());

        $this->assertFalse($resource->hasProperty('foobar'));
        $this->assertNull($resource->getProperty('foobar'));
        $this->assertEquals(33, $resource->getProperty('age'));
        $this->assertFalse($resource->getProperty('expired'));
        $this->assertEquals(123456, $resource->getProperty('id'));
        $this->assertEquals('Example Resource', $resource->getProperty('name'));
        $this->assertTrue($resource->getProperty('optional'));

        $this->assertCount(0, $resource->getResources());
    }

    public function test_subresource(): void
    {
        $client = $this->buildClient(new Response(200, ['Content-Type' => 'application/hal+json'], file_get_contents(__DIR__.'/fixtures/subresource.json')));
        $resource = $client->get('/customer/123456');

        $this->assertTrue($resource->hasResources());
        $this->assertCount(1, $resource->getResources());
        $this->assertCount(1, $resource->getResource('ns:user'));
        $this->assertInstanceOf(HalResource::class, $resource->getFirstResource('ns:user'));
    }

    public function provideFixtures(): array
    {
        $result = [];

        $directory = __DIR__.'/fixtures';
        $files = array_diff(scandir($directory), ['..', '.']);

        foreach ($files as $file) {
            $name = str_replace('_', ' ', pathinfo($file, PATHINFO_FILENAME));
            $content = file_get_contents($directory.'/'.$file);
            $result[$name] = [$content];
        }

        return $result;
    }

    /**
     * @dataProvider provideFixtures
     */
    public function test_fixtures(string $content): void
    {
        $client = $this->buildClient(new Response(200, ['Content-Type' => 'application/hal+json'], $content));
        $resource = $client->get('/customer/123456');

        $this->assertTrue($resource->hasLink('self'));
    }

    public function test_request_exception(): void
    {
        $this->expectException(HttpClientException::class);

        $client = new Client();
        $client->addException(new RequestException('fail', new Request('/customer/123456', 'GET')));

        $resourceFactory = new DefaultHalResourceFactory();
        $requestFactory = new RequestFactory();
        $uriFactory = new UriFactory();

        $client = new HalClient($uriFactory->createUri('http://localhost/api'), $resourceFactory, $client, $requestFactory, $uriFactory);
        $client->get('/customer/123456');
    }

    public function test_found_wrong_content_type(): void
    {
        $this->expectException(BadResponseException::class);

        $client = $this->buildClient(new Response(200, ['Content-Type' => 'text/html'], '<html><body>Error</body></html>'));
        $client->get('/customer/123456');
    }

    public function test_internal_server_error_wrong_content_type(): void
    {
        $this->expectException(BadResponseException::class);

        $client = $this->buildClient(new Response(500, ['Content-Type' => 'text/html'], '<html><body>Error</body></html>'));
        $client->get('/customer/123456');
    }

    public function test_internal_server_error_right_content_type(): void
    {
        $this->expectException(BadResponseException::class);

        $client = $this->buildClient(new Response(500, ['Content-Type' => 'application/vnd.error+json'], '{"error": "gone"}'));
        $client->get('/customer/123456');
    }

    public function test_resource_gone_error(): void
    {
        $this->expectException(BadResponseException::class);

        $client = $this->buildClient(new Response(404, ['Content-Type' => 'application/hal+json'], '{"error": "gone"}'));
        $client->get('/customer/123456');
    }

    public function test_empty_response(): void
    {
        $client = $this->buildClient(new Response(204, ['Content-Type' => 'application/hal+json'], '{"error": "gone"}'));
        $resource = $client->get('/customer/123456');
        $this->assertCount(0, $resource->getProperties());
    }

    public function test_json_error(): void
    {
        $this->expectException(BadResponseException::class);

        $client = $this->buildClient(new Response(200, ['Content-Type' => 'application/hal+json'], '{"abc":}'));
        $client->get('/customer/123456');
    }

    private function buildClient(ResponseInterface $response): HalClient
    {
        $client = new Client();
        $client->addResponse($response);

        $resourceFactory = new DefaultHalResourceFactory();
        $requestFactory = new RequestFactory();
        $uriFactory = new UriFactory();

        $client = new HalClient($uriFactory->createUri('http://localhost/api'), $resourceFactory, $client, $requestFactory);

        return $client;
    }
}
