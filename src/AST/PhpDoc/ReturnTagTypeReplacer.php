<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\PhpDoc;

use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;

/**
 * Class ReturnTagTypeReplacer
 * @package NicMart\Generics\Compiler\PhpDoc
 */
class ReturnTagTypeReplacer extends ReturnTag
{
    /**
     * @param ReturnTag $tag
     * @param $type
     * @return ReturnTag
     */
    public static function setType(ReturnTag $tag, $type)
    {
        $tag->type = $type;
        $tag->types = null;
        $tag->content = null;

        // Trick to remove a trailing space
        $tag->setContent(trim($tag->getContent()));

        return $tag;
    }
}