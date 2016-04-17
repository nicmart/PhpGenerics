<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Name;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Name\Transformer\NameSimplifier;
use PhpParser\Node\Name;

/**
 * Class NameSimplifierPhpParserNameTransformer
 * @package NicMart\Generics\AST\Name
 */
class NameSimplifierPhpParserNameTransformer implements PhpParserNameTransformer
{
    /**
     * @var NameSimplifier
     */
    private $nameSimplifier;

    /**
     * NameSimplifierPhpParserNameTransformer constructor.
     * @param NameSimplifier $nameSimplifier
     */
    public function __construct(NameSimplifier $nameSimplifier)
    {
        $this->nameSimplifier = $nameSimplifier;
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return Name
     */
    public function transform(Name $name, NamespaceContext $namespaceContext)
    {
        $fullName = $name->isFullyQualified()
            ? new FullName($name->parts)
            : $namespaceContext->qualify(new RelativeName($name->parts))
        ;

        return new Name(
            $this->nameSimplifier->simplify($fullName)->parts()
        );
    }
}