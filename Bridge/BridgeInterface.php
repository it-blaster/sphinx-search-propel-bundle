<?php

namespace ItBlaster\SphinxSearchPropelBundle\Bridge;

/**
 * Propel bridge interface
 */
interface BridgeInterface
{
    /**
     * Add entity list to sphinx search results
     * @param  array  $results Sphinx search results
     * @param  string $index   Index name
     * @return array
     */
    public function parseResults(array $results, $index);
}
