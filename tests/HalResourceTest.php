<?php

declare(strict_types=1);

namespace BinSoul\Test\Net\Hal\Client;

use BinSoul\Net\Hal\Client\DefaultHalResourceFactory;
use BinSoul\Net\Hal\Client\HalLink;
use BinSoul\Net\Hal\Client\HalResource;
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

    public function test_set_and_get_property(): void
    {
        $resource = new HalResource();
        $resource->setProperty('key', 'value');

        self::assertEquals('value', $resource->getProperty('key'));
    }

    public function test_update_property_value(): void
    {
        $resource = new HalResource(['key' => 'initial']);
        $resource->setProperty('key', 'updated');

        self::assertEquals('updated', $resource->getProperty('key'));
    }

    public function test_has_property_after_setting(): void
    {
        $resource = new HalResource();
        $resource->setProperty('key', 'value');

        self::assertTrue($resource->hasProperty('key'));
    }
    /**
     * Tests the setLink method for setting and retrieving links.
     */
    public function test_set_and_get_link(): void
    {
        $link1 = $this->createMock(HalLink::class);
        $link2 = $this->createMock(HalLink::class);

        $resource = new HalResource();
        $resource->setLink('example', [$link1, $link2]);

        self::assertTrue($resource->hasLink('example'));
        self::assertCount(2, $resource->getLink('example'));
        self::assertSame($link1, $resource->getLink('example')[0]);
        self::assertSame($link2, $resource->getLink('example')[1]);
    }

    /**
     * Tests the setLink method with an empty array.
     */
    public function test_set_link_with_empty_array(): void
    {
        $resource = new HalResource();
        $resource->setLink('empty', []);

        self::assertTrue($resource->hasLink('empty'));
        self::assertCount(0, $resource->getLink('empty'));
    }

    /**
     * Tests overwriting existing links using the setLink method.
     */
    public function test_set_link_overwrites_existing_links(): void
    {
        $originalLink = $this->createMock(HalLink::class);
        $newLink = $this->createMock(HalLink::class);

        $resource = new HalResource();
        $resource->setLink('overwrite', [$originalLink]);
        $resource->setLink('overwrite', [$newLink]);

        self::assertCount(1, $resource->getLink('overwrite'));
        self::assertSame($newLink, $resource->getLink('overwrite')[0]);
    }
    /**
     * Tests that resources can be set and retrieved for a specific relationship.
     */
    public function test_set_and_get_resource(): void
    {
        $resource1 = $this->createMock(HalResource::class);
        $resource2 = $this->createMock(HalResource::class);

        $resource = new HalResource();
        $resource->setResource('example', [$resource1, $resource2]);

        self::assertTrue($resource->hasResource('example'));
        self::assertCount(2, $resource->getResource('example'));
        self::assertSame($resource1, $resource->getResource('example')[0]);
        self::assertSame($resource2, $resource->getResource('example')[1]);
    }

    /**
     * Tests that an empty array can be set for resources of a specific relationship.
     */
    public function test_set_resource_with_empty_array(): void
    {
        $resource = new HalResource();
        $resource->setResource('empty', []);

        self::assertTrue($resource->hasResource('empty'));
        self::assertCount(0, $resource->getResource('empty'));
    }

    /**
     * Tests that existing resources can be overwritten by setting new ones.
     */
    public function test_set_resource_overwrites_existing_resources(): void
    {
        $originalResource = $this->createMock(HalResource::class);
        $newResource = $this->createMock(HalResource::class);

        $resource = new HalResource();
        $resource->setResource('overwrite', [$originalResource]);
        $resource->setResource('overwrite', [$newResource]);

        self::assertCount(1, $resource->getResource('overwrite'));
        self::assertSame($newResource, $resource->getResource('overwrite')[0]);
    }
    /**
     * Tests retrieving a property object when the property exists.
     */
    public function test_get_value_when_property_exists(): void
    {
        $resource = new HalResource(['key' => 'value']);
        $propertyObject = $resource->getValue('key');

        self::assertEquals('key', $propertyObject->getName());
        self::assertEquals('value', $propertyObject->asMixed());
    }

    /**
     * Tests retrieving a property object when the property does not exist.
     */
    public function test_get_value_when_property_does_not_exist(): void
    {
        $resource = new HalResource();
        $propertyObject = $resource->getValue('nonexistent');

        self::assertEquals('nonexistent', $propertyObject->getName());
        self::assertNull($propertyObject->asMixed());
    }

    /**
     * Tests retrieving a property object with a complex value.
     */
    public function test_get_value_with_complex_value(): void
    {
        $complexValue = ['nestedKey' => 'nestedValue'];
        $resource = new HalResource(['key' => $complexValue]);
        $propertyObject = $resource->getValue('key');

        self::assertEquals('key', $propertyObject->getName());
        self::assertEquals($complexValue, $propertyObject->asMixed());
    }
}
