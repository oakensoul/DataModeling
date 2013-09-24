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

use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Zend\Stdlib\Hydrator\Filter\FilterInterface;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;

/**
 *
 * @author oakensoul
 */
trait HydratableTrait
{

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
     * @param \Zend\Stdlib\Hydrator\Strategy\StrategyInterface $pStrategy
     */
    public function AddHydratorStrategy ($pKey, StrategyInterface $pStrategy)
    {
        $this->GetHydrator()->addStrategy($pKey, $pStrategy);
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
     * @return \Framework\DataAccess\Model\Hydrator\ClassAccessors
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
}