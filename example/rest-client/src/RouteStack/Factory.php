<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/DataModeling for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace Example\ExampleDataAPI\RouteStack;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Router\Http\TreeRouteStack;

class Factory implements FactoryInterface
{

    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');

        $stack_config = $config['ExampleDataAPI']['routes'];

        $route_plugin_manager = $serviceLocator->get('RoutePluginManager');

        $routerClass = 'Zend\Mvc\Router\Http\TreeRouteStack';

        $routerConfig['routes'] = $config['ExampleDataAPI']['routes'];
        $routerConfig['route_plugins'] = $route_plugin_manager;

        $router = TreeRouteStack::factory($routerConfig);

        return $router;
    }
}