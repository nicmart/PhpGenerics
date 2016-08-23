<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Generic\Parser;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\GenericNameApplication;
use NicMart\Generics\Name\RelativeName;

class AngleQuotedGenericTypeNameParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_checks_if_generic()
    {
        $parser = new AngleQuotedGenericTypeNameParser();

        $this->assertTrue(
            $parser->isGeneric(FullName::fromString("Ns\\Class«T»"))
        );

        $this->assertFalse(
            $parser->isGeneric(FullName::fromString("Ns\\Class"))
        );
    }

    /**
     * @test
     */
    public function it_parses()
    {
        $parser = new AngleQuotedGenericTypeNameParser();

        $this->assertEquals(
            new GenericNameApplication(
                FullName::fromString("Ns\\Class"),
                array(
                    RelativeName::fromString('T'),
                    RelativeName::fromString('S')
                )
            ),
            $parser->parse(
                FullName::fromString("Ns\\Class«T·S»")
            )
        );
    }

    public function it_serializes()
    {
        $parser = new AngleQuotedGenericTypeNameParser();

        $application = new GenericNameApplication(
            FullName::fromString("Ns\\Class"),
            array(
                RelativeName::fromString('T'),
                RelativeName::fromString('S')
            )
        );

        $this->assertEquals(
            FullName::fromString("Ns\\Class«T·S»"),
            $parser->serialize($application)
        );
    }
}
