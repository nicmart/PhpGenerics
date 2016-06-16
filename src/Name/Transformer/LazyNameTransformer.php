<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Name;

/**
 * Class LazyNameTransformer
 * @package NicMart\Generics\Name\Transformer
 */
class LazyNameTransformer implements NameTransformer
{
    /**
     * @var NameTransformer
     */
    private $transformer;

    /**
     * @var callable
     */
    private $factory;

    /**
     * LazyNameTransformer constructor.
     * @param callable $factory
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return mixed
     */
    public function transformName(
        Name $name,
        NamespaceContext $namespaceContext
    ) {
        if (!isset($this->transformer)) {
            $this->transformer = call_user_func(
                $this->factory,
                $this
            );
        }

        return $this->transformer->transformName(
            $name,
            $namespaceContext
        );
    }
}