<?php

namespace Bisouland\PreRegistrationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Registers BisoulandPreRegistrationBundle services into the application's DIC.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class BisoulandPreRegistrationExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
