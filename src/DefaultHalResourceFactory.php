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
            $links[$name] = array_map(
                [$this, 'createLink'],
                $this->normalizeData(
                    $item,
                    static function ($link) {
                        return ['href' => $link];
                    }
                )
            );
        }

        foreach ($properties as $key => $value) {
            if (is_array($value) && (isset($value['_links']) || isset($value['_embedded']))) {
                $properties[$key] = $this->createResource($value);
            }
        }

        return new HalResource($properties, $links, $embedded);
    }

    /**
     * Builds a link with the given data.
     *
     * @param mixed[] $data
     */
    public function createLink(array $data): HalLink
    {
        $array = array_replace(
            [
                'href' => null,
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
     * @param mixed $data
     *
     * @return mixed[]
     */
    private function normalizeData($data, callable $arrayNormalizer): array
    {
        if (!$data) {
            return [];
        }

        if (!isset($data[0]) || !is_array($data)) {
            $data = [$data];
        }

        $data = array_map(
            static function ($entry) use ($arrayNormalizer) {
                if ($entry !== null && !is_array($entry)) {
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
}
