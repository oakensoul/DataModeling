<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/DataModeling for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace Example;

use Zend\Mvc\MvcEvent;

class Module
{

    public function onBootstrap (MvcEvent $e)
    {}

    public function getConfig ()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig ()
    {
        $result = array ();
        $result['Zend\Loader\StandardAutoloader']['namespaces'][__NAMESPACE__] = __DIR__ . '/src/';
        return $result;
    }

    public function getServiceConfig ()
    {
        /**
         * Factories should be used when you have logic required to create the
         * requested service or object.
         * If it's a simple instantiation with no
         * dependencies, use an invokable
         */
        $factories = array ();

        $factories['ExampleDataAPI\Service'] = 'Example\ExampleDataAPI\Service\Factory';
        $factories['ExampleDataAPI\RouteStack'] = 'Example\ExampleDataAPI\RouteStack\Factory';

        $factories['Example\Resource\Game'] = 'Example\ExampleDataAPI\Resource\Game\Factory';

        /**
         * Invokables should be used for a simple instantiation with no
         * dependencies.
         * If you have logic required to create the requested
         * service or object, use a factory
         *
         * Generally, invokables are great for strategy objects / Listeners
         */
        $invokables = array ();

        $service_config = array (
            'factories' => $factories,
            'invokables' => $invokables
        );

        return $service_config;
    }
}
