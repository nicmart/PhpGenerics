<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Autoloader;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\NamespaceContextExtractor;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Source\SourceUnit;
use NicMart\Generics\Type\Compiler\CompilationResult;
use NicMart\Generics\Type\Loader\ParametrizedTypeLoader;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\PrimitiveType;
use NicMart\Generics\Type\Serializer\TypeSerializer;
use PHPUnit_Framework_MockObject_MockObject;

class ByContextGenericAutoloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeParser|PHPUnit_Framework_MockObject_MockObject
     */
    private $typeParser;

    /**
     * @var ParametrizedTypeLoader|PHPUnit_Framework_MockObject_MockObject
     */
    private $parametrizedTypeLoader;

    /**
     * @var ByContextGenericAutoloader
     */
    private $genAutoloader;

    public function setUp()
    {
        $this->typeParser = $this->getMock('\NicMart\Generics\Type\Parser\TypeParser');
        $this->parametrizedTypeLoader = $this->getMock('\NicMart\Generics\Type\Loader\ParametrizedTypeLoader');

        $this->genAutoloader = new ByContextGenericAutoloader(
            $this->typeParser,
            $this->parametrizedTypeLoader
        );
    }


    /**
     * @test
     */
    public function it_loads_parametrized_types()
    {
        $className = '\\Foo\\Bar';
        $ns = NamespaceContext::fromNamespaceName("foo");

        $this->typeParser
            ->expects($this->once())
            ->method("parse")
            ->with(FullName::fromString($className), $ns)
            ->willReturn(
                $pt = new ParametrizedType(
                    FullName::fromString("boo"),
                    array()
                )
            )
        ;

        $this->parametrizedTypeLoader
            ->expects($this->once())
            ->method("load")
            ->with($pt)
            ->willReturn(new CompilationResult(
                new SourceUnit(
                    FullName::fromString("foo"),
                    "aaaa"
                ),
                $this->getMock(TypeSerializer::class),
                []
            ))
        ;

        $this->genAutoloader->autoload($className, $ns);
    }

    /**
     * @test
     */
    public function it_ignores_other_types()
    {
        $className = '\\Foo\\Bar';
        $ns = NamespaceContext::fromNamespaceName("foo");


        $this->typeParser
            ->expects($this->once())
            ->method("parse")
            ->with(FullName::fromString($className), $ns)
            ->willReturn(
                $pt = new PrimitiveType(FullName::fromString("string"))
            )
        ;

        $this->parametrizedTypeLoader
            ->expects($this->never())
            ->method("load")
        ;

        $this->genAutoloader->autoload($className, $ns);
    }
}
