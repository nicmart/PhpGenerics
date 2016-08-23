<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpParser;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Transformer\NameTransformer;
use PhpParser\Node;

/**
 * Class PhpNameTransformer
 * @package NicMart\Generics\Infrastructure\PhpParser
 */
final class PhpNameTransformer
{
    /**
     * @var PhpNameAdapter
     */
    private $adapter;
    
    /**
     * @var NameTransformer
     */
    private $nameTransformer;

    /**
     * PhpNameTransformer constructor.
     * @param PhpNameAdapter $adapter
     * @param NameTransformer $nameTransformer
     */
    public function __construct(
        PhpNameAdapter $adapter,
        NameTransformer $nameTransformer
    ) {
        $this->adapter = $adapter;
        $this->nameTransformer = $nameTransformer;
    }

    /**
     * @param Node\Name $name
     * @param NamespaceContext $context
     * @return Node\Name|Node\Name\FullyQualified
     */
    public function transform(Node\Name $name, NamespaceContext $context)
    {
        return $this->adapter->toPhpName(
            $this->nameTransformer->transformName(
                $this->adapter->fromPhpName($name),
                $context
            )
        );
    }
}