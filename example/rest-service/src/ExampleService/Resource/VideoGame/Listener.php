<?php
namespace ExampleService\Resource\VideoGame;

use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;
use DataModeling\DataAccess\ServiceWrapper\RestListenerAbstract;

class Listener extends RestListenerAbstract
{

    public function attach (EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array (
            $this,
            'onFetch'
        ));
        $this->listeners[] = $events->attach('fetchAll', array (
            $this,
            'onFetchAll'
        ));
    }

    public function onFetch (ResourceEvent $pEvent)
    {
        $id = $pEvent->getParam('id');

        $result = $this->mDataMapper->GetPrototype();
        $result->SetProperty(Model::KEY_GAME_ID, $id);

        try
        {
            $result = $this->mDataMapper->FetchByGameId($id)->current();
        }
        catch (\Exception $e)
        {
            throw new DomainException(get_class($e) . ' Game Not Found', 404);
        }

        return $result;
    }

    public function onFetchAll (ResourceEvent $pEvent)
    {
        $offset = $pEvent->getQueryParam('Offset', 0);
        $count = $pEvent->getQueryParam('Count', 6);

        try
        {
            $result = $this->mDataMapper->FetchAll($count, $offset);
        }
        catch (\Exception $e)
        {
            throw new DomainException(get_class($e) . ' No Games Found', 404);
        }

        return $result;
    }
}