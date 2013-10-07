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

/* Use statements for Framework namespaces */
use DataModeling\DataAccess;

class Model extends DataAccess\Model\DomainModelAbstract
{
    const KEY_ID = 'Id';
    const KEY_RELEASE_DATE = 'ReleaseDate';
    const KEY_PLATFORM = 'Platform';
    const KEY_OWNED = 'Owned';
    const KEY_NAME = 'Name';
    const KEY_SHORT_NAME = 'ShortName';
    const KEY_TYPE = 'Type';
    const TYPE_RPG = 'RPG';
    const TYPE_RTS = 'RTS';
    const TYPE_ACTION = 'Action';
    const TYPE_ALL = 'all';

    /**
     *
     * @see \Framework\DataAccess\Model\StandardModelAbstract::SetPropertyMetaData()
     */
    protected function SetPropertyMetaData ()
    {
        $properties = array ();
        $properties[] = static::KEY_ID;
        $properties[] = static::KEY_RELEASE_DATE;
        $properties[] = static::KEY_PLATFORM;
        $properties[] = static::KEY_OWNED;
        $properties[] = static::KEY_NAME;
        $properties[] = static::KEY_SHORT_NAME;
        $properties[] = static::KEY_TYPE;

        foreach ($properties as $property)
        {
            $this->mPropertyMetaData[$property] = $this->BasePropertyMetaDataArray($property);
        }
    }
}