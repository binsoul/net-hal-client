<?php

namespace BinSoul\Net\Hal\Client;

/**
 * Builds resources.
 */
interface HalResourceFactory
{
    /**
     * @param mixed[] $data
     */
    public function createResource(array $data): HalResource;
}
