<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;


use InvalidArgumentException;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Name;

/**
 * Class ListenerNameTransformer
 * @package NicMart\Generics\Name\Transformer
 */
class ListenerNameTransformer implements NameTransformer
{
    /**
     * @var NameTransformer
     */
    private $nameTransformer;
    
    /**
     * @var
     */
    private $listener;

    /**
     * ListenerNameTransformer constructor.
     * @param NameTransformer $nameTransformer
     * @param callable $listener
     */
    public function __construct(NameTransformer $nameTransformer, $listener)
    {
        if (!is_callable($listener)) {
            throw new InvalidArgumentException(
                "Listener must be a valid callable"
            );
        }

        $this->listener = $listener;
        $this->nameTransformer = $nameTransformer;
    }

    /**
     * @param Name $name
     * @param NamespaceContext $namespaceContext
     * @return Name
     */
    public function transformName(
        Name $name,
        NamespaceContext $namespaceContext
    ) {
        $transformed = $this->nameTransformer->transformName(
            $name,
            $namespaceContext
        );

        if ($name != $transformed) {
            call_user_func($this->listener, $name, $transformed);
        }

        return $transformed;
    }
}