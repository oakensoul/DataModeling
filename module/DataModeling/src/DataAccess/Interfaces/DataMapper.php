<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\Interfaces;

use DataModeling\DataAccess\Model;

interface DataMapper
{

    /**
     * Accessor -- GetPrototype
     *
     * Returns a prototype of the model associated with this data mapper
     *
     * @return Model\DomainModelAbstract
     */
    public function GetPrototype ();

    /**
     * Accessor -- SetPrototype
     *
     * Sets the prototype of the model associated with this data mapper
     */
    public function SetPrototype (Model\DomainModelAbstract $pPrototype);

    /**
     * DataPersistence -- Create
     *
     * With the model provided, adds it to the data mapper's data source. The Domain Model
     * must have all of its required properties set.
     *
     * ($pModel->CheckLoaded needs to return true)
     *
     * @param
     *            Model\DomainModelAbstract
     */
    public function Create (Model\DomainModelAbstract $pModel);

    /**
     * DataPersistence -- Read
     *
     * With the model provided, load all of its properties from the Data Source.The
     * Domain Model must have all of its primary keys set.
     *
     * ($pModel->CheckPrimaryKeys needs to return true)
     *
     * @param
     *            Model\DomainModelAbstract
     */
    public function Read (Model\DomainModelAbstract $pModel);

    /**
     * DataPersistence -- Update
     *
     * With the model provided, updates the data mapper's data source. The Domain Model
     * must have all of its required properties set.
     *
     * ($pModel->CheckLoaded needs to return true)
     *
     * @param
     *            Model\DomainModelAbstract
     */
    public function Update (Model\DomainModelAbstract $pModel);

    /**
     * DataPersistence -- Delete
     *
     * With the model provided, delete it from the Data Source.The
     * Domain Model must have all of its primary keys set.
     *
     * ($pModel->CheckPrimaryKeys needs to return true)
     *
     * @param
     *            Model\DomainModelAbstract
     */
    public function Delete (Model\DomainModelAbstract $pModel);

    /**
     * DataPersistence -- Save
     *
     * Saves the model by calling create or update. If the
     * application understands the state of the object, it should use
     * Create or Update, Save should only be used if the system needs
     * to determine the course of action
     *
     * Keep in mind, Save will occur additional overhead in derived classes
     * and should not be used unless necessary.
     */
    public function Save (Model\DomainModelAbstract $pModel);
}