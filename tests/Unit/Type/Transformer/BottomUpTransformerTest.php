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
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\Type;

class BottomUpTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $innerTransformation = new ByCallableTypeTransformer(
            function (Type $type) {

                if ($type instanceof SimpleReferenceType) {
                    return new SimpleReferenceType(
                        FullName::fromString(
                            strtolower($type->name()->toString())
                        )
                    );
                }

                if ($type instanceof ParametrizedType) {
                    $arguments = $type->arguments();
                    return $arguments[0];
                }

                return $type;
            }
        );

        $type = new ParametrizedType(
            FullName::fromString("ParamType1"), array(
                new ParametrizedType(
                    FullName::fromString("ParamType2"), array(
                        new SimpleReferenceType(FullName::fromString("C\\D"))
                    )
                ),
                new SimpleReferenceType(FullName::fromString("A\\B")),
            )
        );

        $bottomUp = new BottomUpTransformer($innerTransformation);

        $this->assertEquals(
            new SimpleReferenceType(FullName::fromString("c\\d")),
            $bottomUp->transform($type)
        );
    }
}
