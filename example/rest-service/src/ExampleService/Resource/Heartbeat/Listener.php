<?php
namespace ExampleService\Resource\Heartbeat;

use PhlyRestfully\Exception\CreationException;
// use PhlyRestfully\Exception\PatchException;
// use PhlyRestfully\Exception\UpdateException;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use DataModeling\DataAccess\Interfaces\DataMapper;

class Listener extends AbstractListenerAggregate
{

    protected $mDataMapper;

    public function __construct (DataMapper $pMapper)
    {
        $this->mDataMapper = $pMapper;
    }

    public function attach (EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('create', array (
            $this,
            'onCreate'
        ));
        $this->listeners[] = $events->attach('fetch', array (
            $this,
            'onFetch'
        ));
        $this->listeners[] = $events->attach('fetchAll', array (
            $this,
            'onFetchAll'
        ));
    }

    public function onCreate (ResourceEvent $e)
    {
        // PhlyRestfully\Exception\CreationException
        $data = $e->getParam('data');
        // $paste = $this->mDataMapper->save($data);
        $paste = false;
        if (! $paste)
        {
            throw new CreationException();
        }
        return NULL;
    }

    public function onFetch (ResourceEvent $e)
    {
        // PhlyRestfully\Exception\DomainException
        $id = $e->getParam('id');

        $result = $this->mDataMapper->GetPrototype();
        $result->SetProperty(Model::KEY_ID, $e->getParam('id'));
        $result->SetProperty(Model::KEY_TIME, time());
        $result->SetProperty(Model::KEY_METHOD, 'get');
        $result->SetProperty(Model::KEY_PARAMETERS, NULL);

        if (false)
        {
            throw new DomainException('Heartbeat Not Found, 404');
        }

        return $result;
    }

    public function onFetchAll (ResourceEvent $e)
    {
        $result = $this->mDataMapper->GetPrototype();
        $result->SetProperty(Model::KEY_ID, 42);
        $result->SetProperty(Model::KEY_TIME, time());
        $result->SetProperty(Model::KEY_METHOD, 'get-fetch-all');
        $result->SetProperty(Model::KEY_PARAMETERS, NULL);

        return array (
            $result
        );
        return $this->mDataMapper->fetchAll();
    }
}