<?php

declare(strict_types=1);

namespace BinSoul\Net\Hal\Client;

/**
 * Builds resources.
 */
interface HalResourceFactory
{
    /**
     * @param array<string, mixed> $data
     */
    public function createResource(array $data): HalResource;
}
