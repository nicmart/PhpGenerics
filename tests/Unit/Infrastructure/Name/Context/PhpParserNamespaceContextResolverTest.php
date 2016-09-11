<?php
/**
 * This file is part of php-generics
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\Infrastructure\Name\Context;


use NicMart\Generics\AST\Context\NamespaceContextNodeExtractor;
use NicMart\Generics\Name\Context\Namespace_;
use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;

class PhpParserNamespaceContextResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_gets_context_from_backtrace()
    {
        $resolver = new PhpParserNamespaceContextExtractor(
            (new ParserFactory)->create(ParserFactory::ONLY_PHP5),
            new NamespaceContextNodeExtractor()
        );

        $context = $resolver->contextOf(
            file_get_contents(__DIR__ . "/caller.php")
        );

        $this->assertEquals(
            NamespaceContext::emptyContext()
                ->withNamespace(Namespace_::fromString('Ns1\Ns2'))
                ->withUse(Use_::fromStrings("A\\B", "C")),
            $context
        );
    }
}
