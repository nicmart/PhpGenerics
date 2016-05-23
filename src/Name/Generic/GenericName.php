<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 18/05/2016, 12:49
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Generic;


use Doctrine\Instantiator\Exception\InvalidArgumentException;
use NicMart\Generics\Name\Assignment\NameAssignment;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Assignment\SimpleNameAssignment;
use NicMart\Generics\Name\Assignment\SimpleNameAssignmentContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Transformer\NameQualifier;

/**
 * Class GenericName
 * @package NicMart\Generics\Name\Generic
 */
final class GenericName
{
    /**
     * @var FullName
     */
    private $main;

    /**
     * @var FullName[]
     */
    private $parameters = array();

    /**
     * GenericName constructor.
     * @param FullName $fullName
     * @param FullName[] $parameters
     */
    public function __construct(
        FullName $fullName,
        array $parameters = array()
    ) {

        $this->main = $fullName;

        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
        }
    }

    /**
     * @return int
     */
    public function arity()
    {
        return count($this->parameters);
    }

    /**
     * @return FullName
     */
    public function main()
    {
        return $this->main;
    }

    /**
     * @return FullName[]
     */
    public function parameters()
    {
        return $this->parameters;
    }

    /**
     * @param FullName[] $arguments
     * @return GenericName
     */
    public function apply(array $arguments)
    {
        $this->assertValidArguments($arguments);

        return new self($this->main(), $arguments);
    }

    /**
     * @param FullName[] $names
     * @return NameAssignmentContext
     *
     */
    public function assignments(
        array $names
    ) {
        $this->assertValidArguments($names);

        $assignments = array();

        foreach ($names as $index => $name) {
            $assignments[] = new NameAssignment(
                $this->parameters[$index],
                $name
            );
        }

        return new NameAssignmentContext($assignments);
    }
    
    /**
     * @param FullName $fullName
     */
    private function addParameter(FullName $fullName)
    {
        $this->parameters[] = $fullName;
    }

    private function assertValidArguments(array $parameters)
    {
        if (count($parameters) != $this->arity()) {
            throw new InvalidArgumentException(sprintf(
                "This Generic Name expects %d parameters, %d received",
                $this->arity(),
                count($parameters)
            ));
        }
    }
}