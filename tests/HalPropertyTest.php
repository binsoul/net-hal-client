<?php

namespace BinSoul\Test\Net\Hal\Client;

use BinSoul\Net\Hal\Client\HalProperty;
use PHPUnit\Framework\TestCase;

class HalPropertyTest extends TestCase
{
    /**
     * Test getName method returns the correct property name.
     */
    public function testGetName(): void
    {
        $property = new HalProperty('validName', 'value');
        $this->assertSame('validName', $property->getName());
    }

    /**
     * Test asMixed method returns null when the value is null.
     */
    public function testAsMixedWithNullValue(): void
    {
        $property = new HalProperty('test', null);
        $this->assertNull($property->asMixed());
    }

    /**
     * Test asMixed method returns the string when the value is an string.
 */
    public function testAsMixedWithStringValue(): void
    {
        $property = new HalProperty('test', 'value');
        $this->assertSame('value', $property->asMixed());
    }

    /**
     * Test asMixed method returns the int when the value is an int.
 */
    public function testAsMixedWithIntValue(): void
    {
        $property = new HalProperty('test', 123);
        $this->assertSame(123, $property->asMixed());
    }

    /**
     * Test asMixed method returns the float when the value is an float.
 */
    public function testAsMixedWithFloatValue(): void
    {
        $property = new HalProperty('test', 123.45);
        $this->assertSame(123.45, $property->asMixed());
    }

    /**
     * Test asMixed method returns the bool when the value is an bool.
 */
    public function testAsMixedWithBoolValue(): void
    {
        $property = new HalProperty('test', true);
        $this->assertTrue($property->asMixed());

        $property = new HalProperty('test', false);
        $this->assertFalse($property->asMixed());
    }

    /**
     * Test asMixed method returns the array when the value is an array.
     */
    public function testAsMixedWithArrayValue(): void
    {
        $property = new HalProperty('test', ['value1', 'value2']);
        $this->assertSame(['value1', 'value2'], $property->asMixed());
    }

    /**
     * Test asMixed method returns the object when the value is an object.
     */
    public function testAsMixedWithObjectValue(): void
    {
        $object = (object)['key' => 'value'];
        $property = new HalProperty('test', $object);
        $this->assertSame($object, $property->asMixed());
    }

    /**
     * Test asString method returns string value when property value is a string.
     */
    public function testAsStringWithStringValue(): void
    {
        $property = new HalProperty('test', 'string value');
        $this->assertSame('string value', $property->asString());
    }

    /**
     * Test asString method returns default value when property value is null.
     */
    public function testAsStringWithNullValue(): void
    {
        $property = new HalProperty('test', null);
        $this->assertNull($property->asString());

        $property = new HalProperty('test', null);
        $this->assertSame('default', $property->asString('default'));
    }

    /**
     * Test asString method returns string representation of numerical value.
     */
    public function testAsStringWithNumericValue(): void
    {
        $property = new HalProperty('test', 123);
        $this->assertSame('123', $property->asString());
    }

    /**
     * Test asString method returns default value when property value is an object.
     */
    public function testAsStringWithObjectValue(): void
    {
        $property = new HalProperty('test', new \stdClass());
        $this->assertSame('default', $property->asString('default'));
    }

    /**
     * Test asString method returns default value when property value is an array.
     */
    public function testAsStringWithArrayValue(): void
    {
        $property = new HalProperty('test', ['value']);
        $this->assertSame('default', $property->asString('default'));
    }
    /**
     * Test asInt method returns integer value when property value is an integer.
     */
    public function testAsIntWithIntValue(): void
    {
        $property = new HalProperty('test', 123);
        $this->assertSame(123, $property->asInt());
    }

    /**
     * Test asInt method returns default value when property value is null.
     */
    public function testAsIntWithNullValue(): void
    {
        $property = new HalProperty('test', null);
        $this->assertNull($property->asInt());

        $property = new HalProperty('test', null);
        $this->assertSame(123, $property->asInt(123));
    }

    /**
     * Test asInt method returns integer value converted from numeric string.
     */
    public function testAsIntWithStringValue(): void
    {
        $property = new HalProperty('test', '123');
        $this->assertSame(123, $property->asInt());
    }

    /**
     * Test asInt method returns default value when property value is a non-numeric string.
     */
    public function testAsIntWithNonNumericStringValue(): void
    {
        $property = new HalProperty('test', 'not a number');
        $this->assertSame(123, $property->asInt(123));
    }

    /**
     * Test asInt method returns integer value converted from float value.
     */
    public function testAsIntWithFloatValue(): void
    {
        $property = new HalProperty('test', 123.45);
        $this->assertSame(123, $property->asInt());
    }

    /**
     * Test asInt method returns default value when property value is an object.
     */
    public function testAsIntWithObjectValue(): void
    {
        $property = new HalProperty('test', new \stdClass());
        $this->assertSame(123, $property->asInt(123));
    }

    /**
     * Test asInt method returns default value when property value is an array.
     */
    public function testAsIntWithArrayValue(): void
    {
        $property = new HalProperty('test', ['value']);
        $this->assertSame(123, $property->asInt(123));
    }
    /**
     * Test asFloat method returns float value when property value is a float.
     */
    public function testAsFloatWithFloatValue(): void
    {
        $property = new HalProperty('test', 123.45);
        $this->assertSame(123.45, $property->asFloat());
    }

    /**
     * Test asFloat method returns default value when property value is null.
     */
    public function testAsFloatWithNullValue(): void
    {
        $property = new HalProperty('test', null);
        $this->assertNull($property->asFloat());

        $property = new HalProperty('test', null);
        $this->assertSame(123.45, $property->asFloat(123.45));
    }

    /**
     * Test asFloat method returns float value converted from numeric string.
     */
    public function testAsFloatWithStringValue(): void
    {
        $property = new HalProperty('test', '123.45');
        $this->assertSame(123.45, $property->asFloat());
    }

    /**
     * Test asFloat method returns default value when property value is a non-numeric string.
     */
    public function testAsFloatWithNonNumericStringValue(): void
    {
        $property = new HalProperty('test', 'not a number');
        $this->assertSame(123.45, $property->asFloat(123.45));
    }

    /**
     * Test asFloat method returns float value converted from integer.
     */
    public function testAsFloatWithIntValue(): void
    {
        $property = new HalProperty('test', 123);
        $this->assertSame(123.0, $property->asFloat());
    }

    /**
     * Test asFloat method returns default value when property value is an object.
     */
    public function testAsFloatWithObjectValue(): void
    {
        $property = new HalProperty('test', new \stdClass());
        $this->assertSame(123.45, $property->asFloat(123.45));
    }

    /**
     * Test asFloat method returns default value when property value is an array.
     */
    public function testAsFloatWithArrayValue(): void
    {
        $property = new HalProperty('test', ['value']);
        $this->assertSame(123.45, $property->asFloat(123.45));
    }

    /**
     * Test asBool method returns boolean value when property value is a boolean.
     */
    public function testAsBoolWithBoolValue(): void
    {
        $property = new HalProperty('test', true);
        $this->assertTrue($property->asBool());

        $property = new HalProperty('test', false);
        $this->assertFalse($property->asBool());
    }

    /**
     * Test asBool method returns default value when property value is null.
     */
    public function testAsBoolWithNullValue(): void
    {
        $property = new HalProperty('test', null);
        $this->assertNull($property->asBool());

        $property = new HalProperty('test', null);
        $this->assertTrue($property->asBool(true));
    }

    /**
     * Test asBool method returns boolean value cast from integer.
     */
    public function testAsBoolWithIntValue(): void
    {
        $property = new HalProperty('test', 1);
        $this->assertTrue($property->asBool());

        $property = new HalProperty('test', 0);
        $this->assertFalse($property->asBool());
    }

    /**
     * Test asBool method returns boolean value cast from non-empty and empty strings.
     */
    public function testAsBoolWithStringValue(): void
    {
        $property = new HalProperty('test', 'true');
        $this->assertTrue($property->asBool());

        $property = new HalProperty('test', '');
        $this->assertFalse($property->asBool());
    }

    /**
     * Test asBool method returns boolean value cast from floats.
     */
    public function testAsBoolWithFloatValue(): void
    {
        $property = new HalProperty('test', 0.0);
        $this->assertFalse($property->asBool());

        $property = new HalProperty('test', 0.5);
        $this->assertTrue($property->asBool());
    }

    /**
     * Test asBool method returns default value when property value is an object.
     */
    public function testAsBoolWithObjectValue(): void
    {
        $property = new HalProperty('test', new \stdClass());
        $this->assertFalse($property->asBool(false));
    }

    /**
     * Test asBool method returns default value when property value is an array.
     */
    public function testAsBoolWithArrayValue(): void
    {
        $property = new HalProperty('test', ['value']);
        $this->assertTrue($property->asBool(true));
    }
    /**
     * Test asArray method returns array value when property value is an array.
     */
    public function testAsArrayWithArrayValue(): void
    {
        $property = new HalProperty('test', ['value1', 'value2']);
        $this->assertSame(['value1', 'value2'], $property->asArray());
    }

    /**
     * Test asArray method returns default value when property value is null.
     */
    public function testAsArrayWithNullValue(): void
    {
        $property = new HalProperty('test', null);
        $this->assertNull($property->asArray());

        $this->assertSame(['default'], $property->asArray(['default']));
    }

    /**
     * Test asArray method casts object to array when property value is an object.
     */
    public function testAsArrayWithObjectValue(): void
    {
        $property = new HalProperty('test', (object)['key' => 'value']);
        $this->assertSame(['key' => 'value'], $property->asArray());
    }

    /**
     * Test asArray method returns default value when property value is a scalar.
     */
    public function testAsArrayWithScalarValue(): void
    {
        $property = new HalProperty('test', 'string value');
        $this->assertSame(['default'], $property->asArray(['default']));

        $property = new HalProperty('test', 123);
        $this->assertSame(['default'], $property->asArray(['default']));
    }
    /**
     * Test asObject method returns object value when property value is an object.
     */
    public function testAsObjectWithObjectValue(): void
    {
        $property = new HalProperty('test', (object)['key' => 'value']);
        $this->assertEquals((object)['key' => 'value'], $property->asObject());
    }

    /**
     * Test asObject method returns default value when property value is null.
     */
    public function testAsObjectWithNullValue(): void
    {
        $property = new HalProperty('test', null);
        $this->assertNull($property->asObject());

        $property = new HalProperty('test', null);
        $this->assertEquals((object)['key' => 'value'], $property->asObject((object)['key' => 'value']));
    }

    /**
     * Test asObject method converts array to object when property value is an array.
     */
    public function testAsObjectWithArrayValue(): void
    {
        $property = new HalProperty('test', ['key' => 'value']);
        $this->assertEquals((object)['key' => 'value'], $property->asObject());
    }

    /**
     * Test asObject method returns default value when property value is a scalar.
     */
    public function testAsObjectWithScalarValue(): void
    {
        $property = new HalProperty('test', 'string value');
        $this->assertEquals((object)['default' => 'value'], $property->asObject((object)['default' => 'value']));
    }

    /**
     * Test asDateTime method parses a valid date string into a DateTime object.
     */
    public function testAsDateTimeWithValidStringValue(): void
    {
        $property = new HalProperty('test', '2023-10-05 12:00:00');
        $this->assertEquals(
            new \DateTime('2023-10-05 12:00:00'),
            $property->asDateTime()
        );
    }

    /**
     * Test asDateTime method returns null for an invalid date string.
     */
    public function testAsDateTimeWithInvalidStringValue(): void
    {
        $property = new HalProperty('test', 'invalid-date');
        $this->assertNull($property->asDateTime());
    }

    /**
     * Test asDateTime method returns default value when property value is null.
     */
    public function testAsDateTimeWithNullValue(): void
    {
        $property = new HalProperty('test', null);
        $this->assertNull($property->asDateTime());

        $defaultDate = new \DateTime('2025-01-01');
        $this->assertEquals($defaultDate, $property->asDateTime($defaultDate));
    }

    /**
     * Test asDateTime method accepts and returns the default value.
     */
    public function testAsDateTimeWithDefaultValue(): void
    {
        $defaultDate = new \DateTime('2025-01-01');
        $property = new HalProperty('test', 'invalid-date');
        $this->assertEquals($defaultDate, $property->asDateTime($defaultDate));
    }

    /**
     * Test asDateTime method doesn't convert non-string values to a DateTime object.
     */
    public function testAsDateTimeWithNonStringValue(): void
    {
        $property = new HalProperty('test', 12345);
        $this->assertNull($property->asDateTime());
    }
}
