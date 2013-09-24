<?php
/**
 *
 * @author Oakensoul (http://www.oakensoul.com/)
 * @link https://github.com/oakensoul/Cornerstone for the canonical source repository
 * @copyright Copyright (c) 2013 Robert Gunnar Johnson Jr.
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @package DataModeling
 */
namespace DataModeling\DataAccess\Model\Hydrator;

use DataModeling\DataAccess\Model\StandardModelAbstract;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Zend\Stdlib\Exception;
use Zend\Stdlib\Hydrator\Filter;

class ArrayHydrator extends AbstractHydrator
{

    /**
     * Extract values from an object with class methods
     *
     * Extracts the getter/setter of the given $object.
     *
     * @param StandardModelAbstract $object
     * @return array
     * @throws Exception\BadMethodCallException for a non-object $object
     */
    public function extract ($object)
    {
        if (! is_object($object) || ! $object instanceof StandardModelAbstract)
        {
            throw new Exception\BadMethodCallException(sprintf('%s expects the provided $object to be a PHP StandardModelAbstract object)', __METHOD__));
        }

        $filter = null;

        if ($object instanceof Filter\FilterProviderInterface)
        {
            $filter_options = array ();
            $filter_options[][] = $object->getFilter();
            $filter_options[][] = new Filter\MethodMatchFilter("getFilter");

            $filter = new Filter\FilterComposite($filter_options);
        }
        else
        {
            $filter = $this->filterComposite;
        }

        $attributes = array ();

        $properties = $object->PropertyList();
        foreach ($properties as $property)
        {
            $attributes[$property] = $this->extractValue($property, $object->GetProperty($property));
        }

        return $attributes;
    }

    /**
     * Hydrate a StandardModelAbstract object by populating getter/setter methods
     *
     * Hydrates an object by getter/setter methods of the object.
     *
     * @param array $data
     * @param StandardModelAbstract $object
     * @return StandardModelAbstract
     * @throws Exception\BadMethodCallException for a non-object $object
     */
    public function hydrate (array $data, $object)
    {
        if (! is_object($object) || ! $object instanceof StandardModelAbstract)
        {
            throw new Exception\BadMethodCallException(sprintf('%s expects the provided $object to be a PHP StandardModelAbstract object)', __METHOD__));
        }

        foreach ($object->PropertyList() as $property)
        {
            if (array_key_exists($property, $data))
            {
                $value = $this->hydrateValue($property, $data[$property]);
                $object->SetProperty($property, $value);
            }
        }

        return $object;
    }
}