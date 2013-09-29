<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\Query;

use DataModeling\DataAccess\Interfaces;
use DataModeling\DataAccess\Interrupt;

abstract class QueryAbstract
{

    /**
     * The payload object provided by the caller
     *
     * @var \Framework\DataAccess\Model\DomainModel
     */
    protected $mPayload;

    /**
     * Stores the payload class name so that we can validate the payload
     * but also so we can provide a prototype copy of the required payload
     *
     * @var string
     */
    protected $mPayloadClass;

    /**
     * Stores the result class name so that we can validate the result
     * but also so we can provide a prototype copy of the required result
     *
     * @var string
     */
    protected $mResultClass = 'SplDoublyLinkedList';

    /**
     * Stores the result of the query to be accessed by the caller.
     * The result
     * is determined by the ProcessResponse method
     *
     * @var mixed
     */
    protected $mResult;

    /**
     * Stores the Service Wrapper for this Query.
     *
     * @var Interfaces\ServiceWrapper
     */
    protected $mServiceWrapper;

    /**
     * Stores the "raw" response from service call
     *
     * @var mixed
     */
    protected $mServiceResponse;

    /**
     * Stores a prototype object for the payload we expect to receive.
     * This way we can own what type of payload we want in the query, and the
     * caller doesn't have to know any details
     *
     * Child class *must* implement an $sPayloadPrototype static property:
     * protected static $sPayloadPrototype;
     */
    //

    /**
     * The constructor for Queries must at the very least set the expected
     * payload class so that the script using the query can get a copy of
     * it to load with the appropriate data.
     */
    public function __construct ()
    {
        $exploded_namespace = explode('\\', get_class($this));
        $class = array_pop($exploded_namespace);

        $namespace = implode('\\', $exploded_namespace);

        $this->mNamespace = implode('\\', $exploded_namespace);

        $this->SetPayloadClass();
    }

    /**
     * Accessor -- SetServiceWrapper
     *
     * Allows the service wrapper for the currently instantiated object
     * to be changed to an alternate service wrapper
     *
     * @param Interfaces\ServiceWrapper $pWrapper
     */
    public function SetServiceWrapper (Interfaces\ServiceWrapper $pWrapper)
    {
        $this->mServiceWrapper = $pWrapper;
    }

    /**
     * DataPersistence -- GetServiceWrapper
     *
     * Returns the service wrapper that this model is currently using
     *
     * @return Interfaces\ServiceWrapper
     */
    public function GetServiceWrapper ()
    {
        return $this->mServiceWrapper;
    }

    /**
     * Accessor -- SetPayload
     *
     * Sets the payload that will be provided when the service call is made
     *
     * @param Model\StandardModel $pPayload
     */
    public function SetPayload ($pPayload)
    {
        if (! $pPayload instanceof $this->mPayloadClass)
        {
            throw new Interrupt\InvalidPayloadException(__METHOD__, get_class($pPayload), $this->mPayloadClass);
        }

        if (false == $pPayload->CheckLoaded())
        {
            throw new Interrupt\PayloadNotLoadedException(__METHOD__, $this->mPayloadClass);
        }

        $this->mPayload = $pPayload;
    }

    /**
     * Accessor -- GetPayload
     *
     * Should return the payload currently associated with the query object
     *
     * @return Model\StandardModel
     */
    public function GetPayload ()
    {
        return $this->mPayload;
    }

    /**
     * Utility -- GetPayloadPrototype
     *
     * Returns a prototype so that the client can use it to build the payload
     * object
     *
     * @return Model\StandardModel
     */
    public function GetPayloadPrototype ()
    {
        if (false === isset(static::$sPayloadPrototype))
        {
            static::$sPayloadPrototype = new $this->mPayloadClass();
        }

        return clone static::$sPayloadPrototype;
    }

    /**
     * Accessor -- GetResult
     *
     * Returns the result of the query after being processed by
     * process response
     *
     * @return mixed
     */
    public function GetResult ()
    {
        return $this->mResult;
    }

    /**
     * Utility -- GetResultPrototype
     *
     * Returns a prototype so that the client can use it to build the result
     * object
     *
     * @return mixed
     */
    public function GetResultPrototype ()
    {
        return new $this->mResultClass();
    }

    /**
     * Accessor -- SetPayloadClass
     *
     * This method should be used to define the $this->mPayloadClass
     * property so that it can be used by the SetPayload function
     */
    abstract protected function SetPayloadClass ();

    /**
     * Utility -- Execute
     *
     * executes the service call, logging, monitoring, etc for the service
     */
    abstract public function Execute ();

    /**
     * Helper -- ProcessResponse
     *
     * Should process the response from the remote service and parse it
     * into a format that will be returned to the caller
     */
    abstract protected function ProcessResponse ();
}