<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 18/08/2016, 18:17
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;

use phpDocumentor\Reflection\DocBlock;

/**
 * Class DocBlockTagFunctor
 *
 * Mapping implementation for DocBlocks intended as containers of tags
 *
 * @package NicMart\Generics\Infrastructure\PhpDocumentor
 */
final class DocBlockTagFunctor
{
    /**
     * @param DocBlock $docBlock
     * @param callable $f
     * @return DocBlock
     */
    public static function map(DocBlock $docBlock, callable $f)
    {
        $tags = $docBlock->getTags();
        $mappedTags = [];

        foreach ($tags as $tag) {
            $mappedTags[] = $f($tag);
        }

        return new DocBlock(
            $docBlock->getSummary(),
            $docBlock->getDescription(),
            $mappedTags,
            $docBlock->getContext(),
            $docBlock->getLocation(),
            $docBlock->isTemplateStart(),
            $docBlock->isTemplateEnd()
        );
    }

    /**
     * @param callable $f
     * @return \Closure
     */
    public static function lift(callable $f)
    {
        return function (DocBlock $docBlock) use ($f) {
            return self::map($docBlock, $f);
        };
    }
}