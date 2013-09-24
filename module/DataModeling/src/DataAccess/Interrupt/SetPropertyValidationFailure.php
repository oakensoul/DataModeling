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

class SetPropertyValidationFailure extends Interrupt\FailureAbstract
{
    const MESSAGE = 'Attempting to set property %1$s for Model %2$s failed due to invalid argument.';

    public function __construct ($pProperty, $pModel)
    {
        $message = sprintf(static::MESSAGE, $pProperty, $pModel);

        /* this exception doesn't need to pass in a 'previous' exception or code as there shouldn't be one */
        parent::__construct($message);
    }
}