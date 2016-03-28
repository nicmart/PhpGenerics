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


use NicMart\Generics\Type\Context\NamespaceContext;
use NicMart\Generics\Type\Context\Namespace_;
use NicMart\Generics\Type\Context\Use_;

class RelativeTypeTest extends TypeTestCase
{
    /**
     * @test
     * @dataProvider data
     * @param RelativeType $relativeType
     * @param NamespaceContext $context
     * @param Type $fullType
     */
    public function it_transforms_to_full_type(
        RelativeType $relativeType,
        NamespaceContext $context,
        Type $fullType
    )
    {
        $this->assertEquals(
            $fullType,
            $relativeType->toFullType($context)
        );
    }
}
