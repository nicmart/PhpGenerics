<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Dumper;


use NicMart\Generics\Source\SourceUnit;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Type\VariableType;
use NicMart\Generics\Variable\Variable;

class VariableDiscriminatingSourceUntiDumper implements SourceUnitDumper
{
    /**
     * @var SourceUnitDumper
     */
    private $defaultDumper;
    /**
     * @var SourceUnitDumper
     */
    private $varDumper;

    /**
     * VariableDiscriminatingSourceUntiDumper constructor.
     * @param SourceUnitDumper $defaultDumper
     * @param SourceUnitDumper $varDumper
     */
    public function __construct(
        SourceUnitDumper $defaultDumper,
        SourceUnitDumper $varDumper
    ) {
        $this->defaultDumper = $defaultDumper;
        $this->varDumper = $varDumper;
    }

    /**
     * @param SourceUnit $sourceUnit
     * @return DumpResult|void
     */
    public function dump(SourceUnit $sourceUnit)
    {
        if ($this->hasVariable($sourceUnit->type())) {
            return $this->varDumper->dump($sourceUnit);
        }

        return $this->defaultDumper->dump($sourceUnit);
    }

    /**
     * @param Type $type
     * @return mixed
     */
    private function hasVariable(Type $type)
    {
        return $type->bottomUpFold(false, function ($hasVar, Type $t) {
            return $hasVar || $t instanceof VariableType;
        });
    }
}