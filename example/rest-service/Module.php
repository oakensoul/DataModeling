<?php
namespace ExampleService;

use Zend\Mvc\MvcEvent;

class Module
{

    public function onBootstrap (MvcEvent $pEvent)
    {
        $event_manager = $pEvent->getApplication()->getEventManager();
        $shared_event_manager = $event_manager->getSharedManager();
        $service_manager = $pEvent->getApplication()->getServiceManager();
    }

    public function getAutoloaderConfig ()
    {
        $autoloaders = array ();

        $autoloaders['Zend\Loader\ClassMapAutoloader'] = array (
            __DIR__ . '/autoload_classmap.php'
        );

        $autoloaders['Zend\Loader\StandardAutoloader'] = array (
            'namespaces' => array (
                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
            )
        );

        return $autoloaders;
    }

    public function getConfig ()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig ()
    {
        $factories = array ();

        /**
         * Resource Mappers
         */
        $factories['ExampleService\Resource\VideoGame\Mapper'] = 'ExampleService\Resource\VideoGame\Factory';
        $factories['ExampleService\Resource\VideoGame\Listener'] = function  ($sm)
        {
            $mapper = $sm->get('ExampleService\Resource\VideoGame\Mapper');
            return new Resource\VideoGame\Listener($mapper);
        };

        $factories['ExampleService\Resource\Heartbeat\Mapper'] = 'ExampleService\Resource\Heartbeat\Factory';
        $factories['ExampleService\Resource\Heartbeat\Listener'] = function  ($sm)
        {
            $mapper = $sm->get('ExampleService\Resource\Heartbeat\Factory');
            return new Resource\Heartbeat\Listener($mapper);
        };

        $invokables = array ();

        return array (
            'factories' => $factories,
            'invokables' => $invokables
        );
    }
}