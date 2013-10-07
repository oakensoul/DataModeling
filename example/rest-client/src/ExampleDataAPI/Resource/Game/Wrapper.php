<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/DataModeling for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace Example\ExampleDataAPI\Resource\Game;

use DataModeling\DataAccess\ServiceWrapper\REST;

class Wrapper extends REST\WrapperAbstract
{
    const RESOURCE_NAME = 'Game';

    /**
     * Stores the factory prototype
     */
    protected static $sFactoryPrototype;

    /**
     * Query -- FindBySeasonType
     *
     * Retrieves the Representation that matches the query from the ServiceAPI
     *
     * @param string $pType
     *
     * @return \SplDoublyLinkedList
     */
    public function FindByType ($pType)
    {
        $query = $this->GetQueryObject('FindByType');

        $payload = $query->GetPayloadPrototype();
        $payload->SetProperty(Query\Payload\FindByType::KEY_TYPE, $pType);

        $query->SetPayload($payload);
        $query->Execute();

        return $query->GetResult();
    }
}