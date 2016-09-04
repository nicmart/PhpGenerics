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
use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 * Class UnionType
 * @package NicMart\Generics\Type
 */
final class UnionType implements Type
{
    /**
     * @var array
     */
    private $types = array();

    /**
     * UnionType constructor.
     * @param array $types
     */
    public function __construct(array $types)
    {
        foreach ($types as $type) {
            $this->addType($type);
        }
    }

    /**
     * @return FullName
     */
    public function name()
    {
        throw new RuntimeException("Union types can't have a name");
    }

    /**
     * @return Type[]
     */
    public function types()
    {
        return $this->types;
    }

    /**
     * @param TypeTransformer $typeTransformer
     * @return UnionType
     */
    public function map(TypeTransformer $typeTransformer)
    {
        $newTypes = array();

        foreach ($this->types() as $type) {
            $newTypes[] = $typeTransformer->transform($type);
        }

        return new self($newTypes);
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
            str_replace("\n", "\n\t", implode(",\n", $this->types()))
        );
    }


    /**
     * @param $z
     * @param callable $fold
     * @return mixed
     */
    public function bottomUpFold($z, callable $fold)
    {
        foreach ($this->types() as $arg) {
            $z = $fold($z, $arg);
        }

        return $fold($z, $this);
    }

    /**
     * @param Type $type
     */
    private function addType(Type $type)
    {
        $this->types[] = $type;
    }
}