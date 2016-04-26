<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Source\Evaluation;

use NicMart\Generics\Source\SourceUnit;

/**
 * Interface SourceUnitEvaluation
 * @package NicMart\Generics\Source\Evaluation
 */
interface SourceUnitEvaluation
{
    /**
     * @param SourceUnit $sourceUnit
     * @return mixed
     */
    public function evaluate(SourceUnit $sourceUnit);
}