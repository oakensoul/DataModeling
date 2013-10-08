<?php
namespace ExampleService\Resource\Heartbeat;

/* Use statements for Framework namespaces */
use DataModeling\DataAccess;

class Mapper extends DataAccess\ServiceWrapper\PDO\WrapperAbstract
{
    const TABLE_NAME = 'Heartbeat';
    const USE_LAST_INSERT_ID = false;

    /**
     * Stores the factory prototype
     */
    protected static $sFactoryPrototype;
}