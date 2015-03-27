<?php

namespace ItBlaster\SphinxSearchPropelBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ItBlasterSphinxSearchPropelExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');
        $loader->load('services.yml');

        if (isset($config['searchd'])) {
            $container->setParameter('it_blaster.sphinx_search_propel.searchd.host', $config['searchd']['host']);
            $container->setParameter('it_blaster.sphinx_search_propel.searchd.port', $config['searchd']['port']);
            $container->setParameter('it_blaster.sphinx_search_propel.searchd.socket', $config['searchd']['socket']);
        }

        if (isset($config['sphinx_api'])) {
            $container->setParameter('it_blaster.sphinx_search_propel.sphinx_api.file', $config['sphinx_api']['file']);
        }

        if (isset($config['indexes'])) {
            $container->setParameter('it_blaster.sphinx_search_propel.indexes', $config['indexes']);
        }

        if (isset($config['doctrine'])) {
            $container->setParameter('it_blaster.sphinx_search_propel.doctrine.entity_manager', $config['doctrine']['entity_manager']);
        }
    }

    public function getAlias()
    {
        return 'sphinx_search_propel';
    }
}
