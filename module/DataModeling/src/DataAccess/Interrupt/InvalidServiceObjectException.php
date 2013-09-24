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

class InvalidServiceObjectException extends Interrupt\ExceptionAbstract
{
    const MESSAGE = 'Invalid Service Object passed to %1$s. Expected %2$s object, received %3$s.';

    public function __construct ($pMethod, $pExpectedObject, $pReceived)
    {
        $message = sprintf(static::MESSAGE, $pMethod, $pExpectedObject, $pReceived);

        /* this exception doesn't need to pass in a 'previous' exception or code as there shouldn't be one */
        parent::__construct($message);
    }
}