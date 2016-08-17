<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\PhpDocumentor;


use NicMart\Generics\Name\RelativeName;
use PHPUnit_Framework_TestCase;


class DocBlockFactory extends PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $relativeNameClass = RelativeName::class;
        $docComment = <<<EOF
        
/**
 * This is an example of a summary.
 *
 * And here is an example of the description
 * of a DocBlock that can span multiple lines.
 *
 * @see \phpDocumentor\Reflection\DocBlock\StandardTagFactory
 * @param string \$test
 * @param FullName \$name
 * @return $relativeNameClass
 */
EOF;
        
        $factory = TypeAnnotatorBlockFactory::createInstance();

        $docblock = $factory->create($docComment);

        $params = $docblock->getTagsByName("param");
        
        $this->assertInstanceOf(
            AnnotatedType::class,
            $params[0]
        );

        $this->assertInstanceOf(
            AnnotatedType::class,
            $params[1]
        );
    }
}