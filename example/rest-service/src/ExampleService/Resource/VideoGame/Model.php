<?php
namespace NFLDataService\Resource\VideoGame;

use DataModeling\DataAccess;

class Model extends DataAccess\Model\DomainModelAbstract
{
    const KEY_GAME_ID = 'GameId';

    /**
     *
     * @see \DataModeling\DataAccess\Model\StandardModelAbstract::SetPropertyMetaData()
     */
    protected function SetPropertyMetaData()
    {
        $properties = array();
        $properties[] = static::KEY_GAME_ID;

        /**
         * this model's primary key
         */
        $this->mPrimaryKeys[static::KEY_GAME_ID] = static::KEY_GAME_ID;

        foreach ($properties as $property)
        {
            $this->mPropertyMetaData[$property] = $this->BasePropertyMetaDataArray($property);
        }
    }
}