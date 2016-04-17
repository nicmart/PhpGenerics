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
use NicMart\Generics\Name\Transformer\NameTransformer;
use PhpParser\Node\Name;

/**
 * Class FullNamePhpParserNameTransformer
 * @package NicMart\Generics\AST\Name
 */
class FullNamePhpParserNameTransformer implements PhpParserNameTransformer
{
    /**
     * @var NameTransformer
     */
    private $nameTransformer;

    /**
     * FullNamePhpParserNameTransformer constructor.
     * @param NameTransformer $nameTransformer
     */
    public function __construct(NameTransformer $nameTransformer)
    {
        $this->nameTransformer = $nameTransformer;
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return Name\FullyQualified
     */
    public function transform(Name $name, NamespaceContext $namespaceContext)
    {
        $fullName = $name->isFullyQualified()
            ? new FullName($name->parts)
            : $namespaceContext->qualify(new RelativeName($name->parts))
        ;

        return new Name\FullyQualified(
            $this->nameTransformer->transform($fullName)->parts()
        );
    }
}