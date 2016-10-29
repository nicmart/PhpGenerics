<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 06/09/2016, 12:21
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\AST\Transformer;


use PhpParser\Node;

/**
 * Class NodeFunctor
 * @package NicMart\Generics\AST\Transformer
 */
class NodeFunctor
{
    /**
     * @param Node $node
     * @param callable $f
     * @return Node
     */
    public static function map(Node $node, callable $f)
    {
        $node = clone $node;

        foreach ($node->getSubNodeNames() as $subNodeName) {
            $subNode = $node->$subNodeName;

            $node->$subNodeName = is_array($subNode)
                ? static::mapArray($subNode, $f)
                : ($subNode instanceof Node
                    ? $f($subNode)
                    : $subNode
                )
            ;
        }

        return $node;
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function lift(callable $f)
    {
        return function (Node $node) use ($f) {
            return self::map($node, $f);
        };
    }

    /**
     * @param Node[] $nodes
     * @param callable $f
     * @return Node[]
     */
    public static function mapArray(array $nodes, callable $f)
    {
        return array_map(
            function ($n) use ($f) {
                $a = $n instanceof Node ? $f($n) : $n;
                return $a;
            },
            $nodes
        );
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function liftArray(callable $f)
    {
        return function (array $nodes) use ($f) {
            return self::mapArray($nodes, $f);
        };
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function bottomUp(callable $f)
    {
        return function (Node $n) use ($f) {
            return $f(self::map($n, self::bottomUp($f)));
        };
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function topDown(callable $f)
    {
        return function (Node $n) use ($f) {
            return self::map($f($n), self::topDown($f));
        };
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function fold(callable $f)
    {
        return self::topDownGenericFold(
            [NodeFunctor::class, "foldChildren"],
            $f
        );
    }

    /**
     * @param callable $childrenFold
     * @param callable $f
     * @return \Closure
     */
    public static function topDownGenericFold(callable $childrenFold, callable $f)
    {
        return $self = function (Node $n, $z) use ($f, &$self, $childrenFold) {
            return call_user_func(
                $childrenFold($self),
                $n,
                $f($n, $z)
            );
        };
    }

    /**
     * @param callable $childrenFold
     * @param callable $f
     * @return \Closure
     */
    public static function bottomUpGenericFold(callable $childrenFold, callable $f)
    {
        return $self = function (Node $n, $z) use ($f, &$self, $childrenFold) {
            return $f($n, call_user_func(
                $childrenFold($self),
                $n,
                $z
            ));
        };
    }

    /**
     * Apply a fold to all the children
     *
     * @param callable $f
     * @return \Closure
     */
    public static function foldChildren(callable $f)
    {
        return function (Node $n, $z) use ($f) {
            foreach ($n->getSubNodeNames() as $subNodeName) {
                foreach ((array) $n->$subNodeName as $subNode) {
                    if ($subNode instanceof Node) {
                        $z = $f($subNode, $z);
                    }
                }
            }

            return $z;
        };
    }
}