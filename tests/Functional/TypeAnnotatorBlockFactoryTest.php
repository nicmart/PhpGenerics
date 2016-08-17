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


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Parser\AngleQuotedGenericTypeNameParser;
use NicMart\Generics\Name\RelativeName;
use NicMart\Generics\Type\Parser\GenericTypeParserAndSerializer;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\SimpleReferenceType;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit_Framework_TestCase;


class TypeAnnotatorBlockFactoryTest extends PHPUnit_Framework_TestCase
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
        
        $factory = TypeAnnotatorBlockFactory::createInstance(
            new GenericTypeParserAndSerializer(
                new AngleQuotedGenericTypeNameParser()
            )
        );

        $context = new Context("\\NicMart\\Generics\\Name");

        $docblock = $factory->create($docComment, $context);

        $params = $docblock->getTagsByName("param");
        
        $this->assertEquals(
            new Param(
                "test",
                new AnnotatedType(
                    new String_(),
                    new PrimitiveType(
                        FullName::fromString("string")
                    )
                ),
                false,
                new Description("")
            ),
            $params[0]
        );

        $this->assertEquals(
            new Param(
                "name",
                new AnnotatedType(
                    new Object_(new Fqsen("\\" . FullName::class)),
                    new SimpleReferenceType(
                        FullName::fromString(FullName::class)
                    )
                ),
                false,
                new Description("")
            ),
            $params[1]
        );
    }
}