<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Map;

use NicMart\Generics\Name\FullName;

/**
 * Class TypeMap
 * @package NicMart\Generics\Map
 */
final class TypeMap
{
    /**
     * @var FullName[]
     */
    private $generic = array();

    /**
     * @var GenericTypeApplication[]
     */
    private $applications = array();

    /**
     * TypeMap constructor.
     * @param array $map
     */
    public function __construct(array $map = array())
    {
        foreach ($map as $typeApplication) {
            $this->addApplication($typeApplication);
        }
    }

    /**
     * @param GenericTypeApplication $typeApplication
     * @return TypeMap
     */
    public function withApplication(GenericTypeApplication $typeApplication)
    {
        $new = clone $this;

        $new->addApplication($typeApplication);

        return $new;
    }

    /**
     * @return FullName[]
     */
    public function genericTypes()
    {
        return array_values($this->generic);
    }

    /**
     * @param FullName $generic
     * @return GenericTypeApplication[]
     */
    public function applications(FullName $generic)
    {
        $genericNameString = $generic->toString();

        if (!isset($this->applications[$genericNameString])) {
            return array();
        }

        return $this->applications[$genericNameString];
    }

    /**
     * @param GenericTypeApplication $typeApplication
     * @return $this
     */
    private function addApplication(GenericTypeApplication $typeApplication)
    {
        $generic = $typeApplication->generic();
        $this->generic[$generic->toString()] = $generic;
        $this->applications[$generic->toString()][] = $typeApplication;

        return $this;
    }
}