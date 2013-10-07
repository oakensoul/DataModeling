<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/DataModeling for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace Example\ExampleDataAPI\Resource\Game;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Factory implements FactoryInterface
{

    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $service = $serviceLocator->get('ExampleDataAPI\Service');

        $wrapper = Wrapper::Factory();

        $wrapper->SetServiceObject($service);

        $route_stack = $serviceLocator->get('ExampleDataAPI\RouteStack');
        $wrapper->setRouteStack($route_stack);

        return $wrapper;
    }
}