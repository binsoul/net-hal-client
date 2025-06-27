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
        $links = $array['_links'] ?? [];
        $embedded = $array['_embedded'] ?? [];

        unset($array['_links'], $array['_embedded']);

        $properties = $array;

        foreach ($embedded as $name => $item) {
            $embedded[$name] = array_map(
                [$this, 'createResource'],
                $this->normalizeData(
                    $item,
                    static function ($resource) {
                        return [$resource];
                    }
                )
            );
        }

        foreach ($links as $name => $item) {
            $halLink = array_map(
                [$this, 'createLink'],
                $this->normalizeData(
                    $item,
                    static function ($link) {
                        return trim((string) $link) !== '' ? ['href' => $link] : null;
                    }
                )
            );

            if ($halLink !== null) {
                $links[$name] = $halLink;
            }
        }

        return new HalResource($this->convertEmbeddedResources($properties), $links, $embedded);
    }

    /**
     * Builds a link with the given data.
     *
     * @param array<string, mixed> $data
     */
    public function createLink(array $data): ?HalLink
    {
        if (! isset($data['href'])) {
            return null;
        }

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
            static function ($entry) {
                return is_array($entry) || is_object($entry) ? null : $entry;
            },
            $array
        );

        return new HalLink(
            $array['href'],
            $array['templated'],
            $array['type'],
            $array['deprecation'],
            $array['name'],
            $array['profile'],
            $array['title'],
            $array['hreflang']
        );
    }

    /**
     * Normalizes the given data.
     *
     * @param mixed $data The data to normalize
     *
     * @return array<int, mixed>
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
            static function ($entry) {
                return $entry !== null;
            }
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

            if (isset($value['_links']) || isset($value['_embedded'])) {
                $array[$key] = $this->createResource($value);
            } else {
                $array[$key] = $this->convertEmbeddedResources($value);
            }
        }

        return $array;
    }
}
