<?php

namespace BinSoul\Test\Net\Hal\Client;

use BinSoul\Net\Hal\Client\HalLink;
use PHPUnit\Framework\TestCase;

class HalLinkTest extends TestCase
{
    /**
     * Tests that the getUri method returns the correct URI when the link is not templated.
     */
    public function testGetUriReturnsCorrectUriWhenNotTemplated(): void
    {
        $link = new HalLink('https://example.com/resource');

        $uri = $link->getUri();

        $this->assertSame('https://example.com/resource', $uri);
    }

    /**
     * Tests that the getUri method correctly expands the URI template when the link is templated.
     */
    public function testGetUriExpandsUriTemplateWhenTemplated(): void
    {
        $link = new HalLink('https://example.com/resource{?id}', true);

        $uri = $link->getUri(['id' => 123]);

        $this->assertSame('https://example.com/resource?id=123', $uri);
    }

    /**
     * Tests that the getUri method handles URI templates with multiple variables.
     */
    public function testGetUriHandlesMultipleTemplateVariables(): void
    {
        $link = new HalLink('https://example.com/resource{?id,type}', true);

        $uri = $link->getUri(['id' => 123, 'type' => 'foo']);

        $this->assertSame('https://example.com/resource?id=123&type=foo', $uri);
    }

    /**
     * Tests that the getUri method returns the base URI when no variables are passed for a templated link.
     */
    public function testGetUriReturnsBaseUriWhenNoVariablesPassed(): void
    {
        $link = new HalLink('https://example.com/resource{?id}', true);

        $uri = $link->getUri();

        $this->assertSame('https://example.com/resource', $uri);
    }

    /**
     * Tests that the getUri method returns the base URI when the link is templated but empty variables are passed.
     */
    public function testGetUriReturnsBaseUriWhenEmptyVariablesPassed(): void
    {
        $link = new HalLink('https://example.com/resource{?id}', true);

        $uri = $link->getUri([]);

        $this->assertSame('https://example.com/resource', $uri);
    }
    /**
     * Tests that the toArray method includes all non-null properties.
     */
    public function testToArrayIncludesAllNonNullProperties(): void
    {
        $link = new HalLink(
            'https://example.com',
            true,
            'application/json',
            'https://example.com/deprecated',
            'example-name',
            'https://example.com/profile',
            'Example Title',
            'en'
        );

        $expectedArray = [
            'href' => 'https://example.com',
            'templated' => true,
            'type' => 'application/json',
            'deprecation' => 'https://example.com/deprecated',
            'name' => 'example-name',
            'profile' => 'https://example.com/profile',
            'title' => 'Example Title',
            'hreflang' => 'en',
        ];

        $this->assertSame($expectedArray, $link->toArray());
    }

    /**
     * Tests that the toArray method skips null properties.
     */
    public function testToArraySkipsNullProperties(): void
    {
        $link = new HalLink('https://example.com');

        $expectedArray = [
            'href' => 'https://example.com',
        ];

        $this->assertSame($expectedArray, $link->toArray());
    }

    /**
     * Tests that the toArray method works correctly with only some properties set.
     */
    public function testToArrayHandlesPartialProperties(): void
    {
        $link = new HalLink(
            'https://example.com',
            false,
            null,
            null,
            null,
            null,
            'Example Title',
            null
        );

        $expectedArray = [
            'href' => 'https://example.com',
            'templated' => false,
            'title' => 'Example Title',
        ];

        $this->assertSame($expectedArray, $link->toArray());
    }

    /**
     * Tests that the getHref method returns the href correctly.
     */
    public function testGetHrefReturnsCorrectHref(): void
    {
        $link = new HalLink('https://example.com/resource');

        $this->assertSame('https://example.com/resource', $link->getHref());
    }

    /**
     * Tests that the getHref method reflects updates made by the setHref method.
     */
    public function testGetHrefUpdatesOnSetHref(): void
    {
        $link = new HalLink('https://example.com/resource');
        $link->setHref('https://new.example.com/resource');

        $this->assertSame('https://new.example.com/resource', $link->getHref());
    }

    /**
     * Tests that isTemplated returns true when templated is set to true.
     */
    public function testIsTemplatedReturnsTrueWhenTemplated(): void
    {
        $link = new HalLink('https://example.com/resource', true);

        $this->assertTrue($link->isTemplated());
    }

    /**
     * Tests that isTemplated returns false when templated is set to false.
     */
    public function testIsTemplatedReturnsFalseWhenNotTemplated(): void
    {
        $link = new HalLink('https://example.com/resource', false);

        $this->assertFalse($link->isTemplated());
    }

    /**
     * Tests that isTemplated returns false when templated is null.
     */
    public function testIsTemplatedReturnsFalseWhenTemplatedIsNull(): void
    {
        $link = new HalLink('https://example.com/resource', null);

        $this->assertFalse($link->isTemplated());
    }
    /**
     * Tests that setTemplated correctly updates the templated property to true.
     */
    public function testSetTemplatedUpdatesToTrue(): void
    {
        $link = new HalLink('https://example.com', false);
        $link->setTemplated(true);

        $this->assertTrue($link->isTemplated());
    }

    /**
     * Tests that isDeprecated returns true when deprecation is set with a non-empty string.
     */
    public function testIsDeprecatedReturnsTrueWhenDeprecatedIsNotEmpty(): void
    {
        $link = new HalLink('https://example.com', null, null, 'https://example.com/deprecated');

        $this->assertTrue($link->isDeprecated());
    }

    /**
     * Tests that isDeprecated returns false when deprecation is an empty string.
     */
    public function testIsDeprecatedReturnsFalseWhenDeprecatedIsEmptyString(): void
    {
        $link = new HalLink('https://example.com', null, null, '');

        $this->assertFalse($link->isDeprecated());
    }

    /**
     * Tests that isDeprecated returns false when deprecation is null.
     */
    public function testIsDeprecatedReturnsFalseWhenDeprecatedIsNull(): void
    {
        $link = new HalLink('https://example.com');

        $this->assertFalse($link->isDeprecated());
    }

    /**
     * Tests that setTemplated correctly updates the templated property to false.
     */
    public function testSetTemplatedUpdatesToFalse(): void
    {
        $link = new HalLink('https://example.com', true);
        $link->setTemplated(false);

        $this->assertFalse($link->isTemplated());
    }

    /**
     * Tests that setTemplated correctly updates the templated property to null.
     */
    public function testSetTemplatedUpdatesToNull(): void
    {
        $link = new HalLink('https://example.com', true);
        $link->setTemplated(null);

        $this->assertFalse($link->isTemplated());
    }
    /**
     * Tests that getType returns null when type is not set.
     */
    public function testGetTypeReturnsNullWhenNotSet(): void
    {
        $link = new HalLink('https://example.com');

        $this->assertNull($link->getType());
    }

    /**
     * Tests that getType returns the correct type when type is set.
     */
    public function testGetTypeReturnsCorrectType(): void
    {
        $link = new HalLink('https://example.com', true, 'application/json');

        $this->assertSame('application/json', $link->getType());
    }

    /**
     * Tests that getType reflects changes made by setType.
     */
    public function testGetTypeReflectsChangesOnSetType(): void
    {
        $link = new HalLink('https://example.com', true);
        $link->setType('application/xml');

        $this->assertSame('application/xml', $link->getType());
    }

    /**
     * Tests that getDeprecation returns the correct deprecation URL when set.
     */
    public function testGetDeprecationReturnsCorrectValueWhenSet(): void
    {
        $link = new HalLink('https://example.com', null, null, 'https://example.com/deprecated');

        $this->assertSame('https://example.com/deprecated', $link->getDeprecation());
    }

    /**
     * Tests that getDeprecation returns null when deprecation is not set.
     */
    public function testGetDeprecationReturnsNullWhenNotSet(): void
    {
        $link = new HalLink('https://example.com');

        $this->assertNull($link->getDeprecation());
    }

    /**
     * Tests that getDeprecation returns an empty string when deprecation is set to an empty string.
     */
    public function testGetDeprecationReturnsEmptyStringWhenSetToEmpty(): void
    {
        $link = new HalLink('https://example.com', null, null, '');

        $this->assertSame('', $link->getDeprecation());
    }
    /**
     * Tests that setDeprecation correctly updates the deprecation property to a non-null string.
     */
    public function testSetDeprecationUpdatesToNonNullString(): void
    {
        $link = new HalLink('https://example.com');
        $link->setDeprecation('https://example.com/deprecated');

        $this->assertSame('https://example.com/deprecated', $link->getDeprecation());
    }

    /**
     * Tests that setDeprecation correctly updates the deprecation property to null.
     */
    public function testSetDeprecationUpdatesToNull(): void
    {
        $link = new HalLink('https://example.com', null, null, 'https://example.com/deprecated');
        $link->setDeprecation(null);

        $this->assertNull($link->getDeprecation());
    }

    /**
     * Tests that setDeprecation correctly updates the deprecation property to an empty string.
     */
    public function testSetDeprecationUpdatesToEmptyString(): void
    {
        $link = new HalLink('https://example.com', null, null, 'https://example.com/deprecated');
        $link->setDeprecation('');

        $this->assertSame('', $link->getDeprecation());
    }

    /**
     * Tests that setName correctly updates the name property to a non-null string.
     */
    public function testSetNameUpdatesToNonNullString(): void
    {
        $link = new HalLink('https://example.com');
        $link->setName('example-name');

        $this->assertSame('example-name', $link->getName());
    }

    /**
     * Tests that setName correctly updates the name property to null.
     */
    public function testSetNameUpdatesToNull(): void
    {
        $link = new HalLink('https://example.com', null, null, null, 'example-name');
        $link->setName(null);

        $this->assertNull($link->getName());
    }

    /**
     * Tests that setName correctly updates the name property to an empty string.
     */
    public function testSetNameUpdatesToEmptyString(): void
    {
        $link = new HalLink('https://example.com', null, null, null, 'example-name');
        $link->setName('');

        $this->assertSame('', $link->getName());
    }
    /**
     * Tests that setProfile correctly updates the profile property to a non-null string.
     */
    public function testSetProfileUpdatesToNonNullString(): void
    {
        $link = new HalLink('https://example.com');
        $link->setProfile('https://example.com/profile');

        $this->assertSame('https://example.com/profile', $link->getProfile());
    }

    /**
     * Tests that setProfile correctly updates the profile property to null.
     */
    public function testSetProfileUpdatesToNull(): void
    {
        $link = new HalLink('https://example.com', null, null, null, null, 'https://example.com/profile');
        $link->setProfile(null);

        $this->assertNull($link->getProfile());
    }

    /**
     * Tests that setProfile correctly updates the profile property to an empty string.
     */
    public function testSetProfileUpdatesToEmptyString(): void
    {
        $link = new HalLink('https://example.com', null, null, null, null, 'https://example.com/profile');
        $link->setProfile('');

        $this->assertSame('', $link->getProfile());
    }

    /**
     * Tests that setTitle correctly updates the title property to a non-null string.
     */
    public function testSetTitleUpdatesToNonNullString(): void
    {
        $link = new HalLink('https://example.com');
        $link->setTitle('Example Title');

        $this->assertSame('Example Title', $link->getTitle());
    }

    /**
     * Tests that setTitle correctly updates the title property to null.
     */
    public function testSetTitleUpdatesToNull(): void
    {
        $link = new HalLink('https://example.com', null, null, null, null, null, 'Example Title');
        $link->setTitle(null);

        $this->assertNull($link->getTitle());
    }

    /**
     * Tests that setTitle correctly updates the title property to an empty string.
     */
    public function testSetTitleUpdatesToEmptyString(): void
    {
        $link = new HalLink('https://example.com', null, null, null, null, null, 'Example Title');
        $link->setTitle('');

        $this->assertSame('', $link->getTitle());
    }
    /**
     * Tests that setHreflang correctly updates the hreflang property to a non-null string.
     */
    public function testSetHreflangUpdatesToNonNullString(): void
    {
        $link = new HalLink('https://example.com');
        $link->setHreflang('en');

        $this->assertSame('en', $link->getHreflang());
    }

    /**
     * Tests that setHreflang correctly updates the hreflang property to null.
     */
    public function testSetHreflangUpdatesToNull(): void
    {
        $link = new HalLink('https://example.com', null, null, null, null, null, null, 'en');
        $link->setHreflang(null);

        $this->assertNull($link->getHreflang());
    }

    /**
     * Tests that setHreflang correctly updates the hreflang property to an empty string.
     */
    public function testSetHreflangUpdatesToEmptyString(): void
    {
        $link = new HalLink('https://example.com', null, null, null, null, null, null, 'en');
        $link->setHreflang('');

        $this->assertSame('', $link->getHreflang());
    }
}
