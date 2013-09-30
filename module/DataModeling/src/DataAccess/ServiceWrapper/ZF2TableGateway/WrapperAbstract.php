<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\ServiceWrapper\ZF2TableGateway;

/* Use statements for Framework namespaces */
use DataModeling\DataAccess\Interfaces;
use DataModeling\DataAccess\Interrupt;
use DataModeling\DataAccess\Model;
use DataModeling\DataAccess\ServiceWrapper;

/* Use statements for ZF2 Table Gateway */
use Zend\Db\TableGateway\TableGateway;
use Exception;

abstract class WrapperAbstract extends ServiceWrapper\WrapperAbstract implements Interfaces\DataMapper
{

    /**
     * Prototype data model for this mapper
     *
     * @var Model;
     */
    protected $mModelPrototype;

    /**
     * Accessor -- SetServiceObject
     *
     * Setter for the service object that is being wrapped
     *
     * @param TableGateway $pServiceObject
     *
     * @see \Framework\DataAccess\ServiceWrapper\WrapperAbstract::SetServiceObject()
     */
    public function SetServiceObject ($pServiceObject)
    {
        if (! is_object($pServiceObject))
        {
            throw new Interrupt\InvalidServiceObjectException(__METHOD__, 'Zend\Db\TableGateway\TableGateway', 'non-object');
        }

        if (! $pServiceObject instanceof TableGateway)
        {
            throw new Interrupt\InvalidServiceObjectException(__METHOD__, 'Zend\Db\TableGateway\TableGateway', get_class($pServiceObject));
        }

        $this->mServiceObject = $pServiceObject;
    }
    const USE_LAST_INSERT_ID = false;

    /**
     * Helper -- GetDefaultServiceObject
     *
     * Getter for the Default service object that is being wrapped. This is
     * meant
     * to be the default service connection if dependency injection is not used,
     * likely determined by a resources configuration in the application config
     *
     * @see \Framework\DataAccess\ServiceWrapper\WrapperAbstract::GetDefaultServiceObject()
     *
     * @return TableGateway
     */
    protected function GetDefaultServiceObject ()
    {
        throw new Exception('DefaultServiceObject not defined.');
    }

    /**
     * Helper -- GetDefaultPrototype
     *
     * The assumption is that there will be a "Model.php" within the namespace
     * corresponding to the DataMapper and thus we can generate a default model
     *
     * This represnets the prototypical data model to be used with this mapper
     *
     * @see \Framework\DataAccess\Interfaces\DataMapper::GetDefaultPrototype()
     */
    protected function GetDefaultPrototype ()
    {
        $model = $this->mNamespace . '\\' . 'Model';

        return new $model($this);
    }

    /**
     * Accessor -- GetPrototype
     *
     * Returns a prototype model for this data mapper
     *
     * @see \Framework\DataAccess\Interfaces\DataMapper::GetPrototype()
     *
     * @return Model\DomainModelAbstract
     */
    public function GetPrototype ()
    {
        if (empty($this->mModelPrototype))
        {
            $this->mModelPrototype = $this->GetDefaultPrototype();
        }

        return clone $this->mModelPrototype;
    }

    /**
     * Accessor -- SetPrototype
     *
     * Setter for the prototype model
     *
     * @throws Interrupt\InvalidModelException
     *
     * @see \Framework\DataAccess\Interfaces\DataMapper::SetPrototype()
     */
    public function SetPrototype (Model\DomainModelAbstract $pPrototype)
    {
        $this->RequireVerifiedModel(__METHOD__, $pPrototype);

        $this->mModelPrototype = $pPrototype;
    }

    /**
     * Helper -- WhereClauseByPrimaryKey
     *
     * returns the WHERE statement with parameter binding for the
     * primary key of the prototype model
     *
     * Note: You'll have to set the values placed into the statement
     * using the :key parameter binding syntax
     *
     * @return string
     */
    public function WhereClauseByPrimaryKey ()
    {
        $pkeys = array ();
        foreach ($this->GetPrototype()->GetPrimaryKeys() as $key)
        {
            $pkeys[] = "`$key` = :$key";
        }

        return ' WHERE ' . implode(' AND ', $pkeys);
    }

    /**
     * Helper -- SelectClause
     *
     * returns a Select statement. by default it explicitly asks for all columns
     * that
     * the model is aware of, not just a SELECT *. To disable that behavior,
     * pass a
     * false into the pWithColumns param
     *
     * @param
     *            bool pWithColumns
     *
     * @return string
     */
    public function SelectClause ($pWithColumns = true)
    {
        $result = 'SELECT ';

        if (true === $pWithColumns)
        {
            $result .= implode(', ', $this->GetPrototype()->PropertyList());
        }
        else
        {
            $result .= '*';
        }

        $result .= ' FROM `' . static::TABLE_NAME . '` ';

        return $result;
    }

    /**
     * DataPersistence -- Create
     *
     * Inserts a record into the database using the provided record
     *
     * @throws Interrupt\InvalidModelException
     * @throws Interrupt\ModelCreateException
     * @throws Interrupt\ModelNotLoadedException
     *
     * @see \Framework\DataAccess\Interfaces\DataMapper::Create()
     */
    public function Create (Model\DomainModelAbstract $pModel)
    {
        $this->RequireVerifiedModel(__METHOD__, $pModel);

        if (false == $pModel->CheckLoaded(false))
        {
            throw new Interrupt\ModelNotLoadedException(__METHOD__, get_class($this));
        }

        $columns = implode(', ', $this->GetPrototype()->PropertyList());

        $value_binds = array ();

        foreach ($this->GetPrototype()->PropertyList() as $property)
        {
            $value_binds[] = ":$property";
        }

        $value_binds = implode(', ', $value_binds);

        $sql = 'INSERT INTO `' . static::TABLE_NAME . '` (' . $columns . ') VALUES (' . $value_binds . ')';

        /* @var $stmt Zend_Db_Statement_Pdo */
        $stmt = $this->GetServiceObject()->prepare($sql);

        foreach ($this->GetPrototype()->PropertyList() as $property)
        {
            $stmt->bindValue($property, $pModel->GetProperty($property));
        }

        try
        {
            $stmt->execute();
        }
        catch (\Exception $e)
        {
            throw new Interrupt\ModelCreateException(__METHOD__, get_class($this->GetPrototype()), $e);
        }

        /**
         * Allow a hook into the post create step, by default this just checks
         * to
         * see if it should populate the primary key using the last insert id,
         * but
         * more could be added
         */
        $this->HookPostCreate($pModel);
    }

    /**
     * Helper -- HookPostCreate
     *
     * The Post Create Hook by default allows the population of the primary key
     * using last insert id, but it has been separated out so that the derived
     * class could do any other number of actions after a Create step
     *
     * @param Model\DomainModelAbstract $pModel
     */
    protected function HookPostCreate (Model\DomainModelAbstract $pModel)
    {
        /**
         * The assumption is that if we're using last insert id there is only
         * one
         * primary key column and it auto-increments.
         * If more complicated logic is
         * needed in the future we can update or this method can be overridden
         * by
         * the child class
         */
        if (true === static::USE_LAST_INSERT_ID)
        {
            $keys = $this->GetPrototype()->GetPrimaryKeys();

            $pModel->SetProperty(current($keys), $this->GetServiceObject()
                ->lastInsertId());
        }
    }

    /**
     * DataPersistence -- Read
     *
     * @param Model\DomainModelAbstract $pModel
     *
     * @throws Interrupt\InvalidModelException
     * @throws Interrupt\ModelPrimaryKeyNotSetException
     * @throws Interrupt\ModelReadException
     *
     * @see \Framework\DataAccess\Interfaces\DataMapper::Read()
     */
    public function Read (Model\DomainModelAbstract $pModel)
    {
        $this->RequireVerifiedModel(__METHOD__, $pModel);

        if (false == $pModel->CheckPrimaryKeys())
        {
            throw new Interrupt\ModelPrimaryKeyNotSetException(__METHOD__, get_class($this));
        }

        $select = $this->SelectClause();
        $where = $this->WhereClauseByPrimaryKey();

        /* @var $stmt Zend_Db_Statement_Pdo */
        $stmt = $this->GetServiceObject()->prepare($select . $where);

        foreach ($pModel->GetPrimaryKeys() as $key)
        {
            $stmt->bindValue($key, $pModel->GetProperty($key));
        }

        try
        {
            $stmt->execute();
            $row = $stmt->fetch();
        }
        catch (\Exception $e)
        {
            throw new Interrupt\ModelReadException(__METHOD__, get_class($this->GetPrototype()), $e);
        }

        if (false == $row)
        {
            /*
             * the getDriverStatement call won't work unless the stmt is a Zend_Db_Statement_Pdo object, so if using DI,
             * it may not work
             */
            throw new Interrupt\ModelNotFoundFailure(__METHOD__, get_class($pModel), $stmt->getDriverStatement()->queryString);
        }
        else
        {
            $pModel->Import(Model\StandardModelAbstract::FORMAT_ARRAY, $row);
        }
    }

    /**
     * DataPersistence -- Update
     *
     * Updates the database with the values provided by the model
     *
     * @param Model\DomainModelAbstract $pModel
     *
     * @throws Interrupt\InvalidModelException
     * @throws Interrupt\ModelPrimaryKeyNotSetException
     * @throws Interrupt\ModelNotLoadedException
     * @throws Interrupt\ModelUpdateException
     *
     * @see \Framework\DataAccess\Interfaces\DataMapper::Update()
     */
    public function Update (Model\DomainModelAbstract $pModel)
    {
        $this->RequireVerifiedModel(__METHOD__, $pModel);

        if (false == $pModel->CheckPrimaryKeys())
        {
            throw new Interrupt\ModelPrimaryKeyNotSetException(__METHOD__, get_class($this));
        }

        if (false == $pModel->CheckLoaded())
        {
            throw new Interrupt\ModelNotLoadedException(__METHOD__, get_class($this));
        }

        $columns = implode(', ', $this->GetPrototype()->PropertyList());

        $update_binds = array ();

        foreach ($this->GetPrototype()->PropertyList() as $property)
        {
            $update_binds[] = "`$property` = :$property";
        }

        $update_binds = implode(', ', $update_binds);
        $where = $this->WhereClauseByPrimaryKey();

        $sql = 'UPDATE `' . static::TABLE_NAME . '` SET ' . $update_binds . $where;

        /* @var $stmt Zend_Db_Statement_Pdo */
        $stmt = $this->GetServiceObject()->prepare($sql);

        foreach ($this->GetPrototype()->PropertyList() as $property)
        {
            $stmt->bindValue($property, $pModel->GetProperty($property));
        }

        try
        {
            $stmt->execute();
        }
        catch (\Exception $e)
        {
            throw new Interrupt\ModelUpdateException(__METHOD__, get_class($this->GetPrototype()), $e);
        }

        $this->HookPostUpdate($pModel);
    }

    /**
     * Helper -- HookPostUpdate
     *
     * The Post Update Hook has been separated out so that the derived
     * class could do any other number of actions after an Update step
     *
     * @param Model\DomainModelAbstract $pModel
     */
    protected function HookPostUpdate (Model\DomainModelAbstract $pModel)
    {}

    /**
     * DataPersistence -- Delete
     *
     * Removes the model's corresponding database entry
     *
     * @param Model\DomainModelAbstract $pModel
     *
     * @throws Interrupt\InvalidModelException
     * @throws Interrupt\ModelPrimaryKeyNotSetException
     * @throws Interrupt\ModelNotLoadedException
     * @throws Interrupt\ModelDeleteExceptionion
     *
     * @see \Framework\DataAccess\Interfaces\DataMapper::Delete()
     */
    public function Delete (Model\DomainModelAbstract $pModel)
    {
        $this->RequireVerifiedModel(__METHOD__, $pModel);

        if (false == $pModel->CheckPrimaryKeys())
        {
            throw new Interrupt\ModelPrimaryKeyNotSetException(__METHOD__, get_class($this));
        }

        if (false == $pModel->CheckLoaded())
        {
            throw new Interrupt\ModelNotLoadedException(__METHOD__, get_class($this));
        }

        $where = $this->WhereClauseByPrimaryKey();
        $sql = 'DELETE FROM `' . static::TABLE_NAME . '`' . $where;

        /* @var $stmt Zend_Db_Statement_Pdo */
        $stmt = $this->GetServiceObject()->prepare($sql);

        foreach ($pModel->GetPrimaryKeys() as $key)
        {
            $stmt->bindValue($key, $pModel->GetProperty($key));
        }

        try
        {
            $stmt->execute();
        }
        catch (\Exception $e)
        {
            throw new Interrupt\ModelDeleteException(__METHOD__, get_class($this->GetPrototype()), $e);
        }

        $this->HookPostDelete($pModel);
    }

    /**
     * Helper -- HookPostDelete
     *
     * The Post Update Hook has been separated out so that the derived
     * class could do any other number of actions after a Delete step
     *
     * @param Model\DomainModelAbstract $pModel
     */
    protected function HookPostDelete (Model\DomainModelAbstract $pModel)
    {}

    /**
     * DataPersistence -- Save
     *
     * Saves the model by calling the DataMapper's Save function. If the
     * application understands the state of the object, it should use
     * Create or Update, Save should only be used if the system needs
     * to determine the course of action
     *
     * Keep in mind, Save incurs additional overhead to determine
     * the appropriate call.
     *
     * @throws Interrupt\InvalidModelException
     * @throws Interrupt\ModelPrimaryKeyNotSetException
     * @throws Interrupt\ModelNotLoadedException
     * @throws Interrupt\ModelUpdateException
     *
     * @see \Framework\DataAccess\Interfaces\DataMapper::Save()
     */
    public function Save (Model\DomainModelAbstract $pModel)
    {
        $this->RequireVerifiedModel(__METHOD__, $pModel);

        if (true == $pModel->CheckLoaded())
        {
            $this->Create($pModel);
        }
        else if (true == $pModel->CheckLoaded(false))
        {
            $this->Update($pModel);
        }
        else
        {
            throw new Interrupt\ModelNotLoadedException(__METHOD__, get_class($this));
        }
    }

    /**
     * Helper -- RequireVerifiedModel
     *
     * @param string $pMethod
     * @param Model\DomainModelAbstract $pModel
     *
     * @throws Interrupt\InvalidModelException
     */
    protected function RequireVerifiedModel ($pMethod, Model\DomainModelAbstract $pModel)
    {
        $expected_model = $this->mNamespace . '\\' . 'Model';

        if (! $pModel instanceof $expected_model)
        {
            throw new Interrupt\InvalidModelException($pMethod, $expected_model, get_class($pModel));
        }
    }
}