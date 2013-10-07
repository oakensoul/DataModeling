<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/DataModeling for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace Example\ExampleDataAPI\Resource\Game\Query\Payload;

use DataModeling\DataAccess\ServiceWrapper\REST;

class FindByType extends REST\PayloadAbstract
{
    const KEY_TYPE = 'Type';

    protected function SetPropertyMetaData ()
    {
        $properties = array ();
        $properties[] = static::KEY_TYPE;

        foreach ($properties as $property)
        {
            $this->mPropertyMetaData[$property] = $this->BasePropertyMetaDataArray($property);
        }
    }
}