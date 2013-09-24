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

class ModelPropertyNotDefinedException extends Interrupt\ExceptionAbstract
{
    const MESSAGE = 'Attempt to call %1$s failed, property %2$s has not been defined.';

    public function __construct ($pMethod, $pProperty)
    {
        $message = sprintf(static::MESSAGE, $pMethod, $pProperty);

        /* this exception doesn't need to pass in a 'previous' exception or code as there shouldn't be one */
        parent::__construct($message);
    }
}