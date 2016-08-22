<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor\Adapter;

use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\SimpleName;
use phpDocumentor\Reflection\Types\Context as PhpDocContext;

/**
 * Class PhpDocContextAdapter
 *
 * Convert back and forth between Domain Namespace Context and PhpDoc Type Context
 *
 * @package NicMart\Generics\Infrastructure\PhpDocumentor\Adapter
 */
class PhpDocContextAdapter
{
    /**
     * @param NamespaceContext $namespaceContext
     * @return PhpDocContext
     */
    public function toPhpDocContext(NamespaceContext $namespaceContext)
    {
        $uses = array();

        foreach ($namespaceContext->uses()->getUsesByAliases() as $alias => $use) {
            $uses[$alias] = $use->name()->toString();
        }

        return new PhpDocContext(
            $namespaceContext->namespace_()->toString(),
            $uses
        );
    }

    /**
     * @param PhpDocContext $phpDocContext
     * @return NamespaceContext
     */
    public function fromPhpDocContext(PhpDocContext $phpDocContext = null)
    {
        if (!$phpDocContext) {
            return NamespaceContext::emptyContext();
        }

        $namespace = Namespace_::fromString($phpDocContext->getNamespace());

        $uses = array();

        foreach ($phpDocContext->getNamespaceAliases() as $alias => $ns) {
            $uses[] = new Use_(
                FullName::fromString($ns),
                new SimpleName($alias)
            );
        }

        return new NamespaceContext(
            $namespace,
            new Uses($uses)
        );
    }
}