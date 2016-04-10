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

use NicMart\Generics\Adapter\PhpParserDocToPhpdoc;
use NicMart\Generics\Name\Assignment\NameAssignmentContext;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use PhpParser\Comment\Doc;

/**
 * Class ReplaceTypePhpDocTransformer
 * @package NicMart\Generics\Compiler\PhpDoc
 */
class ReplaceTypePhpDocTransformer implements PhpDocTransformer
{
    /**
     * @var NameAssignmentContext
     */
    private $nameAssignmentContext;
    /**
     * @var PhpParserDocToPhpdoc
     */
    private $docToPhpdoc;
    /**
     * @var DocBlock\Serializer
     */
    private $docBlockSerializer;

    /**
     * ReplaceTypePhpDocTransformer constructor.
     * @param NameAssignmentContext $nameAssignmentContext
     * @param PhpParserDocToPhpdoc $docToPhpdoc
     * @param DocBlock\Serializer $docBlockSerializer
     */
    public function __construct(
        NameAssignmentContext $nameAssignmentContext,
        PhpParserDocToPhpdoc $docToPhpdoc,
        DocBlock\Serializer $docBlockSerializer
    ) {
        $this->nameAssignmentContext = $nameAssignmentContext;
        $this->docToPhpdoc = $docToPhpdoc;
        $this->docBlockSerializer = $docBlockSerializer;
    }

    /**
     * @param Doc $docBlock
     * @param NamespaceContext $namespaceContext
     * @return Doc
     * @throws \InvalidArgumentException
     */
    public function transform(
        Doc $docBlock,
        NamespaceContext $namespaceContext
    ) {
        $phpDocBlock = $this->docToPhpdoc->transform(
           $docBlock,
           $namespaceContext
        );

        foreach ($phpDocBlock->getTags() as $tag) {
            if (!$tag instanceof ReturnTag) {
                continue;
            }

            $this->transformType(
                $tag,
                $namespaceContext
            );
        }

        return new Doc(
            $this->docBlockSerializer->getDocComment($phpDocBlock)
        );
    }

    /**
     * @param ReturnTag $tag
     * @param NamespaceContext $namespaceContext
     */
    private function transformType(
        ReturnTag $tag,
        NamespaceContext $namespaceContext
    ) {
        $fromTypes = $tag->getTypes();

        $toTypes = array();

        $atLeastOneTypeTransformed = false;

        foreach ($fromTypes as $fromType) {
            $fromType = FullName::fromString($fromType);
            $hasAssignmentFrom = $this->nameAssignmentContext
                ->hasAssignmentFrom($fromType)
            ;
            $atLeastOneTypeTransformed =
                $atLeastOneTypeTransformed
                || $hasAssignmentFrom
            ;
            $toType = $hasAssignmentFrom
                ? $this->nameAssignmentContext->transformName($fromType)
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