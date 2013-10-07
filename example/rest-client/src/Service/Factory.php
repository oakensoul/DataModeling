<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/DataModeling for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace Example\ExampleDataAPI\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http;

class Factory implements FactoryInterface
{

    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        $client = new Http\Client($config['ExampleService']['Location']);

        $request = $client->getRequest();
        $request->getQuery()->set('key', $config['ExampleService']['APIKey']);

        $headers = $request->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/json; charset=UTF-8');
        $headers->addHeaderLine('Accept', 'application/json');
        $headers->addHeaderLine('Accept-Encoding', 'gzip,deflate');

        return $client;
    }
}