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
  * Base Failure that all failures in the framework extend from. Failures should
  * be used for when expected interrupts that occur during normal system execution
  * occur. For instance, a call that should work, but fails due to an expected but
  * rare use case should throw a Failure interrupt, and not an exception
  *
  * Obviously if it's a "common" occurrence, you should probably refactor your code
  * so that you're not incurring the expense of frequent call stack buildup
 */
abstract class FailureAbstract extends ExceptionAbstract
{

  /**
   * Base Failure
   *
   * @param unknown_type $pMessage
   * @param unknown_type $pCode
   * @param unknown_type $pPrevious
   */
  public function __construct ( $pMessage = NULL, $pCode = NULL, $pPrevious = NULL )
  {
    parent::__construct($pMessage, $pCode, $pPrevious);
  }
}