<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Compiler\PhpDoc;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;

/**
 * Class ReplaceTypePhpDocCompiler
 * @package NicMart\Generics\Compiler\PhpDoc
 */
class ReplaceTypePhpDocCompiler implements PhpDocCompiler
{
    /**
     * @param DocBlock $docBlock
     * @param array $typeAssignments
     * @return DocBlock
     */
    public function compile(DocBlock $docBlock, array $typeAssignments)
    {
        foreach ($docBlock->getTags() as $tag) {
            if (!$tag instanceof ReturnTag) {
                continue;
            }

            $this->transformType($tag, $typeAssignments);
        }

        return $docBlock;
    }

    /**
     * @param ReturnTag $tag
     * @param array $typeAssignments
     */
    private function transformType(ReturnTag $tag, array $typeAssignments)
    {
        $fromTypes = $tag->getTypes();

        $toTypes = array();

        $atLeastOneTypeTransformed = false;
        foreach ($fromTypes as $fromType) {
            $fromType = ltrim($fromType, "\\");
            $atLeastOneTypeTransformed = $atLeastOneTypeTransformed || isset($typeAssignments[$fromType]);
            $toTypes[] = isset($typeAssignments[$fromType])
                ? $typeAssignments[$fromType]
                : $fromType
            ;
        }

        if ($atLeastOneTypeTransformed) {
            ReturnTagTypeReplacer::setType(
                $tag,
                implode("|", $toTypes)
            );
        }
    }
}