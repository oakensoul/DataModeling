<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\ServiceWrapper\PDO;

/* Use statements for Framework namespaces */
use DataModeling\DataAccess\Model;

/**
 * PDOPayloadAbstract
 *
 * This model represents the basic payload that we would use for all PDO Queries
 *
 * I have decided to just make it specific for now, if I need a layer between the
 * StandardModelAbstract and itself, I can add it easily later, whereas it
 * is hard to retro-add a layer between this class and the final payload classes
 */
abstract class PayloadAbstract extends Model\StandardModelAbstract
{
}