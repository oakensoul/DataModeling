<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\Utility;

use StdClass;

class ComplexTypeConverter
{

  /**
   * Helper -- ConvertStdClassToArray
   *
   * Converts StdClass objects to arrays
   *
   * @param StdClass $pObject
   *
   * @return array
   */
  public static function StdClassToArray ( StdClass $pObject )
  {
    return static::RecursiveObjectToArray($pObject);
  }

  /**
   * Helper -- RecursiveObjectToArray
   *
   * We use ConvertStdClassToArray to wrap this method so that it can require
   * a StdClass to be passed in. This method basically just converts a StdClass
   * Object into a multi-dimensional array by recursively calling itself
   *
   * @param mixed $pMixed
   *
   * @return array
   */
  protected static function RecursiveObjectToArray ( $pMixed )
  {
    if ( !is_object($pMixed) && !is_array($pMixed) )
    {
      $result = $pMixed;
    }
    else
    {
      if ( is_object($pMixed) )
      {
        $pMixed = get_object_vars($pMixed);
      }

      $result = array_map(array ( 'DataModeling\Utility\ComplexTypeConverter', 'RecursiveObjectToArray' ), $pMixed);
    }

    return $result;
  }
}