<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\Model;

/* Use statements for Framework namespaces */
use DataModeling\DataAccess\Interrupt;
use DataModeling\DataAccess\Interfaces;

/**
 * Standard Model represents the very basic model th
 */
abstract class DomainModelAbstract extends StandardModelAbstract
{

    /**
     * Stores the DataMapper instance for this model
     *
     * @var Interfaces\DataMapper
     */
    protected $mDataMapper;

    /**
     * Read Only flag for this data object, if the data object is in ReadOnly mode
     * then it will throw an exception when you try and update it.
     *
     * @var bool
     */
    protected $mReadOnly = false;

    /**
     * the constructors purpose for this class is to initialize the property meta
     * data array by calling the implemented SetPropertyMetaData method
     */
    public function __construct (Interfaces\DataMapper $pDataMapper)
    {
        $this->SetDataMapper($pDataMapper);

        parent::__construct();
    }

    /**
     * DataPersistence -- SetDataMapper
     *
     * Allows the data mapper for the currently instantiated object
     * to be changed to an alternate data mapper
     *
     * @param Interfaces\DataMapper $pMapper
     */
    public function SetDataMapper (Interfaces\DataMapper $pMapper)
    {
        $this->mDataMapper = $pMapper;
    }

    /**
     * DataPersistence -- GetDataMapper
     *
     * Returns the data mapper that this model is currently using
     *
     * @return Interfaces\DataMapper
     */
    public function GetDataMapper ()
    {
        return $this->mDataMapper;
    }

    /**
     * DataPersistence -- ReadOnly
     *
     * @return bool
     */
    public function ReadOnly ($pSetting = null)
    {
        if (true === $pSetting || false === $pSetting)
        {
            $this->mReadOnly = true === $pSetting ?  : false;
        }

        return $this->mReadOnly;
    }

    /**
     * DataPersistence -- Create
     *
     * Creates the model by calling the DataMapper's Create function. This
     * method should be called if the model does not already exist in the
     * data source
     *
     * If it is unknown as to whether the model needs to be created or updated,
     * call Save. Keep in mind, Save incurs additional overhead to determine
     * the appropriate call.
     *
     * @throws Interrupt\ModelNotLoadedException
     *
     * @see Interfaces\DataMapper::Create()
     */
    public function Create ()
    {
        if (false == $this->CheckLoaded(false))
        {
            throw new Interrupt\ModelNotLoadedException(__METHOD__, get_class($this));
        }

        $this->GetDataMapper()->Create($this);
    }

    /**
     * DataPersistence -- Read
     *
     * Reads the model by calling the DataMapper's Read function. The
     * model retrieved is the model associated with the primary keys set
     * in this object.
     *
     * @throws Interrupt\InvalidModelException
     * @throws Interrupt\ModelPrimaryKeyNotSetException
     * @throws Interrupt\ModelReadException
     *
     * @see Interfaces\DataMapper::Read()
     */
    public function Read ()
    {
        $this->GetDataMapper()->Read($this);
        $this->ReadOnly(false);
    }

    /**
     * DataPersistence -- Update
     *
     * Updates the model by calling the DataMapper's Update function. This
     * method should be called if the model already exists in the data source
     *
     * If it is unknown as to whether the model needs to be created or updated,
     * call Save. Keep in mind, Save incurs additional overhead to determine
     * the appropriate call.
     *
     * @throws Interrupt\InvalidModelException
     * @throws Interrupt\ModelPrimaryKeyNotSetException
     * @throws Interrupt\ModelNotLoadedException
     * @throws Interrupt\ModelUpdateException
     *
     * @see Interfaces\DataMapper::Update()
     */
    public function Update ()
    {
        $this->GetDataMapper()->Update($this);
        $this->ReadOnly(false);
    }

    /**
     * DataPersistence -- Delete
     *
     * Deletes the model by calling the DataMapper's Delete function.
     *
     * @throws Interrupt\InvalidModelException
     * @throws Interrupt\ModelPrimaryKeyNotSetException
     * @throws Interrupt\ModelNotLoadedException
     * @throws Interrupt\ModelDeleteException
     *
     * @see Interfaces\DataMapper::Delete()
     */
    public function Delete ()
    {
        $this->GetDataMapper()->Delete($this);
        $this->ReadOnly(false);
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
     * @see Interfaces\DataMapper::Save()
     */
    public function Save ()
    {
        $this->GetDataMapper()->Save($this);
    }
}