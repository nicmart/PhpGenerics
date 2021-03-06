<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\Transformer\TypeTransformer;

/**
 * Class ParametrizedType
 * @package NicMart\Generics\Type
 */
final class ParametrizedType implements ReferenceType
{
    /**
     * @var Type[]
     */
    private $arguments;

    /**
     * @var FullName
     */
    private $name;

    /**
     * GenericType constructor.
     * @param FullName $name
     * @param Type[] $arguments
     */
    public function __construct(FullName $name, array $arguments)
    {
        $this->name = $name;
        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
    }

    /**
     * @return FullName
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return Type[]
     */
    public function arguments()
    {
        return $this->arguments;
    }

    /**
     * @return int
     */
    public function arity()
    {
        return count($this->arguments);
    }

    /**
     * @param TypeTransformer $typeTransformer
     * @return Type
     */
    public function map(TypeTransformer $typeTransformer)
    {
        $arguments = array();

        foreach ($this->arguments() as $argument) {
            $arguments[] = $typeTransformer->transform($argument);
        }

        return new self($this->name(), $arguments);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            "%s [%s] (\n\t%s\n)",
            $this->name()->toString(),
            FullName::fromString(get_class($this))->last()->toString(),
            str_replace("\n", "\n\t", implode(",\n", $this->arguments()))
        );
    }

    /**
     * @param callable $z
     * @param callable $fold
     * @return mixed
     */
    public function bottomUpFold($z, callable $fold)
    {
        foreach ($this->arguments() as $arg) {
            $z = $fold($z, $arg);
        }

        return $fold($z, $this);
    }


    /**
     * @param Type $argument
     */
    private function addArgument(Type $argument)
    {
        $this->arguments[] = $argument;
    }
}