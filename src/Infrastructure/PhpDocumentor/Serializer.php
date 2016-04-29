<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;


use phpDocumentor\Reflection\DocBlock;

/**
 * Class Serializer
 * @package NicMart\Generics\Infrastructure\PhpDocumentor
 */
class Serializer extends \phpDocumentor\Reflection\DocBlock\Serializer
{
    /**
     * @param DocBlock $docblock
     *
     * @return mixed
     */
    public function getDocComment(DocBlock $docblock)
    {
        return
            $this->removeBlankLines(
            $this->addLineBeforeReturn(
                parent::getDocComment($docblock)
            ));
    }

    /**
     * @param $docBlockText
     * @return mixed
     */
    private function removeBlankLines($docBlockText)
    {
        return preg_replace(
            "#/\*\*\n(\s+\*\s*\n)+#",
            "/**\n",
            $docBlockText
        );
    }

    /**
     * @param $docBlockText
     * @return mixed
     */
    private function addLineBeforeReturn($docBlockText)
    {
        return preg_replace(
            "/(\s+\*)( @return)/",
            "\$1\n\$1\$2",
            $docBlockText
        );
    }
}