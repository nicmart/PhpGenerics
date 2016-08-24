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
use NicMart\Generics\Type\Loader\ParametrizedTypeLoader;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Parser\TypeParser;
use NicMart\Generics\Type\PrimitiveType;
use PHPUnit_Framework_MockObject_MockObject;

class GenAutoloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeParser|PHPUnit_Framework_MockObject_MockObject
     */
    private $typeParser;

    /**
     * @var NamespaceContextExtractor|PHPUnit_Framework_MockObject_MockObject
     */
    private $namespaceContextExtractor;

    /**
     * @var ParametrizedTypeLoader|PHPUnit_Framework_MockObject_MockObject
     */
    private $parametrizedTypeLoader;

    /**
     * @var ByFileGenericAutoloader
     */
    private $genAutoloader;

    public function setUp()
    {
        $this->typeParser = $this->getMock('\NicMart\Generics\Type\Parser\TypeParser');
        $this->namespaceContextExtractor = $this->getMock('\NicMart\Generics\Name\Context\NamespaceContextExtractor');
        $this->parametrizedTypeLoader = $this->getMock('\NicMart\Generics\Type\Loader\ParametrizedTypeLoader');

        $this->genAutoloader = new ByFileGenericAutoloader(
            $this->namespaceContextExtractor,
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
        $fileName = __DIR__ . "/test.php";
        $fileContent = file_get_contents($fileName);
        
        $this->namespaceContextExtractor
            ->expects($this->once())
            ->method("contextOf")
            ->with($fileContent)
            ->willReturn(
                $ns = NamespaceContext::fromNamespaceName("foo")
            )
        ;

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
        ;

        $this->genAutoloader->autoload($className, $fileName);
    }

    /**
     * @test
     */
    public function it_ingores_other_types()
    {
        $className = '\\Foo\\Bar';
        $fileName = __DIR__ . "/test.php";
        $fileContent = file_get_contents($fileName);

        $this->namespaceContextExtractor
            ->expects($this->once())
            ->method("contextOf")
            ->with($fileContent)
            ->willReturn(
                $ns = NamespaceContext::fromNamespaceName("foo")
            )
        ;

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

        $this->genAutoloader->autoload($className, $fileName);
    }
}
