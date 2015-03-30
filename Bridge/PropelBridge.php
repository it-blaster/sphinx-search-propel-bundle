<?php

namespace ItBlaster\SphinxSearchPropelBundle\Bridge;

use Symfony\Component\DependencyInjection\Container;


/**
 * Bridge to find entities for search results
 */
class PropelBridge implements BridgeInterface
{
    /**
     * Symfony2 container
     * @var Container
     */
    protected $container;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * EntityManager name
     * @var string
     */
    protected $emName;

    /**
     * Indexes list
     * Key is index name
     * Value is entity name
     *
     * @var array
     */
    protected $indexes = array();

    /**
     * Constructor
     *
     * @param Container $container Symfony2 DI-container
     * @param string[optional] $emName EntityManager name
     * @param array[optional] $indexes List of search indexes with entity names
     */
    public function __construct(Container $container, $emName = 'default', $indexes = array())
    {
        $this->container = $container;
        $this->emName = $emName;
        $this->setIndexes($indexes);
    }

    /**
     * Индексы
     *
     * @return array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Get an EntityManager
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if ($this->em === null) {
            $this->setEntityManager($this->container->get('propel')->getManager($this->emName));
        }

        return $this->em;
    }

    /**
     * Set an EntityManager
     *
     * @param EntityManager $em
     *
     * @throws LogicException If entity manager already set
     */
    public function setEntityManager(EntityManager $em)
    {
        if ($this->em !== null) {
            throw new \LogicException('Entity manager can only be set before any results are fetched');
        }

        $this->em = $em;
    }

    /**
     * Set indexes list
     *
     * @param array $indexes
     */
    public function setIndexes(array $indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * Add entity list to sphinx search results
     * @param  array  $results Sphinx search results
     * @param  string|array $index   Index name(s)
     *
     * @return array
     *
     * @throws LogicException If results come with error
     * @throws InvalidArgumentException If index name is not valid
     */
    public function parseResults(array $results, $index, $locale = 'en')
    {
        foreach($results as $result) {
            if (!empty($result['error'])) {
                throw new \LogicException('Search completed with errors');
            }
        }

        $dbQueries = array();
        $index_names = array();
        if (is_string($index)) {
            if (!isset($this->indexes[$index])) {
                throw new \InvalidArgumentException('Unknown index name: '.$index);
            }
        } elseif (is_array($index)) {
            foreach ($index as $index_name) {
                if (!isset($this->indexes[$index_name])) {
                    throw new \InvalidArgumentException('Unknown index name: '.$index_name);
                }
                $dbQueries[] = $this->indexes[$index_name];
                $index_names[] = $index_name;
            }
        }

        //$dbQueries = array_keys($this->indexes);

        foreach($results as $i => &$result) {
            $result['index'] = $index_names[$i];
            //$query_class = new $this->indexes[$dbQueries[$i]];
            $query_class = new $dbQueries[$i];
            $ids = array();
            if (isset($result['matches']) && count($result['matches'])) {
                foreach ($result['matches'] as $id => &$match) {
                    $match['object'] = false;
                    $ids[] = $id;
                }
            }
            if (count($ids)) {
                $objects = $query_class::create()->filterById($ids)->find();
                foreach($objects as $object) {
                    $object->setLocale($locale);
                    $result['matches'][$object->getId()]['object'] = $object;
                }
            }
        }
        return $results;
    }
}
