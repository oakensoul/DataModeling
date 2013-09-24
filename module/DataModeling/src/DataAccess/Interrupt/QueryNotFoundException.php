<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\Interrupt;

use DataModeling\Interrupt;

class QueryNotFoundException extends Interrupt\ExceptionAbstract
{
    const MESSAGE = 'Attempt to call query %2$s in %1$s failed, associated Query object was not found.';

    public function __construct ($pMethod, $pQuery)
    {
        $message = sprintf(static::MESSAGE, $pMethod, $pQuery);

        /* this exception doesn't need to pass in a 'previous' exception or code as there shouldn't be one */
        parent::__construct($message);
    }
}