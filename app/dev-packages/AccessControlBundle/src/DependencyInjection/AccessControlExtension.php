<?php

namespace Mygento\AccessControlBundle\DependencyInjection;

use Mygento\AccessControlBundle\Core\Listener\DoctrineMetadataListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AccessControlExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator([
                __DIR__.'/../../config',
                __DIR__.'/../../config/packages',
            ])
        );

        $loader->load('services.yaml');
        $loader->load('doctrine.yaml');
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->register('mygento.access_control.doctrine_metadata_listener', DoctrineMetadataListener::class);
        $doctrineMetadataListenerDefinition = $container->getDefinition('mygento.access_control.doctrine_metadata_listener');
        $doctrineMetadataListenerDefinition->setArgument('$appUserEntityTableName', $config['app_user_entity_table_name']);
        $doctrineMetadataListenerDefinition->addTag('doctrine.event_listener', ['event' => 'loadClassMetadata']);
    }
}
