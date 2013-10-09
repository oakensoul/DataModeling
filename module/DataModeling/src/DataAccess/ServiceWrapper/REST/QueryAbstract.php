<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\ServiceWrapper\REST;

/* Use statements for Framework namespaces */
use DataModeling\DataAccess\Interrupt;
use DataModeling\DataAccess\ServiceWrapper;
use DataModeling\Utility;

/* Use statements for ZF2 namespaces */
// use Zend\Validator;
use Zend\Json\Json;
use Zend\Mvc\Router\RouteStackInterface;

/* Use statements for core php namespaces */
use SplDoublyLinkedList;
use Traversable;
use Exception;

abstract class QueryAbstract extends ServiceWrapper\QueryAbstract
{

    /**
     * Allows the caller to disable the empty model exception from being
     * throw and just return an empty SPL object instead.
     *
     * @var bool
     */
    protected $mThrowEmptyModelExceptions = false;

    protected $mQueryTimeout = 30;

    protected $mRouteName;

    /**
     * RouteStackInterface instance.
     *
     * @var RouteStackInterface
     */
    protected $mRouter;

    /**
     * RouteInterface match returned by the router.
     *
     * @var RouteMatch.
     */
    protected $routeMatch;

    protected function GetClientOptions ()
    {
        $result = array ();
        $result['timeout'] = $this->mQueryTimeout;

        return $result;
    }

    /**
     * Utility -- Execute
     *
     * executes the service call, logging, monitoring, etc for the service
     */
    public function Execute ()
    {
        $client = $this->GetServiceWrapper()->GetServiceObject();
        $client_options = $this->GetClientOptions();
        $client->setOptions($client_options);

        $this->setRouter($this->GetServiceWrapper()->GetRouteStack() );

        $request = $client->getRequest();
        $request->setMethod(static::REQUEST_METHOD);
        $request->getUri()->setPath($this->GetRoute());

        $this->mServiceResponse = $client->dispatch($request)->GetBody();

        $this->mResult = $this->GetResultPrototype();

        if (empty($this->mServiceResponse))
        {
            if (true === $this->mThrowEmptyModelExceptions)
            {
                throw new Interrupt\ModelNotFoundFailure(get_class($this), get_class($this->GetServiceWrapper()->GetPrototype()), $stmt->queryString);
            }
        }
        else
        {
            $this->ProcessResponse();
        }

        return $this->mResult;
    }

    /**
     * Generates an url given the name of a route.
     *
     * @return string Url For the link href attribute
     *
     * @throws Exception If no RouteStackInterface was provided
     * @throws Exception If RouteName not defined
     */
    public function GetRoute ()
    {
        // things that need to be figured out;
        $params = $this->GetPayload()->extract();
        $options = array ();

        if (null === $this->mRouter)
        {
            throw new Exception('No RouteStackInterface instance provided');
        }

        if ($this->mRouteName === null)
        {
            throw new Exception('RouteName not defined for ' . get_class($this));
        }

        $options['name'] = $this->mRouteName;

        $url = $this->mRouter->assemble($params, $options);

        return $url;
    }

    /**
     * Set the mRouter to use for assembling.
     *
     * @param RouteStackInterface $pRouter
     * @return Url
     */
    public function setRouter (RouteStackInterface $pRouter)
    {
        $this->mRouter = $pRouter;
        return $this;
    }

    /**
     * Accessor -- SetPayloadClass
     *
     * This method should be used to define the $this->mPayloadClass
     * property so that it can be used by the SetPayload function
     */
    protected function SetPayloadClass ()
    {
        $exploded_namespace = explode('\\', get_class($this));
        $class = array_pop($exploded_namespace);

        $namespace = implode('\\', $exploded_namespace);

        $this->mPayloadClass = $this->mNamespace . '\\Payload\\' . $class;
    }

    /*
     * @return SplDoublyLinkedList
     */
    protected function ProcessResponse ()
    {
        $representations = Json::decode($this->mServiceResponse);

        foreach ($representations as $representation)
        {
            $data = Utility\ComplexTypeConverter::StdClassToArray($representation);

            $model = $this->GetServiceWrapper()->GetPrototype();
            $model->Hydrate($data);

            $this->mResult->push($model);
        }

        $this->mResult->rewind();
    }
}