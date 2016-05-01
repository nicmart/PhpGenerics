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
     * @var bool
     */
    private $buildContext;

    /**
     * PhpParserDocToPhpdoc constructor.
     * @param bool $buildContext
     */
    public function __construct($buildContext = true)
    {
        $this->buildContext = (bool) $buildContext;
    }

    /**
     * @param Doc $phpdoc
     * @param NamespaceContext $namespaceContext
     * @return DocBlock
     * @throws \InvalidArgumentException
     */
    public function transform(Doc $phpdoc, NamespaceContext $namespaceContext)
    {
        $phpdocText = $phpdoc->getText();

        if (!$this->buildContext) {
            return new DocBlock($phpdocText);
        }

        $namespace = $namespaceContext->namespace_()->toString();

        $aliases = array();
        foreach ($namespaceContext->uses()->getUsesByAliases() as $alias => $use) {
            $aliases[$use->alias()->toString()] = $use->name()->toString();
        }

        return new DocBlock(
            $phpdoc->getText(),
            new DocBlock\Context(
                $namespace,
                $aliases
            )
        );
    }
}