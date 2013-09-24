<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\ServiceWrapper;

use DataModeling\DataAccess\Interfaces;
use DataModeling\DataAccess\Interrupt;
use Exception;

abstract class WrapperAbstract implements Interfaces\ServiceWrapper
{

    /**
     * stores the final class' namespace so we can use it for programmatic
     * determination of class names and other magic
     *
     * @var string
     */
    protected $mNamespace;

    /**
     * storage for the service connection object, used for 'data' from
     * its source, though could be something else with DI
     */
    protected $mServiceObject;

    /**
     * Set as protected so that only Factory methods may create the
     * ServiceWrapper classes
     */
    protected function __construct ()
    {
        $exploded_namespace = explode('\\', get_class($this));
        $class = array_pop($exploded_namespace);

        $namespace = implode('\\', $exploded_namespace);

        $this->mNamespace = implode('\\', $exploded_namespace);
    }

    /**
     * Gets the namespace that queries should be contained under
     *
     * @return namespace
     */
    protected function GetQueryNamespace ()
    {
        return $this->mNamespace . '\\' . 'Query\\';
    }

    /**
     * Construction -- Factory
     *
     * Returns an instance of the Service Wrapper
     *
     * @return ServiceWrapper
     */
    public static function Factory ()
    {
        if (empty(static::$sFactoryPrototype))
        {
            static::$sFactoryPrototype = new static();
        }

        return clone static::$sFactoryPrototype;
    }

    /**
     * Accessor -- GetServiceObject
     *
     * Getter for the service object that is being wrapped
     *
     * @return Object
     */
    public function GetServiceObject ()
    {
        if (empty($this->mServiceObject))
        {
            $this->mServiceObject = $this->GetDefaultServiceObject();
        }

        return $this->mServiceObject;
    }

    /**
     * DataPersistence -- GetQueryObject
     *
     * This method will return a query object associated with the service
     * wrapper.
     *
     * @throws \Framework\DataAccess\Interrupt\QueryNotFoundException
     *
     * @return \Framework\DataAccess\Query\QueryAbstract
     */
    public function GetQueryObject ($pQueryName)
    {
        $query_object_name = $this->GetQueryNamespace() . $pQueryName;

        if (false === class_exists($query_object_name))
        {
            throw new Interrupt\QueryNotFoundException(get_class($this), $query_object_name);
        }

        $result = new $query_object_name();
        $result->SetServiceWrapper($this);

        return $result;
    }

    /**
     * Accessor -- SetServiceObject
     *
     * Setter for the service object that is being wrapped
     *
     * @param Object $pServiceObject
     */
    public function SetServiceObject ($pServiceObject)
    {
        throw new Exception('SetServiceObject is not implemented in abstract, but it is required to be defined to maintain the interface... stoopid php');
    }

    /**
     * Helper -- GetDefaultServiceObject
     *
     * Getter for the Default service object that is being wrapped. This is
     * meant
     * to be the default service connection if dependency injection is not used,
     * likely determined by a resources configuration in the application config
     *
     * @return Object
     */
    abstract protected function GetDefaultServiceObject ();
}