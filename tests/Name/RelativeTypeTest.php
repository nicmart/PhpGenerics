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


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\Use_;

class RelativeTypeTest extends TypeTestCase
{
    /**
     * @test
     * @dataProvider data
     * @param RelativeName $relativeType
     * @param NamespaceContext $context
     * @param FullName $fullType
     */
    public function it_transforms_to_full_type(
        RelativeName $relativeType,
        NamespaceContext $context,
        FullName $fullType
    )
    {
        $this->assertEquals(
            $fullType,
            $relativeType->toFullType($context)
        );
    }
}
