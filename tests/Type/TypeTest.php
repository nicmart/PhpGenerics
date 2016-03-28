<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type;


use NicMart\Generics\Type\Context\Namespace_;
use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\Context\Use_;

class TypeTest extends TypeTestCase
{
    /**
     *
     * @test
     * @dataProvider data
     * @param RelativeType $relativeType
     * @param NamespaceContext $context
     * @param Type $fullType
     */
    public function it_transforms_to_relative_type(
        RelativeType $relativeType,
        NamespaceContext $context,
        Type $fullType
    )
    {
        $this->assertEquals(
            $relativeType,
            $fullType->toRelativeType($context)
        );
    }

    /**
     * @test
     */
    public function it_transforms_to_relative_type_by_ns()
    {
        $fullType = new Type("Ns1\\Ns2\\T");

        $this->assertEquals(
            new RelativeType("T"),
            $fullType->toRelativeTypeForNamespace(
                new Namespace_("Ns1\\Ns2")
            )
        );
    }
}
