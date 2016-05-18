<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Composer;

use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\AngleQuotedGenericNameInterface;
use NicMart\Generics\Name\Generic\Factory\AngleQuotedGenericNameFactory;
use NicMart\Generics\Name\Generic\GenericNameInterface;

/**
 * Class GenericNameResolverTest
 * @package NicMart\Generics\Composer
 * @runTestsInSeparateProcesses
 */
class ComposerGenericNameResolverTest extends \PHPUnit_Framework_TestCase
{
    public function directoryResolver(GenericNameInterface $appliedGeneric)
    {
        /** @var DirectoryResolver $directoryResolver */
        $directoryResolver = $this->getMock(
            '\NicMart\Generics\Composer\DirectoryResolver'
        );

        $directoryResolver
            ->expects($this->once())
            ->method('directories')
            ->with($appliedGeneric->name()->up()->toString())
            ->willReturn(array(
                __DIR__ . "/Fixtures"
            ))
        ;

        return $directoryResolver;
    }

    /**
     * @test
     */
    public function it_resolves_interfaces()
    {
        $appliedGeneric = new AngleQuotedGenericNameInterface(
            FullName::fromString(
                '\NicMart\Generics\Composer\Fixtures\Test«Blabla»'
            )
        );

        $expectedGeneric = new AngleQuotedGenericNameInterface(
            FullName::fromString(
                '\NicMart\Generics\Composer\Fixtures\Test«B»'
            )
        );

        $resolver = new ComposerGenericNameResolver(
            new AngleQuotedGenericNameFactory(),
            $this->directoryResolver($appliedGeneric)
        );

        $this->assertEquals(
            $expectedGeneric,
            $resolver->resolve($appliedGeneric)
        );
    }

    /**
     * @test
     */
    public function it_resolves_classes()
    {
        $appliedGeneric = new AngleQuotedGenericNameInterface(
            FullName::fromString(
                '\NicMart\Generics\Composer\Fixtures\Class«Blabla»'
            )
        );

        $expectedGeneric = new AngleQuotedGenericNameInterface(
            FullName::fromString(
                '\NicMart\Generics\Composer\Fixtures\Class«B»'
            )
        );

        $resolver = new ComposerGenericNameResolver(
            new AngleQuotedGenericNameFactory(),
            $this->directoryResolver($appliedGeneric)
        );

        $this->assertEquals(
            $expectedGeneric,
            $resolver->resolve($appliedGeneric)
        );
    }
}
