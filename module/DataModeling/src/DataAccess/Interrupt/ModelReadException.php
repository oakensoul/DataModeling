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
use Exception;

class ModelReadException extends Interrupt\ExceptionAbstract
{
    const MESSAGE = 'Attempting to read a new %2$s model failed in %1$s. SQL Exception Attached. Original Message: %3$s';

    public function __construct ($pMethod, $pModelClass, Exception $pPrevious)
    {
        $message = sprintf(static::MESSAGE, $pMethod, $pModelClass, $pPrevious->getMessage());

        /* this exception doesn't need to pass in a 'previous' exception or code as there shouldn't be one */
        parent::__construct($message, NULL, $pPrevious);
    }
}