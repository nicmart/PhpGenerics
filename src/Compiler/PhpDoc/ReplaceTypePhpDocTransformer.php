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

/**
 * Class ReplaceTypePhpDocTransformer
 * @package NicMart\Generics\Compiler\PhpDoc
 */
class ReplaceTypePhpDocTransformer extends AbstractPhpDocTransformer
{
    /**
     * @var NameAssignmentContext
     */
    private $nameAssignmentContext;

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
        parent::__construct($docToPhpdoc, $docBlockSerializer);
        $this->nameAssignmentContext = $nameAssignmentContext;
    }

    /**
     * @param string $type
     * @param NamespaceContext $namespaceContext
     * @return string
     */
    protected function transformType(
        $type,
        NamespaceContext $namespaceContext
    ) {
        $fromType = FullName::fromString($type);
        $hasAssignmentFrom = $this->nameAssignmentContext
            ->hasAssignmentFrom($fromType)
        ;

        $toType = $hasAssignmentFrom
            ? $this->nameAssignmentContext->transform($fromType)
            : $fromType
        ;

        return $toType->toAbsoluteString();
    }
}