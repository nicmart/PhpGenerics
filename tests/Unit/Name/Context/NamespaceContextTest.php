<?php
/**
 * @author Nicolò Martini - <nicolo.martini@dxi.eu>
 *
 * Created on 06/04/2016, 14:13
 * Copyright (C) DXI Ltd
 */

namespace NicMart\Generics\Name\Context;


use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\NameTestCase;
use NicMart\Generics\Name\RelativeName;

class NamespaceContextTest extends NameTestCase
{
    /**
     * @test
     * @dataProvider data
     * @param RelativeName $relativeName
     * @param NamespaceContext $context
     * @param FullName $fullName
     */
    public function it_qualifies_type(
        RelativeName $relativeName,
        NamespaceContext $context,
        FullName $fullName
    )
    {
        $this->assertEquals(
            $fullName,
            $context->qualify($relativeName)
        );
    }

    /**
     *
     * @test
     * @dataProvider data
     * @param RelativeName $relativeName
     * @param NamespaceContext $context
     * @param FullName $fullName
     */
    public function it_transforms_to_relative_type(
        RelativeName $relativeName,
        NamespaceContext $context,
        FullName $fullName
    )
    {
        $this->assertEquals(
            $relativeName,
            $context->simplify($fullName)
        );
    }
}
