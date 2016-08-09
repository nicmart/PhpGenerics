<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Type\Resolver;


use NicMart\Generics\Composer\DirectoryResolver;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\NamespaceContextExtractor;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Generic\Parser\AngleQuotedGenericTypeNameParser;
use NicMart\Generics\Type\GenericType;
use NicMart\Generics\Type\ParametrizedType;
use NicMart\Generics\Type\Parser\GenericTypeParserAndSerializer;
use NicMart\Generics\Type\SimpleReferenceType;
use NicMart\Generics\Type\VariableType;

class ComposerGenericTypeResolverTest extends \PHPUnit_Framework_TestCase
{
    public function directoryResolver(FullName $appliedGeneric)
    {
        /** @var DirectoryResolver $directoryResolver */
        $directoryResolver = $this->getMock(
            '\NicMart\Generics\Composer\DirectoryResolver'
        );

        $directoryResolver
            ->expects($this->once())
            ->method('directories')
            ->with($appliedGeneric->up()->toString())
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
        $parser = new GenericTypeParserAndSerializer(
            new AngleQuotedGenericTypeNameParser()
        );

        $parametrizedType = new ParametrizedType(
            FullName::fromString(
                '\NicMart\Generics\Composer\Fixtures\Test'
            ),
            array(new SimpleReferenceType(
                FullName::fromString("blabla")
            ))
        );

        $expectedGenericType = new GenericType(
            FullName::fromString(
                '\NicMart\Generics\Composer\Fixtures\Test'
            ), array(
                new VariableType(
                    FullName::fromString('NicMart\Generics\Variable\B')
                )
            )
        );

        $resolver = new ComposerGenericTypeResolver(
            $parser,
            $parser,
            $this->directoryResolver($parametrizedType->name()),
            $this->namespaceExtractor("/Fixtures/Test«B».php")
        );

        $this->assertEquals(
            $expectedGenericType,
            $resolver->toGenericType($parametrizedType)
        );
    }

    /**
     * @test
     */
    public function it_resolves_classes()
    {
        $parser = new GenericTypeParserAndSerializer(
            new AngleQuotedGenericTypeNameParser()
        );

        $parametrizedType = new ParametrizedType(
            FullName::fromString(
                '\NicMart\Generics\Composer\Fixtures\Class'
            ),
            array(new SimpleReferenceType(
                FullName::fromString("blabla")
            ))
        );

        $expectedGenericType = new GenericType(
            FullName::fromString(
                '\NicMart\Generics\Composer\Fixtures\Class'
            ), array(
                new VariableType(
                    FullName::fromString('NicMart\Generics\Variable\B')
                )
            )
        );

        $resolver = new ComposerGenericTypeResolver(
            $parser,
            $parser,
            $this->directoryResolver($parametrizedType->name()),
            $this->namespaceExtractor("/Fixtures/Class«B».php")
        );

        $this->assertEquals(
            $expectedGenericType,
            $resolver->toGenericType($parametrizedType)
        );
    }

    /**
     * @param $file
     * @return NamespaceContextExtractor
     */
    private function namespaceExtractor($file)
    {
        $namespaceExtractor = $this->getMock(
            '\NicMart\Generics\Name\Context\NamespaceContextExtractor'
        );

        $namespaceExtractor
            ->expects($this->once())
            ->method("contextOf")
            ->with(file_get_contents(__DIR__ . $file))
            ->willReturn(NamespaceContext::fromNamespaceName(
                'NicMart\Generics\Variable'
            ))
        ;

        return $namespaceExtractor;
    }
}
