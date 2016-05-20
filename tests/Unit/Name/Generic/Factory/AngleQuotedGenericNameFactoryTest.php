<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic\Factory;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\GenericName;

/**
 * Class AngleQuotedGenericNameFactoryTest
 * @package NicMart\Generics\Name\Generic\Factory
 */
class AngleQuotedGenericNameFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_checks_if_generic()
    {
        $factory = new AngleQuotedGenericNameFactory();

        $this->assertTrue(
            $factory->isGeneric(FullName::fromString("Ns\\Class«T»"))
        );

        $this->assertFalse(
            $factory->isGeneric(FullName::fromString("Ns\\Class"))
        );
    }

    /**
     * @test
     */
    public function it_transforms_to_generic()
    {
        $factory = new AngleQuotedGenericNameFactory();

        $qualifier = NamespaceContext::fromNamespaceName(
            '\NicMart\Generics\Variable'
        );

        $this->assertEquals(
            new GenericName(
                FullName::fromString("Ns\\Class"),
                array(
                    FullName::fromString('\NicMart\Generics\Variable\T'),
                    FullName::fromString('\NicMart\Generics\Variable\S')
                )
            ),
            $factory->toGeneric(
                FullName::fromString("Ns\\Class«T·S»"),
                $qualifier
            )
        );
    }

    /**
     * @test
     */
    public function it_transforms_to_fullname()
    {
        $factory = new AngleQuotedGenericNameFactory();
        $generic = new GenericName(
            FullName::fromString("Ns\\Class"),
            array(
                FullName::fromString('\NicMart\Generics\Variable\T'),
                FullName::fromString('\NicMart\Generics\Variable\S')
            )
        );

        $this->assertEquals(
            FullName::fromString("Ns\\Class«T·S»"),
            $factory->fromGeneric($generic)
        );
    }
}
