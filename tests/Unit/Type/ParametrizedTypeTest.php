<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Type\Transformer\ByCallableTypeTransformer;

/**
 * Class ParametrizedTypeTest
 * @package NicMart\Generics\Type
 */
class ParametrizedTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $typeTransf = new ByCallableTypeTransformer(
            function (Type $type) {
                return new SimpleReferenceType(
                    FullName::fromString(strtolower($type->name()->toString()))
                );
            }
        );

        $type = new ParametrizedType(
            FullName::fromString("ParamType1"), array(
                new SimpleReferenceType(FullName::fromString("A\\B")),
                new SimpleReferenceType(FullName::fromString("C\\D"))
            )
        );

        $this->assertEquals(
            new ParametrizedType(
                FullName::fromString("ParamType1"), array(
                    new SimpleReferenceType(FullName::fromString("a\\b")),
                    new SimpleReferenceType(FullName::fromString("c\\d"))
                )
            ),
            $type->map($typeTransf)
        );
    }

    public function testFoldBottomUp()
    {
        $type = new ParametrizedType(
            FullName::fromString("ParamType1"), array(
                $t1 = new SimpleReferenceType(FullName::fromString("A\\B")),
                $t2 = new SimpleReferenceType(FullName::fromString("C\\D"))
            )
        );

        $foldFunction = function (array $z, Type $t) {
            $z[] = $t;
            return $z;
        };

        $this->assertEquals(
            [$t1, $t2, $type],
            $type->bottomUpFold([], $foldFunction)
        );
    }
}
