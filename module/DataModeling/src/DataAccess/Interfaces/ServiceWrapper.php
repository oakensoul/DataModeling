<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\Interfaces;

interface ServiceWrapper
{

    /**
     * Construction -- Factory
     *
     * Returns an instance of the Service Wrapper
     *
     * @return ServiceWrapper
     */
    public static function Factory ();

    /**
     * Accessor -- SetServiceObject
     *
     * Setter for the service object that is being wrapped
     *
     * @param Object $pServiceObject
     */
    public function SetServiceObject ($pServiceObject);

    /**
     * Accessor -- GetServiceObject
     *
     * Getter for the service object that is being wrapped
     *
     * @return Object
     */
    public function GetServiceObject ();

    /**
     * DataPersistence -- GetQueryObject
     *
     * This method will return a query object associated with the service wrapper.
     *
     * @throws \Framework\DataAccess\Interrupt\QueryNotFoundException
     *
     * @return \Framework\DataAccess\Query\QueryAbstract
     */
    public function GetQueryObject ($pQueryName);
}