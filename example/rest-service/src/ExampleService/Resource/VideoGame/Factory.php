<?php
namespace ExampleService\Resource\VideoGame;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Factory implements FactoryInterface
{

    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $resource = $serviceLocator->get('ExampleService\Database\Resource');

        $mapper = Mapper::Factory();
        $mapper->SetServiceObject($resource);

        return $mapper;
    }
}