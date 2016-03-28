<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;

use NicMart\Generics\Type\Context\NamespaceContext;

/**
 * Class RelativeType
 * @package NicMart\Generics\Type
 */
final class RelativeType
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $root;

    /**
     * @var string[]
     */
    private $tail;

    /**
     * @param string[] $parts
     * @return RelativeType
     */
    public static function fromParts(array $parts)
    {
        return new self(implode("\\", $parts));
    }

    /**
     * RelativeType constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $parts = explode("\\", $name);

        $this->name = $name;
        $this->root = $parts[0];
        $this->tail = array_slice($parts, 1);
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param NamespaceContext $context
     * @return Type
     * @throws \UnderflowException
     */
    public function toFullType(NamespaceContext $context)
    {
        if ($this->root === "") {
            return new Type($this->name());
        }

        if ($context->hasUse($this->root)) {
            return Type::fromParts(array_merge(
                array($context->getUse($this->root)->name()),
                $this->tail
            ));
        }

        return Type::fromParts(array(
            $context->getNamespace()->name(),
            $this->name()
        ));
    }


    /**
     * @return array
     */
    public function parts()
    {
        return explode("\\", $this->name);
    }
}