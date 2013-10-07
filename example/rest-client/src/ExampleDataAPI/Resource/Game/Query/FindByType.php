<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/DataModeling for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace Example\ExampleDataAPI\Resource\Game\Query;

use DataModeling\DataAccess\ServiceWrapper\REST;
use Zend\Http;

class FindByType extends REST\QueryAbstract
{

    /**
     * Stores a prototype object for the payload we expect to receive.
     * This way we can own what type of payload we want in the query, and the
     * caller doesn't have to know any details
     */
    protected static $sPayloadPrototype;

    protected $mRouteName = 'game/type';
    const REQUEST_METHOD = Http\Request::METHOD_GET;
}