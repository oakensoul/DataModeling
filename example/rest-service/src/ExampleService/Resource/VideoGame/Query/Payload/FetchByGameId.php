<?php
namespace ExampleService\Resource\VideoGame\Query\Payload;

use DataModeling\DataAccess\ServiceWrapper\PDO;

class FetchByGameId extends PDO\PayloadAbstract
{
    const KEY_GAME_ID = 'GameId';

    protected function SetPropertyMetaData()
    {
        $properties = array ();
        $properties[] = static::KEY_GAME_ID;

        foreach ($properties as $property)
        {
            $this->mPropertyMetaData[$property] = $this->BasePropertyMetaDataArray($property);
        }
    }
}