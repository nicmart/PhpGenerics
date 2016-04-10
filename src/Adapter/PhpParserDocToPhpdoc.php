<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Adapter;


use NicMart\Generics\Name\Context\NamespaceContext;
use phpDocumentor\Reflection\DocBlock;
use PhpParser\Comment\Doc;

/**
 * Class PhpParserDocToPhpdoc
 * @package NicMart\Generics\Adapter
 */
class PhpParserDocToPhpdoc
{
    /**
     * @param Doc $phpdoc
     * @param NamespaceContext $namespaceContext
     * @return DocBlock
     * @throws \InvalidArgumentException
     */
    public function transform(Doc $phpdoc, NamespaceContext $namespaceContext)
    {
        $namespace = $namespaceContext->getNamespace()->toString();

        $uses = array();
        foreach ($namespaceContext->getUsesByAliases() as $alias => $use) {
            $uses[$use->alias()->toString()] = $use->name()->toString();
        }

        return new DocBlock(
            $phpdoc->getText(),
            new DocBlock\Context(
                $namespace,
                $uses
            )
        );
    }
}