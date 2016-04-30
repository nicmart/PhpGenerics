<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 20/04/2016, 13:21
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Generic;

use InvalidArgumentException;
use NicMart\Generics\Name\Assignment\NameAssignment;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Assignment\SimpleNameAssignment;
use NicMart\Generics\Name\Assignment\SimpleNameAssignmentContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\Transformer\NameQualifier;

/**
 * Class AngleQuotedGenericName
 * @package NicMart\Generics\Name\GenericName
 */
class AngleQuotedGenericName implements GenericName
{
    const CHAR_CODE = 194;

    /**
     * @var FullName
     */
    private $name;

    /**
     * @var RelativeName[]
     */
    private $typeVars = array();

    /**
     * @var string
     */
    private $nameTemplate;

    /**
     * @param string $name
     * @return AngleQuotedGenericName
     */
    public static function fromString($name)
    {
        return new self(FullName::fromString($name));
    }

    /**
     * AngleQuotedGenericName constructor.
     * @param FullName $name
     */
    public function __construct(FullName $name)
    {
        $this->name = $name;
        $this->parseName();
    }

    /**
     * @return FullName
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param NameQualifier $qualifier
     * @return FullName[]
     */
    public function parameters(NameQualifier $qualifier)
    {
        $names = array();

        foreach ($this->typeVars as $i => $relativeTypeVarName) {
            $names[] = $qualifier->qualify($relativeTypeVarName);
        }

        return $names;
    }

    /**
     * @param FullName[] $names
     * @return FullName
     */
    public function apply(array $names)
    {
        $this->assertValidNumberOfNames($names);

        $nameStrings = array();

        foreach ($names as $name) {
            $nameStrings[] = $name->last()->toString();
        }

        return FullName::fromString(
            vsprintf($this->nameTemplate, $nameStrings)
        );
    }

    /**
     * Given a set of concrete types, and a qualifier that is able
     * to resolve type variable to full types (in general this should be the
     * namespace context of the generic type) returns the generic types to
     * concrete types assignments
     *
     * @param FullName[] $names
     * @param NameQualifier $nameQualifier
     *
     * @return NameAssignmentContext
     */
    public function assignments(
        array $names,
        NameQualifier $nameQualifier
    ) {
        $this->assertValidNumberOfNames($names);

        $assignments = array();

        foreach ($this->typeVars as $i => $relativeTypeVarName) {
            $fullTypeVarName = $nameQualifier->qualify($relativeTypeVarName);
            $assignments[] = new NameAssignment(
                $fullTypeVarName,
                $names[$i]
            );
        }

        return new NameAssignmentContext($assignments);
    }

    /**
     * @param FullName[] $names
     * @return SimpleNameAssignmentContext
     */
    public function simpleAssignments(
        array $names
    ) {
        return new SimpleNameAssignmentContext(array(
            new SimpleNameAssignment(
                $this->name()->last(),
                $this->apply($names)->last()
            )
        ));
    }

    /**
     * @param array $names
     */
    private function assertValidNumberOfNames(array $names)
    {
        if (count($names) != count($this->typeVars)) {
            throw new InvalidArgumentException(
                "Wrong number of names provided"
            );
        }
    }

    /**
     * Parse the generic name
     */
    private function parseName()
    {
        $name = $this->name->toString();
        $len = strlen($name);

        $nameTemplate = "";
        $typeVars = array();
        $currTypeVar = "";

        $level = 0;

        for ($i = 0; $i < $len; $i++) {
            $char = $name[$i];
            if (ord($char) == self::CHAR_CODE) {
                $char .= $name[++$i];
            }

            if ($char == "»" || $char == "·") {
                --$level;
            }

            if ($level == 0) {
                $nameTemplate .= $char;
                if ($char == "«") {
                    $nameTemplate .= "%s";
                } elseif ($char == "·") {
                    $nameTemplate .= "%s";
                    $typeVars[] = RelativeName::fromString($currTypeVar);
                    $currTypeVar = "";
                } elseif ($char == "»") {
                    $typeVars[] = RelativeName::fromString($currTypeVar);
                    $currTypeVar = "";
                }
            } else {
                $currTypeVar .= $char;
            }

            if ($char == "«"  || $char == "·") {
                ++$level;
            }
        }

        $this->nameTemplate = $nameTemplate;
        $this->typeVars = $typeVars;
    }
}