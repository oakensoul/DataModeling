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
use DataModeling\DataAccess\Model;
use DataModeling\DataAccess\ServiceWrapper;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Router\RouteStackInterface;

/* Use statements for Zend namespaces */
use Exception;

abstract class WrapperAbstract extends ServiceWrapper\WrapperAbstract implements EventManagerAwareInterface, ServiceLocatorAwareInterface
{

    /**
     * Prototype data model for this mapper
     *
     * @var Model;
     */
    protected $mModelPrototype;

    protected $mEvents;

    protected $mServices;

    protected $mRouteStack;

    /**
     * Accessor -- SetServiceObject
     *
     * Setter for the service object that is being wrapped
     * This isn't very serviceable but again, i'm just futzing to get it working
     *
     * @param Zend\Http\Request $pServiceObject
     *
     * @see \Framework\DataAccess\ServiceWrapper\WrapperAbstract::SetServiceObject()
     */
    public function SetServiceObject ($pServiceObject)
    {
        if (! is_object($pServiceObject))
        {
            throw new Interrupt\InvalidServiceObjectException(__METHOD__, 'Zend\Http\Client', 'non-object');
        }

        if (! $pServiceObject instanceof \Zend\Http\Client)
        {
            throw new Interrupt\InvalidServiceObjectException(__METHOD__, 'Zend\Http\Client', get_class($pServiceObject));
        }

        $this->mServiceObject = $pServiceObject;
    }

    /**
     * Helper -- GetDefaultServiceObject
     *
     * Getter for the Default service object that is being wrapped. This is
     * meant
     * to be the default service connection if dependency injection is not used,
     * likely determined by a resources configuration in the application config
     *
     * @see \Framework\DataAccess\ServiceWrapper\WrapperAbstract::GetDefaultServiceObject()
     *
     * @return PDO
     */
    protected function GetDefaultServiceObject ()
    {
        throw new \Exception('DefaultServiceObject not defined.');
    }

    /**
     * Helper -- GetDefaultPrototype
     *
     * The assumption is that there will be a "Model.php" within the namespace
     * corresponding to the DataMapper and thus we can generate a default model
     *
     * This represnets the prototypical data model to be used with this mapper
     *
     * @see \DataModeling\DataAccess\Interfaces\DataMapper::GetDefaultPrototype()
     */
    protected function GetDefaultPrototype ()
    {
        $model = $this->mNamespace . '\\' . 'Model';

        return new $model($this);
    }

    /**
     * Accessor -- GetPrototype
     *
     * Returns a prototype model for this data mapper
     *
     * @see \DataModeling\DataAccess\Interfaces\DataMapper::GetPrototype()
     *
     * @return Model\DomainModelAbstract
     */
    public function GetPrototype ()
    {
        if (empty($this->mModelPrototype))
        {
            $this->mModelPrototype = $this->GetDefaultPrototype();
        }

        return clone $this->mModelPrototype;
    }

    /**
     * Accessor -- SetPrototype
     *
     * Setter for the prototype model
     *
     * @throws Interrupt\InvalidModelException
     *
     * @see \DataModeling\DataAccess\Interfaces\DataMapper::SetPrototype()
     */
    public function SetPrototype (Model\DomainModelAbstract $pPrototype)
    {
        $this->RequireVerifiedModel(__METHOD__, $pPrototype);

        $this->mModelPrototype = $pPrototype;
    }

    /**
     * Set the RouteStack object that the Query route names
     * will be processed from by the QueryAbstract class
     *
     * @param RouteStackInterface $pRouteStack
     */
    public function SetRouteStack (RouteStackInterface $pRouteStack)
    {
        $this->mRouteStack = $pRouteStack;
    }

    /**
     * Retrieve the RouteStack object
     *
     * @return RouteStackInterface
     */
    public function GetRouteStack ()
    {
        return $this->mRouteStack;
    }

    /**
     * Helper -- RequireVerifiedModel
     *
     * @param string $pMethod
     * @param Model\DomainModelAbstract $pModel
     *
     * @throws Interrupt\InvalidModelException
     */
    protected function RequireVerifiedModel ($pMethod, Model\DomainModelAbstract $pModel)
    {
        $expected_model = $this->mNamespace . '\\' . 'Model';

        if (! $pModel instanceof $expected_model)
        {
            throw new Interrupt\InvalidModelException($pMethod, $expected_model, get_class($pModel));
        }
    }

    public function setEventManager (EventManagerInterface $pEvents)
    {
        $pEvents->addIdentifiers(array (
            get_called_class()
        ));

        $this->mEvents = $pEvents;
        return $this;
    }

    public function getEventManager ()
    {
        return $this->mEvents;
    }

    public function setServiceLocator (ServiceLocatorInterface $pServiceLocator)
    {
        $this->mServices = $pServiceLocator;
    }

    public function getServiceLocator ()
    {
        return $this->mServices;
    }
}