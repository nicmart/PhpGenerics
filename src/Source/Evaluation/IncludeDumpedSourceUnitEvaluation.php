<?php
/**
 * @author Nicolò Martini - <nicolo@martini.io>
 *
 * Created on 11/05/2016, 13:49
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Source\Evaluation;

use NicMart\Generics\Source\Dumper\SourceUnitDumper;
use NicMart\Generics\Source\SourceUnit;

/**
 * Class IncludeDumpedSourceUnitEvaluation
 * @package NicMart\Generics\Source\Evaluation
 */
class IncludeDumpedSourceUnitEvaluation implements SourceUnitEvaluation
{
    /**
     * @var SourceUnitDumper
     */
    private $sourceUnitDumper;

    /**
     * IncludeDumpedSourceUnitEvaluation constructor.
     * @param SourceUnitDumper $sourceUnitDumper
     */
    public function __construct(
        SourceUnitDumper $sourceUnitDumper
    ) {
        $this->sourceUnitDumper = $sourceUnitDumper;
    }

    /**
     * @param SourceUnit $sourceUnit
     */
    public function evaluate(SourceUnit $sourceUnit)
    {
        include $this->sourceUnitDumper->dump($sourceUnit)->filename();
    }
}