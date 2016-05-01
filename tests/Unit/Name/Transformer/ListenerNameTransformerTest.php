<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Name\Transformer;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\FullName;
use NicMart\Generics\Name\Name;

class ListenerNameTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_calls_listener()
    {
        $from = FullName::fromString("foo");
        $to = FullName::fromString("bar");
        $context = NamespaceContext::emptyContext();

        $transformer = $this->getMock(
            '\NicMart\Generics\Name\Transformer\NameTransformer'
        );

        $transformer
            ->expects($this->exactly(2))
            ->method('transformName')
            ->withConsecutive(
                array($from, $context),
                array($from, $context)
            )
            // The second time the transformer does not transform
            ->willReturnOnConsecutiveCalls(
                $to,
                $from
            )
        ;

        $listener = $this->getMock(
            '\NicMart\Generics\Name\Transformer\Listener'
        );
        $listener
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $from,
                $to
            )
        ;

        $listenerTransformer = new ListenerNameTransformer(
            $transformer,
            $listener
        );

        $this->assertEquals(
            $to,
            $listenerTransformer->transformName($from, $context)
        );
        $this->assertEquals(
            $from,
            $listenerTransformer->transformName($from, $context)
        );
    }
}

interface Listener
{
    /**
     * @param Name $name
     * @return Name
     */
    public function __invoke(Name $name);
}
