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

class ModelNotFoundFailure extends Interrupt\FailureAbstract
{
    const MESSAGE = 'No results found when looking for %2$s model in %1$s with statement: %3$s';

    public function __construct ($pMethod, $pModelClass, $pStatement)
    {
        $message = sprintf(static::MESSAGE, $pMethod, $pModelClass, $pStatement);

        /* this exception doesn't need to pass in a 'previous' exception or code as there shouldn't be one */
        parent::__construct($message);
    }
}