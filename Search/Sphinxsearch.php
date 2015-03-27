<?php

namespace ItBlaster\SphinxSearchPropelBundle\Search;

use SphinxClient;

use ItBlaster\SphinxSearchPropelBundle\Exception\EmptyIndexException;
use ItBlaster\SphinxSearchPropelBundle\Exception\NoSphinxAPIException;
use ItBlaster\SphinxSearchPropelBundle\Doctrine\BridgeInterface;


/**
 * Sphinx search engine
 *
 * @method string GetLastError ()
 * @method string GetLastWarning ()
 * @method bool IsConnectError()
 * @method null SetServer ( $host, $port = 0 )
 * @method null SetConnectTimeout ( $timeout )
 *
 * @method null SetLimits ( $offset, $limit, $max=0, $cutoff=0 )
 * @method null setMaxQueryTime ( $max )
 * @method null SetMatchMode ( $mode )
 * @method null SetSortMode ( $mode, $sortby="" )
 * @method null SetFieldWeights ( $weights )
 * @method null SetIndexWeights ( $weights )
 * @method null SetIDRange ( $min, $max )
 * @method null SetFilter ( $attribute, $values, $exclude=false )
 * @method null SetFilterRange ( $attribute, $min, $max, $exclude=false )
 * @method null SetFilterFloatRange ( $attribute, $min, $max, $exclude=false )
 * @method null SetGeoAnchor ( $attrlat, $attrlong, $lat, $long )
 * @method null SetGroupBy ( $attribute, $func, $groupsort="@group desc" )
 * @method null SetGroupDistinct ( $attribute )
 * @method null SetRetries ( $count, $delay=0 )
 * @method null SetArrayResult ( $arrayresult )
 * @method null SetOverride ( $attrname, $attrtype, $values )
 * @method null SetSelect ( $select )
 *
 * @method null ResetFilters ()
 * @method null ResetGroupBy ()
 * @method null ResetOverrides ()
 *
 * @method bool|array BuildExcerpts ( $docs, $index, $words, $opts=array() )
 * @method bool|array BuildKeywords ( $query, $index, $hits )
 *
 * @method array Status ()
 */
class Sphinxsearch
{
    /**
     * @var string $host
     */
    protected $host;

    /**
     * @var string $port
     */
    protected $port;

    /**
     * @var string $socket
     */
    protected $socket;

    /**
     * @var SphinxClient $sphinx
     */
    protected $sphinx;

    /**
     * @var BridgeInterface
     */
    protected $bridge;

    /**
     * Constructor
     *
     * @param string $host Sphinx host
     * @param string $port Sphinx port
     * @param string $socket UNIX socket. Not required
     *
     * @throws NoSphinxAPIException If class SphinxClient does not exists
     */
    public function __construct($host, $port, $socket = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->socket = $socket;

        if (!class_exists('SphinxClient')) {
            throw new NoSphinxAPIException('You should include PHP SphinxAPI');
        }

        $this->sphinx = new SphinxClient();

        if (!is_null($this->socket)) {
            $this->sphinx->setServer($this->socket);
        } else {
            $this->sphinx->setServer($this->host, $this->port);
        }

        if (!defined('SEARCHD_OK')) {
            // To prevent notice
            define('SEARCHD_OK', 0);
        }

        if (!defined('SEARCHD_WARNING')) {
            // To prevent notice
            define('SEARCHD_WARNING', 3);
        }
    }

    /**
     * Set bridge to database
     *
     * @param BridgeInterface $bridge
     */
    public function setBridge(BridgeInterface $bridge)
    {
        $this->bridge = $bridge;
    }

    /**
     * @return BridgeInterface
     */
    public function getBridge()
    {
        return $this->bridge;
    }

    /**
     * Search for a query string
     * @param  string  $query   Search query
     * @param  array   $indexes Index list to perform the search
     * @param  boolean $escape  Should the query to be escaped?
     *
     * @return array           Search results
     *
     * @throws EmptyIndexException If $indexes is empty
     * @throws \RuntimeException If seaarch failed
     */
    public function search($query, array $indexes, $escape = true)
    {
        if (empty($indexes)) {
            throw new EmptyIndexException('Try to search with empty indexes');
        }

        if ($escape) {
            $query = $this->getClient()->escapeString($query);
        }

        foreach($indexes as $index) {
            $this->getClient()->AddQuery ( $query, $index);
        }
        $results = $this->getClient()->RunQueries();

        if($results && count($results)) {
            foreach ($results as $result) {
                if (!is_array($result)) {
                    throw new \RuntimeException(sprintf('Searching for "%s" failed. Result is not an array. Error "%s"', $query, $this->sphinx->getLastError()));
                }

                if (!isset($result['status'])) {
                    throw new \RuntimeException(sprintf('Searching for "%s" failed. Result with no status. Error "%s"', $query, $this->sphinx->getLastError()));
                }

                if ($result['status'] !== SEARCHD_OK && $result['status'] !== SEARCHD_WARNING) {
                    throw new \RuntimeException(sprintf('Searching for "%s" failed. Result has bad status. Error "%s"', $query, $this->sphinx->getLastError()));
                }
            }
        }
        return $results;
    }

    /**
     * Search for a query string and convert results to entities
     * @param  string  $query   Search query
     * @param  string|array   $index Index name(s) for search
     * @param  boolean $escape  Should the query to be escaped?
     *
     * @return SearchResult Search results
     *
     * @throws \InvalidArgumentException If $index is not valid
     * @throws \LogicException If bridge was not set
     */
    public function searchEx($query, $index, $escape = true, $locale = 'en')
    {
        if (!is_string($index) && !is_array($index)) {
            throw new \InvalidArgumentException('Index must be a string or an array');
        }

        if (is_string($index) && (mb_strpos($index, ' ') !== false || mb_strpos($index, "\t") !== false)) {
            throw new \InvalidArgumentException('Index must not contains a spaces');
        }

        if ($this->bridge === null) {
            throw new \LogicException('Bridge was not set');
        }

        $results = $this->search($query, (is_string($index) ? array($index) : $index), $escape);

        if (!is_array($results)) {
            $results = array();
        }

        /*
        if (empty($results) || !empty($results['error'])) {
            return $results;
        }
        */

        return new SearchResult($this->getBridge()->parseResults($results, $index, $locale));
    }

    /**
     * Adds a query to a multi-query batch
     *
     * @param string $query Search query
     * @param array $indexes Index list to perform the search
     *
     * @throws EmptyIndexException If $indexes is empty
     */
    public function addQuery($query, array $indexes)
    {
        if (empty($indexes)) {
            throw new EmptyIndexException('Try to search with empty indexes');
        }

        $this->sphinx->addQuery($query, implode(' ', $indexes));
    }

    /**
     * Set filter to select between two dates $datestart and $dateend
     * If set only one date it also works
     * If not one date is set method do nothing
     *
     * @param string $attr Timestamp attr
     * @param \DateTime $datestart Date to start filter
     * @param \DateTime $dateend   Date to end filter
     */
    public function setFilterBetweenDates($attr, \DateTime $datestart = null, \DateTime $dateend = null)
    {
        if ($datestart && $dateend) {
            $tsStart = (int)$datestart->format('U');
            $tsEnd = (int)$dateend->format('U');

            $this->getClient()->setFilterRange($attr, $tsStart, $tsEnd);
        } elseif ($datestart) {
            $tsStart = (int)$datestart->format('U');
            $tsEnd = PHP_INT_MAX;

            $this->getClient()->setFilterRange($attr, $tsStart, $tsEnd);
        } elseif ($dateend) {
            $tsStart = 0;
            $tsEnd = (int)$dateend->format('U');

            $this->getClient()->setFilterRange($attr, $tsStart, $tsEnd);
        }
    }

    /**
     * Magic PHP-proxy for SphinxAPI
     *
     * Methods listed in class-level PHPDoc
     */
    public function __call($method, $args)
    {
        if (is_callable(array($this->sphinx, $method))) {
            return call_user_func_array(array($this->sphinx, $method), $args);
        } else {
            trigger_error("Call to undefined method '{$method}'");
        }
    }

    /**
     * Get Sphinx client
     * @return SphinxClient
     */
    public function getClient()
    {
        return $this->sphinx;
    }
}
