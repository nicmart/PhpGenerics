<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\Name\Context;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\NamespaceContextExtractor;

/**
 * Class OptimizedNamespaceExtractor
 * @package NicMart\Generics\Infrastructure\Name\Context
 */
class OptimizedNamespaceExtractor implements NamespaceContextExtractor
{
    /**
     * @var NamespaceContextExtractor
     */
    private $contextExtractor;

    /**
     * OptimizedNamespaceExtractor constructor.
     * @param NamespaceContextExtractor $contextExtractor
     */
    public function __construct(NamespaceContextExtractor $contextExtractor)
    {
        $this->contextExtractor = $contextExtractor;
    }

    /**
     * @param string $source
     * @return NamespaceContext
     */
    public function contextOf($source)
    {
        if (preg_match(
            "/^(.*)\\n(class|interface|trait) /",
            $source,
            $matches
        )) {
            $source = $matches[1];
        }

        return $this->contextExtractor->contextOf($source);
    }
}