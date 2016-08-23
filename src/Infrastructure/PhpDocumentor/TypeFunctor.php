<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 19/08/2016, 09:12
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;


use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;

/**
 * Class TypeFunctor
 *
 * Functorial map function for phpdoc types, intended as containers of themselves
 *
 * @package NicMart\Generics\Infrastructure\PhpDocumentor
 */
final class TypeFunctor
{
    /**
     * @param Type $type
     * @param callable $f
     * @return Type|Compound
     */
    public static function map(Type $type, callable $f)
    {
        if (!$type instanceof Compound) {
            return $type;
        }

        $mappedTypes = array();

        for ($i = 0; $type->has($i); $i++) {
            $mappedTypes[] = $f($type->get($i));
        }

        return new Compound($mappedTypes);
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function lift(callable $f)
    {
        return function (Type $type) use ($f) {
            return $f($type);
        };
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function bottomUp(callable $f)
    {
        return function (Type $type) use ($f) {
            return $f(self::map($type, self::bottomUp($f)));
        };
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function topDown(callable $f)
    {
        return function (Type $type) use ($f) {
            return self::map($f($type), self::topDown($f));
        };
    }
}