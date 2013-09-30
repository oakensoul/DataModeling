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

use DataModeling\DataAccess\Interfaces\DataMapper;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\PluginManager;

abstract class ListenerAbstract extends AbstractListenerAggregate implements ServiceLocatorAwareInterface
{

    protected $mDataMapper;

    protected $mServiceLocator;

    /**
     *
     * @var PluginManager
     */
    protected $plugins;

    public function __construct (DataMapper $pMapper)
    {
        $this->mDataMapper = $pMapper;
    }

    /**
     * Set serviceManager instance
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->mServiceLocator = $serviceLocator;
    }

    /**
     * Retrieve serviceManager instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator ()
    {
        return $this->mServiceLocator;
    }
}