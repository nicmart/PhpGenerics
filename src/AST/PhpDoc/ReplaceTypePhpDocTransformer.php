<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\PhpDoc;

use NicMart\Generics\Adapter\PhpParserDocToPhpdoc;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Transformer\NameTransformer;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use PhpParser\Comment\Doc;

/**
 * Class AbstractPhpDocTransformer
 * @package NicMart\Generics\Compiler\PhpDoc
 */
class ReplaceTypePhpDocTransformer implements PhpDocTransformer
{
    /**
     * @var PhpParserDocToPhpdoc
     */
    private $docToPhpdoc;

    /**
     * @var DocBlock\Serializer
     */
    private $docBlockSerializer;
    /**
     * @var NameTransformer
     */
    private $nameTransformer;

    /**
     * ReplaceTypePhpDocTransformer constructor.
     * @param NameTransformer $nameTransformer
     * @param PhpParserDocToPhpdoc $docToPhpdoc
     * @param DocBlock\Serializer $docBlockSerializer
     */
    public function __construct(
        NameTransformer $nameTransformer,
        PhpParserDocToPhpdoc $docToPhpdoc,
        DocBlock\Serializer $docBlockSerializer
    ) {
        $this->nameTransformer = $nameTransformer;
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

            $this->transformTypes(
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
    private function transformTypes(
        ReturnTag $tag,
        NamespaceContext $namespaceContext
    ) {
        $fromTypes = $tag->getTypes();

        $toTypes = array();

        $atLeastOneTypeTransformed = false;

        foreach ($fromTypes as $fromType) {
            $toType = $this->transformType($fromType, $namespaceContext);
            $atLeastOneTypeTransformed = $atLeastOneTypeTransformed
                || $toType != $fromType
            ;
            $toTypes[] = $toType;
        }

        if ($atLeastOneTypeTransformed) {
            ReturnTagTypeReplacer::setType(
                $tag,
                implode("|", $toTypes)
            );
        }
    }

    /**
     * @param string $type
     * @param NamespaceContext $namespaceContext
     * @return string
     */
    private function transformType(
        $type,
        NamespaceContext $namespaceContext
    ) {
        $from = FullName::fromString($type);
        $to = $this->nameTransformer->transformName(
            $from,
            $namespaceContext
        );

        if ($from == $to) {
            return $type;
        }

        return $to->toCanonicalString();
    }
}