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
     * @param TypeTransformer $typeTransformer
     * @return Type
     */
    public function map(TypeTransformer $typeTransformer)
    {
        $params = array();

        foreach ($this->parameters() as $param) {
            $params[] = $typeTransformer->transform($param);
        }

        return new self($this->name(), $params);
    }


    /**
     * @param VariableType $parameter
     */
    private function addParameter(VariableType $parameter)
    {
        $this->parameters[] = $parameter;
    }
}