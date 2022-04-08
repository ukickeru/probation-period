<?php

namespace Mygento\AccessControlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('access_control_bundle');

        /*
         * Added because PHPStan doesn't see the method NodeDefinition::children()of RootNode's ArrayNodeDefinition class
         *
         * @phpstan-ignore-next-line
         */
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('app_user_entity_table_name')
                    ->info('Database-dependent table name of user entity, used in application (eg. app_user or `user`, etc.)')
                    ->example('app_user or `user`, etc.')
                    ->isRequired()
                    ->validate()->ifEmpty()->thenInvalid('Table name is required for bundle!')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
