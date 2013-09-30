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

/* Use statements for ZF2 namespaces */
//use Zend\Validator;
use Zend\Json\Json;
use Zend\Http;

/* Use statements for core php namespaces */
use SplDoublyLinkedList;

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

    protected function GetClientOptions ()
    {
        $result = array ();
        $result['timeout'] = $this->mQueryTimeout;
    }

    /**
     * Utility -- Execute
     *
     * executes the service call, logging, monitoring, etc for the service
     */
    public function Execute ()
    {
        $so = $this->GetServiceWrapper()->GetServiceObject();

        /**
         * All Query classes should be Get requests
         */
        $so->setMethod(Http\Request::METHOD_GET);

        // the Query should be responsible for adding the "parameters" onto the URL i suppose?
        $get_parameters = $this->FunctionThatDoesntExistYet(); // returns something like: "$pSeason/$pFoo/$pBar/"
        $so->setUri($so->getUri() . $get_parameters);

        $client_options = $this->GetClientOptions();

        $client = new Http\Client();
        $client->setOptions($client_options);

        $this->mServiceResponse = $client->dispatch($so);

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
    protected function ProcessResponse ($pResponse)
    {
        $representations = Json::decode($pResponse->getBody());

        foreach ($representations as $representation)
        {
            $this->mResult->push($representation);
        }

        $this->mResult->rewind();
    }
}