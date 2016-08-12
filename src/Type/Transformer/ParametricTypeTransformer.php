<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Transformer;


use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Type;
use NicMart\Generics\Type\VariableType;

/**
 * Class ParametricTypeTransformer
 *
 * A non-recursive type transformed, that transform variable types of
 * a Generic Type to the correspondent Types of a Parametrized Types
 *
 * Can be made recursive with BottomUp / TopDown transformer combinators
 *
 * @package NicMart\Generics\Type\Transformer
 */
class ParametricTypeTransformer implements TypeTransformer
{
    /**
     * @var GenericType
     */
    private $genericType;

    /**
     * @var ParametrizedType
     */
    private $parametrizedType;

    /**
     * @var Type[]
     */
    private $typeMap = array();

    /**
     * ParametricTypeTransformer constructor.
     * @param GenericType $genericType
     * @param ParametrizedType $parametrizedType
     */
    public function __construct(
        GenericType $genericType,
        ParametrizedType $parametrizedType
    ) {
        $this->genericType = $genericType;
        $this->parametrizedType = $parametrizedType;

        $this->assertValidArity();
        $this->typeMap = $this->indexTypeVars();
    }

    /**
     * @param Type $type
     * @return Type
     */
    public function transform(Type $type)
    {
        if ($type instanceof GenericType) {
            return new ParametrizedType(
                $type->name(),
                $type->parameters()
            );
        }

        if (!$type instanceof VariableType) {
            return $type;
        }

        $varName = $type->name()->toString();

        if (isset($this->typeMap[$varName])) {
            return $this->typeMap[$varName];
        }

        return $type;
    }

    private function assertValidArity()
    {
        if ($this->genericType->arity() == $this->parametrizedType->arity()) {
            return;
        }

        throw new \InvalidArgumentException(
            "Generic type arity (%s) is not equal to parametrized type arity (%d)",
            $this->genericType->arity(),
            $this->parametrizedType->arity()
        );
    }

    /**
     * @return array
     */
    private function indexTypeVars()
    {
        $vars = $this->genericType->parameters();
        $params = $this->parametrizedType->arguments();

        $map = array();

        foreach ($vars as $i => $var) {
            $map[$var->name()->toString()] = $params[$i];
        }

        return $map;
    }
}