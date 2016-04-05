<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name;


use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;

class TypeTest extends TypeTestCase
{
    /**
     *
     * @test
     * @dataProvider data
     * @param RelativeName $relativeType
     * @param NamespaceContext $context
     * @param FullName $fullType
     */
    public function it_transforms_to_relative_type(
        RelativeName $relativeType,
        NamespaceContext $context,
        FullName $fullType
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
        $fullType = FullName::fromString("Ns1\\Ns2\\T");

        $this->assertEquals(
            RelativeName::fromString("T"),
            $fullType->toRelativeTypeForNamespace(
                Namespace_::fromString("Ns1\\Ns2")
            )
        );
    }
}
