<?php
namespace ExampleService\Resource\VideoGame\Query;

use DataModeling\DataAccess\ServiceWrapper\PDO;

class FetchByGameId extends PDO\StoredProcedureAbstract
{

    /**
     * Stores a prototype object for the payload we expect to receive.
     * This way we can own what type of payload we want in the query, and the
     * caller doesn't have to know any details
     */
    protected static $sPayloadPrototype;

    /**
     * Tells execute whether to kick off limit binding or not
     *
     * @var bool
     */
    protected $mLimitEnabled = false;

    /**
     * Accessor -- GetProcedureName
     *
     * Returns the stored procedure name for this query
     *
     * @return string
     */
    public function GetProcedureName ()
    {
        return 'FetchByGameId';
    }
}