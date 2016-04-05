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

use NicMart\Generics\Type\Assignment\TypeAssignmentContext;
use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\RelativeType;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;

/**
 * Class ReplaceTypePhpDocTransformer
 * @package NicMart\Generics\Compiler\PhpDoc
 */
class ReplaceTypePhpDocTransformer implements PhpDocTransformer
{
    /**
     * @param DocBlock $docBlock
     * @param NamespaceContext $namespaceContext
     * @param TypeAssignmentContext $typeAssignmentContext
     * @return DocBlock
     */
    public function transform(
        DocBlock $docBlock,
        NamespaceContext $namespaceContext,
        TypeAssignmentContext $typeAssignmentContext
    ) {
        foreach ($docBlock->getTags() as $tag) {
            if (!$tag instanceof ReturnTag) {
                continue;
            }

            $this->transformType($tag, $namespaceContext, $typeAssignmentContext);
        }

        return $docBlock;
    }

    /**
     * @param ReturnTag $tag
     * @param NamespaceContext $namespaceContext
     * @param TypeAssignmentContext $typeAssignmentContext
     */
    private function transformType(
        ReturnTag $tag,
        NamespaceContext $namespaceContext,
        TypeAssignmentContext $typeAssignmentContext
    ) {
        $fromTypes = $tag->getTypes();

        $toTypes = array();

        $atLeastOneTypeTransformed = false;
        foreach ($fromTypes as $fromType) {
            $fromRelativeType = RelativeType::fromString($fromType);
            $fromType = $fromRelativeType->toFullType($namespaceContext);
            $hasAssignmentFrom = $typeAssignmentContext->hasAssignmentFrom($fromType);
            $atLeastOneTypeTransformed =
                $atLeastOneTypeTransformed
                || $hasAssignmentFrom
            ;
            $toType = $hasAssignmentFrom
                ? $typeAssignmentContext->transformType($fromType)
                : $fromType
            ;
            $toTypes[] = $toType->toString();
        }

        if ($atLeastOneTypeTransformed) {
            ReturnTagTypeReplacer::setType(
                $tag,
                implode("|", $toTypes)
            );
        }
    }
}