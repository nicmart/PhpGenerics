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
 * Class GenericType
 * @package NicMart\Generics\Type
 */
final class GenericType implements ReferenceType
{
    /**
     * @var VariableType[]
     */
    private $parameters;

    /**
     * @var FullName
     */
    private $name;

    /**
     * GenericType constructor.
     * @param FullName $name
     * @param VariableType[] $parameters
     */
    public function __construct(FullName $name, array $parameters)
    {
        $this->name = $name;
        foreach ($parameters as $parameter) {
            $this->addParameter($parameter);
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
     * @return VariableType[]
     */
    public function parameters()
    {
        return $this->parameters;
    }

    /**
     * @return int
     */
    public function arity()
    {
        return count($this->parameters);
    }

    /**
     * This can look strange, but since a GenericType can contain only
     * VariableType, we consider it as an empty container of types, like
     * the other leaves in the type graph.
     *
     * @param TypeTransformer $typeTransformer
     * @return Type
     */
    public function map(TypeTransformer $typeTransformer)
    {
        return $this;
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
            str_replace("\n", "\n\t", implode(",\n", $this->parameters()))
        );
    }

    /**
     * @param callable $z
     * @param callable $fold
     * @return mixed
     */
    public function bottomUpFold($z, callable $fold)
    {
        foreach ($this->parameters() as $arg) {
            $z = $fold($z, $arg);
        }

        return $fold($z, $this);
    }

    /**
     * @return ParametrizedType
     */
    public function toParametrizedType()
    {
        return new ParametrizedType(
            $this->name(),
            $this->parameters()
        );
    }


    /**
     * @param VariableType $parameter
     */
    private function addParameter(VariableType $parameter)
    {
        $this->parameters[] = $parameter;
    }
}