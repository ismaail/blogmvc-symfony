<?php

namespace App;

use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @param \Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $c
     */
    protected function configureContainer(ContainerConfigurator $c): void
    {
        $c->import('../config/{packages}/*.yaml');
        $c->import('../config/{packages}/' . $this->environment . '/*.yaml');

        $path = \dirname(__DIR__) . '/config/services.php';

        if (is_file(\dirname(__DIR__) . '/config/services.yaml')) {
            $c->import('../config/services.yaml');
            $c->import('../config/{services}_' . $this->environment . '.yaml');
        } elseif (is_file($path)) {
            (require $path)($c->withPath($path), $this);
        }
    }

    /**
     * @param \Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator $routes
     */
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        $path = \dirname(__DIR__) . '/config/routes.php';

        if (is_file(\dirname(__DIR__) . '/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } elseif (is_file($path)) {
            (require $path)($routes->withPath($path), $this);
        }
    }
}
