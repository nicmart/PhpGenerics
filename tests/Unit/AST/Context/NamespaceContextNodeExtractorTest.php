<?php
/**
 * This file is part of PhpStorm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicolo@martini.io>
 */

namespace NicMart\Generics\AST\Context;


use NicMart\Generics\Name\Context\NamespaceContext;
use NicMart\Generics\Name\Context\Use_;
use NicMart\Generics\Name\Context\Uses;
use PhpParser\ParserFactory;

class NamespaceContextNodeExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtract()
    {
        $code = <<<CODE
<?php
   
namespace A\B\C;

use Foo\Bar as Blah;
use Bar\Baz;

class A {

}

CODE;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
        $nodes = $parser->parse($code);
        $extractor = new NamespaceContextNodeExtractor();

        $this->assertEquals(
            NamespaceContext::fromNamespaceName("A\\B\\C")
                ->withUse(Use_::fromStrings("Foo\\Bar", "Blah"))
                ->withUse(Use_::fromStrings("Bar\\Baz", "Baz"))
            ,
            $extractor->extractFromArray($nodes)
        );
    }
}
