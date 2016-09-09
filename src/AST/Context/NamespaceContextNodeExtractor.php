<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Context;


use NicMart\Generics\Infrastructure\PhpParser\PhpNameAdapter;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use PhpParser\Node;

class NamespaceContextNodeExtractor
{
    /**
     * @var PhpNameAdapter
     */
    private $nameAdapter;

    /**
     * NamespaceContextNodeExtractor constructor.
     * @param PhpNameAdapter $nameAdapter
     */
    public function __construct(PhpNameAdapter $nameAdapter)
    {
        $this->nameAdapter = $nameAdapter;
    }

    private function namespaceExtractor()
    {
        return function (Node $node) {

            if ($node instanceof Node\Stmt\Namespace_) {
                return NamespaceContext::fromNamespaceName(
                    $node->name->toString()
                );
            }

            if ($node instanceof Node\Stmt\UseUse) {
                return NamespaceContext::emptyContext()
                    ->withUse(Use_::fromStrings(
                        $node->name->toString(),
                        $node->alias
                    ))
                ;
            }

            return NamespaceContext::emptyContext();
        };
    }

    private function map()
}