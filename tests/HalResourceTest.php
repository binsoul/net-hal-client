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

        self::assertFalse($resource->hasLink('foobar'));
        self::assertCount(0, $resource->getLink('foobar'));
        self::assertNull($resource->getFirstLink('foobar'));
    }

    public function test_handles_unknown_subresources(): void
    {
        $factory = new DefaultHalResourceFactory();
        $resource = $factory->createResource(json_decode(file_get_contents(__DIR__ . '/fixtures/basic.json'), true));

        self::assertFalse($resource->hasResource('foobar'));
        self::assertCount(0, $resource->getResource('foobar'));
        self::assertNull($resource->getFirstResource('foobar'));
    }

    public function test_handles_missing_curries(): void
    {
        $factory = new DefaultHalResourceFactory();
        $resource = $factory->createResource(json_decode(file_get_contents(__DIR__ . '/fixtures/underscored_property.json'), true));

        self::assertFalse($resource->hasResource('foobar'));
        self::assertCount(0, $resource->getResource('foobar'));
        self::assertNull($resource->getFirstResource('foobar'));

        self::assertFalse($resource->hasLink('foobar'));
        self::assertCount(0, $resource->getLink('foobar'));
        self::assertNull($resource->getFirstLink('foobar'));
    }

    public function test_resolves_subresource_curies(): void
    {
        $factory = new DefaultHalResourceFactory();
        $resource = $factory->createResource(json_decode(file_get_contents(__DIR__ . '/fixtures/subresource.json'), true));

        self::assertTrue($resource->hasResource('user'));
    }

    public function test_resolves_link_curies(): void
    {
        $factory = new DefaultHalResourceFactory();
        $resource = $factory->createResource(json_decode(file_get_contents(__DIR__ . '/fixtures/basic.json'), true));

        self::assertTrue($resource->hasLink('parent'));
        self::assertTrue($resource->hasLink('users'));
    }

    public function test_is_serializable(): void
    {
        $factory = new DefaultHalResourceFactory();
        $data = json_decode(file_get_contents(__DIR__ . '/fixtures/multiple_nested_subresources.json'), true);
        $resource = $factory->createResource($data);

        self::assertEquals($data, $resource->toArray());
    }
}
