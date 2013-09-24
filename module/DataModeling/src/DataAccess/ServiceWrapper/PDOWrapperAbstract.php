<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\ServiceWrapper;

/* Use statements for Framework namespaces */
use DataModeling\DataAccess\Interfaces;
use DataModeling\DataAccess\Interrupt;
use DataModeling\DataAccess\Model;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/* Use statements for core php namespaces */
use PDO;

/* Use statements for Zend namespaces */
use Exception;

abstract class PDOWrapperAbstract extends WrapperAbstract implements Interfaces\DataMapper, EventManagerAwareInterface, ServiceLocatorAwareInterface
{
    /* the following constants *need* to be defined in the child class */
    const DATABASE_REGISTRY_TOKEN = '';
    const TABLE_NAME = '';
    const USE_LAST_INSERT_ID = false;

    /**
     * Prototype data model for this mapper
     *
     * @var Model;
     */
    protected $mModelPrototype;

    protected $mEvents;
    protected $mServices;

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
     * @return PDO
     */
    protected function GetDefaultServiceObject ()
    {
        /* @var $database PDO */
        throw new Exception('DefaultServiceObject not defined.');
    }

    /**
     * Accessor -- SetServiceObject
     *
     * Setter for the service object that is being wrapped
     *
     * @param
     *            Object PDO
     *
     * @see \Framework\DataAccess\ServiceWrapper\WrapperAbstract::SetServiceObject()
     */
    public function SetServiceObject ($pServiceObject)
    {
        if (! is_object($pServiceObject))
        {
            throw new Interrupt\InvalidServiceObjectException(__METHOD__, 'PDO', 'non-object');
        }

        if (! $pServiceObject instanceof PDO)
        {
            throw new Interrupt\InvalidServiceObjectException(__METHOD__, 'PDO', get_class($pServiceObject));
        }

        $this->mServiceObject = $pServiceObject;
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
     * Accessor -- GetTableName
     *
     * Returns the table name for the wrapper
     *
     * @return string
     */
    public function GetTableName ()
    {
        return static::TABLE_NAME;
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
     * that the model is aware of, not just a SELECT *. To disable that
     * behavior, pass a false into the pWithColumns param
     *
     * @param $pWithColumns bool
     * @param $pTableAlias string
     *
     * @return string
     */
    public function SelectClause ($pWithColumns = true, $pTableAlias = false)
    {
        $result = 'SELECT ';

        if (true === $pWithColumns)
        {
            $pieces = array ();
            foreach ($this->GetPrototype()->PropertyList() as $property)
            {
                $pieces[] = false === $pTableAlias ? "`$property`" : '`' . $pTableAlias . '`.' . "`$property`";
            }

            $result .= implode(', ', $pieces);
        }
        else
        {
            $result .= false === $pTableAlias ? '*' : '`' . $pTableAlias . '`.*';
        }

        return $result . ' ';
    }

    /**
     * Helper -- FromClause
     *
     * returns a From statement attached to the table
     *
     * @return string
     */
    public function FromClause ()
    {
        return ' FROM `' . $this->GetTableName() . '` ';
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

        $value_binds = array ();
        $column_list = array ();

        foreach ($this->GetPrototype()->PropertyList() as $property)
        {
            $column_list[] = "`$property`";
            $value_binds[] = ":$property";
        }

        $columns = implode(', ', $column_list);

        $value_binds = implode(', ', $value_binds);

        $sql = 'INSERT INTO `' . $this->GetTableName() . '` (' . $columns . ') VALUES (' . $value_binds . ')';

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

        /**
         * Allow a hook into the post create step
         */
        $this->getEventManager()->trigger(__FUNCTION__, $this, array (
            'model' => $pModel,
            'service_manager' => $this->getServiceLocator()
        ));
    }

    /**
     * DataPersistence -- Read
     *
     * @param $pModel Model\DomainModelAbstract
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
        $from = ' FROM ' . static::$this->GetTableName();
        $where = $this->WhereClauseByPrimaryKey();

        /* @var $stmt \PDOStatement */
        $stmt = $this->GetServiceObject()->prepare($select . $from . $where);

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
            throw new Interrupt\ModelNotFoundFailure(__METHOD__, get_class($pModel), $stmt->queryString);
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

        $sql = 'UPDATE `' . static::$this->GetTableName() . '` SET ' . $update_binds . $where;

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

        /**
         * Allow a hook into the post update step
         */
        $this->getEventManager()->trigger(__FUNCTION__, $this, array (
            'model' => $pModel,
            'service_manager' => $this->getServiceLocator()
        ));
    }

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
        $sql = 'DELETE FROM `' . $this->GetTableName() . '`' . $where;

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

        /**
         * Allow a hook into the post update step
         */
        $this->getEventManager()->trigger(__FUNCTION__, $this, array (
            'model' => $pModel,
            'service_manager' => $this->getServiceLocator()
        ));
    }

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
            $this->Update($pModel);
        }
        else if (true == $pModel->CheckLoaded(false))
        {
            $this->Create($pModel);
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

    public function setEventManager (EventManagerInterface $pEvents)
    {
        $pEvents->addIdentifiers(array (
            get_called_class()
        ));

        $this->mEvents = $pEvents;
        return $this;
    }

    public function getEventManager ()
    {
        return $this->mEvents;
    }


    public function setServiceLocator(ServiceLocatorInterface $pServiceLocator)
    {
        $this->mServices = $pServiceLocator;
    }

    public function getServiceLocator()
    {
        return $this->mServices;
    }
}