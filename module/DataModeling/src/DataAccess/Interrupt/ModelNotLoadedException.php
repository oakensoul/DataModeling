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

class ModelNotLoadedException extends Interrupt\ExceptionAbstract
{
    const MESSAGE = '%1$s requires that the %2$s model passed to it has all of its required properties set.';

    public function __construct ($pMethod, $pModelClass)
    {
        $message = sprintf(static::MESSAGE, $pMethod, $pModelClass);

        /* this exception doesn't need to pass in a 'previous' exception or code as there shouldn't be one */
        parent::__construct($message);
    }
}