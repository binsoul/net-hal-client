<?php

declare(strict_types=1);

namespace BinSoul\Test\Net\Hal\Client;

use BinSoul\Net\Hal\Client\DefaultHalResourceFactory;
use PHPUnit\Framework\TestCase;

class HalResourceTest extends TestCase
{
    public function test_handles_unknown_links(): void
    {
        $factory = new DefaultHalResourceFactory();
        $resource = $factory->createResource(json_decode(file_get_contents(__DIR__ . '/fixtures/basic.json'), true));

        $this->assertFalse($resource->hasLink('foobar'));
        $this->assertCount(0, $resource->getLink('foobar'));
        $this->assertNull($resource->getFirstLink('foobar'));
    }

    public function test_handles_unknown_subresources(): void
    {
        $factory = new DefaultHalResourceFactory();
        $resource = $factory->createResource(json_decode(file_get_contents(__DIR__ . '/fixtures/basic.json'), true));

        $this->assertFalse($resource->hasResource('foobar'));
        $this->assertCount(0, $resource->getResource('foobar'));
        $this->assertNull($resource->getFirstResource('foobar'));
    }

    public function test_handles_missing_curries(): void
    {
        $factory = new DefaultHalResourceFactory();
        $resource = $factory->createResource(json_decode(file_get_contents(__DIR__ . '/fixtures/underscored_property.json'), true));

        $this->assertFalse($resource->hasResource('foobar'));
        $this->assertCount(0, $resource->getResource('foobar'));
        $this->assertNull($resource->getFirstResource('foobar'));

        $this->assertFalse($resource->hasLink('foobar'));
        $this->assertCount(0, $resource->getLink('foobar'));
        $this->assertNull($resource->getFirstLink('foobar'));
    }

    public function test_resolves_subresource_curies(): void
    {
        $factory = new DefaultHalResourceFactory();
        $resource = $factory->createResource(json_decode(file_get_contents(__DIR__ . '/fixtures/subresource.json'), true));

        $this->assertTrue($resource->hasResource('user'));
    }

    public function test_resolves_link_curies(): void
    {
        $factory = new DefaultHalResourceFactory();
        $resource = $factory->createResource(json_decode(file_get_contents(__DIR__ . '/fixtures/basic.json'), true));

        $this->assertTrue($resource->hasLink('parent'));
        $this->assertTrue($resource->hasLink('users'));
    }

    public function test_is_serializable(): void
    {
        $factory = new DefaultHalResourceFactory();
        $data = json_decode(file_get_contents(__DIR__ . '/fixtures/multiple_nested_subresources.json'), true);
        $resource = $factory->createResource($data);

        $this->assertEquals($data, $resource->toArray());
    }
}
