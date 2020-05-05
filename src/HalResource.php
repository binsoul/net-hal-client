<?php

declare(strict_types=1);

namespace BinSoul\Net\Hal\Client;

/**
 * Represents a resource.
 */
class HalResource
{
    /**
     * @var mixed[]
     */
    private $properties;

    /**
     * @var HalLink[][]
     */
    private $links;

    /**
     * @var HalResource[][]
     */
    private $embedded;

    /**
     * Constructs an instance of this class.
     *
     * @param mixed[]         $properties
     * @param HalLink[][]     $links
     * @param HalResource[][] $embedded
     */
    public function __construct(
        array $properties = [],
        array $links = [],
        array $embedded = []
    ) {
        $this->properties = $properties;
        $this->links = $links;
        $this->embedded = $embedded;
    }

    /**
     * Indicates if the resource has properties.
     */
    public function hasProperties(): bool
    {
        return count($this->properties) > 0;
    }

    /**
     * Returns all properties of the resource.
     *
     * @return mixed[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Indicates if a property with the given name exists.
     */
    public function hasProperty(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    /**
     * Returns a property or null if it doesn't exist.
     *
     * @return mixed The value of the property
     */
    public function getProperty(string $name)
    {
        return $this->properties[$name] ?? null;
    }

    /**
     * Indicates if the resource has links.
     */
    public function hasLinks(): bool
    {
        return count($this->links) > 0;
    }

    /**
     * Returns all links of the resource.
     *
     * @return HalLink[][]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * Indicates if a link for the given rel exists.
     */
    public function hasLink(string $rel): bool
    {
        return $this->resolveLinkRel($rel) !== null;
    }

    /**
     * Returns all links for the given rel.
     *
     * @return HalLink[]
     */
    public function getLink(string $rel): array
    {
        $name = $this->resolveLinkRel($rel);

        if ($name === null) {
            return [];
        }

        return $this->links[$name];
    }

    /**
     * Returns the first link for the given rel.
     */
    public function getFirstLink(string $rel): ?HalLink
    {
        $name = $this->resolveLinkRel($rel);

        if ($name === null) {
            return null;
        }

        return $this->links[$name][0];
    }

    /**
     * Indicates if the resource has embedded resources.
     */
    public function hasResources(): bool
    {
        return count($this->embedded) > 0;
    }

    /**
     * Returns all embedded resources.
     *
     * @return HalResource[][]
     */
    public function getResources(): array
    {
        return $this->embedded;
    }

    /**
     * Indicates if embedded resources for the given rel exist.
     */
    public function hasResource(string $rel): bool
    {
        return $this->resolveResourceName($rel) !== null;
    }

    /**
     * Returns embedded resources for the given rel.
     *
     * @return HalResource[]
     */
    public function getResource(string $rel): array
    {
        $name = $this->resolveResourceName($rel);

        if ($name === null) {
            return [];
        }

        return $this->embedded[$name];
    }

    /**
     * Returns the first embedded resources for the given rel or null if ist doesn't exist.
     *
     * @return HalResource|null
     */
    public function getFirstResource(string $rel): ?self
    {
        $name = $this->resolveResourceName($rel);

        if ($name === null) {
            return null;
        }

        return $this->embedded[$name][0];
    }

    /**
     * Serializes the resource to an array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->getLinks() as $label => $item) {
            if (count($item) === 1) {
                $result['_links'][$label] = $item[0]->toArray();
            } else {
                $result['_links'][$label] = array_map(
                    static function (HalLink $link) {
                        return $link->toArray();
                    },
                    $item
                );
            }
        }

        foreach ($this->getResources() as $label => $item) {
            if (count($item) === 1) {
                $result['_embedded'][$label] = $item[0]->toArray();
            } else {
                $result['_embedded'][$label] = array_map(
                    static function (self $resource) {
                        return $resource->toArray();
                    },
                    $item
                );
            }
        }

        $result = array_merge($result, $this->getProperties());

        return $result;
    }

    /**
     * Resolves the rel using curies if needed.
     */
    private function resolveLinkRel(string $rel): ?string
    {
        if (isset($this->links[$rel])) {
            return $rel;
        }

        if (! isset($this->links['curies'])) {
            return null;
        }

        foreach ($this->getLink('curies') as $curie) {
            if (! $curie->getName()) {
                continue;
            }

            $namespacedRel = $curie->getName() . ':' . $rel;

            if (isset($this->links[$namespacedRel])) {
                return $namespacedRel;
            }
        }

        return null;
    }

    /**
     * Resolves the name using curies if needed.
     */
    private function resolveResourceName(string $name): ?string
    {
        if (isset($this->embedded[$name])) {
            return is_array($this->embedded[$name]) ? $name : null;
        }

        if (! isset($this->links['curies'])) {
            return null;
        }

        foreach ($this->getLink('curies') as $curie) {
            if (! $curie->getName()) {
                continue;
            }

            $namespacedName = $curie->getName() . ':' . $name;

            if (isset($this->embedded[$namespacedName])) {
                return is_array($this->embedded[$namespacedName]) ? $namespacedName : null;
            }
        }

        return null;
    }
}
