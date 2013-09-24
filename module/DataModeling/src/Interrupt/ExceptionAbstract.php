<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\Interrupt;

/**
 * Base Exception that all interrupts in the framework extend from
 */
abstract class ExceptionAbstract extends \Exception
{

    /**
     * Base Exception that all interrupts in the framework extend from
     *
     * @param unknown_type $pMessage
     * @param unknown_type $pCode
     * @param unknown_type $pPrevious
     */
    public function __construct ($pMessage = NULL, $pCode = NULL, $pPrevious = NULL)
    {
        parent::__construct($pMessage, $pCode, $pPrevious);
    }
}