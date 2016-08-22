<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 18/08/2016, 18:08
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

/**
 * Class TagTypeFunctor
 *
 * Map implementation for PhpDoc Tags when intended as a container of PhpDoc types
 *
 * @package NicMart\Generics\Infrastructure\PhpDocumentor
 */
final class TagTypeFunctor
{
    public static function map(Tag $tag, callable $f)
    {
        if ($tag instanceof Param && $tag->getType()) {
            return new Param(
                $tag->getVariableName(),
                $f($tag->getType()),
                $tag->isVariadic(),
                $tag->getDescription()
            );
        }

        if ($tag instanceof Return_ && $tag->getType()) {
            return new Return_(
                $f($tag->getType()),
                $tag->getDescription()
            );
        }

        if ($tag instanceof Var_ && $tag->getType()) {
            return new Var_(
                $tag->getVariableName(),
                $f($tag->getType()),
                $tag->getDescription()
            );
        }

        if ($tag instanceof Property && $tag->getType()) {
            return new Property(
                $tag->getVariableName(),
                $f($tag->getType()),
                $tag->getDescription()
            );
        }

        return $tag;
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function lift(callable $f)
    {
        return function (Tag $tag) use ($f) {
            return self::map($tag, $f);
        };
    }
}