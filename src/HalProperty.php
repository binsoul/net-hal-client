<?php

declare(strict_types=1);

namespace BinSoul\Net\Hal\Client;

use DateTime;
use Throwable;

/**
 * Represents a property.
 */
readonly class HalProperty
{
    /**
     * Constructs an instance of this class.
     */
    public function __construct(
        private string $name,
        private mixed $value,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function asMixed(): mixed
    {
        return $this->value;
    }

    /**
     * @return ($default is null ? string|null : string)
     */
    public function asString(?string $default = null): ?string
    {
        if ($this->value === null) {
            return $default;
        }

        if (is_string($this->value) || is_scalar($this->value)) {
            return (string) $this->value;
        }

        return $default;
    }

    /**
     * @return ($default is null ? int|null : int)
     */
    public function asInt(?int $default = null): ?int
    {
        if ($this->value === null) {
            return $default;
        }

        if (is_float($this->value) || is_int($this->value)) {
            return (int) $this->value;
        }

        if (is_string($this->value) && is_numeric($this->value)) {
            return (int) $this->value;
        }

        return $default;
    }

    /**
     * @return ($default is null ? float|null : float)
     */
    public function asFloat(?float $default = null): ?float
    {
        if ($this->value === null) {
            return $default;
        }

        if (is_float($this->value) || is_int($this->value)) {
            return (float) $this->value;
        }

        if (is_string($this->value) && is_numeric($this->value)) {
            return (float) $this->value;
        }

        return $default;
    }

    /**
     * @return ($default is null ? bool|null : bool)
     */
    public function asBool(?bool $default = null): ?bool
    {
        if ($this->value === null) {
            return $default;
        }

        if (is_bool($this->value)) {
            return $this->value;
        }

        if (is_object($this->value) || is_array($this->value)) {
            return $default;
        }

        return (bool) $this->value;
    }

    /**
     * @param array<array-key, mixed>|null $default
     *
     * @return ($default is null ? array<array-key, mixed>|null : array<array-key, mixed>)
     */
    public function asArray(?array $default = null): ?array
    {
        if ($this->value === null) {
            return $default;
        }

        if (is_array($this->value)) {
            return $this->value;
        }

        if (is_object($this->value)) {
            return (array) $this->value;
        }

        return $default;
    }

    /**
     * @return ($default is null ? object|null : object)
     */
    public function asObject(?object $default = null): ?object
    {
        if ($this->value === null) {
            return $default;
        }

        if (is_object($this->value)) {
            return $this->value;
        }

        if (is_array($this->value)) {
            return (object) $this->value;
        }

        return $default;
    }

    /**
     * @return ($default is null ? DateTime|null : DateTime)
     */
    public function asDateTime(?DateTime $default = null): ?DateTime
    {
        $value = $this->asString();

        if ($value === null) {
            return $default;
        }

        try {
            return new DateTime($value);
        } catch (Throwable) {
            return $default;
        }
    }
}
