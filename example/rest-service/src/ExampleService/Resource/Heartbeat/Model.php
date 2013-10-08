<?php
namespace NFLDataService\Resource\Heartbeat;

/* Use statements for Framework namespaces */
use Framework\DataAccess;

class Model extends DataAccess\Model\DomainModelAbstract
{
    const KEY_ID = 'Id';
    const KEY_METHOD = 'Method';
    const KEY_TIME = 'Time';
    const KEY_PARAMETERS = 'Parameters';

    /**
     *
     * @see \Framework\DataAccess\Model\StandardModelAbstract::SetPropertyMetaData()
     */
    protected function SetPropertyMetaData()
    {
        $properties = array();
        $properties[] = static::KEY_ID;
        $properties[] = static::KEY_METHOD;
        $properties[] = static::KEY_TIME;
        $properties[] = static::KEY_PARAMETERS;

        /**
         * this model's primary key
         */
        $this->mPrimaryKeys[static::KEY_ID] = static::KEY_ID;

        foreach ($properties as $property)
        {
            $this->mPropertyMetaData[$property] = $this->BasePropertyMetaDataArray($property);
        }
    }
}