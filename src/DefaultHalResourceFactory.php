<?php

declare(strict_types=1);

namespace BinSoul\Net\Hal\Client;

/**
 * Provides a default implementation of the {@see HalResourceFactory} interface.
 */
class DefaultHalResourceFactory implements HalResourceFactory
{
    public function createResource(array $data): HalResource
    {
        $array = $data;
        /** @var array<string, mixed> $links */
        $links = $array['_links'] ?? [];
        /** @var array<string, mixed> $embedded */
        $embedded = $array['_embedded'] ?? [];

        unset($array['_links'], $array['_embedded']);

        $properties = $array;

        $embeddedResources = [];

        foreach ($embedded as $name => $item) {
            /** @var array<array-key, array<string, mixed>> $normalizedData */
            $normalizedData = $this->normalizeData(
                $item,
                static fn ($resource): array => ['id' => $resource]
            );

            $embeddedResources[$name] = array_map($this->createResource(...), $normalizedData);
        }

        $embeddedLinks = [];

        foreach ($links as $name => $item) {
            /** @var array<array-key, array<string, mixed>> $normalizedData */
            $normalizedData = $this->normalizeData(
                $item,
                static fn (mixed $link): array => trim(is_scalar($link) ? (string) $link : '') !== '' ? ['href' => $link] : []
            );

            $halLink = array_map($this->createLink(...), $normalizedData);
            $halLink = array_filter($halLink, static fn (?HalLink $entry): bool => $entry !== null);

            if ($halLink !== []) {
                $embeddedLinks[$name] = $halLink;
            }
        }

        return new HalResource($this->convertEmbeddedResources($properties), $embeddedLinks, $embeddedResources);
    }

    /**
     * Builds a link with the given data.
     *
     * @param array<string, mixed> $data
     */
    public function createLink(array $data): ?HalLink
    {
        if (! isset($data['href']) || ! is_scalar($data['href'])) {
            return null;
        }

        $href = (string) $data['href'];

        $array = array_replace(
            [
                'templated' => null,
                'type' => null,
                'deprecation' => null,
                'name' => null,
                'profile' => null,
                'title' => null,
                'hreflang' => null,
            ],
            $data
        );

        $array = array_map(
            static fn ($entry): mixed => is_array($entry) || is_object($entry) ? null : $entry,
            $array
        );

        return new HalLink(
            $href,
            $array['templated'] !== null ? (bool) $array['templated'] : null,
            is_scalar($array['type']) ? (string) $array['type'] : null,
            is_scalar($array['deprecation']) ? (string) $array['deprecation'] : null,
            is_scalar($array['name']) ? (string) $array['name'] : null,
            is_scalar($array['profile']) ? (string) $array['profile'] : null,
            is_scalar($array['title']) ? (string) $array['title'] : null,
            is_scalar($array['hreflang']) ? (string) $array['hreflang'] : null
        );
    }

    /**
     * Normalizes the given data.
     *
     * @param mixed                                    $data            The data to normalize
     * @param callable(mixed): array<array-key, mixed> $arrayNormalizer
     *
     * @return array<array-key, mixed>
     */
    private function normalizeData(mixed $data, callable $arrayNormalizer): array
    {
        if (! $data) {
            return [];
        }

        if (! is_array($data) || ! isset($data[0])) {
            $data = [$data];
        }

        $data = array_map(
            static function ($entry) use ($arrayNormalizer) {
                if ($entry !== null && ! is_array($entry)) {
                    $entry = $arrayNormalizer($entry);
                }

                return $entry;
            },
            $data
        );

        return array_filter(
            $data,
            static fn ($entry): bool => $entry !== null
        );
    }

    /**
     * Converts all array entries containing a "_link" or an "_embedded" key into a resource.
     *
     * @param array<string, mixed> $array
     *
     * @return array<string, mixed>
     */
    private function convertEmbeddedResources(array $array): array
    {
        foreach ($array as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            $data = [];

            foreach ($value as $subkey => $subvalue) {
                $data[(string) $subkey] = $subvalue;
            }

            if (isset($data['_links']) || isset($data['_embedded'])) {
                $array[$key] = $this->createResource($data);
            } else {
                $array[$key] = $this->convertEmbeddedResources($data);
            }
        }

        return $array;
    }
}
