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

use DataModeling\DataAccess\Interrupt;
use DataModeling\DataAccess\Model\Hydrator\ArrayHydrator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Zend\Stdlib\Hydrator\Filter\FilterInterface;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;

/**
 * Standard Model represents the very basic model that we use for data access
 */
abstract class StandardModelAbstract
{
    // use Hydrator\HydratableTrait;

    /**
     * MetaData Keys
     */
    const PROPERTY_NAME = 'name';
    const PROPERTY_FUNCTION = 'function';
    const PROPERTY_FILTER = 'filter';
    const PROPERTY_VALUE = 'value';
    const PROPERTY_VALIDATORS = 'validators';
    const PROPERTY_VALIDATION_CHAIN = 'validation_chain';
    const PROPERTY_VALIDATION_MESSAGES = 'validation_messages';
    const PROPERTY_SET = 'property_set';
    const PROPERTY_ALLOW_EMPTY = 'allow_empty';
    const PROPERTY_ACCESS = 'property_access';

    /**
     * formats for import/export
     */
    const FORMAT_JSON = 'json';
    const FORMAT_ARRAY = 'array';

    /**
     * visibility states for import/export of data
     *
     * public -- can be set via getter/setter
     * protected -- can only be set by the class itself and getter/setter
     * private -- can only be set by the class itself, can't be set via getter/setter
     */
    const ACCESS_PRIVATE = 'private';
    const ACCESS_PUBLIC = 'public';
    const ACCESS_PROTECTED = 'protected';

    /**
     * The array of property keys to property names.
     * Each implemented class
     * should define the method responsible for building this array
     *
     * @var array
     */
    protected $mPropertyMetaData = array ();

    /**
     * An array to store the primary key(s) if this domain model has them.
     *
     * Note: The Primary Keys don't have to be RDBMS related. They simply
     * represent
     * an element whose value may be auto-generated, but represent the domain
     * model as a unique value.
     *
     * @var array
     */
    protected $mPrimaryKeys = array ();

    /**
     * A reverse lookup map of field names to their keys.
     * This allows developers
     * to use the lookup calls directly, but also to allow manual overrides
     * of getters/setters/validators
     *
     * @var array
     */
    protected $mFunctionToKeyLookup = array ();

    /**
     * the constructors purpose for this class is to initialize the property
     * meta
     * data array by calling the implemented SetPropertyMetaData method
     */
    public function __construct ()
    {
        /**
         * sets the property meta data array, should be overridden by child
         */
        $this->SetPropertyMetaData();
    }

    /**
     * This magic method allows easy property access for getters
     *
     * @param string $pString
     * @return string
     */
    public function __get ($pString)
    {
        return $this->GetProperty($pString);
    }

    /**
     * this method should set all of the property meta data for the derived
     * class.
     */
    abstract protected function SetPropertyMetaData ();

    /**
     * Accessor -- GetProperty
     *
     * Gets a property from the PropertyMetaData array and returns it.
     *
     * @param
     *            $pProperty
     *
     * @return variable
     */
    final public function GetProperty ($pProperty)
    {
        if (false == $this->PropertyDefined($pProperty))
        {
            throw new Interrupt\ModelPropertyNotDefinedException(__METHOD__, $pProperty);
        }

        $overloader = 'Get' . $this->mPropertyMetaData[$pProperty][static::PROPERTY_FUNCTION];

        if (method_exists($this, $overloader))
        {
            $result = $this->{$overloader}();
        }
        else
        {
            $result = $this->mPropertyMetaData[$pProperty][static::PROPERTY_VALUE];
        }

        return $result;
    }

    /**
     * Accessor -- SetProperty
     *
     * Sets a property value to the PropertyMetaData array
     *
     * @param
     *            $pProperty
     * @param
     *            $pValue
     * @param
     *            $pBypassValidation
     */
    final public function SetProperty ($pProperty, $pValue, $pBypassValidation = false)
    {
        if (false === $this->PropertyExists($pProperty))
        {
            throw new Interrupt\ModelPropertyDoesNotExistException(__METHOD__, $pProperty);
        }

        if (true == $pBypassValidation || $this->ValidateProperty($pProperty, $pValue))
        {
            $overloader = 'Set' . $this->mPropertyMetaData[$pProperty][static::PROPERTY_FUNCTION];

            if (method_exists($this, $overloader))
            {
                $this->{$overloader}($pValue);
                $this->mPropertyMetaData[$pProperty][static::PROPERTY_SET] = true;
            }
            else
            {
                $this->mPropertyMetaData[$pProperty][static::PROPERTY_SET] = true;
                $this->mPropertyMetaData[$pProperty][static::PROPERTY_VALUE] = $pValue;
            }
        }
        else
        {
            throw new Interrupt\SetPropertyValidationFailure($pProperty, get_class($this));
        }
    }

    /**
     * Accessor -- EmptyProperty
     *
     * Empties the value of a property in the PropertyMetaData array
     *
     * @param
     *            $pProperty
     */
    final public function EmptyProperty ($pProperty)
    {
        if (false === $this->PropertyExists($pProperty))
        {
            throw new Interrupt\ModelPropertyDoesNotExistException(__METHOD__, $pProperty);
        }

        $overloader = 'Empty' . $this->mPropertyMetaData[$pProperty][static::PROPERTY_FUNCTION];

        if (method_exists($this, $overloader))
        {
            $this->{$overloader}();
            $this->mPropertyMetaData[$pProperty][static::PROPERTY_SET] = false;
        }
        else
        {
            $this->mPropertyMetaData[$pProperty][static::PROPERTY_SET] = false;
            $this->mPropertyMetaData[$pProperty][static::PROPERTY_VALUE] = NULL;
        }
    }

    /**
     * Utility -- ValidateProperty
     *
     * Atempts to validate the requested property and value pair
     *
     * @param string $pProperty
     * @param mixed $pValue
     *
     * @throws Exception
     *
     * @return bool
     */
    final public function ValidateProperty ($pProperty, $pValue)
    {
        if (false == $this->PropertyExists($pProperty))
        {
            throw new Interrupt\ModelPropertyDoesNotExistException(__METHOD__, $pProperty);
        }

        // @todo: NYI for ZF2
        return true;
    }

    /**
     * Utility -- PropertyList
     *
     * Returns the list of properties for this model
     *
     * @return string[]
     */
    final public function PropertyList ()
    {
        return array_keys($this->mPropertyMetaData);
    }

    /**
     * Utility -- PropertyFilter
     *
     * Returns the Filter for the property
     *
     * @param string $pProperty
     * @throws Exception\BaseException
     *
     * @return Zend_Filter[]
     */
    final public function PropertyFilter ($pProperty)
    {
        if (false === $this->PropertyExists($pProperty))
        {
            throw new Interrupt\ModelPropertyDoesNotExistException(__METHOD__, $pProperty);
        }

        // @todo: We need to recreate this logic for ZF2

        return $this->mPropertyMetaData[$pProperty][static::PROPERTY_FILTER];
    }

    /**
     * Utility -- PropertyExists
     * This method returns true if the property exists in the meta data array
     *
     * @param string $pProperty
     * @return boolean
     */
    final public function PropertyExists ($pProperty)
    {
        return array_key_exists($pProperty, $this->mPropertyMetaData);
    }

    /**
     * Utility -- PropertyDefined
     * This method returns true if the property is considered "set".
     *
     * @param string $pProperty
     * @return boolean
     */
    public function PropertyDefined ($pProperty)
    {
        if (false === $this->PropertyExists($pProperty))
        {
            $result = false;
        }
        else
        {
            $result = $this->mPropertyMetaData[$pProperty][static::PROPERTY_SET];

            /**
             * if the property is allowed to be null, then null can be
             * considered "set"
             */
            if (false == $result && true == $this->mPropertyMetaData[$pProperty][static::PROPERTY_ALLOW_EMPTY])
            {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Utility -- CheckPrimaryKeys
     *
     * Checks to see if all of the primary key properties have been set.
     *
     * @return bool
     */
    public function CheckPrimaryKeys ()
    {
        $result = true;

        foreach ($this->GetPrimaryKeys() as $key)
        {
            if (false === $this->mPropertyMetaData[$key][static::PROPERTY_SET])
            {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Accessor -- GetPrimaryKeys
     *
     * Returns an array listing the primary key properties
     *
     * @return array
     */
    public function GetPrimaryKeys ()
    {
        return $this->mPrimaryKeys;
    }

    /**
     * Utility -- CheckLoaded
     *
     * Checks to see if all of an objects properties have been set.
     *
     * @param bool $pCheckPrimaryKeys
     *
     * @return bool
     */
    public function CheckLoaded ($pCheckPrimaryKeys = true)
    {
        $result = true;

        try
        {
            if (true == $pCheckPrimaryKeys && false == $this->CheckPrimaryKeys())
            {
                $result = false;
            }
            else
            {
                foreach ($this->mPropertyMetaData as $property => $meta_data)
                {
                    if (false === array_key_exists($property, $this->GetPrimaryKeys()))
                    {
                        $this->GetProperty($property);
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            $result = false;
        }

        return $result;
    }

    /**
     * Utility -- Import
     *
     * @param string $pFormat
     * @param mixed $pValues
     *
     * @throws Exception\BaseException
     */
    public function Import ($pFormat, $pValues)
    {
        switch ($pFormat)
        {
            case static::FORMAT_ARRAY:
                foreach ($pValues as $key => $value)
                {
                    /*
                     * we ignore any properties in the array that the model is unaware of
                     */
                    if ($this->PropertyDefined($key))
                    {
                        $this->SetProperty($key, $value);
                    }
                }
                break;

            case static::FORMAT_JSON:
                $decode = json_decode($pValues);
                $this->Import(static::FORMAT_ARRAY, $decode);
                break;

            default:
                throw new Interrupt\UnrecognizedFormatException(__METHOD__, $pFormat);
                break;
        }
    }

    /**
     * Utility -- Export
     *
     * @param string $pFormat
     * @throws Exception\BaseException
     *
     * @return mixed
     */
    public function Export ($pFormat)
    {
        switch ($pFormat)
        {
            case static::FORMAT_ARRAY:
                foreach ($this->mPropertyMetaData as $key => $values)
                {
                    $result[$key] = $values[static::PROPERTY_VALUE];
                }
                break;

            case static::FORMAT_JSON:
                $result = $this->Export(static::FORMAT_ARRAY);
                $result = json_encode($result);
                break;

            default:
                throw new Interrupt\UnrecognizedFormatException(__METHOD__, $pFormat);
                break;
        }

        return $result;
    }

    /**
     * Helper -- BasePropertyMetaDataArray
     *
     * This method returns the 'empty' meta data array so that we don't have to
     * fill in the parts that are supposed to be empty every time
     *
     * @return array ()
     */
    protected function BasePropertyMetaDataArray ($pKey = null)
    {
        $result = array ();
        $result[static::PROPERTY_NAME] = $pKey;
        $result[static::PROPERTY_FUNCTION] = $pKey;
        $result[static::PROPERTY_VALUE] = NULL;
        $result[static::PROPERTY_VALIDATION_CHAIN] = array ();
        $result[static::PROPERTY_VALIDATION_MESSAGES] = array ();
        $result[static::PROPERTY_SET] = false;
        $result[static::PROPERTY_ALLOW_EMPTY] = true;

        return $result;
    }

    /**
     * Enforces instance of validator
     *
     * @param string $pKey
     *            - KEY_ constant's value
     * @param string $pClass
     *            - _CLASS constant's value
     * @return array
     */
    protected function ObjectPropertyMetaDataArray ($pKey, $pClass)
    {
        $result = $this->BasePropertyMetaDataArray($pKey);

        $validator_list = array_key_exists(static::PROPERTY_VALIDATION_CHAIN, $result) ? $result[static::PROPERTY_VALIDATION_CHAIN] : array ();

        $validator = array ();
        $validator[static::VALIDATOR_CLASS] = 'Validation_IsInstanceOf';
        $validator[static::VALIDATOR_OPTIONS] = array ();
        $validator[static::VALIDATOR_OPTIONS]['class_type'] = $pClass;
        $validator[static::VALIDATOR_BREAK] = false;

        $validator_list[] = $validator;

        $result[static::PROPERTY_VALIDATION_CHAIN] = $validator_list;

        return $result;
    }

    /**
     * *************************
     * Some day when we have PHP 5.4, we can use the HydratorTrait instead
     */

    /**
     * Sets the Hydrator for the Object the Trait is attached to
     *
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $pHydrator
     */
    public function SetHydrator (HydratorInterface $pHydrator)
    {
        $this->mHydrator = $pHydrator;
    }

    /**
     * Gets the Hydrator for the Object the Trait is attached to
     *
     * @return \Zend\Stdlib\Hydrator\HydratorInterface
     */
    public function GetHydrator ()
    {
        if (empty($this->mHydrator))
        {
            $this->mHydrator = $this->DefaultHydrator();
        }

        return $this->mHydrator;
    }

    /**
     * Add a Strategy to the current Hydrator
     *
     * @param string $pProperty
     * @param \Zend\Stdlib\Hydrator\Strategy\StrategyInterface $pStrategy
     */
    public function AddHydratorStrategy ($pProperty, StrategyInterface $pStrategy)
    {
        if (false === $this->PropertyExists($pProperty))
        {
            throw new Interrupt\ModelPropertyDoesNotExistException(__METHOD__, $pProperty);
        }

        $this->GetHydrator()->addStrategy($pProperty, $pStrategy);
    }

    /**
     * Add a Filter to the current Hydrator
     *
     * @param \Zend\Stdlib\Hydrator\Filter\FilterInterface $pFilter
     */
    public function AddHydratorFilter ($pName, FilterInterface $pFilter, $pCondition = FilterComposite::CONDITION_OR)
    {
        $this->GetHydrator()->addFilter($pName, $pFilter, $pCondition);
    }

    /**
     * Returns the default Hydrator for the Object the Trait is attached to
     *
     * @return \Framework\DataAccess\Model\Hydrator\ArrayHydrator
     */
    public function DefaultHydrator ()
    {
        return new ArrayHydrator();
    }

    /**
     * Utility -- Hydrate
     *
     * @param mixed $pValues
     * @param mixed $pHydrator
     *
     * @throws Exception\BaseException
     */
    public function Hydrate ($pValues)
    {
        $this->GetHydrator()->hydrate($pValues, $this);
    }

    /**
     * Utility -- Extract
     *
     * @throws Exception\BaseException
     */
    public function Extract ()
    {
        return $this->GetHydrator()->extract($this);
    }
}