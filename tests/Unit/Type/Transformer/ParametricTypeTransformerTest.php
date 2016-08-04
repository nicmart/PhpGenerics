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

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\VariableType;

/**
 * Class ParametricTypeTransformerTest
 * @package NicMart\Generics\Type\Transformer
 */
class ParametricTypeTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $genericType = new GenericType(
            FullName::fromString("A\\B"), array(
                new VariableType(FullName::fromString(
                    '\NicMart\Generics\Variable\T'
                )),
                new VariableType(FullName::fromString(
                    '\NicMart\Generics\Variable\U'
                )),
            )
        );

        $parametrisedType = new ParametrizedType(
            FullName::fromString("A\\B"), array(
                new PrimitiveType(FullName::fromString("string")),
                new SimpleReferenceType(FullName::fromString("Foo"))
            )
        );

        $transformer = new ParametricTypeTransformer(
            $genericType,
            $parametrisedType
        );

        $this->assertEquals(
            new PrimitiveType(FullName::fromString("string")),
            $transformer->transform(
                new VariableType(FullName::fromString(
                    '\NicMart\Generics\Variable\T'
                ))
            )
        );

        $this->assertEquals(
            new SimpleReferenceType(FullName::fromString("Foo")),
            $transformer->transform(
                new VariableType(FullName::fromString(
                    '\NicMart\Generics\Variable\U'
                ))
            )
        );

        $this->assertEquals(
            $type = new SimpleReferenceType(FullName::fromString("BlaBla")),
            $transformer->transform($type)
        );
    }
}
