<?php
namespace ExampleService\Resource\VideoGame;

/* Use statements for Framework namespaces */
use DataModeling\DataAccess;

class Mapper extends DataAccess\ServiceWrapper\PDO\WrapperAbstract
{
    const TABLE_NAME = 'VideoGame';
    const USE_LAST_INSERT_ID = false;

    /**
     * Stores the factory prototype
     */
    protected static $sFactoryPrototype;

    /**
     * Query -- FetchByGameId
     *
     * Returns the row that matches the provided data
     *
     * @param int $pId
     *
     * @return \SplDoublyLinkedList
     */
    public function FetchByGameId($pId)
    {
        // @todo Validators

        /* @var $query Query\FetchByGameId */
        $query = $this->GetQueryObject('FetchByGameId');

        /* @var $payload Query\Payload\FindBySeasonWeekMetric */
        $payload = $query->GetPayloadPrototype();
        $payload->SetProperty(Query\Payload\FetchByGameId::KEY_GAME_ID, $pId);

        $query->SetPayload($payload);
        $query->Execute();

        return $query->GetResult();
    }

    /**
     * DataPersistence -- Read
     *
     * @param $pModel DataAccess\Model\DomainModelAbstract
     *
     * @throws DataAccess\Interrupt\InvalidModelException
     * @throws DataAccess\Interrupt\ModelPrimaryKeyNotSetException
     * @throws DataAccess\Interrupt\ModelReadException
     *
     * @see \DataModeling\DataAccess\Interfaces\DataMapper::Read()
     */
    public function Read(DataAccess\Model\DomainModelAbstract $pModel)
    {
        throw new DataAccess\Interrupt\QueryNotFoundException('Read', get_class($pModel));
    }

    /**
     * DataPersistence -- Update
     *
     * @throws DataAccess\Interrupt\QueryNotFoundException
     */
    public function Update(DataAccess\Model\DomainModelAbstract $pModel)
    {
        throw new DataAccess\Interrupt\QueryNotFoundException('Update', get_class($pModel));
    }

    /**
     * DataPersistence -- Delete
     *
     * @throws DataAccess\Interrupt\QueryNotFoundException
     */
    public function Delete(DataAccess\Model\DomainModelAbstract $pModel)
    {
        throw new DataAccess\Interrupt\QueryNotFoundException('Delete', get_class($pModel));
    }

    /**
     * DataPersistence -- Save
     *
     * @throws DataAccess\Interrupt\QueryNotFoundException
     */
    public function Save(DataAccess\Model\DomainModelAbstract $pModel)
    {
        throw new DataAccess\Interrupt\QueryNotFoundException('Save', get_class($pModel));
    }
}